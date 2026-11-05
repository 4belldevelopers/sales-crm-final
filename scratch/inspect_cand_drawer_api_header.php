<?php
$content = file_get_contents('app/Http/Controllers/ServiceCompany/ClientCandidateDrawerApiController.php');
echo substr($content, 0, 1000);
