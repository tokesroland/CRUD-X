<?php
session_start();
include 'config.php';
require "./components/auth_check.php";
authorize(['admin','owner','user']);


$pageTitle = "Inventory";
$activePage = "inventory.php";
include './components/navbar.php';

/**
 * ---------------------------------------------
 * Dropdown: raktárak
 * ---------------------------------------------
 */
$stmtWh = $pdo->query("SELECT ID, name FROM warehouses ORDER BY name ASC");
$warehouses = $stmtWh->fetchAll(PDO::FETCH_ASSOC);

/**
 * ---------------------------------------------
 * 1. kártya szűrők: alacsony készlet
 * ---------------------------------------------
 */
$low_wh = isset($_GET['low_wh']) ? (int)$_GET['low_wh'] : 0; // 0 = összes
$low_mode = isset($_GET['low_mode']) ? $_GET['low_mode'] : 'both'; // out|min|both
if (!in_array($low_mode, ['out', 'min', 'both'], true)) $low_mode = 'both';

/**
 * ---------------------------------------------
 * 2. kártya szűrők: kiszállítás/beérkezés alatt
 * ---------------------------------------------
 */
$ship_wh = isset($_GET['ship_wh']) ? (int)$_GET['ship_wh'] : 0; // 0 = összes
$ship_q  = isset($_GET['ship_q']) ? trim($_GET['ship_q']) : '';

/**
 * ---------------------------------------------
 * 1. kártya: alacsony készlet lekérdezés
 * ---------------------------------------------
 */
$paramsLow = [];
$whereLow = [];

$sqlLow = "
    SELECT
        p.ID AS product_id,
        p.name,
        p.item_number,
        c.category_name,
        w.name AS warehouse_name,
        i.quantity,
        i.min_quantity
    FROM inventory i
    JOIN products p ON p.ID = i.product_ID
    LEFT JOIN categories c ON c.ID = p.category_ID
    JOIN warehouses w ON w.ID = i.warehouse_ID
";

if ($low_wh > 0) {
    $whereLow[] = "i.warehouse_ID = :low_wh";
    $paramsLow[':low_wh'] = $low_wh;
}

if ($low_mode === 'out') {
    $whereLow[] = "i.quantity = 0";
} elseif ($low_mode === 'min') {
    $whereLow[] = "i.quantity <= i.min_quantity";
} else { // both
    $whereLow[] = "(i.quantity = 0 OR i.quantity <= i.min_quantity)";
}

if ($whereLow) {
    $sqlLow .= " WHERE " . implode(" AND ", $whereLow);
}

$sqlLow .= " ORDER BY i.quantity ASC, p.name ASC LIMIT 20";

$stmtLow = $pdo->prepare($sqlLow);
$stmtLow->execute($paramsLow);
$lowStockRows = $stmtLow->fetchAll(PDO::FETCH_ASSOC);

/**
 * ---------------------------------------------
 * 2. kártya: várható beérkezések (transports)
 * Feltétel: type='import' és arriveIn nem null és >= ma ÉS STATUS PENDING
 * ---------------------------------------------
 */
$paramsShip = [];
$whereShip = [];

$sqlShip = "
    SELECT
        t.ID,
        t.arriveIn,
        t.date,
        t.description,
        p.name,
        p.item_number,
        w.name AS warehouse_name
    FROM transports t
    JOIN products p ON p.ID = t.product_ID
    JOIN warehouses w ON w.ID = t.warehouse_ID
    WHERE t.type = 'import'
      AND t.status = 'pending'
      AND t.arriveIn IS NOT NULL
      AND t.arriveIn >= CURDATE()
";

if ($ship_wh > 0) {
    $sqlShip .= " AND t.warehouse_ID = :ship_wh";
    $paramsShip[':ship_wh'] = $ship_wh;
}

if ($ship_q !== '') {
    // név vagy cikkszám keresés
    $sqlShip .= " AND (p.name LIKE :ship_q OR p.item_number = :ship_item)";
    $paramsShip[':ship_q'] = "%{$ship_q}%";
    $paramsShip[':ship_item'] = (int)$ship_q;
}

$sqlShip .= " ORDER BY t.arriveIn ASC, t.ID DESC LIMIT 20";

