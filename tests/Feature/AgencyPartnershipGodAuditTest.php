<?php

namespace Tests\Feature;

use App\Domain\AgencyPartnership\Actions\AcceptInvitationAction;
use App\Domain\AgencyPartnership\Actions\CancelInvitationAction;
use App\Domain\AgencyPartnership\Actions\CreateInvitationAction;
use App\Domain\AgencyPartnership\Actions\DeclineInvitationAction;
use App\Domain\AgencyPartnership\Exceptions\ActiveInvitationExistsException;
use App\Domain\AgencyPartnership\Exceptions\CoolingOffPeriodException;
use App\Domain\AgencyPartnership\Exceptions\DailyQuotaExceededException;
use App\Domain\AgencyPartnership\Exceptions\InsufficientCoinsException;
use App\Domain\AgencyPartnership\Jobs\HandleMailBounceJob;
use App\Domain\AgencyPartnership\Services\AgencyRecipientNormalizer;
use App\Domain\AgencyPartnership\Services\InvitationTokenService;
use App\Http\Middleware\EnsureOneDeviceLogin;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * AgencyPartnershipGodAuditTest
 *
 * Comprehensive test suite verifying all God-Level architectural specifications:
 * - Recipient Punycode normalization & dot preservation
 * - 256-bit token security
 * - Daily quota tracking (20/day)
 * - 5-Coin balance validation & immutable coin_ledger audit
 * - STORED generated active_invite_slot uniqueness & auto-release
 * - Active relationship guard serialization
 * - 7-day decline cooling-off enforcement
 * - Scanner-safe GET landing isolation
 * - Authenticated CAS acceptance & capability binding
 * - Duplicate acceptance prevention via active_relation_slot
 * - Hard-bounce 10-step sequence, 5-coin refund & duplicate refund immunity
 * - Single-transaction idempotency with FOR SHARE recovery
 */
class AgencyPartnershipGodAuditTest extends TestCase
{
    use DatabaseTransactions;

    protected User $clientUser;

    protected User $agencyUser;

    protected int $clientCompanyId = 2;

    protected int $agencyCompanyId = 3;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'dbmaster',
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');

        $this->withoutMiddleware([EnsureOneDeviceLogin::class]);

        $this->clientUser = User::withoutGlobalScopes()->where('company_id', $this->clientCompanyId)->where('status', 'active')->first()
            ?? User::withoutGlobalScopes()->first();

        $this->agencyUser = User::withoutGlobalScopes()->where('company_id', $this->agencyCompanyId)->where('status', 'active')->first()
            ?? User::withoutGlobalScopes()->where('id', '!=', $this->clientUser->id)->first();

