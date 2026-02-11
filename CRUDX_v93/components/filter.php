<?php
/*
Szűrők beolvasása (GET)
*/
$category_ID  = isset($_GET['category_ID']) ? (int)$_GET['category_ID'] : 0;
$item_number  = isset($_GET['item_number']) ? trim($_GET['item_number']) : '';
$name         = isset($_GET['name']) ? trim($_GET['name']) : '';
$description  = isset($_GET['description']) ? trim($_GET['description']) : '';
$active       = isset($_GET['active']) ? trim($_GET['active']) : ''; 
$stock        = isset($_GET['stock']) ? trim($_GET['stock']) : '';   
$warehouse_ID = isset($_GET['warehouse_ID']) ? (int)$_GET['warehouse_ID'] : 0;
$qty_min      = isset($_GET['qty_min']) && $_GET['qty_min'] !== '' ? (int)$_GET['qty_min'] : null;
$qty_max      = isset($_GET['qty_max']) && $_GET['qty_max'] !== '' ? (int)$_GET['qty_max'] : null;

// PAGINATION PARAMÉTEREK
$page  = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$limit = 100; // Rekord per oldal
$offset = ($page - 1) * $limit;

/**
 * ----------------------------
 * Dropdown adatok
 * ----------------------------
 * JAVÍTÁS 1: Csak az aktív raktárakat töltjük be a listába
 */
$categories = $pdo->query("SELECT ID, category_name FROM categories ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$warehouses = $pdo->query("SELECT ID, name FROM warehouses WHERE active = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

/**
 * ----------------------------
 * Mennyiségi mező meghatározása a szűréshez
 * ----------------------------
 * JAVÍTÁS 2: A SUM() függvényben ellenőrizzük, hogy a raktár aktív-e (w.active = 1)
 */
if ($warehouse_ID > 0) {
    // Ha konkrét raktárra szűrünk
    $qtySqlSnippet = "SUM(CASE WHEN i.warehouse_ID = :warehouse_ID AND w.active = 1 THEN i.quantity ELSE 0 END)";
} else {
    // Globális nézet (Összes raktár)
    $qtySqlSnippet = "COALESCE(SUM(CASE WHEN w.active = 1 THEN i.quantity ELSE 0 END), 0)";
}

/**
 * ----------------------------
 * SQL Lekérdezés felépítése
 * ----------------------------
 */
$params = [];
if ($warehouse_ID > 0) {
    $params[':warehouse_ID'] = $warehouse_ID;
}

// Alap SELECT
// JAVÍTÁS 3: Hozzáadjuk a JOIN warehouses w sort, hogy elérjük az 'active' mezőt
$sqlBase = "
    SELECT
        p.ID,
        p.name,
        p.item_number,
        p.description,
        p.active,
        c.category_name,
        /* Globális készlet is csak az aktív raktárakból */
        COALESCE(SUM(CASE WHEN w.active = 1 THEN i.quantity ELSE 0 END), 0) AS global_total_qty,
        $qtySqlSnippet AS display_qty
    FROM products p
    LEFT JOIN categories c ON c.ID = p.category_ID
    LEFT JOIN inventory i ON i.product_ID = p.ID
    LEFT JOIN warehouses w ON i.warehouse_ID = w.ID 
";

// WHERE építése
$where = [];
if ($category_ID > 0) {
    $where[] = "p.category_ID = :category_ID";
    $params[':category_ID'] = $category_ID;
}
if ($item_number !== '') {
    $where[] = "p.item_number LIKE :item_number";
    $params[':item_number'] = "%{$item_number}%";
}
if ($name !== '') {
    $where[] = "p.name LIKE :name";
    $params[':name'] = "%{$name}%";
}
if ($description !== '') {
    $where[] = "p.description LIKE :description";
    $params[':description'] = "%{$description}%";
}
if ($active === '1' || $active === '0') {
    $where[] = "p.active = :active";
    $params[':active'] = (int)$active;
}

if (!empty($where)) {
    $sqlBase .= " WHERE " . implode(" AND ", $where);
}

$sqlBase .= " GROUP BY p.ID";

// HAVING építése
$having = [];
if ($stock === 'in') {
    $having[] = "$qtySqlSnippet > 0";
} elseif ($stock === 'out') {
    $having[] = "$qtySqlSnippet = 0";
}
if ($qty_min !== null) {
    $having[] = "$qtySqlSnippet >= :qty_min";
    $params[':qty_min'] = $qty_min;
}
if ($qty_max !== null) {
    $having[] = "$qtySqlSnippet <= :qty_max";
    $params[':qty_max'] = $qty_max;
}

if (!empty($having)) {
    $sqlBase .= " HAVING " . implode(" AND ", $having);
}

/**
 * ----------------------------
 * 1. TALÁLATOK MEGSZÁMLÁLÁSA (PAGINATIONHOZ)
 * ----------------------------
 */
$countSql = "SELECT COUNT(*) FROM ($sqlBase) AS subquery";
$stmtCount = $pdo->prepare($countSql);
$stmtCount->execute($params);
$totalRows = $stmtCount->fetchColumn();
$totalPages = ceil($totalRows / $limit);

/**
 * ----------------------------
 * 2. VÉGLEGES ADATLEKÉRÉS (LIMITTEL)
 * ----------------------------
 */
$sqlFinal = $sqlBase . " ORDER BY p.name ASC LIMIT $limit OFFSET $offset";

$stmt = $pdo->prepare($sqlFinal);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

/**
 * ----------------------------
 * Készlet részletek (Popuphoz) - Csak a lekérdezett 100 termékre
 * ----------------------------
 * JAVÍTÁS 4: Itt is szűrjük az aktív raktárakat
 */
$warehousesByProduct = [];
if (!empty($products)) {
    $productIds = array_map(fn($p) => (int)$p['ID'], $products);
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    
    $sqlInv = "
        SELECT i.product_ID, i.quantity, w.name AS warehouse_name
        FROM inventory i
        JOIN warehouses w ON w.ID = i.warehouse_ID
        WHERE i.product_ID IN ($placeholders)
        AND w.active = 1  -- <--- CSAK AKTÍV RAKTÁRAK
        ORDER BY w.name ASC
    ";
    $stmtInv = $pdo->prepare($sqlInv);
    $stmtInv->execute($productIds);
    $inventoryData = $stmtInv->fetchAll(PDO::FETCH_ASSOC);

    foreach ($inventoryData as $row) {
        $warehousesByProduct[$row['product_ID']][] = [
            'warehouse' => $row['warehouse_name'],
            'quantity'  => (int)$row['quantity'],
        ];
    }
}
?>