<?php
$content = file_get_contents('app/DTO/ServiceCompany/DirectClientCandidateDTO.php');
echo substr($content, 2500, 3000);
