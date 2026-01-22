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
 * Terméklista lekérdezés (szűrhető, aggregált készlettel)
 * ----------------------------
 */
$params = [];
$where = [];

$sqlSelectExtra = "NULL AS wh_qty";
if ($warehouse_ID > 0) {
    // kiválasztott raktár készlete termékenként
    $sqlSelectExtra = "SUM(CASE WHEN i.warehouse_ID = :warehouse_ID THEN i.quantity ELSE 0 END) AS wh_qty";
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
        COALESCE(SUM(i.quantity), 0) AS total_qty,
        $sqlSelectExtra
    FROM products p
    LEFT JOIN categories c ON c.ID = p.category_ID
    LEFT JOIN inventory i ON i.product_ID = p.ID
";

/** WHERE feltételek (nem aggregáltak) */
if ($category_ID > 0) {
    $where[] = "p.category_ID = :category_ID";
    $params[':category_ID'] = $category_ID;
}
if ($item_number !== '') {
    // item_number int a táblában, ezért egyezésre szűrünk
    $where[] = "p.item_number = :item_number";
    $params[':item_number'] = (int)$item_number;
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

/** HAVING feltételek (aggregáltak: készlet, mennyiség) */
$having = [];

if ($stock === 'in') {
    $having[] = "COALESCE(SUM(i.quantity), 0) > 0";
} elseif ($stock === 'out') {
    $having[] = "COALESCE(SUM(i.quantity), 0) = 0";
}

// Mennyiség szűrés: ha van warehouse_ID, akkor wh_qty alapján, különben total_qty alapján
$qtyField = ($warehouse_ID > 0) ? "SUM(CASE WHEN i.warehouse_ID = :warehouse_ID THEN i.quantity ELSE 0 END)" : "COALESCE(SUM(i.quantity), 0)";

if ($qty_min !== null) {
    $having[] = "$qtyField >= :qty_min";
    $params[':qty_min'] = $qty_min;
}
if ($qty_max !== null) {
    $having[] = "$qtyField <= :qty_max";
    $params[':qty_max'] = $qty_max;
}

// Ha raktár kiválasztva: opcionálisan csak azokat mutassuk, ahol abban a raktárban >0
// (ha ezt nem kéred, töröld ezt a blokkot)
if ($warehouse_ID > 0 && $stock === 'in') {
    // ha kifejezetten "készleten" szűrsz és raktárt választottál, akkor abban a raktárban legyen készlet
    $having[] = "SUM(CASE WHEN i.warehouse_ID = :warehouse_ID THEN i.quantity ELSE 0 END) > 0";
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
 * Készlet részletek lekérése csak a listázott termékekhez (popuphoz)
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