$shipRows = [];
try {
    $stmtShip = $pdo->prepare($sqlShip);
    $stmtShip->execute($paramsShip);
    $shipRows = $stmtShip->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Ha nincs még arriveIn oszlop, ne haljon el az oldal; megjelenítünk majd egy figyelmeztetést a kártyában.
    $shipRows = null;
    $shipError = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>CRUD-X – Készlet áttekintés</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="./style/inventory.css?v=<?= time() ?>">
</head>

<body>


<main class="container">

    <div class="dashboard-grid">

        <section class="card col-6">
            <div class="card-header">
                <h2><img class="icon" src="./img/danger_icon_243248.png">Alacsony készlet</h2>
            </div>

            <div class="card-body">
                <form method="get" class="card-tools">
                    <div>
                        <label>Raktár</label><br>
                        <select name="low_wh">
                            <option value="0" <?= $low_wh === 0 ? 'selected' : '' ?>>Összes</option>
                            <?php foreach ($warehouses as $w): ?>
                                <option value="<?= (int)$w['ID'] ?>" <?= $low_wh === (int)$w['ID'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($w['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label>Feltétel</label><br>
                        <select name="low_mode">
                            <option value="both" <?= $low_mode === 'both' ? 'selected' : '' ?>>0 db VAGY minimum alatt</option>
                            <option value="out"  <?= $low_mode === 'out' ? 'selected' : '' ?>>Nincs készleten (0 db)</option>
                            <option value="min"  <?= $low_mode === 'min' ? 'selected' : '' ?>>Elérte a minimumot (<= min)</option>
                        </select>
                    </div>

                    <div style="margin-top:18px;">
                        <button class="btn btn-small" type="submit">Frissítés</button>
                        <a class="btn btn-small btn-outline" href="inventory.php">Alaphelyzet</a>
                    </div>
                </form>

                <table class="mini-table">
                    <thead>
                        <tr>
                            <th>Raktár</th>
                            <th>Termék</th>
                            <th>Cikkszám</th>
                            <th>Készlet</th>
                            <th>Min</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($lowStockRows)): ?>
                            <tr><td colspan="5" style="opacity:.7;">Nincs találat a feltételre.</td></tr>
                        <?php else: ?>
                            <?php foreach ($lowStockRows as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['warehouse_name']) ?></td>
                                    <td><?= htmlspecialchars($r['name']) ?></td>
                                    <td><?= htmlspecialchars($r['item_number']) ?></td>
                                    <td><span class="pill pill-warn"><?= (int)$r['quantity'] ?> db</span></td>
                                    <td><?= (int)$r['min_quantity'] ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card col-6">
            <div class="card-header">
                <h2><img class="icon" src="./img/truck_23929.png"> Kiszállítás alatt (beérkezés várható)</h2>
            </div>

            <div class="card-body">
                <form method="get" class="card-tools">
                    <div>
                        <label>Raktárba</label><br>
                        <select name="ship_wh">
                            <option value="0" <?= $ship_wh === 0 ? 'selected' : '' ?>>Összes</option>
                            <?php foreach ($warehouses as $w): ?>
                                <option value="<?= (int)$w['ID'] ?>" <?= $ship_wh === (int)$w['ID'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($w['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label>Termék / cikkszám</label><br>
                        <input type="text" name="ship_q" value="<?= htmlspecialchars($ship_q) ?>" placeholder="pl. USB vagy 100001">
                    </div>

                    <div style="margin-top:18px;">
                        <button class="btn btn-small" type="submit">Szűrés</button>
                    </div>
                </form>

                <?php if ($shipRows === null): ?>
                    <div class="hint">Hiba: <?= htmlspecialchars($shipError ?? '') ?></div>
                <?php else: ?>
                    <table class="mini-table">
                        <thead>
                            <tr>
                                <th>Raktár</th>
                                <th>Termék</th>
                                <th>Cikkszám</th>
                                <th>Érkezés</th>
                                <th>Státusz</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($shipRows)): ?>
                                <tr><td colspan="5" style="opacity:.7;">Nincs beérkezés a szűrőkre (import + pending + arriveIn >= ma).</td></tr>
                            <?php else: ?>
                                <?php foreach ($shipRows as $t): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($t['warehouse_name']) ?></td>
                                        <td><?= htmlspecialchars($t['name']) ?></td>
                                        <td><?= htmlspecialchars($t['item_number']) ?></td>
                                        <td><strong><?= htmlspecialchars($t['arriveIn']) ?></strong></td>
                                        <td><span class="pill pill-ok">Kiszállítás alatt</span></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </section>
</main>

    <?php include './components/footer.php'; ?>

</body>
</html>