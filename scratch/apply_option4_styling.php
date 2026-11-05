<?php

$filePath = 'resources/views/auth/login.blade.php';
$content = file_get_contents($filePath);

// 1. Update CSS
$oldCss = ".jw-card-team-invite {
    background: linear-gradient(180deg, #f0f6ff 0%, #f8faff 42%, #ffffff 100%) !important;
    border: 1.5px solid #bfdbfe !important;
    border-radius: 20px !important;
    box-shadow: 0 20px 48px rgba(37, 99, 235, 0.08), 0 2px 8px rgba(37, 99, 235, 0.04) !important;
    padding: clamp(18px, 2.2vh, 26px) clamp(22px, 2.5vw, 32px) !important;
    position: relative;
    overflow: hidden;
}
.jw-card-team-invite::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #2563eb 0%, #6366f1 50%, #3b82f6 100%);
}";

$newCss = ".jw-card-team-invite {
    background: #ffffff !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 20px !important;
    box-shadow: 0 24px 50px rgba(23, 40, 84, 0.08), 0 2px 6px rgba(23, 40, 84, 0.04) !important;
    padding: clamp(18px, 2.2vh, 26px) clamp(22px, 2.5vw, 32px) !important;
    position: relative;
    overflow: hidden;
}
.jw-card-team-invite::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #ffd428 0%, #f59e0b 100%) !important;
}";

if (strpos($content, $oldCss) === false) {
    die("Error: Could not find old CSS block\n");
}
$content = str_replace($oldCss, $newCss, $content);

// 2. Update button style
$oldBtn = ".jw-card-team-invite .jw-btn {
    height: 46px !important;
    font-size: 15px !important;
    border-radius: 8px !important;
    margin-top: 6px !important;
    box-shadow: 0 8px 20px rgba(88, 74, 234, .24) !important;
}";

$newBtn = ".jw-card-team-invite .jw-btn {
    height: 46px !important;
    font-size: 15px !important;
    border-radius: 8px !important;
    margin-top: 6px !important;
    background: linear-gradient(180deg, #10143b 0%, #0b0e29 100%) !important;
    color: #ffffff !important;
    border: 1px solid #10143b !important;
    box-shadow: 0 8px 20px rgba(16, 20, 59, 0.22) !important;
}
.jw-card-team-invite .jw-btn:hover {
    background: #000000 !important;
    transform: translateY(-1px);
}";

if (strpos($content, $oldBtn) === false) {
    die("Error: Could not find old button CSS block\n");
}
$content = str_replace($oldBtn, $newBtn, $content);

// 3. Update HTML badges
$oldBadges = '<span style="display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; border-radius: 999px; font-size: 11px; font-weight: 600;">';
$newBadges = '<span style="display: inline-flex; align-items: center; gap: 5px; padding: 3px 9px; background: #fef3c7; color: #b45309; border: 1px solid #fde68a; border-radius: 999px; font-size: 11px; font-weight: 600;">';
if (strpos($content, $oldBadges) === false) {
    die("Error: Could not find old team invitation badge\n");
}
$content = str_replace($oldBadges, $newBadges, $content);

$oldRole = '<span style="display: inline-flex; align-items: center; padding: 2px 8px; background: #f8fafc; color: #475467; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 11px; font-weight: 600;">';
$newRole = '<span style="display: inline-flex; align-items: center; padding: 2px 8px; background: #ffffff; color: #334155; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 11px; font-weight: 600;">';
if (strpos($content, $oldRole) === false) {
    die("Error: Could not find old role badge\n");
}
$content = str_replace($oldRole, $newRole, $content);

$oldAssigned = '<span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; background: #f1f5f9; color: #475467; border: 1px solid #e2e8f0; border-radius: 999px; font-size: 10px; font-weight: 600;">';
$newAssigned = '<span style="display: inline-flex; align-items: center; gap: 4px; padding: 2px 7px; background: #fef9c3; color: #854d0e; border: 1px solid #fef08a; border-radius: 999px; font-size: 10px; font-weight: 600;">';
if (strpos($content, $oldAssigned) === false) {
    die("Error: Could not find old assigned badge\n");
}
$content = str_replace($oldAssigned, $newAssigned, $content);

file_put_contents($filePath, $content);
echo "Option 4 successfully applied to resources/views/auth/login.blade.php\n";
