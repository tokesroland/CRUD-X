<?php
session_start();
require 'config.php';
require "./components/auth_check.php";
authorize(['admin', 'owner']);

$pageTitle = "Jelentések";
$activePage = "reports.php";
include './components/navbar.php';
include './components/reports_filter.php'; 

// --- SZŰRŐK PARAMÉTEREINEK BEOLVASÁSA ---
$range = isset($_GET['range']) ? (int)$_GET['range'] : 30;
$wh_filter = (isset($_GET['wh']) && $_GET['wh'] !== 'all') ? (int)$_GET['wh'] : 'all';

/*
|--------------------------------------------------------------------------
| 1. ADATLEKÉRÉSEK (ALAP STATISZTIKÁK)
|--------------------------------------------------------------------------
*/

// Raktár Terheltség adatok
$stmt = $pdo->query("
    SELECT w.ID, w.name, w.max_quantity, COALESCE(SUM(i.quantity), 0) AS current_quantity
    FROM warehouses w
    LEFT JOIN inventory i ON i.warehouse_ID = w.ID
    GROUP BY w.ID ORDER BY w.name
");
$warehouseStats = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalItems = 0;
$criticalWarehouses = 0;
foreach ($warehouseStats as &$w) {
    $percent = $w['max_quantity'] > 0 ? ($w['current_quantity'] / $w['max_quantity']) * 100 : 0;
    $w['is_critical'] = $percent >= 80;
    if ($w['is_critical'])
        $criticalWarehouses++;
    $totalItems += $w['current_quantity'];
}

// Készlet vs Minimum adatok
// SQL: Csak a kritikus készletek (készlet <= minimum)
$stmt = $pdo->query("
    SELECT p.name AS product_name, w.ID AS warehouse_id, w.name AS warehouse_name, i.quantity, i.min_quantity
    FROM inventory i
    JOIN products p ON p.ID = i.product_ID
    JOIN warehouses w ON w.ID = i.warehouse_ID
    WHERE i.quantity <= i.min_quantity  -- Ez szűri ki a felesleget
    ORDER BY i.quantity ASC
");
$criticalData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$lowStockCount = 0;
foreach ($criticalData as $item) {
    if ($item['quantity'] <= $item['min_quantity'])
        $lowStockCount++;
}

// Kategória és Státusz statisztikák
$statusStats = $pdo->query("SELECT active, COUNT(*) as count FROM products GROUP BY active")->fetchAll(PDO::FETCH_ASSOC);
$totalProductCount = array_sum(array_column($statusStats, 'count'));


/*
|--------------------------------------------------------------------------
| 2. IDŐSZAKOS FORGALOM (JAVÍTOTT SZŰRÉSSEL)
|--------------------------------------------------------------------------
*/
$timeline_sql = "
    SELECT 
        DATE(date) as move_date,
        SUM(CASE WHEN type = 'import' THEN quantity ELSE 0 END) as total_in,
        SUM(CASE WHEN type = 'export' THEN quantity ELSE 0 END) as total_out
    FROM transports
    WHERE date >= DATE_SUB(NOW(), INTERVAL ? DAY)
";

$timeline_params = [$range];

// Ha van raktár szűrő, hozzáadjuk a feltételt
if ($wh_filter !== 'all') {
    $timeline_sql .= " AND warehouse_ID = ?";
    $timeline_params[] = $wh_filter;
}

$timeline_sql .= " GROUP BY DATE(date) ORDER BY move_date ASC";

$stmt = $pdo->prepare($timeline_sql);
$stmt->execute($timeline_params);
$timelineData = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| 3. ELFEKVŐ KÉSZLETEK (JAVÍTOTT: JOIN ÉS SZŰRÉS)
|--------------------------------------------------------------------------
*/
// Itt volt a hiba: hiányzott a raktár nevének lekérése (w.name) és a szűrés
$dead_sql = "
    SELECT p.name, i.quantity, MAX(t.date) as last_move, w.name as warehouse_name
    FROM inventory i
    JOIN products p ON i.product_ID = p.ID
    JOIN warehouses w ON i.warehouse_ID = w.ID
    LEFT JOIN transports t ON (t.product_ID = p.ID AND t.warehouse_ID = i.warehouse_ID)
    WHERE i.quantity > 0
";

if ($wh_filter !== 'all') {
    $dead_sql .= " AND i.warehouse_ID = " . (int)$wh_filter;
}

$dead_sql .= " GROUP BY p.ID, i.warehouse_ID
               HAVING (last_move < DATE_SUB(NOW(), INTERVAL 30 DAY) OR last_move IS NULL) 
               ORDER BY i.quantity DESC LIMIT 10";

$deadStock = $pdo->query($dead_sql)->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <title>Jelentések | CRUD-X</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <style>
        /* Központi szűrő sáv elrendezése */
        .filter-bar {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            align-items: flex-end;
            padding: 0 20px 20px 20px;
        }

        .filter-item {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex: 0 1 auto;
            min-width: 200px;
        }

        .filter-item label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
        }

        .filter-item select {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background-color: white;
            font-size: 0.9rem;
            cursor: pointer;
            width: 100%;
        }

        /* Mobil nézet a szűrőhöz */
        @media (max-width: 900px) {
            .filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
            .filter-item {
                width: 100%;
            }
        }
        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
        }

        .filter-scroll-container {
            display: flex;
            gap: 8px;
            overflow-x: auto;
            padding: 10px 0;
            scrollbar-width: thin;
        }

        .filter-scroll-container::-webkit-scrollbar {
            height: 6px;
        }

        .filter-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .toggle-chip {
            white-space: nowrap;
            cursor: pointer;
            padding: 6px 14px;
            border-radius: 20px;
            background: #f1f5f9;
            color: #475569;
            font-size: 0.8rem;
            border: 1px solid #e2e8f0;
            transition: 0.2s;
            user-select: none;
        }

        .toggle-chip:hover {
            background: #e2e8f0;
        }

        .toggle-chip.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .toggle-chip.critical.active {
            background: var(--danger);
            border-color: var(--danger);
        }

        .filter-group {
            background: #f8fafc;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 10px;
            border: 1px solid #e2e8f0;
        }

        .checkbox-label {
            font-size: 0.9rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
            font-weight: 500;
            color: #dc2626;
        }
        
    </style>
