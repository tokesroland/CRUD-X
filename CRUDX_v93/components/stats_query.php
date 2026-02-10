<?php
/*
--------------------------------------------------------------------------
1. ADATLEKÉRÉSEK (ALAP STATISZTIKÁK)

*/

// Raktár Terheltség adatok
$stmt = $pdo->query("
    SELECT w.ID, w.name, w.max_quantity, COALESCE(SUM(i.quantity), 0) AS current_quantity
    FROM warehouses w
    LEFT JOIN inventory i ON i.warehouse_ID = w.ID
    GROUP BY w.ID ORDER BY w.name
");
$warehouseStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalItems = 0;
$criticalWarehouses = 0;
foreach ($warehouseStats as &$w) {
    $percent = $w['max_quantity'] > 0 ? ($w['current_quantity'] / $w['max_quantity']) * 100 : 0;
    $w['is_critical'] = $percent >= 80;
    if ($w['is_critical'])
        $criticalWarehouses++;
    $totalItems += $w['current_quantity'];
}

// Minimum adatok
// SQL: Csak a kritikus készletek (készlet <= minimum)
$stmt = $pdo->query("
    SELECT p.name AS product_name, w.ID AS warehouse_id, w.name AS warehouse_name, i.quantity, i.min_quantity
    FROM inventory i
    JOIN products p ON p.ID = i.product_ID
    JOIN warehouses w ON w.ID = i.warehouse_ID
    WHERE i.quantity <= i.min_quantity  -- Ez szűri ki a felesleget
    ORDER BY i.quantity ASC
");
$criticalData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$lowStockCount = 0;
foreach ($criticalData as $item) {
    if ($item['quantity'] <= $item['min_quantity'])
        $lowStockCount++;
}

// Státusz statisztikák
$statusStats = $pdo->query("SELECT active, COUNT(*) as count FROM products GROUP BY active")->fetchAll(PDO::FETCH_ASSOC);
$totalProductCount = array_sum(array_column($statusStats, 'count'));


/*
--------------------------------------------------------------------------
2. IDŐSZAKOS FORGALOM

*/
$timeline_sql = "
    SELECT 
        DATE(date) as move_date,
        SUM(CASE WHEN type = 'import' THEN quantity ELSE 0 END) as total_in,
        SUM(CASE WHEN type = 'export' THEN quantity ELSE 0 END) as total_out
    FROM transports
    WHERE date >= DATE_SUB(NOW(), INTERVAL ? DAY)
";

$timeline_params = [$range];

// Ha van raktár szűrő, hozzáadjuk a feltételt
if ($wh_filter !== 'all') {
    $timeline_sql .= " AND warehouse_ID = ?";
    $timeline_params[] = $wh_filter;
}

$timeline_sql .= " GROUP BY DATE(date) ORDER BY move_date ASC";

$stmt = $pdo->prepare($timeline_sql);
$stmt->execute($timeline_params);
$timelineData = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
--------------------------------------------------------------------------
3. ELFEKVŐ KÉSZLETEK

*/
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

<?php
// components/stats_query.php

/* --------------------------------------------------------------------------
   0. JOGOSULTSÁGOK ÉS RAKTÁRAK AZONOSÍTÁSA
   -------------------------------------------------------------------------- */
$userId   = $_SESSION['user_id'];
$userRole = $_SESSION['role'] ?? 'user';

// Alap SQL a raktárak lekéréséhez (Active = 1 fontos!)
$sqlBase = "
    SELECT w.ID, w.name, w.max_quantity, COALESCE(SUM(i.quantity), 0) AS current_quantity
    FROM warehouses w
    LEFT JOIN inventory i ON i.warehouse_ID = w.ID
";

// Jogosultság alapú szűrés hozzáadása
if ($userRole === 'owner') {
    // Owner: Minden AKTÍV raktár
    $sqlBase .= " WHERE w.active = 1";
    $sqlBase .= " GROUP BY w.ID ORDER BY w.name ASC";
    $stmt = $pdo->query($sqlBase);
} else {
    // Admin/User: Csak a hozzárendelt és AKTÍV raktárak
    // JOIN a user_warehouse_access táblával
    $sqlBase .= " JOIN user_warehouse_access uwa ON uwa.warehouse_id = w.ID";
    $sqlBase .= " WHERE w.active = 1 AND uwa.user_id = :uid";
    $sqlBase .= " GROUP BY w.ID ORDER BY w.name ASC";
    
    $stmt = $pdo->prepare($sqlBase);
    $stmt->execute(['uid' => $userId]);
}

$warehouseStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* --------------------------------------------------------------------------
   1. STATISZTIKÁK SZÁMÍTÁSA (Kártyákhoz)
   -------------------------------------------------------------------------- */
$totalItems = 0;
$criticalWarehouses = 0;

foreach ($warehouseStats as &$w) {
    // Százalék számítás (0-val való osztás elkerülése)
    $percent = $w['max_quantity'] > 0 ? ($w['current_quantity'] / $w['max_quantity']) * 100 : 0;
    
    // Kritikus, ha 80% felett van
    $w['is_critical'] = $percent >= 80;
    
    if ($w['is_critical']) {
        $criticalWarehouses++;
    }
    
    $totalItems += $w['current_quantity'];
}
unset($w); // Referencia törlése

/* --------------------------------------------------------------------------
   2. ALACSONY KÉSZLET (Low Stock)
   -------------------------------------------------------------------------- */
// Itt is figyelni kell a jogosultságra!
$lowStockSql = "
    SELECT COUNT(*) 
    FROM inventory i
    JOIN warehouses w ON i.warehouse_ID = w.ID
";

// Státusz statisztikák
$statusStats = $pdo->query("SELECT active, COUNT(*) as count FROM products GROUP BY active")->fetchAll(PDO::FETCH_ASSOC);
$totalProductCount = array_sum(array_column($statusStats, 'count'));


// Ha nem owner, szűrünk jogosultságra
if ($userRole !== 'owner') {
    $lowStockSql .= " JOIN user_warehouse_access uwa ON uwa.warehouse_id = w.ID";
    $lowStockSql .= " WHERE uwa.user_id = :uid AND w.active = 1 AND i.quantity <= i.min_quantity";
    $stmtLow = $pdo->prepare($lowStockSql);
    $stmtLow->execute(['uid' => $userId]);
} else {
    $lowStockSql .= " WHERE w.active = 1 AND i.quantity <= i.min_quantity";
    $stmtLow = $pdo->query($lowStockSql);
}

$lowStockCount = $stmtLow->fetchColumn();

/* --------------------------------------------------------------------------
   3. KRITIKUS KÉSZLET TÁBLÁZAT ADATAI (Reports.php-hoz)
   -------------------------------------------------------------------------- */
// Ez tölti fel az alsó táblázatot azokkal a termékekkel, amik kifogyóban vannak
$criticalDataSql = "
    SELECT p.name AS product_name, w.name AS warehouse_name, i.quantity, i.min_quantity, w.ID as warehouse_id
    FROM inventory i
    JOIN products p ON i.product_ID = p.ID
    JOIN warehouses w ON i.warehouse_ID = w.ID
";

if ($userRole !== 'owner') {
    $criticalDataSql .= " JOIN user_warehouse_access uwa ON uwa.warehouse_id = w.ID";
    $criticalDataSql .= " WHERE uwa.user_id = :uid AND w.active = 1 AND i.quantity <= i.min_quantity";
    $stmtCrit = $pdo->prepare($criticalDataSql);
    $stmtCrit->execute(['uid' => $userId]);
} else {
    $criticalDataSql .= " WHERE w.active = 1 AND i.quantity <= i.min_quantity";
    $stmtCrit = $pdo->query($criticalDataSql);
}

$criticalData = $stmtCrit->fetchAll(PDO::FETCH_ASSOC);
?>