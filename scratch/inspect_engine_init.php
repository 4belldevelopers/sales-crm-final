<?php
$tabAct = file_get_contents(__DIR__ . '/../resources/views/service_company/client/components/tab-activity.blade.php');
$pos = strpos($tabAct, 'window.ClientActivityEngine');
echo substr($tabAct, $pos - 200, 2500);
