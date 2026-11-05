<?php
function findFiles($pattern) {
    $matches = [];
    foreach (glob($pattern) as $filename) {
        $matches[] = $filename;
    }
    return $matches;
}

echo "--- Client Drawer Views ---\n";
print_r(glob(__DIR__ . '/../resources/views/service_company/client/*.blade.php'));
print_r(glob(__DIR__ . '/../resources/views/service_company/client/**/*.blade.php'));

echo "--- Consultancy/Agency Drawer Views ---\n";
print_r(glob(__DIR__ . '/../resources/views/service_company/consultancy/*.blade.php'));
print_r(glob(__DIR__ . '/../resources/views/service_company/consultancy/**/*.blade.php'));

echo "--- Shared Tabs Views ---\n";
print_r(glob(__DIR__ . '/../resources/views/service_company/account/v2/tabs/*.blade.php'));
