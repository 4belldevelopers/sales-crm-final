<?php
$lines = file('app/Jobs/ProcessTeamOutboxEventJob.php');
foreach ($lines as $i => $l) {
    echo ($i + 1) . ": " . $l;
}
