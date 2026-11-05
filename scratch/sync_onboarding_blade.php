<?php

$src = file_get_contents('resources/views/service_company/account/edit.blade.php');

// Replace top header comment
$src = str_replace(
    'JERO GOD-LEVEL COMPANY PROFILE / COMPANY EDIT DESIGN SYSTEM (RULE 20)',
    'JERO GOD-LEVEL COMPANY PROFILE / ONBOARDING SETUP (RULE 20)',
    $src
);

// Replace form action
$oldAction = 'action="{{ route(\'service_company.account.update\', [\App\Helper\GeneralMethod::value_encrypt($company_id)]) }}"';
$newAction = 'action="{{ route(\'service_company.account.onboarding.save\') }}"';
if (strpos($src, $oldAction) === false) {
    die("Error: could not find old action in edit.blade.php\n");
}
$src = str_replace($oldAction, $newAction, $src);

// Replace page_mode default
$oldMode = '<input type="hidden" name="page_mode" id="page_mode" value="{{ $page_mode ?? \'EDIT\' }}">';
$newMode = '<input type="hidden" name="page_mode" id="page_mode" value="ONBOARDING">';
if (strpos($src, $oldMode) === false) {
    die("Error: could not find old page_mode in edit.blade.php\n");
}
$src = str_replace($oldMode, $newMode, $src);

// Replace close redirect
$oldCloseRedirect = 'window.location.href = "{{ route(\'service_company.account.index\') }}";';
$newCloseRedirect = 'window.location.href = "{{ url(\'service_company/dashboard\') }}";';
// Replace only the closeBtn handler occurrence (first occurrence of oldCloseRedirect in closeBtn handler)
$src = preg_replace(
    '/if \(closeBtn\) \{[\s\S]*?\}\);/',
    'if (closeBtn) {
      closeBtn.addEventListener(\'click\', function() {
        if (confirm(\'Leave profile setup? You can complete it later from your dashboard.\')) {
          window.location.href = "{{ url(\'service_company/dashboard\') }}";
        }
      });
    }',
    $src
);

file_put_contents('resources/views/service_company/account/onboarding.blade.php', $src);
echo "Successfully synchronized onboarding.blade.php with edit.blade.php\n";
