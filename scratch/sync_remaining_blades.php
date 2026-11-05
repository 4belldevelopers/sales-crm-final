<?php
// scratch/sync_remaining_blades.php

$sourceFile = 'resources/views/service_company/account/edit.blade.php';
$targetFiles = [
    'resources/views/service_company/account/onboarding.blade.php',
    'resources/views/service_company/client/create.blade.php',
];

$sourceContent = file_get_contents($sourceFile);

// Extract the section from "    // 1002: FinTech, Payments & Digital Banking" to "    INDUSTRY_SPECIFIC_SERVICES[42] = INDUSTRY_SPECIFIC_SERVICES[1011];  // Farming -> AgriTech & Smart Farming"
$startMarker1 = "      // 1002: FinTech, Payments & Digital Banking (and alias 43)";
$endMarker1 = "    INDUSTRY_SPECIFIC_SERVICES[42] = INDUSTRY_SPECIFIC_SERVICES[1011];  // Farming -> AgriTech & Smart Farming\n";

$posStart1 = strpos($sourceContent, $startMarker1);
$posEnd1 = strpos($sourceContent, $endMarker1);
if ($posStart1 === false || $posEnd1 === false) {
    die("Error finding section 1 in source file\n");
}
$section1 = substr($sourceContent, $posStart1, ($posEnd1 + strlen($endMarker1)) - $posStart1);

// Extract the fallback if/else block from updateSuggestedServicesForIndustry
$startMarker2 = "      if (!specificConfig) {\n        if (indId === 131 || indLower.indexOf('staffing') !== -1";
$endMarker2 = "        } else if (indId === 1014 || indId === 10 || indLower.indexOf('spacetech') !== -1 || indLower.indexOf('aerospace') !== -1 || indLower.indexOf('autonomous flight') !== -1) {\n          specificConfig = INDUSTRY_SPECIFIC_SERVICES[1014];\n        }\n      }";

$posStart2 = strpos($sourceContent, $startMarker2);
$posEnd2 = strpos($sourceContent, $endMarker2);
if ($posStart2 === false || $posEnd2 === false) {
    die("Error finding section 2 in source file\n");
}
$section2 = substr($sourceContent, $posStart2, ($posEnd2 + strlen($endMarker2)) - $posStart2);

echo "Extracted Section 1 (" . strlen($section1) . " bytes)\n";
echo "Extracted Section 2 (" . strlen($section2) . " bytes)\n";

foreach ($targetFiles as $targetFile) {
    $content = file_get_contents($targetFile);
    
    // For section 1:
    // Replace from:
    // "    // Aliases for unified taxonomy mapping\n    INDUSTRY_SPECIFIC_SERVICES[21] = INDUSTRY_SPECIFIC_SERVICES[1004];"
    // to:
    // "    INDUSTRY_SPECIFIC_SERVICES[27] = INDUSTRY_SPECIFIC_SERVICES[23];    // Consumer Electronics -> Hardware & Electronics\n"
    $targetOldAliases = "    // Aliases for unified taxonomy mapping\n    INDUSTRY_SPECIFIC_SERVICES[21] = INDUSTRY_SPECIFIC_SERVICES[1004]; // Computer & Network Security -> Cybersecurity\n    INDUSTRY_SPECIFIC_SERVICES[145] = INDUSTRY_SPECIFIC_SERVICES[133];  // Wireless -> Telecommunications & Wireless\n    INDUSTRY_SPECIFIC_SERVICES[24] = INDUSTRY_SPECIFIC_SERVICES[133];   // Computer Networking -> Telecom & Wireless\n    INDUSTRY_SPECIFIC_SERVICES[27] = INDUSTRY_SPECIFIC_SERVICES[23];    // Consumer Electronics -> Hardware & Electronics\n";
    
    $replacement1 = $section1;
    
    // In target, before aliases, we need to insert the items 1002-1014 inside INDUSTRY_SPECIFIC_SERVICES before its closing "    };"
    $targetObjEnd = "        desc: 'is an engineering-driven Computer Hardware enterprise developing high-reliability electronic assemblies, embedded hardware systems, and computing products. The company brings electronic innovations from initial PCB concept through rigorous regulatory compliance to mass production.'\n      }\n    };\n\n" . $targetOldAliases;
    
    $posTarget1 = strpos($content, $targetObjEnd);
    if ($posTarget1 === false) {
        die("Error finding target section 1 in $targetFile\n");
    }
    
    // Reconstruct with new verticals inside the object and updated aliases
    $newBlock1 = "        desc: 'is an engineering-driven Computer Hardware enterprise developing high-reliability electronic assemblies, embedded hardware systems, and computing products. The company brings electronic innovations from initial PCB concept through rigorous regulatory compliance to mass production.'\n      },\n\n" . $section1;
    
    $content = str_replace($targetObjEnd, $newBlock1, $content);
    
    // For section 2:
    $targetOldFallback = "      if (!specificConfig) {\n        if (indId === 131 || indLower.indexOf('staffing') !== -1 || indLower.indexOf('recruiting') !== -1) {\n          specificConfig = INDUSTRY_SPECIFIC_SERVICES[131];\n        } else if (indId === 127 || indLower.indexOf('semiconductor') !== -1 || indLower.indexOf('microchip') !== -1) {\n          specificConfig = INDUSTRY_SPECIFIC_SERVICES[127];\n        } else if (indId === 68 || indLower.indexOf('internet') !== -1 || indLower.indexOf('digital platform') !== -1) {\n          specificConfig = INDUSTRY_SPECIFIC_SERVICES[68];\n        } else if (indId === 1001 || indLower.indexOf('artificial intelligence') !== -1 || indLower === 'ai' || indLower.indexOf('machine learning') !== -1) {\n          specificConfig = INDUSTRY_SPECIFIC_SERVICES[1001];\n        } else if (indId === 1004 || indId === 21 || indLower.indexOf('cybersecurity') !== -1 || indLower.indexOf('infosec') !== -1 || indLower.indexOf('network security') !== -1) {\n          specificConfig = INDUSTRY_SPECIFIC_SERVICES[1004];\n        } else if (indId === 1005 || indLower.indexOf('cloud computing') !== -1 || indLower.indexOf('devops') !== -1) {\n          specificConfig = INDUSTRY_SPECIFIC_SERVICES[1005];\n        } else if (indId === 25 || indLower.indexOf('computer software') !== -1 || indLower.indexOf('saas') !== -1) {\n          specificConfig = INDUSTRY_SPECIFIC_SERVICES[25];\n        } else if (indId === 64 || indLower.indexOf('information technology') !== -1) {\n          specificConfig = INDUSTRY_SPECIFIC_SERVICES[64];\n        } else if (indId === 133 || indId === 145 || indId === 24 || indLower.indexOf('telecom') !== -1 || indLower.indexOf('wireless') !== -1) {\n          specificConfig = INDUSTRY_SPECIFIC_SERVICES[133];\n        } else if (indId === 23 || indId === 27 || indLower.indexOf('computer hardware') !== -1 || indLower.indexOf('consumer electronics') !== -1) {\n          specificConfig = INDUSTRY_SPECIFIC_SERVICES[23];\n        }\n      }";
    
    $posTarget2 = strpos($content, $targetOldFallback);
    if ($posTarget2 === false) {
        die("Error finding target section 2 in $targetFile\n");
    }
    
    $content = str_replace($targetOldFallback, $section2, $content);
    
    file_put_contents($targetFile, $content);
    echo "Successfully updated $targetFile\n";
}
