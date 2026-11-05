<?php

$filePath = 'resources/views/auth/login.blade.php';
$content = file_get_contents($filePath);

$oldBtn = ".jw-card-team-invite .jw-btn {
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

$newBtn = ".jw-card-team-invite .jw-btn {
    height: 46px !important;
    font-size: 15px !important;
    border-radius: 8px !important;
    margin-top: 6px !important;
    background: linear-gradient(180deg, #ffd428 0%, #f5c400 100%) !important;
    color: #10143b !important;
    border: 1px solid #d4a017 !important;
    font-weight: 700 !important;
    box-shadow: 0 4px 14px rgba(245, 196, 0, 0.35) !important;
    transition: all .16s ease !important;
}
.jw-card-team-invite .jw-btn:hover {
    background: #f5c400 !important;
    border-color: #c7950f !important;
    color: #10143b !important;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(245, 196, 0, 0.45) !important;
}";

if (strpos($content, $oldBtn) === false) {
    die("Error: Could not find old button CSS block\n");
}

$content = str_replace($oldBtn, $newBtn, $content);
file_put_contents($filePath, $content);
echo "Gold button style successfully applied to resources/views/auth/login.blade.php\n";
