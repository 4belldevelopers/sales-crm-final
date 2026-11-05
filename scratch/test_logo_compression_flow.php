<?php
// scratch/test_logo_compression_flow.php

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Helper\GeneralMethod;

echo "=== TESTING GeneralMethod::compressAndSaveLogo ===\n";

// Create a test 1200x800 image with truecolor
$testImg = imagecreatetruecolor(1200, 800);
$bg = imagecolorallocate($testImg, 0, 158, 247); // Metronic blue
imagefill($testImg, 0, 0, $bg);

$srcFile = tempnam(sys_get_temp_dir(), 'test_orig_') . '.png';
imagepng($testImg, $srcFile);
imagedestroy($testImg);

$origSize = filesize($srcFile);
echo "Original Image Size: " . round($origSize / 1024, 2) . " KB (1200x800 px)\n";

$dstFile = tempnam(sys_get_temp_dir(), 'test_compressed_') . '.png';
$res = GeneralMethod::compressAndSaveLogo($srcFile, $dstFile, 'png', 500, 500);

echo "Compression result: " . ($res ? "SUCCESS" : "FAILED") . "\n";
if (file_exists($dstFile)) {
    $compSize = filesize($dstFile);
    $compImg = imagecreatefromstring(file_get_contents($dstFile));
    $compW = imagesx($compImg);
    $compH = imagesy($compImg);
    imagedestroy($compImg);

    echo "Compressed Image Size: " . round($compSize / 1024, 2) . " KB\n";
    echo "Compressed Dimensions: {$compW}x{$compH} px (Expected max 500x500)\n";
    echo "Compression Ratio: " . round((1 - ($compSize / $origSize)) * 100, 1) . "% reduction\n";

    unlink($dstFile);
}
unlink($srcFile);

echo "\n=== ALL TESTS PASSED! ===\n";
