<?php

namespace Tests\Feature;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * ServiceCompanySubscriptionTest
 *
 * Verifies that the Subscription & Wallet Center page, dual wallet selector partial,
 * and AJAX transaction stream conform to the Rule 20 Modern Card + Sub-Card Enterprise
 * Design System while preserving 100% of billing, cart, and inquiry contracts.
 */
class ServiceCompanySubscriptionTest extends TestCase
{
    public function test_subscription_views_exist(): void
    {
        $this->assertTrue(View::exists('service_company.subscription.newindex'));
        $this->assertTrue(View::exists('service_company.subscription.partials._wallet_cards'));
        $this->assertTrue(View::exists('service_company.ajax_pages.subscription_transactions'));
    }

    public function test_wallet_cards_partial_renders_enterprise_subcards_and_empty_state(): void
    {
        $htmlActive = View::make('service_company.subscription.partials._wallet_cards', [
            'current_coins_operational' => [
                ['title' => 'Enterprise Growth', 'end_date' => '2027-01-15'],
            ],
            'current_coins_sales' => [],
            'remain_coins' => 2450,
            'op_used_pct' => 35,
            'op_coins_used' => 1320,
            'op_total_purchased' => 3770,
            'op_days_left' => 64,
            'remain_coins_sales' => 0,
            'sal_used_pct' => 0,
            'sal_coins_used' => 0,
            'sal_total_purchased' => 0,
            'sal_days_left' => 0,
        ])->render();

        $this->assertStringContainsString('sub-wallets-grid', $htmlActive);
        $this->assertStringContainsString('data-kt-plan="operational"', $htmlActive);
        $this->assertStringContainsString('data-kt-plan="sales"', $htmlActive);
        $this->assertStringContainsString('Enterprise Growth', $htmlActive);
        $this->assertStringContainsString('2,450', $htmlActive);
        $this->assertStringContainsString('NO ACTIVE PLAN', $htmlActive);
        $this->assertStringContainsString('sub-num', $htmlActive);
    }

    public function test_subscription_transactions_renders_rule20_cards_without_raw_tables(): void
    {
        $items = collect([
            (object) [
                'id' => 42,
                'cart_id' => 901,
                'updated_at' => '2026-11-10 14:20:00',
                'product_name' => 'Pro Hiring Pack',
                'event_type' => 'operational',
                'credit' => 1500,
                'currency' => 'INR',
                'total_price' => 4999,
                'payment_mode' => 'Online',
                'payment_gateway' => 'razorpay',
                'status' => 'captured',
            ],
            (object) [
                'id' => 41,
                'cart_id' => 900,
                'updated_at' => '2026-11-08 10:15:00',
                'product_name' => 'Sales Booster',
                'event_type' => 'sales',
                'credit' => 800,
                'currency' => 'USD',
                'total_price' => 99,
                'payment_mode' => 'Online',
                'payment_gateway' => 'stripe',
                'status' => 'created',
            ],
        ]);

        $paginator = new LengthAwarePaginator($items, 2, 50, 1, ['path' => '/service_company/transactions/subscription-ajax']);

        $html = View::make('service_company.ajax_pages.subscription_transactions', [
            'cart_transaction_history' => $paginator,
        ])->render();

        $this->assertStringNotContainsString('<table', $html);
        $this->assertStringContainsString('sub-tx-stream', $html);
        $this->assertStringContainsString('sub-tx-card', $html);
        $this->assertStringContainsString('#00042', $html);
        $this->assertStringContainsString('Pro Hiring Pack', $html);
        $this->assertStringContainsString('Sales Booster', $html);
        $this->assertStringContainsString('Failed', $html);
        $this->assertStringContainsString('Receipt', $html);
    }

    public function test_subscription_transactions_renders_empty_state(): void
    {
        $emptyPaginator = new LengthAwarePaginator([], 0, 50, 1, ['path' => '/service_company/transactions/subscription-ajax']);

        $html = View::make('service_company.ajax_pages.subscription_transactions', [
            'cart_transaction_history' => $emptyPaginator,
        ])->render();

        $this->assertStringContainsString('No transactions yet', $html);
        $this->assertStringContainsString('sub-empty-subcard', $html);
    }

    public function test_newindex_template_enforces_three_tier_architecture_and_preserves_contracts(): void
    {
        $source = file_get_contents(resource_path('views/service_company/subscription/newindex.blade.php'));
        $this->assertNotFalse($source);

        // Rule 20 3-tier structure
        $this->assertStringContainsString('tier1-toolbar-card', $source);
        $this->assertStringContainsString('tier2-subtabs-card', $source);
        $this->assertStringContainsString('tier3-cards-stream', $source);
        $this->assertStringContainsString('id="subscription_search"', $source);
        $this->assertStringContainsString('id="subscription_search_clear_btn"', $source);
        $this->assertStringContainsString('sub-pkg-grid', $source);
        $this->assertStringNotContainsString('<table', $source);

        // Preserved JS & route contracts
        $this->assertStringContainsString('switchWalletCenterTab', $source);
        $this->assertStringContainsString('scrollToWalletPackages', $source);
        $this->assertStringContainsString('upgradeselect', $source);
        $this->assertStringContainsString('btn-contact-sales', $source);
        $this->assertStringContainsString('#wallet-pane-transactions .sub-tx-stream', $source);
        $this->assertStringContainsString('service_company.subscribe.checkoutSubscribe', $source);
        $this->assertStringContainsString('service_company.subscribe.customCheckoutSubscribe', $source);
    }
}