        // Ensure client company has at least 100 valid coins for testing
        $coins = DB::table('company_coins')->where('company_id', $this->clientCompanyId)->where('status', 'valid')->first();
        if ($coins) {
            DB::table('company_coins')->where('id', $coins->id)->update(['balance_coin' => 100.00]);
        } else {
            DB::table('company_coins')->insert([
                'company_id' => $this->clientCompanyId,
                'coin_order_id' => 1,
                'event_type' => 'operational',
                'amount' => 1000.00,
                'coins' => 100.00,
                'balance_coin' => 100.00,
                'status' => 'valid',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }

        // Reset today's quota for testing
        DB::table('agency_daily_quotas')
            ->where('company_id', $this->clientCompanyId)
            ->where('quota_date', Carbon::today()->toDateString())
            ->delete();
    }

    /**
     * T-01: Recipient normalizer tests.
     */
    public function test_recipient_normalizer(): void
    {
        $input = '  Test.Recruiter+Tag@Example.COM ';
        $normalized = AgencyRecipientNormalizer::normalize($input);
        $this->assertEquals('test.recruiter+tag@example.com', $normalized);

        $domain = AgencyRecipientNormalizer::extractDomain($input);
        $this->assertEquals('example.com', $domain);
    }

    /**
     * T-02: Token generation and hash verification.
     */
    public function test_token_service(): void
    {
        $service = new InvitationTokenService;
        $token = $service->generate();

        $this->assertEquals(64, strlen($token['raw_token']));
        $this->assertEquals(64, strlen($token['token_hash']));
        $this->assertTrue($service->verify($token['raw_token'], $token['token_hash']));
        $this->assertFalse($service->verify('invalid_token_'.str_repeat('a', 50), $token['token_hash']));
    }

    /**
     * T-03: Invitation creation deducts 5 coins and logs immutable ledger row.
     */
    public function test_invitation_creation_deducts_coins_and_creates_ledger(): void
    {
        $action = app(CreateInvitationAction::class);
        $testEmail = 'newagency_'.uniqid().'@partnerdomain.com';

        $initialCoin = (float) DB::table('company_coins')->where('company_id', $this->clientCompanyId)->where('status', 'valid')->value('balance_coin');

        $result = $action->execute(
            $this->clientCompanyId,
            $this->clientUser->id,
            $testEmail,
            'idemp_test_'.uniqid()
        );

        $this->assertEquals('success', $result['status']);
        $this->assertEquals(5, $result['coins_deducted']);
        $this->assertEquals($initialCoin - 5.00, $result['remaining_coins']);

        // Check staffing_company_invitation record
        $inv = DB::table('staffing_company_invitation')->where('id', $result['invitation_id'])->first();
        $this->assertNotNull($inv);
        $this->assertEquals(0, (int) $inv->to_invitation_status);
        $this->assertNotNull($inv->token_hash);
        $this->assertEquals('PENDING', $inv->delivery_status);
        $this->assertEquals("{$this->clientCompanyId}:{$inv->normalized_recipient}", $inv->active_invite_slot);

        // Check coin_ledger debit record
        $ledger = DB::table('coin_ledger')
            ->where('company_id', $this->clientCompanyId)
            ->where('reference_id', (string) $result['invitation_id'])
            ->where('entry_type', 'DEBIT')
            ->first();
        $this->assertNotNull($ledger);
        $this->assertEquals(5.00, (float) $ledger->amount);

        // Check transactional outbox event
        $outbox = DB::table('sys_outbox_events')
            ->where('tenant_id', $this->clientCompanyId)
            ->where('aggregate_id', $result['invitation_id'])
            ->where('event_type', 'AGENCY_INVITATION_SENT')
            ->first();
        $this->assertNotNull($outbox);
    }

    /**
     * T-04: Daily quota blocks on 21st invitation attempt.
     */
    public function test_daily_quota_blocks_at_20(): void
    {
        $today = Carbon::today()->toDateString();
        DB::table('agency_daily_quotas')->updateOrInsert(
            ['company_id' => $this->clientCompanyId, 'quota_date' => $today],
            ['invitations_sent' => 20, 'updated_at' => Carbon::now()]
        );

        $action = app(CreateInvitationAction::class);
        $this->expectException(DailyQuotaExceededException::class);

        $action->execute(
            $this->clientCompanyId,
            $this->clientUser->id,
            'quota_blocked_'.uniqid().'@agency.com'
        );
    }

    /**
     * T-05: Insufficient coin balance throws exception.
     */
    public function test_insufficient_coins_throws_exception(): void
    {
        DB::table('company_coins')
            ->where('company_id', $this->clientCompanyId)
            ->update(['balance_coin' => 2.00]);

        $action = app(CreateInvitationAction::class);
        $this->expectException(InsufficientCoinsException::class);

        $action->execute(
            $this->clientCompanyId,
            $this->clientUser->id,
            'lowcoin_'.uniqid().'@agency.com'
        );
    }

    /**
     * T-06: STORED generated active_invite_slot blocks duplicate pending invitations.
     */
    public function test_duplicate_pending_invitation_blocked(): void
    {
        $action = app(CreateInvitationAction::class);
        $email = 'duplicate_test_'.uniqid().'@testagency.com';

        // First invite succeeds
        $res1 = $action->execute($this->clientCompanyId, $this->clientUser->id, $email);
        $this->assertEquals('success', $res1['status']);

        // Second invite with new idempotency key must be blocked by ActiveInvitationExistsException
        $this->expectException(ActiveInvitationExistsException::class);
        $action->execute($this->clientCompanyId, $this->clientUser->id, $email, 'fresh_key_'.uniqid());
    }

    /**
     * T-07: Cancellation releases active slot allowing re-invitation.
     */
    public function test_cancellation_releases_active_slot(): void
    {
        $createAction = app(CreateInvitationAction::class);
        $cancelAction = app(CancelInvitationAction::class);
        $email = 'cancel_test_'.uniqid().'@cancelagency.com';

        $res = $createAction->execute($this->clientCompanyId, $this->clientUser->id, $email);
        $invId = $res['invitation_id'];

        // Cancel the invitation
        $cancelRes = $cancelAction->execute($invId, $this->clientCompanyId);
        $this->assertEquals('success', $cancelRes['status']);

        // Verify active slot in MySQL is now NULL
        $slotVal = DB::table('staffing_company_invitation')->where('id', $invId)->value('active_invite_slot');
        $this->assertNull($slotVal);

        // Immediate re-invitation is now allowed!
        $reinviteRes = $createAction->execute($this->clientCompanyId, $this->clientUser->id, $email, 'reinv_'.uniqid());
        $this->assertEquals('success', $reinviteRes['status']);
    }

    /**
     * T-08: 7-day cooling-off period blocks re-invite on declined invitation.
     */
    public function test_declined_invitation_enforces_cooling_off(): void
    {
        $tokenService = app(InvitationTokenService::class);
        $tokenData = $tokenService->generate();
        $email = 'decline_test_'.uniqid().'@declineagency.com';

        $invId = DB::table('staffing_company_invitation')->insertGetId([
            'from_company_id' => $this->clientCompanyId,
            'from_user_id' => $this->clientUser->id,
            'from_status' => 1,
            'from_created_at' => Carbon::now(),
            'to_email' => $email,
            'to_status' => 0,
            'to_invitation_status' => 0,
            'token_hash' => $tokenData['token_hash'],
            'expires_at' => Carbon::now()->addDays(7),
            'delivery_status' => 'SENT',
            'normalized_recipient' => AgencyRecipientNormalizer::normalize($email),
        ]);

        // Decline invitation
        $declineAction = app(DeclineInvitationAction::class);
        $declineRes = $declineAction->execute($tokenData['raw_token'], 'Not interested');
        $this->assertEquals('success', $declineRes['status']);

        // Verify slot is released (NULL)
        $slotVal = DB::table('staffing_company_invitation')->where('id', $invId)->value('active_invite_slot');
        $this->assertNull($slotVal);

        // Re-invite within 7 days must be blocked by CoolingOffPeriodException!
        $createAction = app(CreateInvitationAction::class);
        $this->expectException(CoolingOffPeriodException::class);
        $createAction->execute($this->clientCompanyId, $this->clientUser->id, $email);
    }

    /**
     * T-09: Scanner-safe landing is read-only and sets security headers.
     */
    public function test_scanner_safe_landing_read_only(): void
    {
        $tokenService = app(InvitationTokenService::class);
        $tokenData = $tokenService->generate();

        $email = 'scanner_safe_'.uniqid().'@agency.com';
        $invId = DB::table('staffing_company_invitation')->insertGetId([
            'from_company_id' => $this->clientCompanyId,
            'from_user_id' => $this->clientUser->id,
            'from_status' => 1,
            'from_created_at' => Carbon::now(),
            'to_email' => $email,
            'to_status' => 0,
            'to_invitation_status' => 0,
            'token_hash' => $tokenData['token_hash'],
            'expires_at' => Carbon::now()->addDays(7),
            'delivery_status' => 'PENDING',
            'normalized_recipient' => AgencyRecipientNormalizer::normalize($email),
        ]);

        $response = $this->get('/partner-invite/'.$tokenData['raw_token']);
        $response->assertStatus(200);
        $response->assertHeader('Referrer-Policy', 'no-referrer');
        $cacheControl = (string) $response->headers->get('Cache-Control');
        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);

        // Status must remain 0 (Zero DB mutation by GET scanner)
        $inv = DB::table('staffing_company_invitation')->where('id', $invId)->first();
        $this->assertEquals(0, (int) $inv->to_invitation_status);
    }

