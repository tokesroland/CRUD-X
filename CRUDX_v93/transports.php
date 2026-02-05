<?php
session_start();
include 'config.php';
require "./components/auth_check.php";
authorize(['admin', 'owner']); // Csak admin és tulajdonos indíthat szállítást

$pageTitle = "Szállítás";
$activePage = "transports.php";
include './components/navbar.php'; 

$message = "";
$msgType = "";

// --------------------------------------------------------
// SEGÉDFÜGGVÉNYEK ÉS JOGOSULTSÁGOK
// --------------------------------------------------------

// Batch ID generálás
function generateBatchId() {
    return 'TR-' . time() . '-' . strtoupper(substr(md5(uniqid()), 0, 4));
}

// Felhasználóhoz tartozó raktárak lekérése
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'] ?? 'user';
$allowedWarehouses = [];

if ($userRole === 'owner') {
    // Owner mindent lát
    $stmtWh = $pdo->query("SELECT ID, name, type FROM warehouses ORDER BY name ASC");
    $allowedWarehouses = $stmtWh->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Admin/User: Csak a hozzárendelteket látja a kapcsolótáblából
    $stmtWh = $pdo->prepare("
        SELECT w.ID, w.name, w.type 
        FROM warehouses w
        JOIN user_warehouse_access uwa ON w.ID = uwa.warehouse_id
        WHERE uwa.user_id = ?
        ORDER BY w.name ASC
    ");
    $stmtWh->execute([$userId]);
    $allowedWarehouses = $stmtWh->fetchAll(PDO::FETCH_ASSOC);
}

$allowedWarehouseIds = array_column($allowedWarehouses, 'ID');

// --------------------------------------------------------
// 1. KOSÁR MŰVELETEK (SESSION KEZELÉS)
// --------------------------------------------------------

if (!isset($_SESSION['transport_cart'])) {
    $_SESSION['transport_cart'] = [
        'source_wh' => null, 
        'items' => []        
    ];
}

// 1.A - Forrás raktár kiválasztása
if (isset($_POST['set_source'])) {
    $sourceId = (int)$_POST['source_wh_id'];
    
    if (!in_array($sourceId, $allowedWarehouseIds) && $userRole !== 'owner') {
        $message = "Nincs jogosultságod ebből a raktárból indítani!";
        $msgType = "danger";
    } elseif (empty($_SESSION['transport_cart']['items'])) {
        $_SESSION['transport_cart']['source_wh'] = $sourceId;
    } else {
        $message = "A forrás raktár nem módosítható, amíg van termék a listában!";
        $msgType = "danger";
    }
}

// 1.B - Termék hozzáadása a listához
if (isset($_POST['add_item'])) {
    $prodId = (int)$_POST['product_id'];
    $qty = (int)$_POST['quantity'];
    $prodName = $_POST['product_name']; 

    $stmt = $pdo->prepare("SELECT quantity FROM inventory WHERE product_ID = ? AND warehouse_ID = ?");
    $stmt->execute([$prodId, $_SESSION['transport_cart']['source_wh']]);
    $stock = $stmt->fetchColumn();

    if ($stock >= $qty && $qty > 0) {
        if (isset($_SESSION['transport_cart']['items'][$prodId])) {
            $_SESSION['transport_cart']['items'][$prodId]['qty'] += $qty;
        } else {
            $_SESSION['transport_cart']['items'][$prodId] = [
                'name' => $prodName,
                'qty' => $qty
            ];
        }
        if ($_SESSION['transport_cart']['items'][$prodId]['qty'] > $stock) {
            $_SESSION['transport_cart']['items'][$prodId]['qty'] = $stock; 
            $message = "A mennyiséget a maximális készletre korlátoztuk.";
            $msgType = "warning";
        }
    } else {
        $message = "Nincs ennyi termék a forrás raktárban!";
        $msgType = "danger";
    }
}

// 1.C - Tétel törlése
if (isset($_GET['remove_item'])) {
    $remId = (int)$_GET['remove_item'];
    unset($_SESSION['transport_cart']['items'][$remId]);
    header("Location: transports.php"); 
    exit;
}

// 1.D - Kosár ürítése
if (isset($_POST['clear_cart'])) {
    $_SESSION['transport_cart'] = ['source_wh' => null, 'items' => []];
    header("Location: transports.php");
    exit;
}

// --------------------------------------------------------
// 2. TRANZAKCIÓ INDÍTÁSA (FÜGGŐ ÁLLAPOT) - JAVÍTOTT
// --------------------------------------------------------
if (isset($_POST['finalize_transport'])) {
    if (empty($_SESSION['transport_cart']['items'])) {
        $message = "A lista üres!";
        $msgType = "danger";
    } elseif (empty($_POST['target_wh_id']) || $_POST['target_wh_id'] == $_SESSION['transport_cart']['source_wh']) {
        $message = "Érvénytelen célraktár!";
        $msgType = "danger";
    } else {
        try {
            $pdo->beginTransaction();

            $sourceWh = $_SESSION['transport_cart']['source_wh'];
            $targetWh = (int)$_POST['target_wh_id'];
            $description = $_POST['description'] ?? 'Átszállítás';
            $arriveDate = !empty($_POST['arrive_date']) ? $_POST['arrive_date'] : null;
            
            $batchId = generateBatchId(); 
            
            // JAVÍTÁS: Ellenőrizzük újra a készletet tranzakción belül!
            foreach ($_SESSION['transport_cart']['items'] as $pId => $item) {
                $qty = $item['qty'];

                // 1. Készlet ellenőrzése (Lockolhatnánk is FOR UPDATE-tel a precizitásért)
                $stmtCheck = $pdo->prepare("SELECT quantity, id FROM inventory WHERE product_ID = ? AND warehouse_ID = ?");
                $stmtCheck->execute([$pId, $sourceWh]);
                $invData = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if (!$invData || $invData['quantity'] < $qty) {
                    // Ha közben elfogyott, megszakítjuk a folyamatot
                    throw new Exception("Hiba: A '" . htmlspecialchars($item['name']) . "' termékből már nincs elegendő készlet!");
                }

                // 2. Forrás csökkentése (AZONNALI HATÁLY)
                $stmtSub = $pdo->prepare("UPDATE inventory SET quantity = quantity - ?, updated_at = NOW() WHERE id = ?");
                $stmtSub->execute([$qty, $invData['id']]);

                // 3. Naplózás (EXPORT) -> Status: COMPLETED
                $logExport = $pdo->prepare("
                    INSERT INTO transports (batch_id, product_ID, warehouse_ID, type, quantity, date, user_ID, description, arriveIn, status) 
                    VALUES (?, ?, ?, 'export', ?, NOW(), ?, ?, ?, 'completed')
                ");
                $descExport = "Kiszállítás cél: Raktár #$targetWh. ($description)";
                $logExport->execute([$batchId, $pId, $sourceWh, $qty, $userId, $descExport, $arriveDate]);

                // 4. Naplózás (IMPORT) -> Status: PENDING
                $logImport = $pdo->prepare("
                    INSERT INTO transports (batch_id, product_ID, warehouse_ID, type, quantity, date, user_ID, description, arriveIn, status) 
                    VALUES (?, ?, ?, 'import', ?, NOW(), ?, ?, ?, 'pending')
                ");
                $descImport = "Beérkezés forrás: Raktár #$sourceWh. ($description)";
                $logImport->execute([$batchId, $pId, $targetWh, $qty, $userId, $descImport, $arriveDate]);
            }

            $pdo->commit();
            $_SESSION['transport_cart'] = ['source_wh' => null, 'items' => []];
            $message = "A szállítás elindítva! Státusz: Függőben (Pending). Azonosító: $batchId";
            $msgType = "success";

        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "Hiba: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

// --------------------------------------------------------
// 3. MEGJELENÍTÉSHEZ SZÜKSÉGES ADATOK
// --------------------------------------------------------

$allWarehouses = $pdo->query("SELECT * FROM warehouses ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$sourceInventory = [];
if ($_SESSION['transport_cart']['source_wh']) {
    $stmtSrc = $pdo->prepare("
        SELECT p.ID, p.name, p.item_number, i.quantity 
        FROM inventory i 
        JOIN products p ON i.product_ID = p.ID 
        WHERE i.warehouse_ID = ? AND i.quantity > 0
        ORDER BY p.name
    ");
    $stmtSrc->execute([$_SESSION['transport_cart']['source_wh']]);
    $sourceInventory = $stmtSrc->fetchAll(PDO::FETCH_ASSOC);
}

// BEJÖVŐ FÜGGŐ SZÁLLÍTMÁNYOK
$pendingQuery = "
    SELECT t.batch_id, t.date, t.arriveIn, t.description, w.name as target_wh_name, COUNT(t.ID) as item_count
    FROM transports t
    JOIN warehouses w ON t.warehouse_ID = w.ID
    WHERE t.type = 'import' AND t.status = 'pending'
";

if ($userRole !== 'owner') {
    if (!empty($allowedWarehouseIds)) {
        $inClause = implode(',', array_map('intval', $allowedWarehouseIds));
        $pendingQuery .= " AND t.warehouse_ID IN ($inClause)";
    } else {
        $pendingQuery .= " AND 1=0"; 
    }
}

$pendingQuery .= " GROUP BY t.batch_id ORDER BY t.date DESC";
$pendingTransports = $pdo->query($pendingQuery)->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Szállítás | CRUD-X</title>
    <link rel="stylesheet" href="./style/style.css">
    <link rel="stylesheet" href="./style/transports.css">
</head>

<body>

<main class="container">
    
    <?php if ($message): ?>
        <div style="margin-bottom: 20px; text-align:center;">
            <span class="badge badge-<?= $msgType === 'success' ? 'success' : 'muted' ?>" 
                  style="<?= $msgType === 'danger' ? 'background:#fee2e2; color:#b91c1c;' : '' ?>">
                <?= htmlspecialchars($message) ?>
            </span>
        </div>
    <?php endif; ?>

    <?php if (!empty($pendingTransports)): ?>
        <section class="card pending-card" style="margin-bottom: 30px;">
            <div class="card-header">
                <h2><img class="icon" src="./img/1485477075-calendar_78587.png"> Beérkezésre váró szállítmányok (Átvétel szükséges)</h2>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Batch ID</th>
                            <th>Célraktár</th>
                            <th>Indítva</th>
                            <th>Várható érkezés</th>
                            <th>Tételek</th>
                            <th>Művelet</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($pendingTransports as $pt): ?>
                            <tr>
                                <td>
                                    <a href="transport.php?batch=<?= $pt['batch_id'] ?>" class="batch-link">
                                        <?= htmlspecialchars($pt['batch_id']) ?>
                                    </a>
                                </td>
                                <td><strong><?= htmlspecialchars($pt['target_wh_name']) ?></strong></td>
                                <td><?= date('Y.m.d H:i', strtotime($pt['date'])) ?></td>
                                <td><?= $pt['arriveIn'] ? date('Y.m.d', strtotime($pt['arriveIn'])) : '-' ?></td>
                                <td><?= $pt['item_count'] ?> db tétel</td>
                                <td>
                                    <a href="transport.php?batch=<?= $pt['batch_id'] ?>" class="btn btn-small btn-primary">Megtekintés / Átvétel</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #dde1e7;">
    <?php endif; ?>


    <div class="card-header">
        <h2><img class="icon" src="./img/truck_23929.png">  Új Szállítás Indítása</h2>
    </div>

    <section class="card">
        <h3>1. Honnan indul az áru?</h3>
        <?php if ($_SESSION['transport_cart']['source_wh']): ?>
            <?php 
                $sourceName = "Ismeretlen";
                foreach($allWarehouses as $w) { if($w['ID'] == $_SESSION['transport_cart']['source_wh']) $sourceName = $w['name']; }
            ?>
            <div style="display:flex; justify-content:space-between; align-items:center; background:#f0f9ff; padding:15px; border-radius:8px; border:1px solid #bae6fd;">
                <div>
                    <strong>Kiválasztott forrás:</strong> <br> 
                    <span style="font-size:1.2em; color:#0284c7;"><?= htmlspecialchars($sourceName) ?></span>
                </div>
                <form method="POST">
                    <button type="submit" name="clear_cart" class="btn btn-outline danger" onclick="return confirm('Biztosan üríted a listát?')">Módosítás / Ürítés</button>
                </form>
            </div>
        <?php else: ?>
            <form method="POST" style="display:flex; gap:10px; align-items:end;">
                <div class="field" style="flex:1;">
                    <label>Válassz indító raktárat (Csak saját):</label>
                    <select name="source_wh_id" required>
                        <option value="">-- Válassz --</option>
                        <?php foreach($allowedWarehouses as $w): ?>
                            <option value="<?= $w['ID'] ?>"><?= htmlspecialchars($w['name']) ?> (<?= $w['type'] == 'store' ? 'Bolt' : 'Raktár' ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="set_source" class="btn">Kiválasztás</button>
            </form>
        <?php endif; ?>
    </section>

    <?php if ($_SESSION['transport_cart']['source_wh']): ?>
    <div class="management-grid" style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
        <section class="card">
            <h3>2. Termékek hozzáadása</h3>
            <form method="POST">
                <div class="field" style="margin-bottom:15px;">
                    <label>Válassz terméket:</label>
                    <select name="product_id" id="productSelect" required onchange="updateMaxQty()">
                        <option value="" data-qty="0">-- Válassz --</option>
                        <?php foreach($sourceInventory as $prod): ?>
                            <option value="<?= $prod['ID'] ?>" data-qty="<?= $prod['quantity'] ?>" data-name="<?= htmlspecialchars($prod['name']) ?>">
                                <?= htmlspecialchars($prod['name']) ?> - Készleten: <?= $prod['quantity'] ?> db
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="product_name" id="productNameHidden">
                </div>
                <div class="field" style="margin-bottom:15px;">
                    <label>Mennyiség:</label>
                    <input type="number" name="quantity" id="qtyInput" min="1" required>
                    <small id="maxQtyHint">Válassz terméket...</small>
                </div>
                <button type="submit" name="add_item" class="btn btn-outline" style="width:100%;">+ Hozzáadás</button>
            </form>
        </section>

        <section class="card">
            <h3 style="margin-bottom:15px;">3. Szállítmány tartalma</h3>
            <p style="font-size: 0.8rem;">Telefonos nézeten görgessen a részletekhez!</p>
            <?php if (empty($_SESSION['transport_cart']['items'])): ?>
                <p style="text-align:center; color:#aaa;">A lista üres.</p>
            <?php else: ?>
                <table class="data-table">
                    <thead><tr><th>Termék</th><th>Mennyiség</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach($_SESSION['transport_cart']['items'] as $pid => $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= $item['qty'] ?> db</td>
                                <td><a href="transports.php?remove_item=<?= $pid ?>" style="color:red;">Törlés</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </div>

    <?php if (!empty($_SESSION['transport_cart']['items'])): ?>
        <section class="card" style="margin-top:20px;">
            <h3>4. Véglegesítés és Indítás</h3>
            <form method="POST">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                    <div class="field">
                        <label>Célállomás:</label>
                        <select name="target_wh_id" required>
                            <option value="">-- Válassz --</option>
                            <?php foreach($allWarehouses as $w): ?>
                                <?php if($w['ID'] != $_SESSION['transport_cart']['source_wh']): ?>
                                    <option value="<?= $w['ID'] ?>"><?= htmlspecialchars($w['name']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Várható érkezés:</label>
                        <input type="date" name="arrive_date" id="arriveDate"
                               onkeydown="return false" onpaste="return false" ondrop="return false">
                    </div>
                    <div class="field col-12" style="grid-column: span 2;">
                        <label>Megjegyzés:</label>
                        <input type="text" name="description" required placeholder="Pl. Heti utánpótlás">
                    </div>
                </div>
                <div style="text-align:right; margin-top:20px;">
                    <button type="submit" name="finalize_transport" class="btn">Szállítás Indítása (Pending)</button>
                </div>
            </form>
        </section>
    <?php endif; ?>
    <?php endif; ?>
</main>

<?php include './components/footer.php'; ?>

<script src="./script/script.js"></script>
<script>
    function updateMaxQty() {
        const select = document.getElementById('productSelect');
        const selectedOption = select.options[select.selectedIndex];
        const maxQty = selectedOption.getAttribute('data-qty');
        const name = selectedOption.getAttribute('data-name');
        
        document.getElementById('qtyInput').max = maxQty;
        document.getElementById('maxQtyHint').innerText = "Max: " + maxQty + " db";
        document.getElementById('productNameHidden').value = name;
    }
</script>
</body>
</html>