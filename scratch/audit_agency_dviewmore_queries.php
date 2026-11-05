<?php
$ac = file_get_contents(__DIR__ . '/../app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php');
$dviewMoreSlice = substr($ac, strpos($ac, 'public function dviewMore($id)'), 30000); // 30k chars

preg_match_all('/DB::table\([^\)]+\)/i', $dviewMoreSlice, $dbQueries);
echo "DB::table calls in Agency dviewMore:\n";
print_r(array_unique($dbQueries[0]));

preg_match_all('/\$this->kpi[a-zA-Z0-9_]+\s*=\s*[^;]+;/i', $dviewMoreSlice, $kpis);
echo "\nKPI calculations in Agency dviewMore:\n";
print_r($kpis[0]);