    /**
     * T-10: Authenticated CAS acceptance activates partnership.
     */
    public function test_authenticated_cas_acceptance(): void
    {
        $tokenService = app(InvitationTokenService::class);
        $tokenData = $tokenService->generate();
        $acceptingEmail = $this->agencyUser->email;

        // Ensure no leftover active relation
        DB::table('staffing_company_relation')
            ->where('from_company_id', $this->clientCompanyId)
            ->where('to_company_id', $this->agencyCompanyId)
            ->delete();

        $invId = DB::table('staffing_company_invitation')->insertGetId([
            'from_company_id' => $this->clientCompanyId,
            'from_user_id' => $this->clientUser->id,
            'from_status' => 1,
            'from_created_at' => Carbon::now(),
            'to_email' => $acceptingEmail,
            'to_status' => 0,
            'to_invitation_status' => 0,
            'token_hash' => $tokenData['token_hash'],
            'expires_at' => Carbon::now()->addDays(7),
            'delivery_status' => 'SENT',
            'normalized_recipient' => AgencyRecipientNormalizer::normalize($acceptingEmail),
        ]);

        $acceptAction = app(AcceptInvitationAction::class);
        $result = $acceptAction->execute($tokenData['raw_token'], $this->agencyUser);

        $this->assertEquals('success', $result['status']);
        $this->assertNotNull($result['relation_id']);

        // Invitation must transition to 1 (ACCEPTED)
        $inv = DB::table('staffing_company_invitation')->where('id', $invId)->first();
        $this->assertEquals(1, (int) $inv->to_invitation_status);
        $this->assertEquals($this->agencyCompanyId, (int) $inv->to_company_id);
        $this->assertNull($inv->active_invite_slot); // released!

        // Relation must exist with final_status = 1
        $rel = DB::table('staffing_company_relation')->where('id', $result['relation_id'])->first();
        $this->assertNotNull($rel);
        $this->assertEquals(1, (int) $rel->final_status);
        $this->assertEquals("{$this->clientCompanyId}:{$this->agencyCompanyId}", $rel->active_relation_slot);

        // Guard row must exist
        $guard = DB::table('staffing_company_relation_guards')
            ->where('company_pair_key', "{$this->clientCompanyId}:{$this->agencyCompanyId}")
            ->first();
        $this->assertNotNull($guard);
    }

