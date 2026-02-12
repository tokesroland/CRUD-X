<?php
session_start();
require 'config.php';
require "./components/auth_check.php";
authorize(['admin', 'owner']);

$pageTitle = "Kezelés";
$activePage = "admin.php";
require './components/navbar.php';

// Backend logika betöltése
require 'components/admin_backend.php';
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adminisztráció | CRUD-X</title>
    <link rel="stylesheet" href="./style/style.css">
    <link rel="stylesheet" href="./style/admin.css">
</head>
<body>

    <main class="container">

        <?php if ($message): ?>
            <div style="margin-bottom: 20px; text-align:center;">
                <span class="badge badge-<?= $msgType === 'success' ? 'success' : 'muted' ?>"
                    style="<?= $msgType === 'danger' ? 'background:#fee2e2; color:#b91c1c;' : '' ?>">
                    <?= htmlspecialchars($message) ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="stats-grid">

            <div class="card">
                <div class="card-header">
                    <h2><img src="./img/document_23966.png" class="icon" alt=""> CSV Import</h2>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="field" style="margin-bottom:12px;">
                            <label>CSV Fájl</label>
                            <input type="file" name="csv_file" accept=".csv" required style="padding: 6px; width: 100%;">
                        </div>
                        <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 12px;">
                            <strong>Formátum:</strong> Név, RaktárID, Mennyiség, <em>Leírás, Kategória</em>
                        </div>
                        <div class="actions" style="text-align: right;">
                            <button type="submit" name="csv_submit" class="btn btn-outline" style="width:100%">Feltöltés</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2><img src="./img/create_new_add_plus_icon_219839.png" alt="" class="icon"> Új Készlet Hozzárendelés</h2>
                </div>