</head>

<body>

    <main class="container">

        <div class="stats-grid">
            <div class="card stat-card">
                <span class="stat-label">Összes készlet</span>
                <span class="stat-value"><?= number_format($totalItems, 0, ',', ' ') ?> db</span>
            </div>
            <div class="card stat-card <?= $lowStockCount > 0 ? 'critical' : '' ?>">
                <span class="stat-label">Alacsony készlet</span>
                <span class="stat-value"><?= $lowStockCount ?> db</span>
                <span class="stat-sub">Minimum szint alatt</span>
            </div>
            <div class="card stat-card <?= $criticalWarehouses > 0 ? 'critical' : '' ?>">
                <span class="stat-label">Kritikus raktár</span>
                <span class="stat-value"><?= $criticalWarehouses ?> db</span>
            </div>
        </div>

        <section class="card">
            <div class="card-header">
                <h2><img class="icon" src="./img/category_icon_241610.png"> Jelentések Szűrése</h2>
            </div>
            <form method="GET" class="filter-bar" style="display:flex; gap:15px; background:none; border:none; padding:0 20px 20px 20px; flex-wrap: wrap;">
                <div class="filter-item">
                    <label>Raktár kiválasztása:</label>
                    <select name="wh" onchange="this.form.submit()">
                        <option value="all" <?= $wh_filter === 'all' ? 'selected' : '' ?>>Összes raktár (Globális)</option>
                        <?php foreach ($warehouseStats as $w): ?>
                            <option value="<?= $w['ID'] ?>" <?= (int)$wh_filter === (int)$w['ID'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($w['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-item">
                    <label>Időszak:</label>
                    <select name="range" onchange="this.form.submit()">
                        <option value="7" <?= $range == 7 ? 'selected' : '' ?>>Utolsó 7 nap</option>
                        <option value="30" <?= $range == 30 ? 'selected' : '' ?>>Utolsó 30 nap</option>
                        <option value="90" <?= $range == 90 ? 'selected' : '' ?>>Utolsó 90 nap</option>
                    </select>
                </div>
                <div class="filter-item" style="display:flex; align-items:flex-end;">
                     <a href="reports.php" class="btn btn-outline" style="padding: 8px 15px; font-size:0.9rem;">Szűrők törlése</a>
                </div>
            </form>
        </section>

        <section class="card">
            <div class="card-header">
                <h2><img class="icon" src="./img/1485477213-statistics_78572.png"> Forgalmi Idővonal</h2>
            </div>
            <div class="chart-container">
                <canvas id="timelineChart"></canvas>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2>⚠️ Elfekvő készletek (Inaktív > 30 nap)</h2>
            </div>
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Termék</th>
                            <th>Raktár</th>
                            <th>Készlet</th>
                            <th>Utolsó mozgás</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($deadStock)): ?>
                            <tr><td colspan="4" style="text-align:center;">Nincs elfekvő készlet a szűrés alapján.</td></tr>
                        <?php else: ?>
                            <?php foreach ($deadStock as $ds): ?>
                            <tr>
                                <td><?= htmlspecialchars($ds['name']) ?></td>
                                <td><small><?= htmlspecialchars($ds['warehouse_name']) ?></small></td>
                                <td><strong><?= $ds['quantity'] ?> db</strong></td>
                                <td><span class="badge badge-muted"><?= $ds['last_move'] ? date('Y.m.d', strtotime($ds['last_move'])) : 'Soha nem mozdult' ?></span></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card">
            <div class="card-header">
                <h2><img class="icon" src="./img/1485477213-statistics_78572.png"> Raktár Kapacitás Kihasználtság</h2>
            </div>
            <div class="filter-group">
                <div class="filter-scroll-container">
                    <?php foreach ($warehouseStats as $index => $w): ?>
                        <div class="toggle-chip capacity-filter <?= $w['is_critical'] ? 'critical' : '' ?>"
                            data-index="<?= $index ?>" onclick="toggleFilter(this, 'cap')">
                            <?= htmlspecialchars($w['name']) ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="chart-container"><canvas id="capacityChart"></canvas></div>
        </section>

<section class="card">
    <div class="card-header">
        <h2><img class="icon" src="./img/product_icon_238584.png">Kritikus készletek</h2>
    </div>
    
    <div class="filter-group">
        <div class="filter-scroll-container">
            <div class="toggle-chip active stock-wh-filter" data-id="all" onclick="filterByWarehouse(this)">
                Összes raktár</div>
            <?php foreach ($warehouseStats as $w): ?>
                <div class="toggle-chip stock-wh-filter" data-id="<?= $w['ID'] ?>" onclick="filterByWarehouse(this)">
                    <?= htmlspecialchars($w['name']) ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div style="overflow-x: auto; width: 100%; -webkit-overflow-scrolling: touch;">
        <table class="data-table" style="width: 100%; min-width: 500px; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left;">
                    <th style="padding: 12px">Termék</th>
                    <th style="padding: 12px">Raktár</th>
                    <th style="padding: 12px">Készlet</th>
                    <th style="padding: 12px">Minimum</th>
                </tr>
            </thead>
            <tbody id="criticalTableBody">
                </tbody>
        </table>
    </div>
</section>
        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));">
            <section class="card">
                <div class="card-header">
                    <h2><img class="icon" src="./img/status_icon_241869.png"> Termék státuszok</h2>
                </div>
                <div class="chart-container"><canvas id="statusChart"></canvas></div>
                <i style="font-size: 0.8rem;">* < 3% nem jelenik meg.</i>
            </section>
        </div>

    </main>

    <?php include './components/footer.php'; ?>

