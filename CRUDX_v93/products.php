<?php
session_start();
include 'config.php';
require "./components/auth_check.php";
authorize(['admin','owner','user']);
include 'components/filter.php';

?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>CRUD-X – Termékek</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css?v=2.0">

   
    <style>
    </style>
</head>

<body>

<?php include './components/navbar.php'; ?>

<main class="container">

    <section class="card">
        <div class="card-header">
            <h2>📦 Terméklista</h2>
        </div>

        <!-- SZŰRŐK -->
        <form method="get" class="filters">
            <div class="field col-3">
                <label>Kategória</label>
                <select name="category_ID">
                    <option value="0">Összes</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= (int)$c['ID'] ?>" <?= $category_ID === (int)$c['ID'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field col-2">
                <label>Cikkszám</label>
                <input type="number" name="item_number" value="<?= htmlspecialchars($item_number) ?>" placeholder="pl. 100001">
            </div>

            <div class="field col-3">
                <label>Név</label>
                <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" placeholder="keresés névre">
            </div>

            <div class="field col-4">
                <label>Leírás</label>
                <input type="text" name="description" value="<?= htmlspecialchars($description) ?>" placeholder="keresés leírásban">
            </div>

            <div class="field col-2">
                <label>Aktív</label>
                <select name="active">
                    <option value="" <?= $active === '' ? 'selected' : '' ?>>Összes</option>
                    <option value="1" <?= $active === '1' ? 'selected' : '' ?>>Aktív</option>
                    <option value="0" <?= $active === '0' ? 'selected' : '' ?>>Inaktív</option>
                </select>
            </div>

            <div class="field col-2">
                <label>Elérhetőség (készlet)</label>
                <select name="stock">
                    <option value="" <?= $stock === '' ? 'selected' : '' ?>>Összes</option>
                    <option value="in" <?= $stock === 'in' ? 'selected' : '' ?>>Készleten</option>
                    <option value="out" <?= $stock === 'out' ? 'selected' : '' ?>>Nincs készleten</option>
                </select>
            </div>

            <div class="field col-3">
                <label>Melyik raktárban</label>
                <select name="warehouse_ID">
                    <option value="0">Összes raktár (összes készlet)</option>
                    <?php foreach ($warehouses as $w): ?>
                        <option value="<?= (int)$w['ID'] ?>" <?= $warehouse_ID === (int)$w['ID'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($w['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="field col-2">
                <label>Mennyiség min</label>
                <input type="number" name="qty_min" value="<?= $qty_min !== null ? (int)$qty_min : '' ?>" placeholder="pl. 10">
            </div>

            <div class="field col-2">
                <label>Mennyiség max</label>
                <input type="number" name="qty_max" value="<?= $qty_max !== null ? (int)$qty_max : '' ?>" placeholder="pl. 200">
            </div>

            <div class="actions col-3">
                <button class="btn btn-small" type="submit">Szűrés</button>
                <a class="btn btn-small btn-outline" href="products.php">Alaphelyzet</a>
            </div>

            <div class="col-12" style="opacity:.75;font-size:12px;">
                Tipp: ha kiválasztasz egy raktárat, a min/max mennyiség arra a raktárra szűr. Ha nincs raktár kiválasztva, az összes raktár összegére szűr.
            </div>
        </form>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Név</th>
                    <th>Cikkszám</th>
                    <th>Kategória</th>
                    <th>Raktárak / Készlet</th>
                    <th>Státusz</th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($products as $prod): ?>
                    <?php
                        $id = (int)$prod['ID'];
                        $totalQty = (int)$prod['total_qty'];
                        $warehousesForProd = $warehousesByProduct[$id] ?? [];
                    ?>
                    <tr class="<?= (int)$prod['active'] === 1 ? '' : 'inactive-row' ?>">
                        <td><?= $id ?></td>
                        <td> <a class="product-link" href="product.php?id=<?= $id ?>"> <?= htmlspecialchars($prod['name']) ?></a></td>
                        <td><?= htmlspecialchars($prod['item_number']) ?></td>
                        <td><?= htmlspecialchars($prod['category_name'] ?? 'Nincs kategória') ?></td>

                        <td>
                            <?php if ($totalQty > 0): ?>
                                <div class="stock-wrapper">
                                    <button type="button" class="badge badge-success stock-btn" onclick="toggleStockPopup(<?= $id ?>, event)">
                                        Készleten (<?= $totalQty ?> db)
                                    </button>

                                    <div id="stock-popup-<?= $id ?>" class="popup-card">
                                        <h3>Raktárkészlet</h3>
                                        <?php foreach ($warehousesForProd as $wh): ?>
                                            <div class="row">
                                                <strong><?= htmlspecialchars($wh['warehouse']) ?></strong>
                                                <span><?= (int)$wh['quantity'] ?> db</span>
                                            </div>
                                        <?php endforeach; ?>
                                        <div class="muted">Összesen: <?= $totalQty ?> db</div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="badge badge-muted">Nincs készlet</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <?php if ((int)$prod['active'] === 1): ?>
                                <span class="badge badge-success">Aktív</span>
                            <?php else: ?>
                                <span class="badge badge-muted">Inaktív</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;opacity:.7;padding:16px;">
                            Nincs találat a megadott szűrőkre.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>
        </div>

    </section>

</main>

<footer class="footer">
        CRUD-X Raktárkezelő &copy; <?= date('Y') ?>
</footer>

<script>
function toggleStockPopup(productId, event) {
    event.stopPropagation();

    const popup = document.getElementById('stock-popup-' + productId);

    // zárjuk be a többit
    document.querySelectorAll('.popup-card').forEach(el => {
        if (el !== popup) el.style.display = 'none';
    });

    popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
}

// bárhová kattintasz -> bezár
document.addEventListener('click', function() {
    document.querySelectorAll('.popup-card').forEach(el => el.style.display = 'none');
});
</script>

</body>
</html>
