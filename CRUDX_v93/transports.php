<?php
session_start();
include 'config.php';
require "./components/auth_check.php";
authorize(['admin', 'owner']); // Csak admin és tulajdonos szállíthat
$pageTitle = "Szállítások";
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

// Kosár inicializálása, ha még nincs
if (!isset($_SESSION['transport_cart'])) {
    $_SESSION['transport_cart'] = [
        'source_wh' => null,  // Honnan
        'items' => []         // Termékek listája
    ];
}

// 1.A - Forrás raktár kiválasztása (Lockoljuk a kosarat ehhez a raktárhoz)
if (isset($_POST['set_source'])) {
    if (empty($_SESSION['transport_cart']['items'])) {
        $_SESSION['transport_cart']['source_wh'] = (int)$_POST['source_wh_id'];
    } else {
        $message = "A forrás raktár nem módosítható, amíg van termék a listában! Előbb ürítsd a listát.";
        $msgType = "danger";
    }
}

// 1.B - Termék hozzáadása a listához
if (isset($_POST['add_item'])) {
    $prodId = (int)$_POST['product_id'];
    $qty = (int)$_POST['quantity'];
    $prodName = $_POST['product_name']; // Rejtett mezőből jön a név, hogy ne kelljen lekérdezni újra

    // Validáció: Van-e elég készlet?
    $stmt = $pdo->prepare("SELECT quantity FROM inventory WHERE product_ID = ? AND warehouse_ID = ?");
    $stmt->execute([$prodId, $_SESSION['transport_cart']['source_wh']]);
    $stock = $stmt->fetchColumn();

    if ($stock >= $qty && $qty > 0) {
        // Hozzáadás (vagy növelés ha már benne van)
        if (isset($_SESSION['transport_cart']['items'][$prodId])) {
            $_SESSION['transport_cart']['items'][$prodId]['qty'] += $qty;
        } else {
            $_SESSION['transport_cart']['items'][$prodId] = [
                'name' => $prodName,
                'qty' => $qty
            ];
        }
        // Ellenőrizzük újra, nem léptük-e túl a keretet összesítve
        if ($_SESSION['transport_cart']['items'][$prodId]['qty'] > $stock) {
            $_SESSION['transport_cart']['items'][$prodId]['qty'] = $stock; // Maxoljuk a készletre
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
    header("Location: transports.php"); // Tiszta URL újratöltés
    exit;
}

// 1.D - Kosár ürítése (Újra lehet forrást választani)
if (isset($_POST['clear_cart'])) {
    $_SESSION['transport_cart'] = ['source_wh' => null, 'items' => []];
    header("Location: transports.php");
    exit;
}

// --------------------------------------------------------
// 2. TRANZAKCIÓ VÉGLEGESÍTÉSE (MENTÉS AZ ADATBÁZISBA)
// --------------------------------------------------------
if (isset($_POST['finalize_transport'])) {
    if (empty($_SESSION['transport_cart']['items'])) {
        $message = "A lista üres!";
        $msgType = "danger";
    } elseif (empty($_POST['target_wh_id']) || $_POST['target_wh_id'] == $_SESSION['transport_cart']['source_wh']) {
        $message = "Érvénytelen célraktár! (Nem lehet ugyanaz, mint a forrás)";
        $msgType = "danger";
    } else {
        try {
            $pdo->beginTransaction();

            $sourceWh = $_SESSION['transport_cart']['source_wh'];
            $targetWh = (int)$_POST['target_wh_id'];
            $description = $_POST['description'] ?? 'Átszállítás';
            $arriveDate = !empty($_POST['arrive_date']) ? $_POST['arrive_date'] : null;
            
            // 5. PONT: Batch ID generálás
            $batchId = generateBatchId(); 
            $userId = $_SESSION['user_id']; // Naplózás: Ki csinálta?

            foreach ($_SESSION['transport_cart']['items'] as $pId => $item) {
                $qty = $item['qty'];

                // 1. Forrás csökkentése
                $stmtSub = $pdo->prepare("UPDATE inventory SET quantity = quantity - ?, updated_at = NOW() WHERE product_ID = ? AND warehouse_ID = ?");
                $stmtSub->execute([$qty, $pId, $sourceWh]);

                // 2. Cél növelése (vagy létrehozása)
                // Megnézzük, van-e már ilyen termék a célraktárban
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

                // 3. Naplózás a transports táblába (EXPORT oldal)
                $logExport = $pdo->prepare("
                    INSERT INTO transports (batch_id, product_ID, warehouse_ID, type, date, user_ID, description, arriveIn) 
                    VALUES (?, ?, ?, 'export', NOW(), ?, ?, ?)
                ");
                $descExport = "Kiszállítás cél: Raktár #$targetWh. ($description)";
                $logExport->execute([$batchId, $pId, $sourceWh, $userId, $descExport, $arriveDate]);

                // 4. Naplózás a transports táblába (IMPORT oldal)
                $logImport = $pdo->prepare("
                    INSERT INTO transports (batch_id, product_ID, warehouse_ID, type, date, user_ID, description, arriveIn) 
                    VALUES (?, ?, ?, 'import', NOW(), ?, ?, ?)
                ");
                $descImport = "Beérkezés forrás: Raktár #$sourceWh. ($description)";
                $logImport->execute([$batchId, $pId, $targetWh, $userId, $descImport, $arriveDate]);
            }

            $pdo->commit();
            
            // Siker esetén kosár ürítése
            $_SESSION['transport_cart'] = ['source_wh' => null, 'items' => []];
            $message = "A szállítás sikeresen rögzítve! Azonosító: $batchId";
            $msgType = "success";

        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "Hiba a tranzakció során: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

// --------------------------------------------------------
// ADATOK LEKÉRÉSE A MEGJELENÍTÉSHEZ
// --------------------------------------------------------
$warehouses = $pdo->query("SELECT * FROM warehouses ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Ha van kiválasztva forrás, lekérjük az ottani készletet a választóhoz
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
        <h2>🚚 Készlet Átszállítás (Admin)</h2>
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
                // Keressük meg a nevet a listából
                $sourceName = "Ismeretlen";
                foreach($warehouses as $w) { if($w['ID'] == $_SESSION['transport_cart']['source_wh']) $sourceName = $w['name']; }
            ?>
            <div style="display:flex; justify-content:space-between; align-items:center; background:#f0f9ff; padding:15px; border-radius:8px; border:1px solid #bae6fd;">
                <div>
                    <strong>Kiválasztott forrás:</strong> <br> 
                    <span style="font-size:1.2em; color:#0284c7;"><?= htmlspecialchars($sourceName) ?></span>
                </div>
                
                <form method="POST">
                    <button type="submit" name="clear_cart" class="btn btn-outline danger" onclick="return confirm('Biztosan törlöd a teljes listát és új raktárat választasz?')">
                        Módosítás / Ürítés
                    </button>
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
                    <label>Válassz terméket (csak ami készleten van):</label>
                    <select name="product_id" id="productSelect" required onchange="updateMaxQty()">
                        <option value="" data-qty="0">-- Válassz --</option>
                        <?php foreach($sourceInventory as $prod): ?>
                            <option value="<?= $prod['ID'] ?>" data-qty="<?= $prod['quantity'] ?>" data-name="<?= htmlspecialchars($prod['name']) ?>">
                                <?= htmlspecialchars($prod['name']) ?> (Cikkszám: <?= $prod['item_number'] ?>) - Készleten: <?= $prod['quantity'] ?> db
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <input type="hidden" name="product_name" id="productNameHidden">
                </div>

                <div class="field" style="margin-bottom:15px;">
                    <label>Mennyiség:</label>
                    <input type="number" name="quantity" id="qtyInput" min="1" max="1" required placeholder="0">
                    <small style="color:#666;" id="maxQtyHint">Válassz terméket a készlet megtekintéséhez.</small>
                </div>

                <button type="submit" name="add_item" class="btn btn-outline" style="width:100%;">+ Hozzáadás a szállítmányhoz</button>
            </form>
        </section>

        <section class="card" style="border: 2px solid var(--primary);">
            <div style="background:var(--primary); color:white; padding:10px; margin:-1.5rem -1.5rem 1rem -1.5rem; border-radius: 10px 10px 0 0;">
                <h3 style="margin:0;">3. Szállítmány tartalma</h3>
            </div>

            <?php if (empty($_SESSION['transport_cart']['items'])): ?>
                <div style="text-align:center; padding:20px; color:#aaa;">
                    Még nincs termék a listában.
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Termék</th>
                            <th>Mennyiség</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($_SESSION['transport_cart']['items'] as $pid => $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td style="font-weight:bold; font-size:1.1em;"><?= $item['qty'] ?> db</td>
                                <td style="text-align:right;">
                                    <a href="transports.php?remove_item=<?= $pid ?>" style="color:red;">&times; Törlés</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

    </div>

    <?php if (!empty($_SESSION['transport_cart']['items'])): ?>
        <section class="card" style="margin-top:20px; background:#f8fafc;">
            <h3>4. Hova és hogyan? (Véglegesítés)</h3>
            <form method="POST">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                    <div class="field">
                        <label>Célállomás (Hova érkezik):</label>
                        <select name="target_wh_id" required>
                            <option value="">-- Válassz célraktárat --</option>
                            <?php foreach($warehouses as $w): ?>
                                <?php if($w['ID'] != $_SESSION['transport_cart']['source_wh']): ?>
                                    <option value="<?= $w['ID'] ?>"><?= htmlspecialchars($w['name']) ?> (<?= $w['type'] ?>)</option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="field">
                        <label>Várható érkezés (Opcionális):</label>
                        <input type="date" name="arrive_date" min="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="field col-12" style="grid-column: span 2;">
                        <label>Megjegyzés / Jármű rendszám / Szállító:</label>
                        <input type="text" name="description" placeholder="Pl. Ford Transit ABC-123, Sofőr: Nagy János..." required>
                    </div>
                </div>

                <hr style="margin:20px 0; border:0; border-top:1px solid #ddd;">

                <div style="text-align:right;">
                    <button type="submit" name="finalize_transport" class="btn" style="padding:12px 24px; font-size:1.1em;">
                        🚀 Szállítás Indítása & Készletmozgatás
                    </button>
                </div>
            </form>
        </section>
    <?php endif; ?>

    <?php endif; ?>

</main>

<script>
    // Kis JS segédlet, hogy a mennyiség ne lehessen több, mint a készlet
    function updateMaxQty() {
        const select = document.getElementById('productSelect');
        const selectedOption = select.options[select.selectedIndex];
        const maxQty = selectedOption.getAttribute('data-qty');
        const prodName = selectedOption.getAttribute('data-name');
        const qtyInput = document.getElementById('qtyInput');
        const hint = document.getElementById('maxQtyHint');
        const hiddenName = document.getElementById('productNameHidden');

        if (maxQty) {
            qtyInput.max = maxQty;
            qtyInput.value = 1;
            hint.textContent = "Elérhető készlet: " + maxQty + " db";
            hiddenName.value = prodName;
        } else {
            qtyInput.removeAttribute('max');
            hint.textContent = "Válassz terméket...";
            hiddenName.value = "";
        }
    }
</script>

</body>
</html>