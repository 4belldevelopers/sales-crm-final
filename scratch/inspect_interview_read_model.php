<?php
$content = file_get_contents('app/Services/Interview/InterviewReadModel.php');
echo substr($content, 0, 2000);
