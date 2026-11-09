<?php
/**
 * scripts/deploy-tools-to-server.php
 *
 * Automates installation of ProxySQL on Linux host (Ubuntu 24.04).
 */

$token = trim(file_get_contents(__DIR__ . '/../tools/aapanel-mcp/token.txt'));
$baseUrl = 'https://76.13.243.68:25168/';

function runOnServer($taskName, $shellScript, $timeoutSeconds = 30) {
    global $baseUrl, $token;

    // 1. Add Crontab
    $time = time();
    $reqToken = md5($time . md5($token));
    $url = $baseUrl . "crontab?action=AddCrontab&request_time={$time}&request_token={$reqToken}";

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'name' => $taskName,
        'type' => 'day',
        'where1' => '1',
        'hour' => '23',
        'minute' => '59',
        'sType' => 'toShell',
        'sBody' => $shellScript,
        'sName' => '',
        'backupTo' => 'localhost',
        'save' => '',
        'urladdress' => ''
    ]));
    $res = json_decode(curl_exec($ch), true);
    curl_close($ch);

    // 2. Locate task ID
    $time = time();
    $reqToken = md5($time . md5($token));
    $url = $baseUrl . "crontab?action=GetCrontab&request_time={$time}&request_token={$reqToken}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POST, true);
    $list = json_decode(curl_exec($ch), true);
    curl_close($ch);

    $taskId = null;
    foreach ($list as $t) {
        if ($t['name'] === $taskName) {
            $taskId = $t['id'];
            break;
        }
    }

    if (!$taskId) {
        echo "❌ Error: Could not locate task '{$taskName}' on server.\n";
        return false;
    }

    echo "▶ Triggering server task '{$taskName}' (ID: {$taskId})...\n";

    // 3. Start task
    $time = time();
    $reqToken = md5($time . md5($token));
    $url = $baseUrl . "crontab?action=StartTask&request_time={$time}&request_token={$reqToken}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['id' => $taskId]));
    curl_exec($ch);
    curl_close($ch);

    // 4. Wait for execution
    echo "⏳ Waiting {$timeoutSeconds}s for completion...\n";
    sleep($timeoutSeconds);

    // 5. Get Logs
    $time = time();
    $reqToken = md5($time . md5($token));
    $url = $baseUrl . "crontab?action=GetLogs&request_time={$time}&request_token={$reqToken}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['id' => $taskId]));
    $logs = json_decode(curl_exec($ch), true);
    curl_close($ch);

    echo "\n================= SERVER OUTPUT =================\n";
    echo $logs['msg'] ?? 'No output captured';
    echo "\n=================================================\n";

    // 6. Delete task
    $time = time();
    $reqToken = md5($time . md5($token));
    $url = $baseUrl . "crontab?action=DelCrontab&request_time={$time}&request_token={$reqToken}";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['id' => $taskId]));
    curl_exec($ch);
    curl_close($ch);

    return true;
}

// ==========================================
// 2. INSTALL PROXYSQL ON UBUNTU LINUX HOST
// ==========================================
echo "\n=============================================================\n";
echo " 🛡️ INSTALLING PROXYSQL ON UBUNTU 24.04 (76.13.243.68)\n";
echo "=============================================================\n";

$proxysqlScript = <<<'BASH'
set -e
echo ">> 1. Downloading ProxySQL v4.0.12 deb for Ubuntu 24.04..."
curl -sL https://github.com/sysown/proxysql/releases/download/v4.0.12/proxysql_4.0.12-ubuntu24_amd64.deb -o /tmp/proxysql.deb

echo ">> 2. Installing ProxySQL package via dpkg..."
dpkg -i /tmp/proxysql.deb || apt-get install -fy
rm -f /tmp/proxysql.deb

echo ">> 3. Checking ProxySQL version:"
proxysql --version

echo ">> 4. Starting and enabling ProxySQL systemd service..."
systemctl daemon-reload
systemctl enable proxysql
systemctl restart proxysql
sleep 2

echo ">> 5. Checking service status:"
systemctl is-active proxysql

echo ">> 6. Checking listening ports (6032 Admin, 6033 Proxy):"
ss -tlpn | grep proxysql || netstat -tlpn | grep proxysql || true

echo ">> PROXYSQL INSTALLATION COMPLETE!"
BASH;

runOnServer('Install ProxySQL on Linux', $proxysqlScript, 25);
