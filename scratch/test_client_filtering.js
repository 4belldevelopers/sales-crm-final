const fs = require('fs');

// Fetch the raw_data that PHP data_ajax produces
const phpOutput = require('child_process').execSync('php scratch/inspect_html_response.php', { encoding: 'utf8' });
console.log('PHP check:\n', phpOutput);

// Now let's test getDerivedRecords with the actual raw_data from scratch/test_data_ajax.php
const phpTestDataAjax = `
require 'vendor/autoload.php';
\$app = require_once 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\\Contracts\\Console\\Kernel::class);
\$kernel->bootstrap();
\$user = App\\Models\\User::find(2);
auth()->login(\$user);
\$controller = app(App\\Http\\Controllers\\ServiceCompany\\ServiceCompanyTeamController::class);
\$controller->user = \$user;
\$request = Illuminate\\Http\\Request::create('/service_company/team/data_ajax', 'GET', ['page' => 1, 'size' => 10, 'statusTab' => 'all']);
\$response = \$controller->data_ajax(\$request);
echo json_encode(\$response->getData(true)['raw_data']);
`;

fs.writeFileSync('scratch/get_raw_data.php', '<?php ' + phpTestDataAjax);
const rawDataJson = require('child_process').execSync('php scratch/get_raw_data.php', { encoding: 'utf8' });
const records = JSON.parse(rawDataJson);

console.log('Records count from data_ajax:', records.length);
records.forEach(r => {
    console.log(`- ID: ${r.id}, Name: ${r.name}, Status: ${r.status}, Password: "${r.password}", email_verification_code: ${r.email_verification_code}`);
});

// Test filtering logic for each tab:
function testTab(tabName) {
    let list = records.map(r => Object.assign({}, r));
    if (tabName && tabName !== 'all') {
        list = list.filter(function(r) {
            var channels = Array.isArray(r.channels) ? r.channels : [];
            var status = (r.status || '').toLowerCase();
            var isInvited = !r.password || status === 'invited';

            if (tabName === 'client') {
                return channels.indexOf('client') !== -1 || !!r.is_client_rep;
            } else if (tabName === 'vendor' || tabName === 'agency') {
                return channels.indexOf('agency') !== -1 || channels.indexOf('vendor') !== -1 || !!r.is_agency_rep || ((r.pre_agency_shares || 0) > 0);
            } else if (tabName === 'recruitment' || tabName === 'bench') {
                return channels.indexOf('bench') !== -1 || channels.indexOf('recruitment') !== -1 || (parseInt(r.root_user, 10) !== 1 && status === 'active');
            } else if (tabName === 'inactive') {
                return status === 'inactive';
            } else if (tabName === 'invited') {
                return isInvited;
            } else if (tabName === 'active') {
                return status === 'active' && !isInvited;
            }
            return true;
        });
    }
    console.log(`Tab [${tabName}] matched: ${list.length} users -> [${list.map(u => u.name).join(', ')}]`);
}

['all', 'recruitment', 'client', 'vendor', 'inactive', 'invited'].forEach(testTab);
