<?php
session_start();
require 'config.php';
require "./components/auth_check.php";
authorize(['admin', 'owner']); // Csak admin és tulajdonos indíthat szállítást

$pageTitle = "Szállítás";
$activePage = "transports.php";

// initialize message variables early so backend can set them before any output
$message = "";
$msgType = "";

// --------------------------------------------------------
// SEGÉDFÜGGVÉNYEK ÉS JOGOSULTSÁGOK

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
    $stmtWh = $pdo->query("SELECT ID, name, type FROM warehouses WHERE active = 1 ORDER BY name ASC");
    $allowedWarehouses = $stmtWh->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Admin/User: Csak a hozzárendelteket látja a kapcsolótáblából
    $stmtWh = $pdo->prepare("
        SELECT w.ID, w.name, w.type 
        FROM warehouses w
        JOIN user_warehouse_access uwa ON w.ID = uwa.warehouse_id
        WHERE uwa.user_id = ? AND w.active = 1
        ORDER BY w.name ASC
    ");
    $stmtWh->execute([$userId]);
    $allowedWarehouses = $stmtWh->fetchAll(PDO::FETCH_ASSOC);
}

$allowedWarehouseIds = array_column($allowedWarehouses, 'ID');

// transports.php-hoz kapcsolódó kosár logika és műveletek
// Include backend BEFORE any HTML output (navbar) so header redirects work
require './components/transports_backend.php'; 

// Now include the navbar (outputs HTML)
require './components/navbar.php'; 

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

        <section class="card pending-card" style="margin-bottom: 30px;">
            <div class="card-header">
                <h2><img class="icon" src="./img/1485477075-calendar_78587.png"> Beérkezésre váró szállítmányok (Átvétel szükséges)</h2>
            </div>

            <div class="filter">
                <form id="filterForm" style="display:flex; gap:10px; margin-bottom:15px; flex-wrap:wrap;">
                    <div class="field" style="flex:1; min-width:200px;">
                        <input type="text" id="batchFilter" placeholder="Batch ID keresése..." style="width:100%;">
                    </div>
                    <div class="field" style="flex:1; min-width:200px;">
                        <input type="date" id="dateFilter" style="width:100%;">
                    </div>
                    <button type="button" onclick="resetFilters()" class="btn btn-primary" style="align-self:flex-end; background:grey">Szűrés törlése</button>
                </form>

                <script src="./script/transports_filter.js"></script>
                </script>
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
                        <?php if (!empty($pendingTransports)) foreach($pendingTransports as $pt): ?>
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
                                    <a href="transport.php?batch=<?= $pt['batch_id'] ?>" class="btn btn-small btn-primary" style="color:white !important;">Megtekintés / Átvétel</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                        <?php if (empty($pendingTransports)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center; color:#aaa;">Nincs beérkezésre váró szállítmány.</td>
                            </tr>
                        <?php endif; ?>
                </table>
            </div>
        </section>

        <?php if (!empty($myOutgoingTransports)): ?>
        <section class="card" style="margin-bottom: 30px; border-left: 5px solid #f59e0b;">
            <div class="card-header">
                <h2><img class="icon" src="./img/danger_icon_243248.png">Függő szállításaim</h2>
            </div>
            
            <p style="padding: 0 15px; color: #666; font-size: 0.9em;">
                Ezeket a szállításokat még nem vette át a címzett. Szükség esetén visszavonhatod őket, ekkor a készlet azonnal visszakerül a te raktáradba.
            </p>

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Batch ID</th>
                            <th>Célraktár</th>
                            <th>Indítva</th>
                            <th>Tételek</th>
                            <th style="text-align: right;">Művelet</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($myOutgoingTransports as $mo): ?>
                            <tr>
                                <td>
                                    <a href="transport.php?batch=<?= $mo['batch_id'] ?>" class="batch-link">
                                        <?= htmlspecialchars($mo['batch_id']) ?>
                                    </a>
                                </td>
                                <td><?= htmlspecialchars($mo['target_wh_name']) ?></td>
                                <td><?= date('Y.m.d H:i', strtotime($mo['date'])) ?></td>
                                <td><?= $mo['item_count'] ?> db</td>
                                <td style="text-align: right;">
                                    <form method="POST" onsubmit="return confirm('BIZTOSAN visszavonod? A termékek visszakerülnek az indító raktár készletébe!');">
                                        <input type="hidden" name="cancel_batch_id" value="<?= $mo['batch_id'] ?>">
                                        <button type="submit" name="cancel_batch" class="btn btn-small btn-outline danger">
                                            ✖ Visszavonás
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
        <?php endif; ?>
        
        <hr style="margin: 30px 0; border: 0; border-top: 1px solid #dde1e7;">

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
            <form method="POST" style="gap:10px; align-items:end;">
                <div class="field" style="flex:1;">
                    <label>Válassz indító raktárat (Csak saját):</label>
                    <select name="source_wh_id" required>
                        <option value="">-- Válassz --</option>
                        <?php foreach($allowedWarehouses as $w): ?>
                            <option value="<?= $w['ID'] ?>"><?= htmlspecialchars($w['name']) ?> (<?= $w['type'] == 'store' ? 'Bolt' : 'Raktár' ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <br>
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
                        <input type="date" min="<?= date('Y-m-d') ?>" name="arrive_date" id="arriveDate"
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