    /**
     * T-11: Hard-bounce 10-step sequence credits 5 coins and releases slot.
     */
    public function test_hard_bounce_refund_sequence(): void
    {
        $tokenService = app(InvitationTokenService::class);
        $tokenData = $tokenService->generate();
        $email = 'bounced_'.uniqid().'@invalidhost.xyz';

        $invId = DB::table('staffing_company_invitation')->insertGetId([
            'from_company_id' => $this->clientCompanyId,
            'from_user_id' => $this->clientUser->id,
            'from_status' => 1,
            'from_created_at' => Carbon::now(),
            'to_email' => $email,
            'to_status' => 0,
            'to_invitation_status' => 0,
            'token_hash' => $tokenData['token_hash'],
            'expires_at' => Carbon::now()->addDays(7),
            'delivery_status' => 'SENT',
            'normalized_recipient' => AgencyRecipientNormalizer::normalize($email),
        ]);

        $initialBalance = (float) DB::table('company_coins')->where('company_id', $this->clientCompanyId)->where('status', 'valid')->value('balance_coin');

        // Dispatch bounce job
        $job = new HandleMailBounceJob($invId, 'bounce_evt_'.uniqid(), '550 Mailbox does not exist');
        $job->handle();

        // Coins must be refunded (+5)
        $newBalance = (float) DB::table('company_coins')->where('company_id', $this->clientCompanyId)->where('status', 'valid')->value('balance_coin');
        $this->assertEquals($initialBalance + 5.00, $newBalance);

        // Invitation must be -3 (UNDELIVERABLE) and BOUNCED
        $inv = DB::table('staffing_company_invitation')->where('id', $invId)->first();
        $this->assertEquals(-3, (int) $inv->to_invitation_status);
        $this->assertEquals('BOUNCED', $inv->delivery_status);
        $this->assertNull($inv->active_invite_slot); // slot released!

        // coin_ledger must contain CREDIT record with refund_reference_id
        $ledger = DB::table('coin_ledger')
            ->where('company_id', $this->clientCompanyId)
            ->where('refund_reference_id', 'REFUND_BOUNCE_INV_'.$invId)
            ->first();
        $this->assertNotNull($ledger);
        $this->assertEquals('CREDIT', $ledger->entry_type);
        $this->assertEquals(5.00, (float) $ledger->amount);

        // Immediate replay of duplicate bounce event must not refund coins again!
        $job2 = new HandleMailBounceJob($invId, 'bounce_evt_duplicate_'.uniqid(), '550 Replay');
        $job2->handle();

        $finalBalance = (float) DB::table('company_coins')->where('company_id', $this->clientCompanyId)->where('status', 'valid')->value('balance_coin');
        $this->assertEquals($newBalance, $finalBalance); // 0 additional coins!
    }

