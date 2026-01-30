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

// Segédfüggvény: Batch ID generálás (pl: TR-1705934-KJ8)
function generateBatchId() {
    return 'TR-' . time() . '-' . strtoupper(substr(md5(uniqid()), 0, 4));
}

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
    if (empty($_SESSION['transport_cart']['items'])) {
        $_SESSION['transport_cart']['source_wh'] = (int)$_POST['source_wh_id'];
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
// 2. TRANZAKCIÓ VÉGLEGESÍTÉSE ÉS NAPLÓZÁS
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
            $userId = $_SESSION['user_id']; 

            foreach ($_SESSION['transport_cart']['items'] as $pId => $item) {
                $qty = $item['qty'];

                // 1. Forrás csökkentése
                $stmtSub = $pdo->prepare("UPDATE inventory SET quantity = quantity - ?, updated_at = NOW() WHERE product_ID = ? AND warehouse_ID = ?");
                $stmtSub->execute([$qty, $pId, $sourceWh]);

                // 2. Cél növelése
                $stmtCheck = $pdo->prepare("SELECT ID FROM inventory WHERE product_ID = ? AND warehouse_ID = ?");
                $stmtCheck->execute([$pId, $targetWh]);
                $exists = $stmtCheck->fetch();

                if ($exists) {
                    $stmtAdd = $pdo->prepare("UPDATE inventory SET quantity = quantity + ?, updated_at = NOW() WHERE product_ID = ? AND warehouse_ID = ?");
                    $stmtAdd->execute([$qty, $pId, $targetWh]);
                } else {
                    $stmtIns = $pdo->prepare("INSERT INTO inventory (product_ID, warehouse_ID, quantity, created_at) VALUES (?, ?, ?, NOW())");
                    $stmtIns->execute([$pId, $targetWh, $qty]);
                }

                // 3. Naplózás (EXPORT) - quantity oszloppal!
                $logExport = $pdo->prepare("
                    INSERT INTO transports (batch_id, product_ID, warehouse_ID, type, quantity, date, user_ID, description, arriveIn) 
                    VALUES (?, ?, ?, 'export', ?, NOW(), ?, ?, ?)
                ");
                $descExport = "Kiszállítás cél: Raktár #$targetWh. ($description)";
                $logExport->execute([$batchId, $pId, $sourceWh, $qty, $userId, $descExport, $arriveDate]);

                // 4. Naplózás (IMPORT) - quantity oszloppal!
                $logImport = $pdo->prepare("
                    INSERT INTO transports (batch_id, product_ID, warehouse_ID, type, quantity, date, user_ID, description, arriveIn) 
                    VALUES (?, ?, ?, 'import', ?, NOW(), ?, ?, ?)
                ");
                $descImport = "Beérkezés forrás: Raktár #$sourceWh. ($description)";
                $logImport->execute([$batchId, $pId, $targetWh, $qty, $userId, $descImport, $arriveDate]);
            }

            $pdo->commit();
            $_SESSION['transport_cart'] = ['source_wh' => null, 'items' => []];
            $message = "A szállítás sikeresen rögzítve! Azonosító: $batchId";
            $msgType = "success";

        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "Hiba: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

$warehouses = $pdo->query("SELECT * FROM warehouses ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

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
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Szállítás | CRUD-X</title>
    <link rel="stylesheet" href="./style/style.css">
</head>
<body>

<main class="container">
    <div class="card-header">
        <h2><img class="icon" src="./img/truck_23929.png">  Készlet Átszállítás</h2>
    </div>

    <?php if ($message): ?>
        <div style="margin-bottom: 20px; text-align:center;">
            <span class="badge badge-<?= $msgType === 'success' ? 'success' : 'muted' ?>" 
                  style="<?= $msgType === 'danger' ? 'background:#fee2e2; color:#b91c1c;' : '' ?>">
                <?= htmlspecialchars($message) ?>
            </span>
        </div>
    <?php endif; ?>

    <section class="card">
        <h3>1. Honnan indul az áru?</h3>
        <?php if ($_SESSION['transport_cart']['source_wh']): ?>
            <?php 
                $sourceName = "Ismeretlen";
                foreach($warehouses as $w) { if($w['ID'] == $_SESSION['transport_cart']['source_wh']) $sourceName = $w['name']; }
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
                    <label>Válassz indító raktárat:</label>
                    <select name="source_wh_id" required>
                        <option value="">-- Válassz --</option>
                        <?php foreach($warehouses as $w): ?>
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
            <h3>4. Véglegesítés</h3>
            <form method="POST">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                    <div class="field">
                        <label>Célállomás:</label>
                        <select name="target_wh_id" required>
                            <option value="">-- Válassz --</option>
                            <?php foreach($warehouses as $w): ?>
                                <?php if($w['ID'] != $_SESSION['transport_cart']['source_wh']): ?>
                                    <option value="<?= $w['ID'] ?>"><?= htmlspecialchars($w['name']) ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Várható érkezés:</label>
                        <input type="date" name="arrive_date" id="arriveDate"
                               onkeydown="return false" onpaste="return false" ondrop="return false"
                               aria-label="Várható érkezés (csak dátum kiválasztása)">
                    </div>
                    </div>
                    <div class="field col-12" style="grid-column: span 2;">
                        <label>Megjegyzés:</label>
                        <input type="text" name="description" required>
                    </div>
                </div>
                <div style="text-align:right; margin-top:20px;">
                    <button type="submit" name="finalize_transport" class="btn">Szállítás Indítása</button>
                </div>
            </form>
        </section>
    <?php endif; ?>
    <?php endif; ?>
</main>

<?php include './components/footer.php'; ?>

<script src="./script/script.js"></script>
</script>
</body>
</html>