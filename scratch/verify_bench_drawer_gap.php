<?php
/**
 * Verification test for Super Admin Bench Candidate Drawer Gap Fix
 */
$indexContent = file_get_contents(__DIR__ . '/../resources/views/super-admin/bench-lists/index.blade.php');
$showDetailContent = file_get_contents(__DIR__ . '/../resources/views/super-admin/bench-lists/showdetail.blade.php');

$checks = [];

// 1. Drawer width attribute empty to let CSS control it
$checks['Drawer width attribute empty (prevents inline lock)'] = (strpos($indexContent, 'data-kt-drawer-width=""') !== false);

// 2. Drawer background set to #f1f5f9 (no white gap leak)
$checks['Drawer background #f1f5f9 in index.blade.php'] = (strpos($indexContent, 'background:#f1f5f9 !important;') !== false);
$checks['Drawer background #f1f5f9 in showdetail.blade.php'] = (strpos($showDetailContent, '#kt_activitiesBenchApplication {') !== false && strpos($showDetailContent, 'background: #f1f5f9 !important;') !== false);

// 3. No scrollbar-gutter: stable (prevents phantom right gutter)
$checks['No scrollbar-gutter: stable in index.blade.php'] = (strpos($indexContent, 'scrollbar-gutter: stable') === false);
$checks['No scrollbar-gutter: stable in showdetail.blade.php'] = (strpos($showDetailContent, 'scrollbar-gutter: stable') === false);

// 4. Page wrapper width 100% and max-width 100%
$checks['Page wrapper full width in showdetail'] = (strpos($showDetailContent, 'width: 100% !important;') !== false && strpos($showDetailContent, 'max-width: 100% !important;') !== false);

// 5. Top action bar spans 100% edge-to-edge
$checks['Topbar edge-to-edge with 12px 16px padding'] = (strpos($showDetailContent, '#kt_activitiesBenchApplication .cdr-topbar {') !== false && strpos($showDetailContent, 'padding: 12px 16px !important;') !== false);

// 6. Cards have balanced 16px margins on both sides (calc(100% - 32px))
$checks['Cards width calc(100% - 32px) with margin 0 16px'] = (strpos($showDetailContent, 'margin: 0 16px !important;') !== false && strpos($showDetailContent, 'width: calc(100% - 32px) !important;') !== false);

// 7. Pipeline section width calc(100% - 32px) with margin 0 16px
$checks['Pipeline section calc(100% - 32px) with margin 0 16px'] = (strpos($showDetailContent, '#kt_activitiesBenchApplication .cdr-pipeline-section {') !== false && strpos($showDetailContent, 'max-width: calc(100% - 32px) !important;') !== false);

// 8. Box-sizing border-box applied across drawer
$checks['Universal box-sizing border-box in showdetail'] = (strpos($showDetailContent, 'box-sizing: border-box !important;') !== false);

// 9. Responsive media queries for 15.6" screens (1366x768 -> 1025px, 1920x1080 -> 1200px)
$checks['Responsive query for 1025px (1366x768 screens)'] = (strpos($showDetailContent, '1025px !important') !== false);
$checks['Responsive query for 1200px (1920x1080 screens)'] = (strpos($showDetailContent, '1200px !important') !== false);

echo "=== SUPER ADMIN BENCH DRAWER GAP VERIFICATION ===\n\n";
$allPassed = true;
foreach ($checks as $title => $passed) {
    echo ($passed ? "[PASS]" : "[FAIL]") . " " . $title . "\n";
    if (!$passed) $allPassed = false;
}

echo "\nResult: " . ($allPassed ? "ALL 11 CHECKS PASSED SUCCESSFULLY!" : "SOME CHECKS FAILED!") . "\n";
exit($allPassed ? 0 : 1);