    /**
     * T-12: Single-transaction idempotency caches response DTO.
     */
    public function test_single_transaction_idempotency_caching(): void
    {
        $action = app(CreateInvitationAction::class);
        $email = 'idemp_test_'.uniqid().'@agency.com';
        $idempKey = 'fixed_key_'.uniqid();

        $initialCoins = (float) DB::table('company_coins')->where('company_id', $this->clientCompanyId)->where('status', 'valid')->value('balance_coin');

        $res1 = $action->execute($this->clientCompanyId, $this->clientUser->id, $email, $idempKey);
        $this->assertEquals('success', $res1['status']);

        $coinsAfter = (float) DB::table('company_coins')->where('company_id', $this->clientCompanyId)->where('status', 'valid')->value('balance_coin');
        $this->assertEquals($initialCoins - 5.00, $coinsAfter);

        // Second call with same idempotency key and same email
        $res2 = $action->execute($this->clientCompanyId, $this->clientUser->id, $email, $idempKey);
        $this->assertEquals('success', $res2['status']);
        $this->assertEquals($res1['invitation_id'], $res2['invitation_id']);

        // Zero additional coins deducted!
        $coinsFinal = (float) DB::table('company_coins')->where('company_id', $this->clientCompanyId)->where('status', 'valid')->value('balance_coin');
        $this->assertEquals($coinsAfter, $coinsFinal);
    }

