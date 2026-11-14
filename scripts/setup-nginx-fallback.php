<?php
/**
 * scripts/setup-nginx-fallback.php
 *
 * Idempotently injects the 502/503/504 graceful auto-reconnect fallback
 * into the host Nginx virtual host configuration and reloads Nginx.
 *
 * Usage:
 *   php scripts/setup-nginx-fallback.php [domain] [public_path]
 * Default:
 *   domain = hire.jero.works
 *   public_path = /www/wwwroot/hire.jero.works/public
 */

$domain = $argv[1] ?? 'hire.jero.works';
$public_path = $argv[2] ?? "/www/wwwroot/{$domain}/public";

echo "==================================================\n";
echo "🛡️  JERO NGINX 502 FALLBACK AUTO-SETUP\n";
echo "Target Domain     : {$domain}\n";
echo "Public Root Path  : {$public_path}\n";
echo "==================================================\n";

// 1. Locate Nginx configuration file
$candidates = [
    "/www/server/panel/vhost/nginx/{$domain}.conf",
    "/www/server/nginx/conf/vhost/{$domain}.conf",
    "/etc/nginx/conf.d/{$domain}.conf",
    "/etc/nginx/sites-available/{$domain}.conf",
    "/etc/nginx/sites-available/{$domain}",
    "/etc/nginx/sites-enabled/{$domain}",
];

$conf_file = null;
foreach ($candidates as $candidate) {
    if (file_exists($candidate) && is_readable($candidate)) {
        $conf_file = $candidate;
        break;
    }
}

// Fallback search across aaPanel vhost directory if exact file name differed
if (!$conf_file && is_dir('/www/server/panel/vhost/nginx')) {
    $files = glob('/www/server/panel/vhost/nginx/*.conf');
    if ($files) {
        foreach ($files as $file) {
            $content = file_get_contents($file);
            if (preg_match("/server_name\s+[^;]*" . preg_quote($domain, '/') . "/i", $content)) {
                $conf_file = $file;
                break;
            }
        }
    }
}

if (!$conf_file) {
    echo "⚠️  [NGINX-FALLBACK] Warning: Could not locate Nginx virtual host configuration file for {$domain}.\n";
    echo "Checked paths:\n  - " . implode("\n  - ", $candidates) . "\n";
    exit(0); // Do not fail deployment if running in non-standard or containerized environment
}

echo ">> Found Nginx config : {$conf_file}\n";

$content = file_get_contents($conf_file);

// 2. Check if already configured
if (str_contains($content, '502_fallback.html')) {
    echo "✅ [NGINX-FALLBACK] 502 fallback is ALREADY configured in {$conf_file}.\n";
    
    // Verify Nginx syntax and ensure it is reloaded
    exec('nginx -t 2>&1', $tOut, $tRet);
    if ($tRet === 0) {
        exec('nginx -s reload 2>&1 || systemctl reload nginx 2>&1', $rOut, $rRet);
        echo "✅ [NGINX-FALLBACK] Nginx configuration verified and reloaded.\n";
    }
    exit(0);
}

// 3. Prepare block to insert
$fallback_block = "\n    # Graceful 502/503/504 auto-reconnecting fallback during momentary Octane restarts\n" .
                  "    error_page 502 503 504 /502_fallback.html;\n" .
                  "    location = /502_fallback.html {\n" .
                  "        root {$public_path};\n" .
                  "        internal;\n" .
                  "    }\n\n";

// 4. Determine insertion point (prefer right before `location /` or `# Laravel -> Octane`)
$updated = null;
if (preg_match('/(\n\s*#?\s*Laravel\s*->\s*Octane|\n\s*location\s+\/\s*\{)/i', $content, $matches, PREG_OFFSET_CAPTURE)) {
    $offset = $matches[0][1];
    $updated = substr($content, 0, $offset) . $fallback_block . ltrim(substr($content, $offset));
} else {
    // Insert before the last closing brace of the server block
    $last_brace = strrpos($content, '}');
    if ($last_brace !== false) {
        $updated = substr($content, 0, $last_brace) . $fallback_block . "\n}\n";
    }
}

if (!$updated) {
    echo "⚠️  [NGINX-FALLBACK] Warning: Could not find suitable insertion point in {$conf_file}.\n";
    exit(0);
}

// 5. Create backup
$backup_file = $conf_file . '.bak.' . time();
if (!copy($conf_file, $backup_file)) {
    echo "❌ [NGINX-FALLBACK] Error: Could not create backup file {$backup_file}. Aborting.\n";
    exit(1);
}
echo ">> Created backup     : {$backup_file}\n";

// 6. Write updated config
if (file_put_contents($conf_file, $updated) === false) {
    echo "❌ [NGINX-FALLBACK] Error: Could not write updated configuration to {$conf_file}.\n";
    copy($backup_file, $conf_file);
    exit(1);
}

// 7. Test Nginx syntax
exec('nginx -t 2>&1', $testOutput, $testReturn);
if ($testReturn !== 0) {
    echo "❌ [NGINX-FALLBACK] Nginx configuration test FAILED! Rolling back...\n";
    echo implode("\n", $testOutput) . "\n";
    copy($backup_file, $conf_file);
    echo ">> Successfully restored original configuration from backup.\n";
    exit(1);
}

// 8. Reload Nginx
exec('nginx -s reload 2>&1 || systemctl reload nginx 2>&1', $reloadOutput, $reloadReturn);
echo "✅ [NGINX-FALLBACK] SUCCESS: Graceful 502 fallback shield activated in {$conf_file}!\n";
echo "==================================================\n";
exit(0);