<div class="card-body" id="newInvBody"> 
                    <form method="POST">
                        
                        <div class="field" style="margin-bottom:10px;">
                            <label>Raktár</label>
                            <?php 
                                // Ellenőrizzük, hány raktára van a felhasználónak
                                $whCount = count($warehouses);
                            ?>

                            <?php if ($whCount === 1): ?>
                                <?php $singleWh = $warehouses[0]; ?>
                                <input type="hidden" name="warehouse_id" value="<?= $singleWh['ID'] ?>">
                                <select class="table-input" disabled style="background-color: #e9ecef; cursor: not-allowed;">
                                    <option selected><?= htmlspecialchars($singleWh['name']) ?></option>
                                </select>
                                <small style="color:var(--text-muted); font-size:0.8rem;">
                                    Ez az egyetlen hozzárendelt raktárad.
                                </small>
                            <?php else: ?>
                                <select name="warehouse_id" required class="table-input">
                                    <option value="">Válassz raktárat...</option>
                                    <?php foreach ($warehouses as $w): ?>
                                        <option value="<?= $w['ID'] ?>"><?= htmlspecialchars($w['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            <?php endif; ?>
                        </div>

                        <div class="field" style="margin-bottom:10px;">
                            <label>Termék</label>
                            <select name="product_id" required class="table-input">
                                <option value="">Válassz terméket...</option>
                                <?php foreach ($activeProducts as $p): ?>
                                    <option value="<?= $p['ID'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="field" style="margin-bottom:10px;">
                            <label>Mennyiség</label>
                            <input type="number" name="quantity" required placeholder="0" min="0" max="9999" class="table-input">
                        </div>

                        <div class="actions" style="text-align: right;">
                            <button type="submit" name="create_inventory" class="btn btn-small" style="width:100%">Hozzáadás</button>
                        </div>
                    </form>
                </div>            </div>

        </div> <section class="card" style="margin-top: 20px;">
            <div class="card-header toggle-header" onclick="toggleCard('inventoryEditBody')">
                <h2><img class="icon" src="./img/create_117333.png"> Készlet Módosítás</h2>
                <span style="font-size: 0.8rem; color: #666;">▼</span>
            </div>
            
            <div class="card-body" id="inventoryEditBody" style="<?= isset($_GET['search_inv']) ? '' : 'display:none;' ?>">
                
                <form method="GET" class="search-form">
                    <input type="text" name="search_inv" value="<?= htmlspecialchars($_GET['search_inv'] ?? '') ?>" 
                           placeholder="Keresés név vagy cikkszám alapján..." class="table-input">
                    <?php if(isset($_GET['search_prod'])): ?>
                        <input type="hidden" name="search_prod" value="<?= htmlspecialchars($_GET['search_prod']) ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn">Keresés</button>
                </form>

                <?php if (empty($inventoryList)): ?>
                    <div style="text-align:center; color:#666; padding:20px;">
                        <?= isset($_GET['search_inv']) ? "Nincs találat." : "A lista betöltéséhez használja a keresőt." ?>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Termék</th>
                                        <th>Cikkszám</th>
                                        <th>Raktár</th>
                                        <th style="width: 100px;">Mennyiség</th>
                                        <th style="width: 100px;">Min. Limit</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($inventoryList as $inv): ?>
                                        <tr class="inventory-row">
                                            <td><strong><?= htmlspecialchars($inv['p_name']) ?></strong></td>
                                            <td><small><?= htmlspecialchars($inv['item_number']) ?></small></td>
                                            <td><span class="badge badge-muted"><?= htmlspecialchars($inv['w_name']) ?></span></td>
                                            <td>
                                                <input type="number" name="inventory[<?= $inv['ID'] ?>][quantity]" 
                                                       value="<?= $inv['quantity'] ?>" class="table-input">
                                            </td>
                                            <td>
                                                <input type="number" name="inventory[<?= $inv['ID'] ?>][min_quantity]" 
                                                       value="<?= $inv['min_quantity'] ?>" class="table-input">
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div style="margin-top: 15px; display: flex; flex-direction: column; gap: 10px; align-items: center;">
                            <small style="color: #666;">(Találatok limitálva: 40 db)</small>
                            <button type="submit" name="bulk_update_inventory" class="btn" style="width:100%; max-width: 300px;">Kijelöltek Mentése</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </section>

        <section class="card">
            <div class="card-header toggle-header" onclick="toggleCard('editProductBody')">
                <h2><img class="icon" src="./img/create_117333.png"> Meglévő termékek szerkesztése</h2>
                <span style="font-size: 0.8rem; color: #666;">▼</span>
            </div>
            <div class="card-body" id="editProductBody" style="<?= isset($_GET['search_prod']) ? '' : 'display:none;' ?>">
                
                <form method="GET" class="search-form">
                    <input type="text" name="search_prod" value="<?= htmlspecialchars($_GET['search_prod'] ?? '') ?>" 
                           placeholder="Termék keresése szerkesztéshez..." class="table-input">
                    <?php if(isset($_GET['search_inv'])): ?>
                        <input type="hidden" name="search_inv" value="<?= htmlspecialchars($_GET['search_inv']) ?>">
                    <?php endif; ?>
                    <button type="submit" class="btn">Keresés</button>
                </form>

                <?php if (empty($allProducts)): ?>
                    <div style="text-align:center; color:#666; padding:20px;">
                        <?= isset($_GET['search_prod']) ? "Nincs találat." : "Keressen rá a szerkesztendő termékre." ?>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <div class="table-wrapper">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th style="min-width: 150px;">Név</th>
                                        <th style="min-width: 100px;">Cikkszám</th>
                                        <th style="min-width: 150px;">Leírás</th>
                                        <th style="min-width: 150px;">Kategória</th>
                                        <th style="width: 50px; text-align: center;">Aktív</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($allProducts as $p): ?>
                                        <tr class="product-row">
                                            <td>
                                                <input type="text" name="products[<?= $p['ID'] ?>][name]" 
                                                       value="<?= htmlspecialchars($p['name']) ?>" class="table-input">
                                            </td>
                                            <td>
                                                <input type="text" name="products[<?= $p['ID'] ?>][item_number]" 
                                                       value="<?= htmlspecialchars($p['item_number']) ?>" class="table-input">
                                            </td>
                                            <td>
                                                <input type="text" name="products[<?= $p['ID'] ?>][description]" 
                                                       value="<?= htmlspecialchars($p['description']) ?>" class="table-input" placeholder="-">
                                            </td>
                                            <td>
                                                <select name="products[<?= $p['ID'] ?>][category_id]" class="table-input">
                                                    <?php foreach ($categories as $cat): ?>
                                                        <option value="<?= $cat['ID'] ?>" <?= $p['category_ID'] == $cat['ID'] ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($cat['category_name']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td style="text-align: center;">
                                                <input type="checkbox" name="products[<?= $p['ID'] ?>][active]" <?= $p['active'] ? 'checked' : '' ?>>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div style="margin-top: 15px; text-align: right;">
                            <button type="submit" name="update_products_bulk" class="btn" style="width:100%; max-width: 300px;">Változások Mentése</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </section>

        <section class="card">
            <div class="card-header toggle-header" onclick="toggleCard('createProductBody')">
                <h2><img class="icon" src="./img/create_new_plus_add_icon_232794.png"> Új Termék Létrehozása</h2>
                <span style="font-size: 0.8rem; color: #666;">▼</span>
            </div>
            <div class="card-body" id="createProductBody" style="display:none;">
                <form method="POST">
                    <div class="filters" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                        <div class="field">
                            <label>Termék neve *</label>
                            <input type="text" name="name" required placeholder="Pl. USB Kábel" maxlength="100" class="table-input">
                        </div>
                        <div class="field">
                            <label>Cikkszám</label>
                            <input type="text" name="item_number" placeholder="Generáláshoz üresen" maxlength="100" class="table-input">
                        </div>
                        <div class="field">
                            <label>Kategória *</label>
                            <select name="category_id" id="categorySelect" required onchange="checkNewCategory(this.value)" class="table-input">
                                <option value="">Válassz...</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['ID'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                                <?php endforeach; ?>
                                <option value="NEW" style="font-weight:bold; color:var(--primary);">+ Új kategória...</option>
                            </select>
                        </div>

                        <div class="field" id="newCategoryField" style="display:none; grid-column: 1 / -1;">
                            <label style="color:var(--primary);">Új kategória neve *</label>
                            <input type="text" name="new_category_name" placeholder="Írd be az új kategória nevét" class="table-input">
                        </div>

                        <div class="field" style="grid-column: 1 / -1;">
                            <label>Leírás</label>
                            <input type="text" name="description" placeholder="Rövid leírás..." class="table-input">
                        </div>
                        <div class="field actions" style="grid-column: 1 / -1; text-align: right;">
                            <button type="submit" name="create_product" class="btn" style="width:100%; max-width:300px;">Termék Mentése</button>
                        </div>
                    </div>
                </form>
            </div>
        </section>

    </main>

    <script>
        function toggleCard(id) {
            const el = document.getElementById(id);
            if (el) {
                el.style.display = (el.style.display === 'none') ? 'block' : 'none';
            }
        }

        function checkNewCategory(val) {
            const field = document.getElementById('newCategoryField');
            const input = field.querySelector('input');
            if (val === 'NEW') {
                field.style.display = 'block';
                input.required = true;
                input.focus();
            } else {
                field.style.display = 'none';
                input.required = false;
            }
        }
    </script>
    <?php include './components/footer.php'; ?>
</body>
</html>