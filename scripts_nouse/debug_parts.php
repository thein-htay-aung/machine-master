<?php
// Usage: php debug_parts.php [name] [category_id] [is_active]
$path = __DIR__ . '/../database/database.sqlite';
if (!file_exists($path)) {
    echo "Database not found at: $path\n";
    exit(1);
}
$db = new PDO('sqlite:' . $path);
$name = $argv[1] ?? null;
$category = $argv[2] ?? null;
$is_active = $argv[3] ?? null;
$sql = 'SELECT p.id, p.name, p.model, p.brand, p.category_id, p.unit_id, p.is_active, p.image, c.name as category_name FROM parts p LEFT JOIN categories c ON p.category_id = c.id';
$conds = [];
$params = [];
if ($name) { $conds[] = 'p.name LIKE :name'; $params[':name'] = "%$name%"; }
if ($category !== null && $category !== '') { $conds[] = 'p.category_id = :cat'; $params[':cat'] = (int)$category; }
if ($is_active !== null && $is_active !== '') { $conds[] = 'p.is_active = :active'; $params[':active'] = (int)$is_active; }
if ($conds) { $sql .= ' WHERE ' . implode(' AND ', $conds); }
$sql .= ' ORDER BY p.name';
$stmt = $db->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$rows) {
    echo "No rows returned.\n";
    exit(0);
}
foreach ($rows as $r) {
    echo sprintf("%d | %-30s | model:%-10s | cat:%-15s | active:%s | image:%s\n", $r['id'], $r['name'], $r['model'] ?? '-', $r['category_name'] ?? ($r['category_id'] ?? '-'), $r['is_active'], $r['image'] ?? '');
}
echo "Total: " . count($rows) . "\n";
