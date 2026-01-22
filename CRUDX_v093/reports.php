<?php
include 'config.php';

/*
|--------------------------------------------------------------------------
| 1. ADATLEKÉRÉSEK
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
    $w['is_critical'] = $percent >= 90;
    if ($w['is_critical']) $criticalWarehouses++;
    $totalItems += $w['current_quantity'];
}

// Készlet vs Minimum adatok
$stmt = $pdo->query("
    SELECT p.name AS product_name, w.ID AS warehouse_id, w.name AS warehouse_name, i.quantity, i.min_quantity
    FROM inventory i
    JOIN products p ON p.ID = i.product_ID
    JOIN warehouses w ON w.ID = i.warehouse_ID
    ORDER BY i.quantity ASC
");
$stockData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$lowStockCount = 0;
foreach ($stockData as $item) {
    if ($item['quantity'] <= $item['min_quantity']) $lowStockCount++;
}

// Kategória és Státusz statisztikák
$catStats = $pdo->query("SELECT c.category_name, SUM(i.quantity) as q FROM inventory i JOIN products p ON i.product_ID = p.ID JOIN categories c ON p.category_ID = c.ID GROUP BY c.ID")->fetchAll(PDO::FETCH_ASSOC);
$totalStockForCats = array_sum(array_column($catStats, 'q'));

$statusStats = $pdo->query("SELECT active, COUNT(*) as count FROM products GROUP BY active")->fetchAll(PDO::FETCH_ASSOC);
$totalProductCount = array_sum(array_column($statusStats, 'count'));
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
        .chart-container { position: relative; height: 350px; width: 100%; }
        
        .filter-scroll-container {
            display: flex; gap: 8px; overflow-x: auto; padding: 10px 0;
            scrollbar-width: thin;
        }
        .filter-scroll-container::-webkit-scrollbar { height: 6px; }
        .filter-scroll-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        .toggle-chip {
            white-space: nowrap; cursor: pointer; padding: 6px 14px; border-radius: 20px;
            background: #f1f5f9; color: #475569; font-size: 0.8rem; border: 1px solid #e2e8f0; transition: 0.2s;
            user-select: none;
        }
        .toggle-chip:hover { background: #e2e8f0; }
        .toggle-chip.active { background: var(--primary); color: white; border-color: var(--primary); }
        .toggle-chip.critical.active { background: var(--danger); border-color: var(--danger); }

        .filter-group { background: #f8fafc; padding: 15px; border-radius: 12px; margin-bottom: 10px; border: 1px solid #e2e8f0; }
        .checkbox-label { font-size: 0.9rem; cursor: pointer; display: flex; align-items: center; gap: 8px; margin-top: 10px; font-weight: 500; color: #dc2626; }
    </style>
</head>
<body>

<?php include './components/navbar_admin.php'; ?> 

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
        <div class="card-header"><h2>🏭 Raktár Kapacitás Kihasználtság</h2></div>
        <div class="filter-group">
            <div class="filter-scroll-container">
                <?php foreach ($warehouseStats as $index => $w): ?>
                    <div class="toggle-chip active capacity-filter <?= $w['is_critical'] ? 'critical' : '' ?>" 
                         data-index="<?= $index ?>" onclick="toggleFilter(this, 'cap')">
                        <?= htmlspecialchars($w['name']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="chart-container"><canvas id="capacityChart"></canvas></div>
    </section>

    <section class="card">
        <div class="card-header"><h2>📦 Készlet vs. Minimum Szint</h2></div>
        <div class="filter-group">
            <div class="filter-scroll-container">
                <div class="toggle-chip active stock-wh-filter" data-id="all" onclick="selectStockWarehouse(this)">Összes raktár</div>
                <?php foreach ($warehouseStats as $w): ?>
                    <div class="toggle-chip stock-wh-filter" data-id="<?= $w['ID'] ?>" onclick="selectStockWarehouse(this)">
                        <?= htmlspecialchars($w['name']) ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <label class="checkbox-label">
                <input type="checkbox" id="onlyCriticalCheck" onchange="updateStockChart()"> Csak kritikus készletek (hiány)
            </label>
        </div>
        <div class="chart-container"><canvas id="stockChart"></canvas></div>
    </section>

    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));">
        <section class="card"><div class="card-header"><h2>📁 Kategória megoszlás</h2></div><div class="chart-container"><canvas id="categoryChart"></canvas></div></section>
        <section class="card"><div class="card-header"><h2>⚙️ Termék státuszok</h2></div><div class="chart-container"><canvas id="statusChart"></canvas></div></section>
    </div>

</main>

<script>
    // Plugin regisztrálása
    Chart.register(ChartDataLabels);
    Chart.defaults.font.family = "system-ui, sans-serif";

    const warehouseData = <?= json_encode($warehouseStats) ?>;
    const stockRaw = <?= json_encode($stockData) ?>;
    let capChart, stockChart;
    let selectedStockWarehouseId = 'all';

    // --- 1. Kapacitás Chart Logika (Százalékkal) ---
    function renderCap() {
        const activeIdx = Array.from(document.querySelectorAll('.capacity-filter.active')).map(el => parseInt(el.dataset.index));
        const labels = activeIdx.map(i => warehouseData[i].name);
        const current = activeIdx.map(i => warehouseData[i].current_quantity);
        const max = activeIdx.map(i => warehouseData[i].max_quantity);
        const colors = activeIdx.map(i => warehouseData[i].is_critical ? '#dc2626' : '#2563eb');

        if(capChart) capChart.destroy();
        capChart = new Chart(document.getElementById('capacityChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { 
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
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    // --- 2. Készlet vs Minimum Chart Logika (Változatlanul) ---
    function selectStockWarehouse(el) {
        document.querySelectorAll('.stock-wh-filter').forEach(chip => chip.classList.remove('active'));
        el.classList.add('active');
        selectedStockWarehouseId = el.dataset.id;
        updateStockChart();
    }

    function updateStockChart() {
        const onlyCrit = document.getElementById('onlyCriticalCheck').checked;
        let filtered = stockRaw.filter(i => (selectedStockWarehouseId === 'all' || i.warehouse_id == selectedStockWarehouseId));
        if (onlyCrit) {
            filtered = filtered.filter(i => parseInt(i.quantity) <= parseInt(i.min_quantity));
        }
        filtered = filtered.slice(0, 15);

        const labels = filtered.map(i => i.product_name);
        const currentData = filtered.map(i => parseInt(i.quantity));
        const minData = filtered.map(i => parseInt(i.min_quantity));
        const barColors = filtered.map(i => (parseInt(i.quantity) <= parseInt(i.min_quantity)) ? '#dc2626' : '#16a34a');

        if(stockChart) stockChart.destroy();
        stockChart = new Chart(document.getElementById('stockChart'), {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    { label: 'Aktuális készlet', data: currentData, backgroundColor: barColors, order: 1, borderRadius: 4, datalabels: { display: false } },
                    { label: 'Elvárt minimum', data: minData, backgroundColor: 'rgba(220, 38, 38, 0.25)', grouped: false, order: 2, borderRadius: 4, datalabels: { display: false } }
                ]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { 
                    tooltip: { mode: 'index', intersect: false },
                    datalabels: { display: false } 
                },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    function toggleFilter(el, type) {
        el.classList.toggle('active');
        if(type === 'cap') renderCap();
    }

    // --- 3. Kategória megoszlás (Százalékkal) ---
    const totalStock = <?= $totalStockForCats ?>;
    new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($catStats, 'category_name')) ?>,
            datasets: [{ 
                data: <?= json_encode(array_column($catStats, 'q')) ?>, 
                backgroundColor: ['#2563eb', '#16a34a', '#f59e0b', '#dc2626', '#8b5cf6'] 
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
                        return totalStock > 0 ? Math.round((value / totalStock) * 100) + '%' : '0%';
                    }
                }
            } 
        }
    });

    // --- 4. Termék státuszok (Százalékkal) ---
    const totalProdCount = <?= $totalProductCount ?>;
    new Chart(document.getElementById('statusChart'), {
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
                        return totalProdCount > 0 ? Math.round((value / totalProdCount) * 100) + '%' : '0%';
                    }
                }
            } 
        }
    });

    renderCap();
    updateStockChart();
</script>
</body>
</html>