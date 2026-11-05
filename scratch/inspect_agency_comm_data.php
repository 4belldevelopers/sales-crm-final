<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$agencyId = 3;
$company = DB::table('companies')->where('id', $agencyId)->first();
dump('Company:', $company ? ['id' => $company->id, 'name' => $company->company_name, 'success_per' => $company->success_per] : null);

$agencyTerms = DB::table('agency_commercial_terms')->where('company_id', $agencyId)->first();
dump('Agency Commercial Terms:', $agencyTerms);

$commericials = DB::table('commericials')->where('company_id', $agencyId)->get();
dump('Commericials count:', $commericials->count());
foreach ($commericials as $c) {
    dump('Comm row:', [
        'id' => $c->id,
        'commtype' => $c->commtype,
        'status' => $c->status,
        'rateType' => $c->rateType,
        'sal_mode' => $c->sal_mode,
        'salary_currency' => $c->salary_currency,
        'replacement_period_days' => $c->replacement_period_days,
        'payment_terms_days' => $c->payment_terms_days,
        'billing_frequency' => $c->billing_frequency,
        'billing_trigger' => $c->billing_trigger,
    ]);
    $details = DB::table('commericial_details')->where('commericial_id', $c->id)->get();
    dump('Details count: ' . $details->count(), $details->take(3)->toArray());
}

$tenantId = 2; // service company tenant
dump('Tenant Commericials for tenant 2:');
$tenantComms = DB::table('commericials')->where('company_id', $tenantId)->get();
dump('Tenant Comms count:', $tenantComms->count());
foreach ($tenantComms as $c) {
    dump('Tenant Comm row:', [
        'id' => $c->id,
        'commtype' => $c->commtype,
        'status' => $c->status,
        'rateType' => $c->rateType,
        'sal_mode' => $c->sal_mode,
        'salary_currency' => $c->salary_currency,
        'replacement_period_days' => $c->replacement_period_days,
        'payment_terms_days' => $c->payment_terms_days,
        'billing_frequency' => $c->billing_frequency,
        'billing_trigger' => $c->billing_trigger,
    ]);
}
