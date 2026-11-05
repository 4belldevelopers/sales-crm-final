<?php
$tabAct = file_get_contents(__DIR__ . '/../resources/views/service_company/client/components/tab-activity.blade.php');
echo substr($tabAct, 0, 3000);
