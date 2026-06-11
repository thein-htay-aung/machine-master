<?php
$db = new PDO('sqlite:' . __DIR__ . '/database/database.sqlite');
foreach ($db->query("PRAGMA table_info('machines')") as $row) {
    echo implode('|', $row) . PHP_EOL;
}
