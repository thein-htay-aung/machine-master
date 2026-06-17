<?php
$path = __DIR__ . '/../database/database.sqlite';
$db = new PDO('sqlite:' . $path);
$stmt = $db->query('SELECT id, name, category_id, is_active, image FROM parts ORDER BY id');
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo implode(' | ', [$r['id'], $r['name'], 'cat_id:'.$r['category_id'], 'active:'.$r['is_active'], 'image:'.$r['image']]) . PHP_EOL;
}
