<?php
session_start();
include 'config.php';
require "./components/auth_check.php";
authorize(['admin', 'owner', 'user']);
include 'components/filter.php';

$activePage = "products.php";
$pageTitle = "Termékek";
include './components/navbar.php';
?>
<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <title>CRUD-X – Termékek</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css?v=2.0">
</head>

<body>

    <main class="container">

        <section class="card">
            <div class="card-header">
                <h2><img class="icon" src="./img/products_box.png">Terméklista</h2>
            </div>

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
                    <input type="text" name="item_number" value="<?= htmlspecialchars($item_number) ?>" placeholder="pl. ABC-123">
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
                    <input type="number" name="qty_min" value="<?= $qty_min !== null ? (int)$qty_min : '' ?>" placeholder="0">
                </div>

                <div class="field col-2">
                    <label>Mennyiség max</label>
                    <input type="number" name="qty_max" value="<?= $qty_max !== null ? (int)$qty_max : '' ?>" placeholder="9999">
                </div>

                <div class="actions col-3">
                    <button class="btn btn-small" type="submit">Szűrés</button>
                    <a class="btn btn-small btn-outline" href="products.php">Alaphelyzet</a>
                </div>

                <div class="col-12" style="opacity:.75;font-size:12px;">
                    Tipp: ha kiválasztasz egy raktárat, a lista és a mennyiségek csak arra a raktárra vonatkoznak.
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
                            $displayQty = (int)$prod['display_qty']; // A szűrt mennyiség (raktár vagy globális)
                            $globalTotal = (int)$prod['global_total_qty']; // Mindig a teljes készlet
                            $warehousesForProd = $warehousesByProduct[$id] ?? [];
                            ?>
                            <tr class="<?= (int)$prod['active'] === 1 ? '' : 'inactive-row' ?>">
                                <td><?= $id ?></td>
                                <td> <a class="product-link" href="product.php?id=<?= $id ?>"> <?= htmlspecialchars($prod['name']) ?></a></td>
                                <td><?= htmlspecialchars($prod['item_number']) ?></td>
                                <td><?= htmlspecialchars($prod['category_name'] ?? 'Nincs kategória') ?></td>

                                <td>
                                    <?php if ($displayQty > 0): ?>
                                        <div class="stock-wrapper">
                                            <?php if ((int)$prod['active'] === 1): ?>
                                                <button type="button" class="badge badge-success stock-btn" onclick="toggleStockPopup(<?= $id ?>, event)">
                                                    Készleten (<?= $displayQty ?> db)
                                                </button>

                                                <div id="stock-popup-<?= $id ?>" class="popup-card">
                                                    <h3>Raktárkészlet (Részletes)</h3>
                                                    <?php foreach ($warehousesForProd as $wh): ?>
                                                        <div class="row">
                                                            <strong><?= htmlspecialchars($wh['warehouse']) ?></strong>
                                                            <span><?= (int)$wh['quantity'] ?> db</span>
                                                        </div>
                                                    <?php endforeach; ?>
                                                    <div class="muted">Összesen (minden raktárban): <?= $globalTotal ?> db</div>
                                                </div>
                                            <?php else: ?>
                                                <span style="color: #dc2626; font-weight: bold; font-size: 0.7rem;user-select: none;">
                                                    Készleten (<?= $displayQty ?> db)
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php elseif ($warehouse_ID > 0 && $displayQty == 0): ?>
                                        <span class="badge badge-muted">Ebben a raktárban nincs</span>
                                    <?php else: ?>
                                        <span class="badge badge-muted">Nincs készleten</span>
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

            <?php if ($totalPages > 1): ?>
                <div style="padding: 20px; display: flex; justify-content: center; gap: 5px;">
                    <?php
                    // Segédfüggvény az URL generáláshoz, megtartva a szűrőket
                    function getPageUrl($pageNum)
                    {
                        $params = $_GET;
                        $params['page'] = $pageNum;
                        return '?' . http_build_query($params);
                    }
                    ?>

                    <?php if ($page > 1): ?>
                        <a href="<?= getPageUrl($page - 1) ?>" class="btn btn-small btn-outline">&laquo; Előző</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                            <a href="<?= getPageUrl($i) ?>" class="btn btn-small <?= $i === $page ? '' : 'btn-outline' ?>"
                                style="<?= $i === $page ? 'background: var(--primary); color: white; border-color: var(--primary);' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php elseif ($i == $page - 3 || $i == $page + 3): ?>
                            <span style="padding: 5px;">...</span>
                        <?php endif; ?>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="<?= getPageUrl($page + 1) ?>" class="btn btn-small btn-outline">Következő &raquo;</a>
                    <?php endif; ?>
                </div>
                <div style="text-align: center; margin-bottom: 20px; font-size: 0.8rem; color: #666;">
                    Összes találat: <?= $totalRows ?> db (<?= $totalPages ?> oldal)
                </div>
            <?php endif; ?>

        </section>

    </main>

    <?php include './components/footer.php'; ?>

    <script>
        function toggleStockPopup(productId, event) {
            event.stopPropagation();
            const popup = document.getElementById('stock-popup-' + productId);
            document.querySelectorAll('.popup-card').forEach(el => {
                if (el !== popup) el.style.display = 'none';
            });
            popup.style.display = (popup.style.display === 'block') ? 'none' : 'block';
        }

        document.addEventListener('click', function() {
            document.querySelectorAll('.popup-card').forEach(el => el.style.display = 'none');
        });
    </script>

</body>

</html>