<script>
    // Plugin regisztrálása
    Chart.register(ChartDataLabels);
    Chart.defaults.font.family = "system-ui, sans-serif";

    // ADATOK ÁTVÉTELE PHP-BŐL
    const warehouseData = <?= json_encode($warehouseStats) ?>;
    const timelineRaw = <?= json_encode($timelineData) ?>;
    // Itt volt a hiba: a stockData már nem létezik, helyette a criticalData kell a táblázathoz
    const criticalStockData = <?= json_encode($criticalData) ?>;

    // Globális változók a grafikonoknak
    let capChart;

    // --- 1. TIMELINE CHART (FORGALMI IDŐVONAL) ---
    if (window.myTimelineChart) window.myTimelineChart.destroy();

    const ctxTimeline = document.getElementById('timelineChart');
    if (ctxTimeline) {
        window.myTimelineChart = new Chart(ctxTimeline, {
            type: 'line',
            data: {
                labels: timelineRaw.map(d => d.move_date),
                datasets: [{
                        label: 'Bevételezés',
                        data: timelineRaw.map(d => d.total_in),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.3
                    },
                    {
                        label: 'Kiadás',
                        data: timelineRaw.map(d => d.total_out),
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.3
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    datalabels: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // --- 2. RAKTÁR KAPACITÁS CHART ---
    function renderCap() {
        const chartCanvas = document.getElementById('capacityChart');
        if (!chartCanvas) return; // Ha nincs ott az elem, ne fusson

        const activeIdx = Array.from(document.querySelectorAll('.capacity-filter.active')).map(el => parseInt(el.dataset.index));
        const labels = activeIdx.map(i => warehouseData[i].name);
        const current = activeIdx.map(i => warehouseData[i].current_quantity);
        const max = activeIdx.map(i => warehouseData[i].max_quantity);
        const colors = activeIdx.map(i => warehouseData[i].is_critical ? '#dc2626' : '#2563eb');

        if (capChart) capChart.destroy();
        
        capChart = new Chart(chartCanvas, {
            type: 'bar',
            data: {
                labels,
                datasets: [{
                        label: 'Jelenlegi készlet',
                        data: current,
                        backgroundColor: colors,
                        order: 1,
                        borderRadius: 4,
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                            color: '#475569',
                            font: { weight: 'bold' },
                            formatter: (value, context) => {
                                const maxVal = max[context.dataIndex];
                                return maxVal > 0 ? Math.round((value / maxVal) * 100) + '%' : '0%';
                            }
                        }
                    },
                    {
                        label: 'Maximális kapacitás',
                        data: max,
                        backgroundColor: '#e2e8f0',
                        grouped: false,
                        order: 2,
                        borderRadius: 4,
                        datalabels: { display: false }
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: { mode: 'index', intersect: false },
                    datalabels: { display: true }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    }

    // A toggleFilter függvény hiányzott a kódodból, de a HTML hivatkozik rá!
    function toggleFilter(el, type) {
        el.classList.toggle('active');
        if (type === 'cap') renderCap();
    }


    // --- 3. KRITIKUS KÉSZLET TÁBLÁZAT ---
    function filterByWarehouse(element) {
        // Chip vizuális váltása
        document.querySelectorAll('.stock-wh-filter').forEach(c => c.classList.remove('active'));
        element.classList.add('active');

        const warehouseId = element.getAttribute('data-id');
        const tbody = document.getElementById('criticalTableBody');
        if (!tbody) return;
        
        tbody.innerHTML = '';

        const filtered = criticalStockData.filter(item =>
            warehouseId === 'all' || item.warehouse_id == warehouseId
        );

        if (filtered.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" style="padding: 20px; text-align: center; color: #666;">Nincs kritikus készlet ebben a nézetben.</td></tr>';
            return;
        }

        filtered.forEach(item => {
            const row = `
            <tr>
                <td style="padding: 12px;"><strong>${item.product_name}</strong></td>
                <td style="padding: 12px;">${item.warehouse_name}</td>
                <td style="padding: 12px; text-align: center; color: #dc2626; font-weight: bold;">${item.quantity} db</td>
                <td style="padding: 12px; text-align: center;">${item.min_quantity} db</td>
            </tr>`;
            tbody.innerHTML += row;
        });
    }


    // --- 4. KÖRDIAGRAM (Státusz) ---
    // Státusz
    const statusCanvas = document.getElementById('statusChart');
    if (statusCanvas) {
        const totalProdCount = <?= $totalProductCount ?>;
        new Chart(statusCanvas, {
            type: 'pie',
            data: {
                labels: ['Inaktív', 'Aktív'],
                datasets: [{
                    data: <?= json_encode(array_column($statusStats, 'count')) ?>,
                    backgroundColor: ['#ef4444', '#10b981']
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    datalabels: {
                        color: '#fff',
                        font: { weight: 'bold' },
                        formatter: (value) => {
                            if (totalProdCount <= 0) return null;
                            const percentage = (value / totalProdCount) * 100;
                            if (percentage < 3) return null;
                            return Math.round(percentage) + '%';
                        }
                    }
                }
            }
        });
    }

    // --- INDÍTÁS ---
    document.addEventListener('DOMContentLoaded', () => {
        // Kapacitás chart indítása
        renderCap();
        
        // Kritikus táblázat indítása (megkeressük az aktív chipet)
        const activeChip = document.querySelector('.stock-wh-filter.active');
        if (activeChip) {
            filterByWarehouse(activeChip);
        }
    });
</script>
    
</body>

</html>