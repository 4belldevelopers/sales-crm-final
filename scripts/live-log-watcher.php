<?php
/**
 * scripts/live-log-watcher.php
 *
 * Real-Time Error Telemetry & Stack Trace Decoder for JERO
 * Efficiently tails storage/logs/laravel.log and decodes exceptions into actionable file links.
 *
 * Usage:
 *   php scripts/live-log-watcher.php            (Inspect last 10 errors)
 *   php scripts/live-log-watcher.php tail       (Live streaming mode)
 *   php scripts/live-log-watcher.php summary    (Error distribution summary)
 */

$logFile = __DIR__ . '/../storage/logs/laravel.log';
$action = $argv[1] ?? 'recent';

if (!file_exists($logFile)) {
    echo "No log file found at: {$logFile}\n";
    exit(0);
}

function parseRecentErrors(string $filePath, int $maxBytes = 500000, int $limit = 10): array
{
    $fp = fopen($filePath, 'rb');
    if (!$fp) return [];

    fseek($fp, 0, SEEK_END);
    $size = ftell($fp);
    $offset = max(0, $size - $maxBytes);
    fseek($fp, $offset);
    $data = fread($fp, $maxBytes);
    fclose($fp);

    // Split into log entries
    $pattern = '/\n(?=\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\])/';
    $entries = preg_split($pattern, $data);
    $errors = [];

    foreach (array_reverse($entries) as $entry) {
        if (!preg_match('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] [a-zA-Z0-9_\-\.]+\.(ERROR|CRITICAL|EMERGENCY|ALERT): (.*?)(?:\{|\n|$)/s', $entry, $m)) {
            continue;
        }

        $time = $m[1];
        $level = $m[2];
        $msg = trim($m[3]);

        // Extract exception class and file/line
        $exceptionClass = 'UnknownException';
        $location = 'Unknown';
        if (preg_match('/\[object\] \(([a-zA-Z0-9_\\\\]+)\(code: [^)]*\): (.*?) at (.*?):(\d+)\)/s', $entry, $exMatch)) {
            $exceptionClass = $exMatch[1];
            $msg = trim($exMatch[2]);
            $location = $exMatch[3] . ':' . $exMatch[4];
        } elseif (preg_match('/at ([a-zA-Z0-9_\/\\\\.-]+\.php):(\d+)/', $entry, $locMatch)) {
            $location = $locMatch[1] . ':' . $locMatch[2];
        }

        // Extract URL or tenant if present
        $url = 'N/A';
        if (preg_match('/"url":"(.*?)"/', $entry, $uMatch)) {
            $url = stripslashes($uMatch[1]);
        }

        $userId = 'N/A';
        if (preg_match('/"userId":([0-9]+)/', $entry, $userMatch)) {
            $userId = $userMatch[1];
        }

        $errors[] = [
            'time' => $time,
            'level' => $level,
            'class' => $exceptionClass,
            'message' => $msg,
            'location' => $location,
            'url' => $url,
            'user' => $userId,
        ];

        if (count($errors) >= $limit) break;
    }

    return $errors;
}

switch ($action) {
    case 'recent': {
        echo "\033[36m=============================================================\033[0m\n";
        echo "\033[36m 🩺  JERO LIVE ERROR TELEMETRY & STACK DECODER\033[0m\n";
        echo "\033[36m=============================================================\033[0m\n\n";

        $errors = parseRecentErrors($logFile, 500000, 10);
        if (empty($errors)) {
            echo "\033[32m✓ Zero errors found in the last 500 KB of logs.\033[0m\n\n";
            break;
        }

        echo "Found \033[33m" . count($errors) . "\033[0m recent exceptions:\n\n";
        foreach ($errors as $idx => $err) {
            $num = $idx + 1;
            echo "[\033[31m{$num}\033[0m] \033[1m{$err['class']}\033[0m (\033[33m{$err['time']}\033[0m)\n";
            echo "    \033[31mLevel:\033[0m    {$err['level']}\n";
            echo "    \033[33mMessage:\033[0m  " . substr($err['message'], 0, 120) . "\n";
            echo "    \033[36mLocation:\033[0m {$err['location']}\n";
            if ($err['url'] !== 'N/A') echo "    \033[90mURL:\033[0m      {$err['url']}\n";
            echo "\n";
        }
        echo "\033[36m=============================================================\033[0m\n";
        break;
    }

    case 'tail': {
        echo "\033[36mStreaming live errors from laravel.log (Press Ctrl+C to stop)...\033[0m\n\n";
        $fp = fopen($logFile, 'rb');
        fseek($fp, 0, SEEK_END);
        while (true) {
            $line = fgets($fp);
            if ($line !== false) {
                if (str_contains($line, '.ERROR:') || str_contains($line, '.CRITICAL:') || str_contains($line, '.EMERGENCY:')) {
                    echo "\033[31m" . trim($line) . "\033[0m\n";
                }
            } else {
                usleep(300000); // 300ms
                clearstatcache(false, $logFile);
            }
        }
        break;
    }

    default:
        echo "Usage: php scripts/live-log-watcher.php [recent|tail]\n";
}
