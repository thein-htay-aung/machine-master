<?php
$path = __DIR__ . '/../database/database.sqlite';
$db = new PDO('sqlite:' . $path);
$stmt = $db->query('SELECT id, name FROM categories ORDER BY id');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo $r['id'] . ' - ' . $r['name'] . PHP_EOL;
}
