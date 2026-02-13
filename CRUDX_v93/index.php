<?php
session_start();
include './config.php';
require "./components/auth_check.php";
authorize(['admin', 'owner', 'user']);

$pageTitle = "Dashboard";
$activePage = "index.php";
include './components/navbar.php';

/* -------------------------------------------------------------------------- */
/* 1. JOGOSULTSÁGOK ÉS RAKTÁRAK LEKÉRÉSE                                    */

$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'] ?? 'user';
$isRestricted = ($userRole !== 'owner'); // Owner mindent lát, többiek csak a sajátot

// Lekérjük az engedélyezett raktár ID-kat a kapcsolótáblából
$allowedWarehouseIds = [];
if ($isRestricted) {
    $stmtAccess = $pdo->prepare("SELECT warehouse_id FROM user_warehouse_access WHERE user_id = ?");
    $stmtAccess->execute([$userId]);
    $allowedWarehouseIds = $stmtAccess->fetchAll(PDO::FETCH_COLUMN);
}

/* -------------------------------------------------------------------------- */
/* 2. ADATLEKÉRÉSEK A STATISZTIKÁKHOZ                                         */

// Felső kártyák adatai
$totalProdCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$warehouseCount = $pdo->query("SELECT COUNT(*) FROM warehouses")->fetchColumn();

// Alacsony készlet: Szűrés, ha van raktár jogosultság és nem owner
$lowStockCount = 0;
if ($isRestricted && !empty($allowedWarehouseIds)) {
    // IN clause generálás
    $inQuery = implode(',', array_map('intval', $allowedWarehouseIds));
    $lowStockCount = $pdo->query("SELECT COUNT(*) FROM inventory WHERE quantity <= min_quantity AND warehouse_ID IN ($inQuery)")->fetchColumn();
} elseif (!$isRestricted) {
    // Owner mindent lát
    $lowStockCount = $pdo->query("SELECT COUNT(*) FROM inventory WHERE quantity <= min_quantity")->fetchColumn();
}

