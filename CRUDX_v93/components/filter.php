<?php
/*
Szűrők beolvasása (GET)
*/
$category_ID  = isset($_GET['category_ID']) ? (int)$_GET['category_ID'] : 0;
$item_number  = isset($_GET['item_number']) ? trim($_GET['item_number']) : '';
$name         = isset($_GET['name']) ? trim($_GET['name']) : '';
$description  = isset($_GET['description']) ? trim($_GET['description']) : '';
$active       = isset($_GET['active']) ? trim($_GET['active']) : ''; // '' | '1' | '0'
$stock        = isset($_GET['stock']) ? trim($_GET['stock']) : '';   // '' | 'in' | 'out'
$warehouse_ID = isset($_GET['warehouse_ID']) ? (int)$_GET['warehouse_ID'] : 0;
$qty_min      = isset($_GET['qty_min']) && $_GET['qty_min'] !== '' ? (int)$_GET['qty_min'] : null;
$qty_max      = isset($_GET['qty_max']) && $_GET['qty_max'] !== '' ? (int)$_GET['qty_max'] : null;

/**
 * ----------------------------
 * Dropdown adatok
 * ----------------------------
 */
$categories = $pdo->query("SELECT ID, category_name FROM categories ORDER BY category_name ASC")->fetchAll(PDO::FETCH_ASSOC);
$warehouses = $pdo->query("SELECT ID, name FROM warehouses ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

/**
 * ----------------------------
 * Mennyiségi mező meghatározása a szűréshez
 * ----------------------------
 */
// Ha van raktár azonosító, akkor csak az adott raktár SUM(quantity)-jét nézzük, egyébként a globálisat.
if ($warehouse_ID > 0) {
    $qtySqlSnippet = "SUM(CASE WHEN i.warehouse_ID = :warehouse_ID THEN i.quantity ELSE 0 END)";
} else {
    $qtySqlSnippet = "COALESCE(SUM(i.quantity), 0)";
}

/**
 * ----------------------------
 * Terméklista lekérdezés
 * ----------------------------
 */
$params = [];
if ($warehouse_ID > 0) {
    $params[':warehouse_ID'] = $warehouse_ID;
}

$sql = "
    SELECT
        p.ID,
        p.name,
        p.item_number,
        p.description,
        p.active,
        c.category_name,
        COALESCE(SUM(i.quantity), 0) AS global_total_qty,
        $qtySqlSnippet AS display_qty
    FROM products p
    LEFT JOIN categories c ON c.ID = p.category_ID
    LEFT JOIN inventory i ON i.product_ID = p.ID
";

$where = [];
if ($category_ID > 0) {
    $where[] = "p.category_ID = :category_ID";
    $params[':category_ID'] = $category_ID;
}
if ($item_number !== '') {
    // Stringként kezeljük, hogy a kötőjeles cikkszámok is működjenek
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
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " GROUP BY p.ID";

/** HAVING feltételek (Készlet szűrése a display_qty alapján) */
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

// Fontos: Ha kiválasztottunk egy raktárt, de nem állítottunk be "nincs készleten" szűrőt,
// akkor alapértelmezésben csak azokat mutassuk, amik ott vannak (ha ez az elvárás).
// Ha minden terméket látni akarsz a 0-s értékkel is a raktár kiválasztásakor, ezt hagyd ki.
if ($warehouse_ID > 0 && $stock === '') {
    // $having[] = "$qtySqlSnippet > 0"; // Opcionális: csak a raktárban létező termékek
}

if (!empty($having)) {
    $sql .= " HAVING " . implode(" AND ", $having);
}

$sql .= " ORDER BY p.name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

/**
 * ----------------------------
 * Készlet részletek a popuphoz
 * ----------------------------
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