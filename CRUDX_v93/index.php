<?php 
    session_start();
    include './config.php';
    require "./components/auth_check.php";
    authorize(['admin','owner','user']);

    // include './components/navbar.php'; 
    $pageTitle = "Dashboard";
    $activePage = "index.php";
    include './components/navbar.php'; 

    /* -------------------------------------------------------------------------- */
    /* 1. ADATLEKÉRÉSEK A STATISZTIKÁKHOZ                                         */
    /* -------------------------------------------------------------------------- */
    
    // Felső kártyák adatai
    $totalProdCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $warehouseCount = $pdo->query("SELECT COUNT(*) FROM warehouses")->fetchColumn();
    $lowStockCount = $pdo->query("SELECT COUNT(*) FROM inventory WHERE quantity <= min_quantity")->fetchColumn();

    // 2. MODUL: Raktárak és Üzletek telítettsége (Típus szerint lekérdezve)
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
        if ($cap['type'] === 'store') { $stores_list[] = $cap; } 
        else { $warehouses_list[] = $cap; }
    }

    // 1. MODUL: Mai érkezések
    $todayArrivals = $pdo->query("
        SELECT t.*, p.name as p_name, w.name as w_name
        FROM transports t
        JOIN products p ON t.product_ID = p.ID
        JOIN warehouses w ON t.warehouse_ID = w.ID
        WHERE t.type = 'import' AND t.arriveIn = CURDATE()
        ORDER BY t.ID DESC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // 3. MODUL: Legutóbbi 5 mozgás
    $recentActivity = $pdo->query("
        SELECT t.*, u.username, p.name as p_name, w.name as w_name
        FROM transports t
        JOIN users u ON t.user_ID = u.ID
        JOIN products p ON t.product_ID = p.ID
        JOIN warehouses w ON t.warehouse_ID = w.ID
        ORDER BY t.date DESC LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // 4. MODUL: Top 5 legaktívabb termék (elmúlt 30 nap)
    $topMovers = $pdo->query("
        SELECT p.name, COUNT(t.ID) as move_count
        FROM transports t
        JOIN products p ON t.product_ID = p.ID
        WHERE t.date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        GROUP BY t.product_ID
        ORDER BY move_count DESC LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Segédfüggvény a telítettségi lista kirajzolásához
    function renderCapacityList($list) {
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
    <link rel="stylesheet" href="./style/style.css">
    <style>
        /* Dashboard Layout */
        .dashboard-layout { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 20px; }
        .full-width { grid-column: span 2; }
        
        /* Progress bar */
        .progress-container { background: #e2e8f0; border-radius: 8px; height: 10px; width: 100%; margin: 5px 0; overflow: hidden; }
        .progress-bar { height: 100%; background: var(--primary); transition: width 0.3s; }
        .progress-bar.critical { background: var(--danger); }

        /* Tabs & Scroll */
        .tab-buttons { display: flex; gap: 5px; }
        .tab-btn { 
            padding: 5px 12px; border: 1px solid var(--border); background: #f1f5f9; 
            border-radius: 6px; cursor: pointer; font-size: 0.8rem; transition: 0.2s; 
        }
        .tab-btn.active { background: var(--primary); color: white; border-color: var(--primary); }
        .scrollable-stats { max-height: 300px; overflow-y: auto; padding-top: 10px; scrollbar-width: thin; }
        .scrollable-stats::-webkit-scrollbar { width: 6px; }
        .scrollable-stats::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        /* Quick actions */
        .action-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .action-btn { 
            display: flex; flex-direction: column; align-items: center; justify-content: center;
            padding: 15px; background: #f8fafc; border: 1px solid var(--border); border-radius: 12px;
            text-decoration: none; color: var(--text); transition: 0.2s; text-align: center;
        }
        .action-btn:hover { background: var(--primary-soft); border-color: var(--primary); transform: translateY(-2px); }
        .action-btn span { font-size: 1.5rem; margin-bottom: 5px; }

        /* Timeline */
        .timeline-item { padding: 10px 0; border-left: 2px solid var(--border); padding-left: 15px; position: relative; margin-left: 10px; }
        .timeline-item::before { content: ""; position: absolute; left: -7px; top: 15px; width: 12px; height: 12px; background: white; border: 2px solid var(--primary); border-radius: 50%; }
        
        @media (max-width: 900px) { .dashboard-layout { grid-template-columns: 1fr; } .full-width { grid-column: span 1; } }
    </style>
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
            <div class="stat-sub">Azonnali utánpótlás kell</div>
        </div>
    </section>

    <div class="dashboard-layout">
        
        <section class="card">
            <div class="card-header">
                <h2>📊 Telítettség</h2>
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
            <div class="card-header"><h2>⚡ Gyorsműveletek</h2></div>
            <div class="action-grid">
                <a href="admin.php" class="action-btn"><span>➕</span> Új termék</a>
                <a href="transports.php" class="action-btn"><span>🚚</span> Átszállítás</a>
                <a href="inventory.php" class="action-btn"><span>📦</span> Készlet</a>
                <a href="reports.php" class="action-btn"><span>📈</span> Riportok</a>
            </div>
        </section>

        <section class="card full-width">
            <div class="card-header"><h2>📅 Ma érkező áruk</h2></div>
            <?php if (empty($todayArrivals)): ?>
                <p style="text-align:center; padding: 20px; color: #94a3b8;">Mára nincs ütemezett beérkezés.</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Batch ID</th>
                            <th>Termék</th>
                            <th>Célállomás</th>
                            <th>Megjegyzés</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($todayArrivals as $arr): ?>
                            <tr>
                                <td><code style="background:#f1f5f9;padding:3px 6px;border-radius:4px;">#<?= $arr['batch_id'] ?></code></td>
                                <td><strong><?= htmlspecialchars($arr['p_name']) ?></strong></td>
                                <td><?= htmlspecialchars($arr['w_name']) ?></td>
                                <td><?= htmlspecialchars($arr['description']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>

        <section class="card">
            <div class="card-header"><h2>🕒 Utolsó mozgások</h2></div>
            <div style="margin-top: 10px;">
                <?php foreach($recentActivity as $act): ?>
                    <div class="timeline-item">
                        <div style="font-size: 0.75rem; color: #64748b;"><?= date('H:i', strtotime($act['date'])) ?> - <?= date('Y.m.d', strtotime($act['date'])) ?></div>
                        <div style="font-size: 0.9rem;">
                            <strong><?= htmlspecialchars($act['username']) ?></strong> 
                            <span style="color: <?= $act['type']=='import' ? '#16a34a' : '#dc2626' ?>;">
                                <?= $act['type'] == 'import' ? 'bevételezett' : 'kiadott' ?>
                            </span> 
                            <strong><?= htmlspecialchars($act['p_name']) ?></strong> 
                            (<?= htmlspecialchars($act['w_name']) ?>)
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="card">
            <div class="card-header"><h2>🔥 Legaktívabb (30 nap)</h2></div>
            <div style="margin-top: 10px;">
                <?php foreach($topMovers as $mover): ?>
                    <div style="display:flex; justify-content:space-between; padding: 12px 0; border-bottom: 1px solid #f1f5f9;">
                        <span style="font-size: 0.9rem;"><?= htmlspecialchars($mover['name']) ?></span>
                        <span class="badge badge-success" style="font-size: 0.75rem;"><?= $mover['move_count'] ?> esemény</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

    </div>
</main>

<?php include './components/footer.php'; ?>

<script>
    // Tab kezelő funkció
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