<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\SaasFront\SaasFrontBaseController;
use App\Models\FrontCmsHeader;
use App\Models\ThemeSetting;
use App\Models\User;
use App\Services\RegistrationActivationService;
use App\Services\RegistrationContextResolver;
use App\Services\TeamInvitationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends SaasFrontBaseController
{
    protected $redirectTo = '/home';

    public function __construct()
    {
        parent::__construct();

        $this->pageTitle = 'Meecruit | Register';
        $this->pageIcon = 'icon-grid';

        $this->middleware('guest')->except(['logout', 'activateTeamMember', 'showRegistrationForm', 'connectAgency']);
    }

    public function showRegistrationForm(Request $request)
    {
        $envelopeCookie = null;

        // 1. Process raw secret token if provided in query string (Direct Zero-Latency Resolution)
        if ($request->has('token')) {
            $rawSecret = (string) $request->query('token');
            try {
                $envelopeData = app(TeamInvitationService::class)->resolveTokenAndCreateEnvelope($rawSecret, $request);
                $envelopeCookie = $envelopeData['cookie'] ?? null;
            } catch (\Throwable $e) {
                // If token is invalid or expired, continue to fallback
            }
        }

        // 2. Resolve Dynamic Registration Context (SELF, AGENCY, or TEAM)
        $contextResolver = app(RegistrationContextResolver::class);
        $registrationContext = $contextResolver->resolve($request);

        if (auth()->check()) {
            // Self-registration: redirect already-authenticated users to their dashboard
            if ($registrationContext['context'] === RegistrationContextResolver::CONTEXT_SELF) {
                return app(\App\Actions\Auth\ShowLoginFormAction::class)->redirectIfAuthenticated() ?? redirect('/service_company/dashboard');
            }

            // Agency Invitation: show in-page CAS 1-Click Connect Card for already-authenticated agency users
            if ($registrationContext['context'] === RegistrationContextResolver::CONTEXT_AGENCY) {
                $currentUser = auth()->user();
                $authCompany = \Illuminate\Support\Facades\DB::table('companies')->where('id', $currentUser->company_id)->first();
                $invitingCompany = $registrationContext['inviting_company'] ?? null;

                $alreadyConnected = false;
                if (! empty($invitingCompany['id'])) {
                    $alreadyConnected = \Illuminate\Support\Facades\DB::table('staffing_company_relation')
                        ->where('from_company_id', $invitingCompany['id'])
                        ->where('to_company_id', $currentUser->company_id)
                        ->exists();
                }

                $this->setting = $this->global;
                $this->headerData = FrontCmsHeader::first();
                $this->frontTheme = $this->adminTheme;

                return response()->view('auth.login', array_merge($this->data, [
                    'initialMode' => 'agency_connect',
                    'registrationContext' => $registrationContext,
                    'currentUser' => $currentUser,
                    'authCompany' => $authCompany,
                    'alreadyConnected' => $alreadyConnected,
                    'setting' => $this->global,
                    'global' => $this->global,
                    'frontTheme' => ThemeSetting::whereNull('company_id')->first(),
                    'headerData' => FrontCmsHeader::first(),
                    'adminTheme' => $this->adminTheme,
                ]));
            }
        }

        $this->setting = $this->global;
        $this->headerData = FrontCmsHeader::first();
        $this->frontTheme = $this->adminTheme;

        $viewResponse = response()->view('auth.login', array_merge($this->data, [
            'initialMode' => ($registrationContext['context'] === RegistrationContextResolver::CONTEXT_TEAM) ? 'team_invite' : 'signup',
            'registrationContext' => $registrationContext,
            'setting' => $this->global,
            'global' => $this->global,
            'frontTheme' => ThemeSetting::whereNull('company_id')->first(),
            'headerData' => FrontCmsHeader::first(),
            'adminTheme' => $this->adminTheme,
        ]));

        if ($envelopeCookie) {
            $viewResponse->withCookie($envelopeCookie);
        }

        return $viewResponse;
    }

    /**
     * Authenticated CAS Agency Partnership Connection for Option 2 links (Scenario A).
     */
    public function connectAgency(Request $request): JsonResponse
    {
        if (! auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Please sign in to connect your agency account.',
                'redirect' => route('login', ['return_to' => $request->fullUrl()]),
            ], 401);
        }

        $user = auth()->user();
        $code = (string) ($request->input('agency_code') ?? $request->input('direct_user') ?? '');
        if (empty($code)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid or missing invitation reference.'], 422);
        }

        $contextResolver = app(RegistrationContextResolver::class);
        $context = $contextResolver->resolve($request);
        $invitingCompanyId = $context['inviting_company']['id'] ?? null;
        $invitingUserId = $context['inviting_user']['id'] ?? null;

        if (! $invitingCompanyId) {
            return response()->json(['status' => 'error', 'message' => 'Inviting company could not be identified.'], 404);
        }

        if ($invitingCompanyId == $user->company_id) {
            return response()->json(['status' => 'error', 'message' => 'You cannot establish a partnership with your own organization.'], 422);
        }

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            $clientRowFrom = \Illuminate\Support\Facades\DB::table('clients')->where('company_id', $invitingCompanyId)->where('self_company', 1)->first();
            $clientRowTo = \Illuminate\Support\Facades\DB::table('clients')->where('company_id', $user->company_id)->where('self_company', 1)->first();

            $fromClientId = $clientRowFrom ? $clientRowFrom->id : 0;
            $toClientId = $clientRowTo ? $clientRowTo->id : 0;

            // Check if relation already exists (idempotency guarantee)
            $relationExists = \Illuminate\Support\Facades\DB::table('staffing_company_relation')
                ->where('from_company_id', $invitingCompanyId)
                ->where('to_company_id', $user->company_id)
                ->exists();

            if (! $relationExists) {
                \Illuminate\Support\Facades\DB::table('staffing_company_invitation')->insert([
                    'from_client_id' => $fromClientId,
                    'from_company_id' => $invitingCompanyId,
                    'from_user_id' => $invitingUserId ?: 0,
                    'from_status' => 1,
                    'from_created_at' => now()->toDateString(),
                    'to_company_id' => $user->company_id,
                    'to_client_id' => $toClientId,
                    'to_email' => $user->email,
                    'to_status' => 1,
                    'to_invitation_status' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                \Illuminate\Support\Facades\DB::table('staffing_company_relation')->insert([
                    'from_company_id' => $invitingCompanyId,
                    'from_client_id' => $fromClientId,
                    'from_user_id' => $invitingUserId ?: 0,
                    'from_status' => 1,
                    'from_created_at' => date('Y-m-d H:i:s'),
                    'to_company_id' => $user->company_id,
                    'to_client_id' => $toClientId,
                    'to_user_id' => $user->id,
                    'to_status' => 1,
                    'to_created_at' => date('Y-m-d H:i:s'),
                    'final_status' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                $relationId = \Illuminate\Support\Facades\DB::getPdo()->lastInsertId();
                \App\Helper\GeneralMethod::appendClientNestedData($relationId);

                // Multi-Tenant Atomic Cache Invalidation (Rule 6)
                \App\Helper\GeneralMethod::cacheClean('allclients_version_user_'.$user->id);
                \App\Helper\GeneralMethod::cacheClean('alldirectclient_version_user_'.$user->id);
                if ($invitingUserId) {
                    \App\Helper\GeneralMethod::cacheClean('allconsultancy_version_user_'.$invitingUserId);
                    \App\Helper\GeneralMethod::cacheClean('allclients_version_user_'.$invitingUserId);
                }
            }

            \Illuminate\Support\Facades\DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Partnership successfully established! Your agency is now connected.',
                'redirect' => url('/service_company/clients/direct_client/0'),
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('connectAgency failed: '.$e->getMessage(), ['exception' => $e]);

            return response()->json(['status' => 'error', 'message' => 'Failed to establish partnership. Please try again.'], 500);
        }
    }

    /**
     * Activates an invited team member account (Boundary 3).
     */
    public function activateTeamMember(Request $request): JsonResponse
    {
        return app(RegistrationActivationService::class)->activate($request);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }
}
