<?php
session_start();
require 'config.php';
require "./components/auth_check.php";
authorize(['admin', 'owner', 'user']);

if (!isset($_GET['batch'])) {
    die("Érvénytelen szállítási azonosító.");
}

$batchId = $_GET['batch'];
$message = "";
$msgType = "";

// Felhasználó ID és Szerepkör
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'] ?? 'user';

// ----------------------------------------------------------
// 1. ÁTVÉTEL (ELFOGADÁS) LOGIKA
// ----------------------------------------------------------
if (isset($_POST['accept_transport'])) {
    try {
        $pdo->beginTransaction();

        // Lekérjük a batch-hez tartozó PENDING IMPORT tételeket
        $stmtPending = $pdo->prepare("
            SELECT ID, product_ID, warehouse_ID, quantity 
            FROM transports 
            WHERE batch_id = ? AND type = 'import' AND status = 'pending'
        ");
        $stmtPending->execute([$batchId]);
        $pendingItems = $stmtPending->fetchAll(PDO::FETCH_ASSOC);

        if (empty($pendingItems)) {
            throw new Exception("Ez a szállítmány már át lett véve vagy nem létezik.");
        }

        // JOGOSULTSÁG ELLENŐRZÉS a user_warehouse_access tábla alapján
        if ($userRole !== 'owner') {
            // Lekérjük a user jogait
            $stmtRights = $pdo->prepare("SELECT warehouse_id FROM user_warehouse_access WHERE user_id = ?");
            $stmtRights->execute([$userId]);
            $rights = $stmtRights->fetchAll(PDO::FETCH_COLUMN);

            foreach ($pendingItems as $pi) {
                if (!in_array($pi['warehouse_ID'], $rights)) {
                    throw new Exception("Nincs jogosultságod a célraktár (" . $pi['warehouse_ID'] . ") készletének kezeléséhez.");
                }
            }
        }

        // Készletnövelés és Státusz frissítés
        foreach ($pendingItems as $pi) {
            // A. Készlet ellenőrzés/Létrehozás
            $checkInv = $pdo->prepare("SELECT ID FROM inventory WHERE product_ID = ? AND warehouse_ID = ?");
            $checkInv->execute([$pi['product_ID'], $pi['warehouse_ID']]);
            $invId = $checkInv->fetchColumn();

            if ($invId) {
                // Update
                $updInv = $pdo->prepare("UPDATE inventory SET quantity = quantity + ?, updated_at = NOW() WHERE ID = ?");
                $updInv->execute([$pi['quantity'], $invId]);
            } else {
                // Insert
                $insInv = $pdo->prepare("INSERT INTO inventory (product_ID, warehouse_ID, quantity, created_at) VALUES (?, ?, ?, NOW())");
                $insInv->execute([$pi['product_ID'], $pi['warehouse_ID'], $pi['quantity']]);
            }

            // B. Transzport státusz frissítése COMPLETED-re
            $updTrans = $pdo->prepare("UPDATE transports SET status = 'completed' WHERE ID = ?");
            $updTrans->execute([$pi['ID']]);
        }

        $pdo->commit();
        $message = "Szállítmány sikeresen átvéve! A készletek frissültek.";
        $msgType = "success";

    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Hiba: " . $e->getMessage();
        $msgType = "danger";
    }
}

// ----------------------------------------------------------
// 2. ADATLEKÉRÉS MEGJELENÍTÉSHEZ
// ----------------------------------------------------------

// Alapadatok
$stmtBase = $pdo->prepare("
    SELECT t.date, t.batch_id, t.description, t.status, t.type, u.username, w.name as wh_name, w.ID as wh_id
    FROM transports t 
    JOIN users u ON t.user_ID = u.ID 
    JOIN warehouses w ON t.warehouse_ID = w.ID
    WHERE t.batch_id = ? 
    LIMIT 1
");
$stmtBase->execute([$batchId]);
$baseInfo = $stmtBase->fetch(PDO::FETCH_ASSOC);

if (!$baseInfo) die("A szállítás nem található.");

// Tételek lekérése
$stmtItems = $pdo->prepare("
    SELECT p.name, t.quantity, t.type, t.status, w.name as warehouse_name, w.ID as warehouse_id
    FROM transports t
    JOIN products p ON t.product_ID = p.ID
    JOIN warehouses w ON t.warehouse_ID = w.ID
    WHERE t.batch_id = ?
    ORDER BY t.type DESC
");
$stmtItems->execute([$batchId]);
$items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

// Átvétel gomb feltételeinek vizsgálata
// Van-e PENDING IMPORT tétel?
$hasPendingImport = false;
$targetWarehouseId = null;

foreach ($items as $itm) {
    if ($itm['type'] === 'import' && $itm['status'] === 'pending') {
        $hasPendingImport = true;
        $targetWarehouseId = $itm['warehouse_id'];
        break; // Elég egyet találni
    }
}

// Van-e joga a usernek ehhez a raktárhoz?
$canAccept = false;
if ($hasPendingImport) {
    if ($userRole === 'owner') {
        $canAccept = true;
    } else {
        $stmtAccess = $pdo->prepare("SELECT id FROM user_warehouse_access WHERE user_id = ? AND warehouse_id = ?");
        $stmtAccess->execute([$userId, $targetWarehouseId]);
        if ($stmtAccess->fetch()) {
            $canAccept = true;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Szállítás részletei: <?= htmlspecialchars($batchId) ?></title>
    <link rel="stylesheet" href="./style/style.css">
    <link rel="stylesheet" href="./style/transport.css">
</head>
<body>
<?php require './components/navbar.php'; ?>

<main class="container">

    <?php if ($message): ?>
        <div style="margin-bottom: 20px; text-align:center;">
            <span class="badge badge-<?= $msgType === 'success' ? 'success' : 'muted' ?>" 
                  style="<?= $msgType === 'danger' ? 'background:#fee2e2; color:#b91c1c;' : '' ?>">
                <?= htmlspecialchars($message) ?>
            </span>
        </div>
    <?php endif; ?>

    <?php if ($hasPendingImport && $canAccept): ?>
        <section class="accept-section">
            <div>
                <h3 style="margin:0; color:#c2410c;">⚠️ Átvételre váró szállítmány</h3>
                <p style="margin:5px 0 0 0; font-size:0.9rem; color:#9a3412;">
                    Ez a szállítmány megérkezett a <strong><?= htmlspecialchars($items[0]['warehouse_name']) ?></strong> célállomásra (vagy te vagy a felelőse). 
                    A készlet jóváírásához kattints az átvételre.
                </p>
            </div>
            <form method="POST">
                <div style="text-align: center; margin-top: 15px;">
                    <button type="submit" name="accept_transport" class="btn" style="background:#ea580c; border:none;"> 
                        Átvétel & Készlet növelése
                    </button>
                </div>
            </form>
        </section>
    <?php endif; ?>

    <section class="card">
        <div class="card-header">
            <h2><img class="icon" src="./img/truck_23929.png"> Szállítmány: <?= htmlspecialchars($batchId) ?></h2>
            <a href="<?= $_SESSION['role'] === 'user' || $_SESSION['role'] === 'admin' ? 'index.php' : 'transports.php' ?>" class="btn btn-outline">
                Vissza
            </a>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:20px;">
            <div>
                <p><strong>Rögzítette:</strong> <?= htmlspecialchars($baseInfo['username']) ?></p>
                <p><strong>Időpont:</strong> <?= $baseInfo['date'] ?></p>
            </div>
            <div style="word-wrap: break-word; overflow-wrap: break-word; word-break: break-word; min-width: 0;">
                <p><strong>Leírás:</strong> <?= htmlspecialchars($baseInfo['description']) ?></p>
                <p>
                    <strong>Státusz:</strong> 
                        <?php if ($baseInfo['status'] === 'canceled'): ?>
                            <span class="status-badge" style="background:#fee2e2; color:#b91c1c; border:1px solid #fca5a5; padding: 4px 8px; border-radius: 4px;">
                                ❌ Visszavonva / Törölve
                            </span>
                        <?php elseif($hasPendingImport): ?>
                            <span class="status-badge status-pending">Függőben (Szállítás alatt)</span>
                        <?php else: ?>
                            <span class="status-badge status-completed">Teljesítve</span>
                        <?php endif; ?>                
                </p>
            </div>
        </div>

        <h2><img class="icon" src="./img/product_icon_238584.png"> Mozgatott tételek</h2><br>
        <p style="font-size:0.8rem; margin-bottom:10px;">Telefonos nézeten görgessen a részletekhez.</p>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Termék</th>
                    <th>Mennyiség</th>
                    <th>Típus</th>
                    <th>Státusz</th>
                    <th>Érintett raktár/üzlet</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($item['name']) ?></strong></td>
                    <td><?= $item['quantity'] ?> db</td>
                    <td>
                        <span class="badge <?= $item['type'] == 'import' ? 'badge-success' : 'badge-muted' ?>">
                            <?= $item['type'] == 'import' ? 'BEVÉTELEZÉS' : 'KIADÁS' ?>
                        </span>
                    </td>
                    <td>
                        <?php if($item['status'] == 'pending'): ?>
                            <span style="color:#d97706; font-size:0.8rem;">⏳ Függő</span>
                            
                        <?php elseif($item['status'] == 'canceled'): ?>
                            <span style="color:#dc2626; font-size:0.8rem; font-weight:bold;">
                                ❌ Visszavonva
                            </span>
                            
                        <?php else: ?>
                            <span style="color:#16a34a; font-size:0.8rem;">✔ Kész</span>
                        <?php endif; ?>
                    </td>                    
                    <td><?= htmlspecialchars($item['warehouse_name']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>

<?php require './components/footer.php'; ?>

</body>
</html>