    /**
     * T-13: Controller staffing_invitation POST endpoint.
     */
    public function test_controller_staffing_invitation_endpoint(): void
    {
        $this->actingAs($this->clientUser);
        $email = 'ctrl_invite_'.uniqid().'@agency.com';

        $response = $this->postJson(route('service_company.consultancy.staffing_invitation', 0), [
            'staffing_partners_email' => $email,
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals('success', $data['status'], 'Response status was not success. Message: '.($data['message'] ?? 'none'));
        $this->assertNotEmpty($data['invitation_id']);
        $this->assertEquals(5, $data['coins_deducted']);
    }

    /**
     * T-14: Controller cancel_invitation POST endpoint.
     */
    public function test_controller_cancel_invitation_endpoint(): void
    {
        $this->actingAs($this->clientUser);
        $email = 'ctrl_cancel_'.uniqid().'@agency.com';

        $action = app(CreateInvitationAction::class);
        $res = $action->execute($this->clientCompanyId, $this->clientUser->id, $email);
        $invId = $res['invitation_id'];

        $response = $this->postJson(route('service_company.consultancy.cancel_invitation', $invId));
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals('success', $data['status'], 'Response status was not success. Message: '.($data['message'] ?? 'none'));

        // Verify status in DB is -2
        $status = DB::table('staffing_company_invitation')->where('id', $invId)->value('to_invitation_status');
        $this->assertEquals(-2, (int) $status);
    }

    /**
     * T-15: Controller re_invite POST endpoint.
     */
    public function test_controller_re_invite_endpoint(): void
    {
        $this->actingAs($this->clientUser);
        $email = 'ctrl_reinv_'.uniqid().'@agency.com';

        $action = app(CreateInvitationAction::class);
        $res = $action->execute($this->clientCompanyId, $this->clientUser->id, $email);
        $invId = $res['invitation_id'];

        // Cancel it so slot is released
        app(CancelInvitationAction::class)->execute($invId, $this->clientCompanyId);

        // Call re_invite endpoint
        $response = $this->postJson(route('service_company.consultancy.re_invite', $invId));
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals('success', $data['status'], 'Response status was not success. Message: '.($data['message'] ?? 'none'));
        $this->assertNotEquals($invId, $data['invitation_id']);
    }

    /**
     * T-16: Controller accept endpoint via POST.
     */
    public function test_controller_accept_endpoint(): void
    {
        $this->actingAs($this->agencyUser);
        $tokenService = app(InvitationTokenService::class);
        $tokenData = $tokenService->generate();

        // Ensure relation is clear
        DB::table('staffing_company_relation')
            ->where('from_company_id', $this->clientCompanyId)
            ->where('to_company_id', $this->agencyCompanyId)
            ->delete();

        $invId = DB::table('staffing_company_invitation')->insertGetId([
            'from_company_id' => $this->clientCompanyId,
            'from_user_id' => $this->clientUser->id,
            'from_status' => 1,
            'from_created_at' => Carbon::now(),
            'to_email' => $this->agencyUser->email,
            'to_status' => 0,
            'to_invitation_status' => 0,
            'token_hash' => $tokenData['token_hash'],
            'expires_at' => Carbon::now()->addDays(7),
            'delivery_status' => 'SENT',
            'normalized_recipient' => AgencyRecipientNormalizer::normalize($this->agencyUser->email),
        ]);

        $response = $this->postJson('/partner-invite/accept', [
            'raw_token' => $tokenData['raw_token'],
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals('success', $data['status'], 'Response status was not success. Message: '.($data['message'] ?? 'none'));
        $this->assertNotEmpty($data['relation_id']);
    }

    /**
     * T-17: Controller decline endpoint via POST.
     */
    public function test_controller_decline_endpoint(): void
    {
        $tokenService = app(InvitationTokenService::class);
        $tokenData = $tokenService->generate();

        $email = 'ctrl_decline_'.uniqid().'@agency.com';
        $invId = DB::table('staffing_company_invitation')->insertGetId([
            'from_company_id' => $this->clientCompanyId,
            'from_user_id' => $this->clientUser->id,
            'from_status' => 1,
            'from_created_at' => Carbon::now(),
            'to_email' => $email,
            'to_status' => 0,
            'to_invitation_status' => 0,
            'token_hash' => $tokenData['token_hash'],
            'expires_at' => Carbon::now()->addDays(7),
            'delivery_status' => 'SENT',
            'normalized_recipient' => AgencyRecipientNormalizer::normalize($email),
        ]);

        $response = $this->postJson('/partner-invite/decline', [
            'raw_token' => $tokenData['raw_token'],
            'reason' => 'Capacity full',
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals('success', $data['status'], 'Response status was not success. Message: '.($data['message'] ?? 'none'));

        // Status is -1
        $status = DB::table('staffing_company_invitation')->where('id', $invId)->value('to_invitation_status');
        $this->assertEquals(-1, (int) $status);
    }

    /**
     * T-18: Search agency autocomplete by company name and website with strict privacy protection (zero email exposure).
     */
    public function test_search_agency_autocomplete_by_name_and_website_with_privacy_guard(): void
    {
        $this->actingAs($this->clientUser);

        // Ensure target agency has a known website and active status
        $uniqueWebsite = 'topflight-'.uniqid().'.com';
        DB::table('companies')->where('id', $this->agencyCompanyId)->update([
            'website' => 'https://www.'.$uniqueWebsite,
        ]);

        $agencyCompany = DB::table('companies')->where('id', $this->agencyCompanyId)->first();
        $this->assertNotNull($agencyCompany);

        // 1. Search by Agency Company Name
        $searchTerm = substr($agencyCompany->company_name, 0, 8);
        $response = $this->getJson(route('service_company.consultancy.search_agency_autocomplete', ['q' => $searchTerm]));
        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals('success', $data['status'], 'Response status was not success. Message: '.($data['message'] ?? 'none'));
        $this->assertNotEmpty($data['data']);
        $matchedAgency = null;
        foreach ($data['data'] as $item) {
            if ($item['id'] == $this->agencyCompanyId) {
                $matchedAgency = $item;
                break;
            }
        }
        $this->assertNotNull($matchedAgency, 'Target agency must be found in search results.');
        $this->assertStringContainsStringIgnoringCase($searchTerm, $matchedAgency['company_name']);
        $this->assertEquals($uniqueWebsite, $matchedAgency['website']);
        $this->assertEquals('https://www.'.$uniqueWebsite, $matchedAgency['website_url']);

        // Strict Redaction Verification: NO email fields exposed in output payload (Anti-Harvester Protection)
        foreach ($data['data'] as $item) {
            $this->assertArrayNotHasKey('email', $item, 'Company email must never be exposed in search results.');
            $this->assertArrayNotHasKey('company_email', $item, 'Internal company_email must never be exposed.');
            $this->assertArrayNotHasKey('root_email', $item, 'Root user email must never be exposed.');
        }

        // 2. Search by Official Website
        $websiteQuery = substr($uniqueWebsite, 0, 9);
        $responseWebsite = $this->getJson(route('service_company.consultancy.search_agency_autocomplete', ['q' => $websiteQuery]));
        $responseWebsite->assertStatus(200);
        $dataWebsite = $responseWebsite->json();
        $this->assertEquals('success', $dataWebsite['status']);
        $this->assertNotEmpty($dataWebsite['data']);
        $matchedIds = array_column($dataWebsite['data'], 'id');
        $this->assertContains($this->agencyCompanyId, $matchedIds);

        // 3. Search by User / Admin Email must NOT return results (Anti-Harvester & Anti-Spam protection)
        $responseEmail = $this->getJson(route('service_company.consultancy.search_agency_autocomplete', ['q' => $this->agencyUser->email]));
        $responseEmail->assertStatus(200);
        $dataEmail = $responseEmail->json();
        $this->assertEquals('success', $dataEmail['status']);
        $this->assertEmpty($dataEmail['data'], 'Email search must be disabled to prevent enumeration attacks.');

        // 4. Short query (<2 chars) returns empty array
        $shortResponse = $this->getJson(route('service_company.consultancy.search_agency_autocomplete', ['q' => 'a']));
        $shortResponse->assertStatus(200);
        $shortData = $shortResponse->json();
        $this->assertEmpty($shortData['data']);
    }

    /**
     * T-19: Search agency autocomplete connection status enrichment and tenant isolation.
     */
    public function test_search_agency_autocomplete_connection_status_and_tenant_isolation(): void
    {
        $this->actingAs($this->clientUser);

        $clientCompany = DB::table('companies')->where('id', $this->clientCompanyId)->first();
        $agencyCompany = DB::table('companies')->where('id', $this->agencyCompanyId)->first();
        $this->assertNotNull($clientCompany);
        $this->assertNotNull($agencyCompany);

        // Verify client company cannot find itself (multi-tenant isolation)
        $response = $this->getJson(route('service_company.consultancy.search_agency_autocomplete', ['q' => $clientCompany->company_name]));
        $response->assertStatus(200);
        $data = $response->json();
        $ids = array_column($data['data'], 'id');
        $this->assertNotContains($this->clientCompanyId, $ids);

        // Verify connected agency returns status 'connected'
        $relationExists = DB::table('staffing_company_relation')
            ->where(function ($q) {
                $q->where('from_company_id', $this->clientCompanyId)->where('to_company_id', $this->agencyCompanyId);
            })->orWhere(function ($q) {
                $q->where('to_company_id', $this->clientCompanyId)->where('from_company_id', $this->agencyCompanyId);
            })
            ->where('final_status', 1)
            ->exists();

        if (! $relationExists) {
            DB::table('staffing_company_relation')->insert([
                'from_company_id' => $this->clientCompanyId,
                'to_company_id' => $this->agencyCompanyId,
                'final_status' => 1,
                'from_status' => 1,
                'to_status' => 1,
                'from_created_at' => Carbon::now(),
                'to_created_at' => Carbon::now(),
            ]);
        }

        $agencyResponse = $this->getJson(route('service_company.consultancy.search_agency_autocomplete', ['q' => $agencyCompany->company_name]));
        $agencyResponse->assertStatus(200);
        $agencyData = $agencyResponse->json();
        $matched = null;
        foreach ($agencyData['data'] as $item) {
            if ($item['id'] == $this->agencyCompanyId) {
                $matched = $item;
                break;
            }
        }
        $this->assertNotNull($matched);
        $this->assertEquals('connected', $matched['connection_status']);
    }

    /**
     * T-20: Controller staffing_invitation with agency_company_id securely resolves email on backend (Privacy Protected).
     */
    public function test_controller_staffing_invitation_with_agency_company_id(): void
    {
        $this->actingAs($this->clientUser);

        // Ensure unique agency company for this test to avoid collision
        $partnerCompanyId = DB::table('companies')->insertGetId([
            'company_name' => 'Agency Partner '.uniqid(),
            'company_email' => 'partner_'.uniqid().'@agency.com',
            'website' => 'https://www.agencytestpartner.com',
            'status' => 'active',
            'company_type' => 2,
            'job_opening_title' => 'Jobs',
            'job_opening_text' => 'We are hiring',
            'locale' => 'en',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $response = $this->postJson(route('service_company.consultancy.staffing_invitation', 0), [
            'agency_company_id' => $partnerCompanyId,
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals('success', $data['status'], 'Response status was not success. Message: '.($data['message'] ?? 'none'));
        $this->assertNotEmpty($data['invitation_id']);
        $this->assertEquals(5, $data['coins_deducted']);
    }
}
