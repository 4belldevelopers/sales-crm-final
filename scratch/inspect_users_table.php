<?php
$schema = file_get_contents('database/schema/mysql-schema.sql');
$start = strpos($schema, 'CREATE TABLE `users`');
if ($start !== false) {
    $end = strpos($schema, ';', $start);
    echo substr($schema, $start, $end - $start + 1);
}
