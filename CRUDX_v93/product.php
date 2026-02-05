<?php
session_start();
require 'config.php';
require "./components/auth_check.php";
authorize(['admin', 'owner', 'user']);

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Érvénytelen termék azonosító.");
}

$productId = (int) $_GET['id'];

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

/* ===== API INTEGRATION ===== */
$searchTerm = $product['name'];

// Use a relative path if possible for file_get_contents to avoid local DNS issues
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$currentDir = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
// Double check: if product.php is in the root, and api.php is in /api/
$internalApiUrl = $protocol . $_SERVER['HTTP_HOST'] . $currentDir . "/api/api.php?query=" . urlencode($searchTerm);

// Add a timeout to prevent infinite loading
$ctx = stream_context_create(['http' => ['timeout' => 5]]); 
$apiResponse = @file_get_contents($internalApiUrl, false, $ctx);

if ($apiResponse) {
    $imageData = json_decode($apiResponse, true);
    $imagePath = $imageData['image_url'] ?? "images/products/no-image.png";
} else {
    $imagePath = "images/products/no-image.png";
}
?>
<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?></title>
    <link rel="stylesheet" href="./style/style.css">
    <link rel="stylesheet" href="./style/product.css">
</head>

<body>
    <?php
    $activePage = "products.php"; // Hogy a navbarban a Termékek maradjon aktív
    include './components/navbar.php';
    ?>

    <main class="container">

        <section class="card">
            <div class="card-header">
                <h2><img class="icon" src="./img/products_box.png"> <?= htmlspecialchars($product['name']) ?></h2>
                <a href="products.php" class="btn btn-outline btn-small">Vissza a listához</a>
            </div>

            <div style="display:flex; gap:32px; flex-wrap:wrap; margin-bottom: 20px;">
                <div style="flex: 0 0 280px;">
                    <img src="<?= $imagePath ?>" onerror="this.src='images/products/no-image.png';"
                        style="width:100%; border-radius:12px; border: 1px solid var(--border); box-shadow: 0 2px 10px rgba(0,0,0,0.05);"
                        alt="Product Image">
                </div>

                <div style="flex: 1; min-width: 300px;">
                    <div style="display: grid; grid-template-columns: auto 1fr; gap: 10px 20px; align-items: center;">
                        <span class="muted">Azonosító:</span> <strong>#<?= $product['ID'] ?></strong>

                        <span class="muted">Cikkszám:</span>
                        <strong><?= htmlspecialchars($product['item_number']) ?></strong>

                        <span class="muted">Kategória:</span>
                        <strong><?= htmlspecialchars($product['category_name'] ?? '—') ?></strong>

                        <span class="muted">Státusz:</span>
                        <div>
                            <?= $product['active']
                                ? '<span class="badge badge-success">Aktív</span>'
                                : '<span class="badge badge-muted">Inaktív</span>' ?>
                        </div>

                        <span class="muted">Rögzítve:</span> <small><?= $product['created_at'] ?></small>

                        <span class="muted">Utolsó mozgás:</span>
                        <small><?= $product['updated_at'] ?? 'Nincs adat' ?></small>
                    </div>
                </div>
            </div>

            <hr>

            <h3><img class="icon" src="./img/document_23966.png"> Leírás</h3>
            <div style="padding: 10px 0; line-height: 1.6; color: var(--text);">
                <?= $product['description'] ? nl2br(htmlspecialchars($product['description'])) : '<em class="muted">Nincs megadott leírás.</em>' ?>
            </div>

            <hr>

            <h3><img class="icon" src="./img/products_box.png"> Jelenlegi készletek</h3>

            <?php if (!empty($inventory)): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Raktár / Üzlet</th>
                            <th style="text-align: right;">Mennyiség</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventory as $row): ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($row['warehouse']) ?></strong></td>
                                <td style="text-align: right; font-family: monospace; font-weight: bold;">
                                    <?= number_format($row['quantity'], 0, '.', ' ') ?> db</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div style="padding: 20px; text-align: center; background: #f8fafc; border-radius: 8px; color: #64748b;">
                    Jelenleg egyetlen raktárban sem található készlet ebből a termékből.
                </div>
            <?php endif; ?>

        </section>

    </main>

    <?php include './components/footer.php'; ?>

</body>

</html>