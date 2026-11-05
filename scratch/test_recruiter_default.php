<?php
$content = file_get_contents('resources/views/service_company/team/index.blade.php');

assert(strpos($content, 'autoSelectDefaultRecruiterRole') !== false, 'autoSelectDefaultRecruiterRole missing');
assert(strpos($content, "n === 'recruiter'") !== false, 'recruiter exact match missing');
assert(strpos($content, "indexOf('recruiter') !== -1") !== false, 'recruiter substring match missing');

echo "✓ Recruiter auto-selection logic successfully verified in index.blade.php!\n";
