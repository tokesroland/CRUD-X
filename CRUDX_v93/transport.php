<?php
session_start();
require 'config.php';
require "./components/auth_check.php";
authorize(['admin', 'owner', 'user']);

if (!isset($_GET['batch'])) {
    die("Érvénytelen szállítási azonosító.");
}

$batchId = $_GET['batch'];

// 1. Alapadatok lekérése (User és Dátum) - az első rekord alapján
$stmtBase = $pdo->prepare("
    SELECT t.date, t.batch_id, t.description, u.username 
    FROM transports t 
    JOIN users u ON t.user_ID = u.ID 
    WHERE t.batch_id = ? LIMIT 1
");
$stmtBase->execute([$batchId]);
$baseInfo = $stmtBase->fetch(PDO::FETCH_ASSOC);

if (!$baseInfo) die("A szállítás nem található.");

// 2. Tételek lekérése (Összevonva a típusokat)
$stmtItems = $pdo->prepare("
    SELECT p.name, t.quantity, t.type, w.name as warehouse_name
    FROM transports t
    JOIN products p ON t.product_ID = p.ID
    JOIN warehouses w ON t.warehouse_ID = w.ID
    WHERE t.batch_id = ?
    ORDER BY t.type DESC
");
$stmtItems->execute([$batchId]);
$items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Szállítás részletei: <?= $batchId ?></title>
    <link rel="stylesheet" href="./style/style.css">
</head>
<body>
<?php include './components/navbar.php'; ?>

<main class="container">
    <section class="card">
        <div class="card-header">
            <h2><img class="icon" src="./img/truck_23929.png"> Szállítmány: <?= htmlspecialchars($batchId) ?></h2>
            <a href="<?= $_SESSION['role'] === 'user' ? 'index.php' : 'owner.php' ?>" class="btn btn-outline">
                <?= $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'owner' ? 'Vissza a naplóhoz' : 'Vissza a főoldalra' ?>
            </a>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-bottom:20px;">
            <div>
                <p><strong>Rögzítette:</strong> <?= htmlspecialchars($baseInfo['username']) ?></p>
                <p><strong>Időpont:</strong> <?= $baseInfo['date'] ?></p>
            </div>
            <div>
                <p><strong>Leírás:</strong> <?= htmlspecialchars($baseInfo['description']) ?></p>
            </div>
        </div>

        <h2><img class="icon" src="./img/product_icon_238584.png"> Mozgatott tételek</h2><br>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Termék</th>
                    <th>Mennyiség</th>
                    <th>Típus</th>
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
                    <td><?= htmlspecialchars($item['warehouse_name']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>
</main>

<?php include './components/footer.php'; ?>

</body>
</html>