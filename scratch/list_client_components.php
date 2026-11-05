<?php
$files = glob('resources/views/service_company/client/components/*');
foreach ($files as $f) {
    echo $f . " (" . filesize($f) . " bytes)\n";
}
