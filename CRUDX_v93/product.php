<?php
session_start();
require 'config.php';
require "./components/auth_check.php";
authorize(['admin','owner','user']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Érvénytelen termék azonosító.");
}

$productId = (int)$_GET['id'];

/* ===== TERMÉK + KATEGÓRIA ===== */
$stmt = $pdo->prepare("
    SELECT p.*, c.category_name
    FROM products p
    LEFT JOIN categories c ON c.ID = p.category_ID
    WHERE p.ID = :id
");
$stmt->execute(['id' => $productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("A termék nem található.");
}

/* ===== RAKTÁRKÉSZLET ===== */
$stmtInv = $pdo->prepare("
    SELECT w.name AS warehouse, i.quantity
    FROM inventory i
    JOIN warehouses w ON w.ID = i.warehouse_ID
    WHERE i.product_ID = :id
");
$stmtInv->execute(['id' => $productId]);
$inventory = $stmtInv->fetchAll(PDO::FETCH_ASSOC);

/* ===== KÉP ===== */
$imagePath = "images/products/{$productId}.jpg";
if (!file_exists($imagePath)) {
    $imagePath = "images/products/no-image.png";
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="./style/style.css">
</head>
<body>

<header class="topbar">
    <div class="logo">
        <a href="products.php" style="color:white;text-decoration:none;">⬅ Vissza a termékekhez</a>
    </div>
</header>

<main class="container">

<section class="card">
    <div class="card-header">
        <h2><img class="icon" src="./img/products_box.png"> <?= htmlspecialchars($product['name']) ?></h2>
    </div>

    <div style="display:flex;gap:24px;flex-wrap:wrap;">
        <div>
            <img src="<?= $imagePath ?>" style="max-width:280px;border-radius:8px;">
        </div>

        <div>
            <p><strong>ID:</strong> <?= $product['ID'] ?></p>
            <p><strong>Cikkszám:</strong> <?= htmlspecialchars($product['item_number']) ?></p>
            <p><strong>Kategória:</strong> <?= htmlspecialchars($product['category_name'] ?? '—') ?></p>
            <p><strong>Státusz:</strong>
                <?= $product['active']
                    ? '<span class="badge badge-success">Aktív</span>'
                    : '<span class="badge badge-muted">Inaktív</span>' ?>
            </p>
            <p><strong>Létrehozva:</strong> <?= $product['created_at'] ?></p>
            <p><strong>Módosítva:</strong> <?= $product['updated_at'] ?></p>
        </div>
    </div>

    <hr>

    <h3><img class="icon" src="./img/document_23966.png"> Leírás</h3>
    <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>

    <hr>

    <h3><img class="icon" src="./img/products_box.png"> Raktárkészlet</h3>

    <?php if (!empty($inventory)): ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Raktár</th>
                    <th>Mennyiség</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($inventory as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['warehouse']) ?></td>
                    <td><?= (int)$row['quantity'] ?> db</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Nincs készlet.</p>
    <?php endif; ?>

</section>

</main>

<?php include './components/footer.php'; ?>

</body>
</html>
