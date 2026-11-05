<?php
$lines = file(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
for ($i = 1250; $i <= 1550; $i++) {
    if (preg_match('/\$this->([a-zA-Z0-9_]+)\s*=\s*(?:DB::table|\$)/', $lines[$i - 1])) {
        echo "Line $i: " . trim($lines[$i - 1]) . "\n";
    }
}