// Telítettség (Változatlan, csak megjelenítés)
$capacities = $pdo->query("
        SELECT w.name, w.max_quantity, w.type, COALESCE(SUM(i.quantity), 0) as current_qty
        FROM warehouses w
        LEFT JOIN inventory i ON w.ID = i.warehouse_ID
        GROUP BY w.ID
        ORDER BY w.name ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

$warehouses_list = [];
$stores_list = [];
foreach ($capacities as $cap) {
    if ($cap['type'] === 'store') {
        $stores_list[] = $cap;
    } else {
        $warehouses_list[] = $cap;
    }
}

// MAI ÉRKEZÉSEK (SZŰRTEN: Csak pending, Csak mai, Csak saját raktár)
// ------------------------------------------------------------------
$sqlArrivals = "
        SELECT t.batch_id, MAX(w.name) as w_name, MAX(t.arriveIn) as arrive_date
        FROM transports t
        JOIN warehouses w ON t.warehouse_ID = w.ID
        WHERE t.type = 'import' 
          AND t.arriveIn = CURDATE()
          AND t.status = 'pending'
    ";

// Ha korlátozott nézet, csak a saját raktárak érkezéseit mutassuk
if ($isRestricted) {
    if (!empty($allowedWarehouseIds)) {
        $inQuery = implode(',', array_map('intval', $allowedWarehouseIds));
        $sqlArrivals .= " AND t.warehouse_ID IN ($inQuery)";
    } else {
        $sqlArrivals .= " AND 1=0"; // Ha nincs joga semmihez, ne lásson semmit
    }
}

$sqlArrivals .= " GROUP BY t.batch_id ORDER BY t.ID DESC";

$todayArrivals = $pdo->query($sqlArrivals)->fetchAll(PDO::FETCH_ASSOC);

// Utolsó mozgások és Késésben lévő szállítások.
$recentActivity = $pdo->query("
        SELECT t.*, u.username, p.name as p_name, w.name as w_name
        FROM transports t
        JOIN users u ON t.user_ID = u.ID
        JOIN products p ON t.product_ID = p.ID
        JOIN warehouses w ON t.warehouse_ID = w.ID
        ORDER BY t.date DESC LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

// KÉSÉSBEN LÉVŐ SZÁLLÍTMÁNYOK (Overdue)
// ------------------------------------------------------------------

$sqlOverdue = "
        SELECT 
            t.batch_id, 
            t.arriveIn, 
            t.date,
            w_source.name as source_wh_name,
            COUNT(t.ID) as item_count
        FROM transports t
        -- Megkeressük a szállítmány párját (export), hogy tudjuk honnan jön
        LEFT JOIN transports t_export ON t.batch_id = t_export.batch_id AND t_export.type = 'export'
        LEFT JOIN warehouses w_source ON t_export.warehouse_ID = w_source.ID
        WHERE t.type = 'import' 
        AND t.status = 'pending'
        AND t.arriveIn < CURDATE()
    ";

// Jogosultság szűrés: Csak azokat látom, amik az ÉN raktáraimba (target) érkeznek
if ($isRestricted) {
    if (!empty($allowedWarehouseIds)) {
        $inQuery = implode(',', array_map('intval', $allowedWarehouseIds));
        // Importnál a warehouse_ID a CÉL raktár
        $sqlOverdue .= " AND t.warehouse_ID IN ($inQuery)";
    } else {
        // Ha nincs raktára, ne lásson semmit
        $sqlOverdue .= " AND 1=0";
    }
}

$sqlOverdue .= " GROUP BY t.batch_id ORDER BY t.arriveIn ASC LIMIT 5";

$overdueTransports = $pdo->query($sqlOverdue)->fetchAll(PDO::FETCH_ASSOC);
// Segédfüggvény
function renderCapacityList($list)
{
    if (empty($list)) {
        echo '<p style="text-align:center; color:#aaa; padding:20px;">Nincs megjeleníthető adat.</p>';
        return;
    }
    foreach ($list as $cap):
        $percent = $cap['max_quantity'] > 0 ? round(($cap['current_qty'] / $cap['max_quantity']) * 100) : 0;
        $isCritical = $percent >= 90;
?>
        <div style="margin-bottom: 15px; padding-right: 10px;">
            <div style="display:flex; justify-content:space-between; font-size: 0.85rem;">
                <span><?= htmlspecialchars($cap['name']) ?></span>
                <span style="font-weight:bold;"><?= $percent ?>%</span>
            </div>
            <div class="progress-container">
                <div class="progress-bar <?= $isCritical ? 'critical' : '' ?>" style="width: <?= min($percent, 100) ?>%"></div>
            </div>
            <small style="color: #666; font-size: 0.75rem;">
                <?= number_format($cap['current_qty'], 0, '.', ' ') ?> / <?= number_format($cap['max_quantity'], 0, '.', ' ') ?> db
            </small>
        </div>
<?php endforeach;
}
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <title>CRUD-X Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css?v=1.0">
    <link rel="stylesheet" href="./style/index.css?v=1.0">
</head>

<body>

    <main class="container">
        <section class="stats-grid">
            <div class="card stat-card">
                <div class="stat-label">Összes termék</div>
                <div class="stat-value"><?= $totalProdCount ?></div>
                <div class="stat-sub">Regisztrált típusok</div>
            </div>
            <div class="card stat-card">
                <div class="stat-label">Aktív raktárak</div>
                <div class="stat-value"><?= $warehouseCount ?></div>
                <div class="stat-sub">Összes logisztikai pont</div>
            </div>
            <div class="card stat-card <?= $lowStockCount > 0 ? 'critical' : '' ?>">
                <div class="stat-label">Alacsony készlet</div>
                <div class="stat-value"><?= $lowStockCount ?></div>
                <div class="stat-sub">
                    <?= ($isRestricted) ? 'Saját egységekben' : 'Azonnali utánpótlás kell' ?>
                </div>
            </div>
        </section>

        <div class="dashboard-layout">

            <?php if (in_array($userRole, ['admin', 'owner'])): ?>
                <section class="card">
                    <div class="card-header">
                        <h2><img class="icon" src="./img/1485477213-statistics_78572.png"> Telítettség</h2>
                        <div class="tab-buttons">
                            <button class="tab-btn active" onclick="showTab('warehouses', this)">Raktárak</button>
                            <button class="tab-btn" onclick="showTab('stores', this)">Üzletek</button>
                        </div>
                    </div>
                    <div class="scrollable-stats" id="warehouses-tab">
                        <?php renderCapacityList($warehouses_list); ?>
                    </div>
                    <div class="scrollable-stats" id="stores-tab" style="display: none;">
                        <?php renderCapacityList($stores_list); ?>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header">
                        <h2><img class="icon" src="./img/lightning_icon_187922.png"> Gyorsműveletek</h2>
                    </div>
                    <div class="action-grid">
                        <a href="admin.php" class="action-btn"><span><img class="icon" src="./img/create_new_plus_add_icon_232794.png"></span> Új termék</a>
                        <a href="transports.php" class="action-btn"><span><img class="icon" src="./img/truck_23929.png"></span> Átszállítás</a>
                        <a href="inventory.php" class="action-btn"><span><img class="icon" src="./img/products_box.png"></span> Készlet</a>
                        <a href="reports.php" class="action-btn"><span><img class="icon" src="./img/1485477213-statistics_78572.png"></span> Riportok</a>
                        <a href="products.php" class="action-btn"><span><img class="icon" src="./img/category_icon_241610.png"></span> Termékek</a>
                        <a href="owner.php" class="action-btn"><span><img class="icon" src="./img/create-group-button_icon-icons.com_72792.png"></span> Rendszer</a>
                    </div>
                </section>
            <?php endif; ?>

            <section class="card full-width">
                <div class="card-body">
                    <h2>
                        <img class="icon" src="./img/1485477075-calendar_78587.png">Ma érkező áruk
                        <?php if ($isRestricted): ?>
                            <small style="font-weight:normal; font-size:0.7em; color:#666;">(Csak saját, átvételre vár)</small>
                        <?php endif; ?>
                    </h2>
                    <br>
                </div>

                <?php if (empty($todayArrivals)): ?>
                    <p style="text-align:center; padding: 20px; color: #94a3b8;">Mára nincs függő beérkezés.</p>
                <?php else: ?>

                    <div class="table-scroll-container">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 40%;">Batch ID</th>
                                    <th>Érkezés dátuma</th>
                                    <th>Célállomás</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($todayArrivals as $arr): ?>
                                    <tr>
                                        <td class="batch-id-cell" onclick="location.href='transport.php?batch=<?= $arr['batch_id'] ?>'">
                                            <a href="transport.php?batch=<?= $arr['batch_id'] ?>"><?= htmlspecialchars($arr['batch_id']) ?></a>
                                        </td>
                                        <td><?= htmlspecialchars($arr['arrive_date']) ?></td>
                                        <td><?= htmlspecialchars($arr['w_name']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <section class="card">
                <div class="card-header">
                    <h2><img class="icon" src="./img/clock_80424.png"> Utolsó mozgások</h2>
                </div>
                <div style="margin-top: 10px;">
                    <?php foreach ($recentActivity as $act): ?>
                        <div class="timeline-item">
                            <div style="font-size: 0.75rem; color: #64748b;"><?= date('H:i', strtotime($act['date'])) ?> - <?= date('Y.m.d', strtotime($act['date'])) ?></div>
                            <div style="font-size: 0.9rem;">
                                <strong><?= htmlspecialchars($act['username']) ?></strong>
                                <span style="color: <?= $act['type'] == 'import' ? '#16a34a' : '#dc2626' ?>;">
                                    <?= $act['type'] == 'import' ? 'bevételezett' : 'kiadott' ?>
                                </span>
                                <strong><?= htmlspecialchars($act['p_name']) ?></strong>
                                (<?= htmlspecialchars($act['w_name']) ?>)
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="card" style="border-left: 5px solid #ef4444;">
                <div class="card-header">
                    <h2>
                        <img class="icon" src="./img/danger_icon_243248.png" onerror="this.src='./img/document_23966.png'">
                        Késésben lévő érkezések
                    </h2>
                </div>

                <?php if (empty($overdueTransports)): ?>
                    <div style="text-align:center; padding: 30px; color: #16a34a;">
                        <span style="font-size: 1.5rem; display:block; margin-bottom:5px;">✓</span>
                        Minden szállítmány időben!
                    </div>
                <?php else: ?>
                    <div style="margin-top: 10px;">
                        <?php foreach ($overdueTransports as $ot): ?>
                            <?php
                            // Késés kiszámítása napokban
                            $targetDate = !empty($ot['arriveIn']) ? $ot['arriveIn'] : date('Y-m-d', strtotime($ot['date']));
                            // Időbélyegek különbsége másodpercben, osztva egy nap hosszával
                            $diffSeconds = time() - strtotime($targetDate);
                            $daysLate = floor($diffSeconds / (60 * 60 * 24));

                            // Ha valamiért negatív lenne (pl. ma van), akkor legalább 1-et írjunk, ha már bekerült a listába
                            if ($daysLate < 1) $daysLate = 1;
                            ?>
                            <div style="padding: 12px 0; border-bottom: 1px solid #fee2e2;">
                                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                                    <a href="transport.php?batch=<?= htmlspecialchars($ot['batch_id']) ?>"
                                        style="font-weight:bold; color:#b91c1c; text-decoration:none;">
                                        <?= htmlspecialchars($ot['batch_id']) ?>
                                    </a>
                                    <span class="badge" style="background:#fecaca; color:#991b1b; font-size:0.75rem;">
                                        -<?= $daysLate ?> nap
                                    </span>
                                </div>

                                <div style="display:flex; justify-content: space-between; font-size: 0.8rem; color: #64748b;">
                                    <span>Honnan: <strong><?= htmlspecialchars($ot['source_wh_name'] ?? 'Külső forrás') ?></strong></span>
                                    <span><?= $ot['item_count'] ?> tétel</span>
                                </div>

                                <div style="font-size: 0.75rem; color: #ef4444; margin-top:2px;">
                                    Várt érkezés: <?= date('Y.m.d', strtotime($targetDate)) ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </main>

    <?php include './components/footer.php'; ?>

    <script>
        function showTab(tabId, btn) {
            document.getElementById('warehouses-tab').style.display = 'none';
            document.getElementById('stores-tab').style.display = 'none';
            document.getElementById(tabId + '-tab').style.display = 'block';
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        }
    </script>
</body>

</html>