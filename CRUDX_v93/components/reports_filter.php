<?php
// --- SZŰRŐK BEÁLLÍTÁSA ---
$range = isset($_GET['range']) ? (int)$_GET['range'] : 30;
$wh_filter = (isset($_GET['wh']) && $_GET['wh'] !== 'all') ? (int)$_GET['wh'] : 'all';

// 1. FORGALMI IDŐVONAL (SZŰRT)
$timeline_sql = "
    SELECT 
        DATE(date) as move_date,
        SUM(CASE WHEN type = 'import' THEN quantity ELSE 0 END) as total_in,
        SUM(CASE WHEN type = 'export' THEN quantity ELSE 0 END) as total_out
    FROM transports
    WHERE date >= DATE_SUB(NOW(), INTERVAL ? DAY)
";
if ($wh_filter !== 'all') {
    $timeline_sql .= " AND warehouse_ID = " . (int)$wh_filter;
}
$timeline_sql .= " GROUP BY DATE(date) ORDER BY move_date ASC";

$stmt = $pdo->prepare($timeline_sql);
$stmt->execute([$range]);
$timelineData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. ELFEKVŐ KÉSZLETEK (JAVÍTOTT SQL - warehouse_name hozzáadva)
$dead_sql = "
    SELECT p.name, i.quantity, MAX(t.date) as last_move, w.name as warehouse_name
    FROM inventory i
    JOIN products p ON i.product_ID = p.ID
    JOIN warehouses w ON i.warehouse_ID = w.ID
    LEFT JOIN transports t ON (t.product_ID = p.ID AND t.warehouse_ID = i.warehouse_ID)
    WHERE i.quantity > 0
";
if ($wh_filter !== 'all') {
    $dead_sql .= " AND i.warehouse_ID = " . (int)$wh_filter;
}
$dead_sql .= " GROUP BY p.ID, i.warehouse_ID
               HAVING (last_move < DATE_SUB(NOW(), INTERVAL 30 DAY) OR last_move IS NULL)
               ORDER BY i.quantity DESC LIMIT 10";

$deadStock = $pdo->query($dead_sql)->fetchAll(PDO::FETCH_ASSOC);
?>