<?php
$path = __DIR__ . '/../database/database.sqlite';
$db = new PDO('sqlite:' . $path);
$cat = 16;
$stmt = $db->prepare('SELECT p.id, p.name, p.category_id, c.name as cname FROM parts p LEFT JOIN categories c ON p.category_id = c.id WHERE p.category_id = :cat');
$stmt->execute([':cat' => $cat]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$rows) { echo "No rows for cat $cat\n"; exit; }
foreach ($rows as $r) {
    echo $r['id'].' | '.$r['name'].' | cat_id:'.$r['category_id'].' | cname:'.$r['cname'].PHP_EOL;
}
