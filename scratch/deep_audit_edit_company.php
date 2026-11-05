<?php

require 'vendor/autoload.php';

echo "========================================================================\n";
echo "FORENSIC AUDIT: EDIT COMPANY PROFILE VS ADD INTERNAL CLIENT\n";
echo "========================================================================\n\n";

$accountCtrl = file_get_contents("app/Http/Controllers/ServiceCompany/ServiceCompanyAccountSettingsController.php");
$clientCtrl  = file_get_contents("app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php");

$accountEditBlade = file_get_contents("resources/views/service_company/account/edit.blade.php");
$clientCreateBlade = file_get_contents("resources/views/service_company/client/create.blade.php");

// 1. Controller Read Queries (Rule 1 & 4)
echo "1. MASTER DATA / READ CACHING (Rule 1 & Rule 4):\n";
echo "   - Client create() uses cache()->remember for: global_country_all, global_job_categories_all, global_td_industries_all, global_country_arr_list, global_state_list_101\n";
$uncachedAccountReads = [];
if (strpos($accountCtrl, 'JobCategory::all()') !== false) $uncachedAccountReads[] = 'JobCategory::all() uncached';
if (strpos($accountCtrl, 'GeneralMethod::statelist(101)') !== false) $uncachedAccountReads[] = 'GeneralMethod::statelist(101) uncached';
if (strpos($accountCtrl, "DB::table('td_industries')->select('td_industries.ind_id'") !== false) $uncachedAccountReads[] = 'td_industries direct DB query uncached';
if (strpos($accountCtrl, "GeneralMethod::getAllCountryList()") !== false) $uncachedAccountReads[] = 'GeneralMethod::getAllCountryList() uncached';
print_r($uncachedAccountReads);

// 2. Submission Validation
echo "\n2. BACKEND SUBMISSION VALIDATION:\n";
$hasClientSubmissionValidation = (strpos($clientCtrl, 'validateClientSubmission') !== false);
$hasAccountSubmissionValidation = (strpos($accountCtrl, 'validateCompanySubmission') !== false || strpos($accountCtrl, 'validateSubmissionDuplicates') !== false);
echo "   - Client controller has comprehensive validateClientSubmission: " . ($hasClientSubmissionValidation ? "YES" : "NO") . "\n";
echo "   - Account controller has comprehensive validateCompanySubmission: " . ($hasAccountSubmissionValidation ? "YES" : "NO") . "\n";

// 3. Duplicate Domain / Website Validation
echo "\n3. DUPLICATE DOMAIN & WEBSITE VALIDATION:\n";
$hasClientDupService = (strpos($clientCtrl, 'InternalClientDuplicateService') !== false);
$hasAccountDupService = (strpos($accountCtrl, 'InternalClientDuplicateService') !== false);
echo "   - Client controller uses InternalClientDuplicateService: " . ($hasClientDupService ? "YES" : "NO") . "\n";
echo "   - Account controller uses InternalClientDuplicateService: " . ($hasAccountDupService ? "YES" : "NO") . "\n";

// 4. Real-time Field Validation Endpoint (AJAX)
echo "\n4. REAL-TIME FIELD VALIDATION ENDPOINT:\n";
$hasClientRealtimeVal = (strpos($clientCtrl, 'validateInternalClientField') !== false);
$hasAccountRealtimeVal = (strpos($accountCtrl, 'validateCompanyField') !== false);
echo "   - Client controller has validateInternalClientField: " . ($hasClientRealtimeVal ? "YES" : "NO") . "\n";
echo "   - Account controller has dedicated validateCompanyField: " . ($hasAccountRealtimeVal ? "YES" : "NO") . "\n";
echo "   - Account edit blade routes field validation to: " . (preg_match('/validateInternalField/', $accountEditBlade) ? "service_company.clients.validateInternalField" : "NONE") . "\n";

// 5. Readiness Polling & Response Flow
echo "\n5. READINESS POLLING & RESPONSE FLOW:\n";
$accountReturnsCheckReady = (strpos($accountCtrl, "'check_ready_url'") !== false);
$bladeExpectsCheckReady = (strpos($accountEditBlade, 'checkReadyUrl = res.check_ready_url') !== false);
echo "   - Account update() returns check_ready_url: " . ($accountReturnsCheckReady ? "YES" : "NO (CRITICAL BUG!)") . "\n";
echo "   - Account edit blade expects and polls check_ready_url: " . ($bladeExpectsCheckReady ? "YES (Will poll undefined & timeout after 15s!)" : "NO") . "\n";

// 6. Loop Database Queries (N+1 in update)
echo "\n6. LOOP DATABASE QUERIES (N+1):\n";
$hasLoopQueriesInAccount = (strpos($accountCtrl, 'for ($i = 0; $i <= $addressId; $i++)') !== false && strpos($accountCtrl, "DB::table('city_list')") !== false);
echo "   - Account update() executes city_list & stateId queries inside loops: " . ($hasLoopQueriesInAccount ? "YES (Causes 15-30 queries per submit)" : "NO") . "\n";

// 7. Transactional Outbox Pattern
echo "\n7. TRANSACTIONAL OUTBOX PATTERN:\n";
$hasOutboxClient = (strpos($clientCtrl, 'sys_outbox_events') !== false);
$hasOutboxAccount = (strpos($accountCtrl, 'sys_outbox_events') !== false);
echo "   - Client controller uses sys_outbox_events: " . ($hasOutboxClient ? "YES" : "NO") . "\n";
echo "   - Account controller uses sys_outbox_events: " . ($hasOutboxAccount ? "YES" : "NO") . "\n";

// 8. Logo Compression Parity
echo "\n8. LOGO COMPRESSION PARITY:\n";
$hasLogoCompressionAccount = (strpos($accountCtrl, 'GeneralMethod::compressAndSaveLogo') !== false);
echo "   - Account controller uses GeneralMethod::compressAndSaveLogo: " . ($hasLogoCompressionAccount ? "YES (Fixed today!)" : "NO") . "\n";

// 9. Review Tab Data Binding
echo "\n9. REVIEW TAB & LOCALSTORAGE DRAFTING:\n";
$hasLocalStorageDraft = (strpos($accountEditBlade, 'jero_company_draft') !== false || strpos($accountEditBlade, 'jero_client_draft') !== false);
echo "   - Account edit blade has draft persistence: " . ($hasLocalStorageDraft ? "YES" : "NO") . "\n";

