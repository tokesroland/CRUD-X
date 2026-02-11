<?php
// components/stats_query.php
$warehouseActiveCondition = "w.active = 1";

// --------------------------------------------------------------------------
// 1. RAKTÁR KAPACITÁS & LISTA (JAVÍTVA: Minden aktív raktár megjelenik)
// --------------------------------------------------------------------------
$sqlBase = "
     SELECT w.ID, w.name, w.max_quantity, COALESCE(SUM(i.quantity), 0) AS current_quantity
     FROM warehouses w
     LEFT JOIN inventory i ON i.warehouse_ID = w.ID
     WHERE {$warehouseActiveCondition}
     GROUP BY w.ID 
     ORDER BY w.name ASC
";

// Nem szűrünk user_id-ra, így mindenki lát mindent ezen a jelentés oldalon.
$stmt = $pdo->query($sqlBase);
$warehouseStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* --------------------------------------------------------------------------
    2. STATISZTIKÁK SZÁMÍTÁSA
    -------------------------------------------------------------------------- */
$totalItems = 0;
$criticalWarehouses = 0;

foreach ($warehouseStats as &$w) {
     $percent = $w['max_quantity'] > 0 ? ($w['current_quantity'] / $w['max_quantity']) * 100 : 0;
     $w['is_critical'] = $percent >= 80;
     
     if ($w['is_critical']) {
          $criticalWarehouses++;
     }
     $totalItems += $w['current_quantity'];
}
unset($w);

/* --------------------------------------------------------------------------
    3. ALACSONY KÉSZLET (Low Stock számláló)
    -------------------------------------------------------------------------- */
// Itt is globális nézetet adunk (de CSAK az aktív raktárakat vesszük figyelembe)
$lowStockSql = "
     SELECT COUNT(*) 
     FROM inventory i
     JOIN warehouses w ON i.warehouse_ID = w.ID
     WHERE {$warehouseActiveCondition} AND i.quantity <= i.min_quantity
";
$stmtLow = $pdo->query($lowStockSql);
$lowStockCount = $stmtLow->fetchColumn();

/* --------------------------------------------------------------------------
    4. KRITIKUS KÉSZLET TÁBLÁZAT ADATAI (JAVÍTVA: Minden aktív raktár látszik)
    -------------------------------------------------------------------------- */
$criticalDataSql = "
     SELECT p.name AS product_name, w.name AS warehouse_name, i.quantity, i.min_quantity, w.ID as warehouse_id
     FROM inventory i
     JOIN products p ON i.product_ID = p.ID
     JOIN warehouses w ON i.warehouse_ID = w.ID
     WHERE {$warehouseActiveCondition} AND i.quantity <= i.min_quantity
     ORDER BY i.quantity ASC
";

// Itt sem szűrünk user_warehouse_access-re
$stmtCrit = $pdo->query($criticalDataSql);
$criticalData = $stmtCrit->fetchAll(PDO::FETCH_ASSOC);

/* --------------------------------------------------------------------------
    5. STÁTUSZ STATISZTIKÁK
    --------------------------------------------------------------------------
    Számítás: csak azok a termékek kerülnek ide, amelyek aktív raktárban vannak.
    (Ha szeretnéd, hogy a státusz statisztika teljesen globális legyen
    függetlenül a raktáraktól, jelezd és visszaállítom az eredeti lekérdezést.)
    -------------------------------------------------------------------------- */
$statusStats = $pdo->query("
     SELECT p.active, COUNT(DISTINCT p.ID) as count
     FROM products p
     JOIN inventory i ON i.product_ID = p.ID
     JOIN warehouses w ON i.warehouse_ID = w.ID
     WHERE {$warehouseActiveCondition}
     GROUP BY p.active
")->fetchAll(PDO::FETCH_ASSOC);

$totalProductCount = array_sum(array_column($statusStats, 'count'));

?>