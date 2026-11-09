<?php
/**
 * scripts/aapanel-query.php
 *
 * aaPanel Real-Time Telemetry & Management Bridge for JERO
 * Securely queries aaPanel API on https://76.13.243.68:25168/
 *
 * Usage:
 *   php scripts/aapanel-query.php system
 *   php scripts/aapanel-query.php disk
 *   php scripts/aapanel-query.php network
 *   php scripts/aapanel-query.php sites
 *   php scripts/aapanel-query.php databases
 */

$action = $argv[1] ?? 'system';

$envFile = __DIR__ . '/../.env';
$token = getenv('AAPANEL_API_TOKEN') ?: (file_exists(__DIR__ . '/.aapanel_token') ? trim(file_get_contents(__DIR__ . '/.aapanel_token')) : '');
if (empty($token) && file_exists(__DIR__ . '/../tools/aapanel-mcp/token.txt')) {
    $token = trim(file_get_contents(__DIR__ . '/../tools/aapanel-mcp/token.txt'));
}
$baseUrl = 'https://76.13.243.68:25168/';

$endpoints = [
    'system'    => ['path' => 'system?action=GetSystemTotal', 'data' => []],
    'disk'      => ['path' => 'system?action=GetDiskInfo', 'data' => []],
    'network'   => ['path' => 'system?action=GetNetWork', 'data' => []],
    'sites'     => ['path' => 'data?action=getData&table=sites', 'data' => ['p' => '1', 'limit' => '100']],
    'databases' => ['path' => 'data?action=getData&table=databases', 'data' => ['p' => '1', 'limit' => '100']],
    'docker'    => ['path' => 'btdocker/container/get_list', 'data' => []],
    'crontab'   => ['path' => 'crontab?action=GetCrontab', 'data' => []],
];

if (!isset($endpoints[$action])) {
    echo "Unknown action: {$action}. Available actions: system, disk, network, sites, databases\n";
    exit(1);
}

$time = time();
$reqToken = md5($time . md5($token));
$ep = $endpoints[$action];

$sep = str_contains($ep['path'], '?') ? '&' : '?';
$url = $baseUrl . $ep['path'] . "{$sep}request_time={$time}&request_token={$reqToken}";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($ep['data']));
curl_setopt($ch, CURLOPT_TIMEOUT, 8);

$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

if ($err) {
    echo "Connection Error: {$err}\n";
    exit(1);
}

$decoded = json_decode($res, true);
if ($decoded) {
    echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
} else {
    echo $res;
}
echo PHP_EOL;
