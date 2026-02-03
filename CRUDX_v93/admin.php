<?php
session_start();
include 'config.php';
require "./components/auth_check.php";
authorize(['admin','owner']);

$pageTitle = "Kezelés";
$activePage = "admin.php";
include './components/navbar.php';

$message = ""; // Üzenet megjelenítéshez
$msgType = ""; // success | danger

/*
|--------------------------------------------------------------------------
| 1️⃣ ÚJ TERMÉK LÉTREHOZÁSA (Precíziós Validációval)
|--------------------------------------------------------------------------
*/
if (isset($_POST['create_product'])) {
    try {
        $categoryID = $_POST['category_id'];
        $name = trim($_POST['name']);
        // Ha üres, generálunk egyet, egyébként a beírtat használjuk
        $itemNumber = !empty($_POST['item_number']) ? trim($_POST['item_number']) : (string)time();
        $desc = !empty($_POST['description']) ? $_POST['description'] : '-';

        // --- 1. ALAP VALIDÁCIÓK ---
        if (mb_strlen($name) > 100) {
            throw new Exception("Hiba: A termék neve nem lehet hosszabb 100 karakternél!");
        }
        if (mb_strlen($itemNumber) > 100) {
            throw new Exception("Hiba: A cikkszám nem lehet hosszabb 100 karakternél!");
        }
        if (strpos($itemNumber, '-') === 0) {
            throw new Exception("Hiba: A cikkszám nem lehet negatív szám!");
        }
        if (empty($name) || empty($categoryID) || $categoryID === "NEW") {
            throw new Exception("Név és Kategória kötelező!");
        }

        // --- 2. DUPLIKÁCIÓ ELLENŐRZÉS (PONTOS HIBAÜZENETÉRT) ---
        // Név ellenőrzése
        $stmtCheckName = $pdo->prepare("SELECT ID FROM products WHERE name = ?");
        $stmtCheckName->execute([$name]);
        if ($stmtCheckName->fetch()) {
            throw new Exception("Hiba: Ezzel a névvel már létezik termék az adatbázisban!");
        }

        // Cikkszám ellenőrzése
        $stmtCheckItem = $pdo->prepare("SELECT ID FROM products WHERE item_number = ?");
        $stmtCheckItem->execute([$itemNumber]);
        if ($stmtCheckItem->fetch()) {
            throw new Exception("Hiba: Ez a cikkszám már foglalt egy másik terméknél!");
        }

        // --- 3. ÚJ KATEGÓRIA KEZELÉS ---
        if ($categoryID === "NEW" && !empty($_POST['new_category_name'])) {
            $newCatName = trim($_POST['new_category_name']);
            if (mb_strlen($newCatName) > 50) { 
                throw new Exception("Hiba: A kategória neve túl hosszú!");
            }
            
            $checkCat = $pdo->prepare("SELECT ID FROM categories WHERE category_name = ?");
            $checkCat->execute([$newCatName]);
            $existingCatId = $checkCat->fetchColumn();

            if ($existingCatId) {
                $categoryID = $existingCatId;
            } else {
                $insCat = $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)");
                $insCat->execute([$newCatName]);
                $categoryID = $pdo->lastInsertId();
            }
        }

        // --- 4. MENTÉS ---
        $stmt = $pdo->prepare("
            INSERT INTO products 
            (name, item_number, description, category_ID, active) 
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $name,
            $itemNumber,
            $desc,
            $categoryID,
            isset($_POST['active']) ? 1 : 0
        ]);

        $message = "Termék sikeresen létrehozva!";
        $msgType = "success";

    } catch (Exception $e) {
        $message = $e->getMessage();
        $msgType = "danger";
    }
}

/*
|--------------------------------------------------------------------------
| 🆕 TERMÉK TÖMEGES MÓDOSÍTÁS (Precíziós Validációval)
|--------------------------------------------------------------------------
*/
if (isset($_POST['update_products_bulk']) && isset($_POST['products'])) {
    $pdo->beginTransaction();
    try {
        $count = 0;
        $stmt = $pdo->prepare("
            UPDATE products 
            SET name = ?, 
                item_number = ?, 
                description = ?, 
                category_ID = ?, 
                active = ?, 
                updated_at = NOW() 
            WHERE ID = ?
        ");

        // Előkészített lekérdezések a duplikáció kereséshez (saját magát kizárva)
        $checkNameUpd = $pdo->prepare("SELECT ID FROM products WHERE name = ? AND ID != ?");
        $checkNumUpd  = $pdo->prepare("SELECT ID FROM products WHERE item_number = ? AND ID != ?");

        foreach ($_POST['products'] as $id => $data) {
            $id = (int)$id;
            $name = trim($data['name']);
            $itemNum = trim($data['item_number']);

            // --- PHP VALIDÁCIÓ ---
            if (empty($name) || empty($data['category_id'])) {
                throw new Exception("Minden terméknél kötelező a név és a kategória! (ID: $id)");
            }
            if (mb_strlen($name) > 100) {
                throw new Exception("Hiba (ID: $id): A név max 100 karakter lehet!");
            }
            if (mb_strlen($itemNum) > 100) {
                throw new Exception("Hiba (ID: $id): A cikkszám max 100 karakter lehet!");
            }
            if (strpos($itemNum, '-') === 0) {
                throw new Exception("Hiba (ID: $id): A cikkszám nem lehet negatív!");
            }

            // --- DUPLIKÁCIÓ ELLENŐRZÉS (Saját magát kivéve) ---
            $checkNameUpd->execute([$name, $id]);
            if ($checkNameUpd->fetch()) {
                throw new Exception("Hiba (ID: $id): A '$name' név már foglalt egy másik terméknél!");
            }

            if (!empty($itemNum)) {
                $checkNumUpd->execute([$itemNum, $id]);
                if ($checkNumUpd->fetch()) {
                    throw new Exception("Hiba (ID: $id): A '$itemNum' cikkszám már foglalt egy másik terméknél!");
                }
            }

            $stmt->execute([
                $name,
                !empty($itemNum) ? $itemNum : (string)time(),
                $data['description'],
                $data['category_id'],
                isset($data['active']) ? 1 : 0,
                $id
            ]);
            $count++;
        }

        $pdo->commit();
        $message = "$count termék adatai sikeresen frissítve!";
        $msgType = "success";

    } catch (Exception $e) {
        $pdo->rollBack();
        $message = $e->getMessage();
        $msgType = "danger";
    }
}

/*
|--------------------------------------------------------------------------
| 2️⃣ ÚJ KÉSZLET HOZZÁRENDELÉS (Szigorú Backend Validációval)
|--------------------------------------------------------------------------
*/
if (isset($_POST['create_inventory'])) {
    try {
        $productId   = (int)$_POST['product_id'];
        $warehouseId = (int)$_POST['warehouse_id'];
        $quantity    = (int)$_POST['quantity'];

        // --- PHP VALIDÁCIÓ: MENNYISÉG ---
        if ($quantity < 0) {
            throw new Exception("Hiba: A mennyiség nem lehet negatív!");
        }
        if ($quantity > 9999) {
            throw new Exception("Hiba: A maximális bevihető mennyiség 9999 db!");
        }

        $stmt = $pdo->prepare("SELECT ID FROM inventory WHERE product_ID = ? AND warehouse_ID = ?");
        $stmt->execute([$productId, $warehouseId]);

        if ($stmt->fetch()) {
            throw new Exception("Ez a termék már szerepel ebben a raktárban. Használd a módosítást.");
        }

        $stmt = $pdo->prepare("
            INSERT INTO inventory (product_ID, warehouse_ID, quantity, created_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$productId, $warehouseId, $quantity]);
        
        $message = "Készlet sikeresen hozzáadva.";
        $msgType = "success";
    } catch (Exception $e) {
        $message = $e->getMessage();
        $msgType = "danger";
    }
}

/*
|--------------------------------------------------------------------------
| 3️⃣ KÉSZLET TÖMEGES MÓDOSÍTÁS (Szigorú Backend Validációval)
|--------------------------------------------------------------------------
*/
if (isset($_POST['update_inventory_bulk']) && isset($_POST['inventory'])) {
    $pdo->beginTransaction();
    try {
        $count = 0;
        $stmt = $pdo->prepare("
            UPDATE inventory 
            SET quantity = ?, min_quantity = ?, updated_at = NOW() 
            WHERE ID = ?
        ");

        foreach ($_POST['inventory'] as $invId => $data) {
            $invId = (int)$invId;
            $quantity = (int)$data['quantity'];
            $minQty = ($data['min_quantity'] !== "") ? (int)$data['min_quantity'] : null;

            // --- PHP VALIDÁCIÓ ---
            // 1. Mennyiség ellenőrzése
            if ($quantity < 0) {
                throw new Exception("Hiba (ID: $invId): A mennyiség nem lehet negatív!");
            }
            if ($quantity > 9999) {
                throw new Exception("Hiba (ID: $invId): A mennyiség nem lehet több mint 9999!");
            }

            // 2. Min. mennyiség ellenőrzése
            if ($minQty !== null) {
                if ($minQty < 0) {
                    throw new Exception("Hiba (ID: $invId): A min. mennyiség nem lehet negatív!");
                }
                if ($minQty > 9999) {
                    throw new Exception("Hiba (ID: $invId): A min. mennyiség nem lehet több mint 9999!");
                }
            }

            $stmt->execute([$quantity, $minQty, $invId]);
            $count++;
        }

        $pdo->commit();
        $message = "$count készlet tétel frissítve.";
        $msgType = "success";

    } catch (Exception $e) {
        $pdo->rollBack();
        $message = $e->getMessage();
        $msgType = "danger";
    }
}

/*
|--------------------------------------------------------------------------
| 4️⃣ CSV IMPORT (Szigorú Backend Validációval)
|--------------------------------------------------------------------------
*/
if (isset($_POST['csv_submit']) && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($file, "r")) !== false) {
        $pdo->beginTransaction();
        $row = 0;
        $imported = 0;

        try {
            $defaultCat = $pdo->query("SELECT ID FROM categories LIMIT 1")->fetchColumn();
            if (!$defaultCat) throw new Exception("Nincs kategória az adatbázisban!");

            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $row++;
                if ($row === 1) continue; // Fejléc skip

                $productName = trim($data[0] ?? '');
                $warehouseId = (int)($data[1] ?? 0);
                $quantity    = (int)($data[2] ?? 0);

                if ($productName === "") continue;

                // --- PHP VALIDÁCIÓ CSV SOROKRA ---
                if (mb_strlen($productName) > 100) {
                    throw new Exception("Hiba a $row. sorban: A terméknév túl hosszú (max 100 karakter).");
                }
                if ($quantity < 0) {
                    throw new Exception("Hiba a $row. sorban: A mennyiség nem lehet negatív ($quantity).");
                }
                if ($quantity > 9999) {
                    throw new Exception("Hiba a $row. sorban: A mennyiség túl sok ($quantity). Max 9999.");
                }

                // 1. Termék keresés / Létrehozás
                $stmt = $pdo->prepare("SELECT ID FROM products WHERE name = ?");
                $stmt->execute([$productName]);
                $pId = $stmt->fetchColumn();

                if (!$pId) {
                    $stmt = $pdo->prepare("
                        INSERT INTO products (name, item_number, description, category_ID, active)
                        VALUES (?, ?, ?, ?, 1)
                    ");
                    $stmt->execute([
                        $productName, 
                        (string)(time() + $row), 
                        'CSV Importált termék', 
                        $defaultCat
                    ]);
                    $pId = $pdo->lastInsertId();
                }

                // 2. Készlet kezelés
                $stmt = $pdo->prepare("SELECT ID FROM inventory WHERE product_ID = ? AND warehouse_ID = ?");
                $stmt->execute([$pId, $warehouseId]);
                $invId = $stmt->fetchColumn();

                if ($invId) {
                    $upd = $pdo->prepare("UPDATE inventory SET quantity = ?, updated_at = NOW() WHERE ID = ?");
                    $upd->execute([$quantity, $invId]);
                } else {
                    $ins = $pdo->prepare("INSERT INTO inventory (product_ID, warehouse_ID, quantity, created_at) VALUES (?, ?, ?, NOW())");
                    $ins->execute([$pId, $warehouseId, $quantity]);
                }
                $imported++;
            }

            fclose($handle);
            $pdo->commit();
            $message = "CSV Import kész: $imported tétel feldolgozva.";
            $msgType = "success";
        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "CSV Hiba: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY category_name")->fetchAll(PDO::FETCH_ASSOC);
$warehouses = $pdo->query("SELECT * FROM warehouses ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$activeProducts = $pdo->query("SELECT ID, name FROM products WHERE active = 1 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$allProducts = $pdo->query("SELECT * FROM products ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$inventoryList = $pdo->query("
    SELECT i.*, p.name as p_name, w.name as w_name 
    FROM inventory i
    JOIN products p ON i.product_ID = p.ID
    JOIN warehouses w ON i.warehouse_ID = w.ID
    ORDER BY p.name ASC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adminisztráció | CRUD-X</title>
    <link rel="stylesheet" href="./style/style.css">
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

        <section class="card">
            <div class="card-header">
                <h2><img class="icon" src="./img/create_new_plus_add_icon_232794.png"> Új Termék Létrehozása</h2>
            </div>
            <form method="POST">
                <div class="filters">
                    <div class="field col-4">
                        <label>Termék neve * (Max 100)</label>
                        <input type="text" name="name" required placeholder="Pl. USB Kábel" maxlength="100">
                    </div>
                    <div class="field col-4">
                        <label>Cikkszám (Max 100)</label>
                        <input type="text" name="item_number" placeholder="Hagy üresen generáláshoz" maxlength="100">
                    </div>
                    <div class="field col-4">
                        <label>Kategória *</label>
                        <select name="category_id" id="categorySelect" required onchange="checkNewCategory(this.value)">
                            <option value="">Válassz...</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['ID'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                            <?php endforeach; ?>
                            <option value="NEW" style="font-weight:bold; color:var(--primary);">+ Új kategória létrehozása...</option>
                        </select>
                    </div>

                    <div class="field col-12" id="newCategoryField" style="display:none; margin-top:-10px; margin-bottom:15px;">
                        <label style="color:var(--primary);">Új kategória neve *</label>
                        <input type="text" name="new_category_name" placeholder="Írd be az új kategória nevét">
                    </div>

                    <div class="field col-12">
                        <label>Leírás</label>
                        <input type="text" name="description" placeholder="Rövid leírás...">
                    </div>
                    <div class="field col-12 actions" style="justify-content: space-between;">
                        <label style="display:flex; align-items:center; gap:8px; font-size:14px;">
                            <input type="checkbox" name="active" checked style="width:auto;"> Aktív termék
                        </label>
                        <button type="submit" name="create_product" class="btn">Termék Mentése</button>
                    </div>
                </div>
            </form>
        </section>

        <section class="card">
            <details>
                <summary style="cursor:pointer; font-weight:700; outline:none; padding:10px;">
                    <img class="icon" src="./img/create_117333.png"> Meglévő Termékek Szerkesztése
                </summary>
                <div style="margin-top: 20px;">
                    <div class="field" style="margin-bottom: 15px;">
                        <input type="text" id="productSearchInput" onkeyup="filterProducts()" placeholder="🔍 Keress terméknévre vagy cikkszámra...">
                    </div>
                    
                    <form method="POST">
                        <div style="display:flex; justify-content:flex-end; margin-bottom:10px;">
                            <button type="submit" name="update_products_bulk" class="btn">💾 Összes módosítás mentése</button>
                        </div>

                        <div class="filters" style="background: #f1f5f9; border-bottom: 2px solid #e2e8f0; font-weight:bold; padding:10px;">
                            <div class="col-3">Termék Név</div>
                            <div class="col-2">Cikkszám</div>
                            <div class="col-2">Kategória</div>
                            <div class="col-3">Leírás</div>
                            <div class="col-2" style="text-align:right;">Státusz</div>
                        </div>
                        <div id="productListContainer">
                            <?php foreach ($allProducts as $prod): ?>
                                <div class="product-row" style="border-bottom: 1px solid #eee;">
                                    <div class="filters" style="margin-bottom: 0; align-items: center; padding: 8px 14px;">
                                        <div class="col-3 field prod-name">
                                            <input type="text" name="products[<?= $prod['ID'] ?>][name]" value="<?= htmlspecialchars($prod['name']) ?>" required maxlength="100">
                                        </div>
                                        <div class="col-2 field prod-item-num">
                                            <input type="text" name="products[<?= $prod['ID'] ?>][item_number]" value="<?= htmlspecialchars($prod['item_number']) ?>" maxlength="100">
                                        </div>
                                        <div class="col-2 field">
                                            <select name="products[<?= $prod['ID'] ?>][category_id]">
                                                <?php foreach($categories as $cat): ?>
                                                    <option value="<?= $cat['ID'] ?>" <?= $cat['ID'] == $prod['category_ID'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['category_name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-3 field">
                                            <input type="text" name="products[<?= $prod['ID'] ?>][description]" value="<?= htmlspecialchars($prod['description']) ?>">
                                        </div>
                                        <div class="col-2 field" style="display:flex; justify-content:flex-end;">
                                            <label style="font-size: 0.75rem; display: flex; align-items: center; margin-top: 4px;">
                                                <input type="checkbox" name="products[<?= $prod['ID'] ?>][active]" <?= $prod['active'] == 1 ? 'checked' : '' ?> style="width: auto; margin-right: 5px;"> Aktív
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </form>
                </div>
            </details>
        </section>

        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));">
            <div class="card">
                <div class="card-header"><h2>➕ Új Készlet</h2></div>
                <form method="POST">
                    <div class="field" style="margin-bottom:12px;">
                        <label>Termék</label>
                        <select name="product_id" required>
                            <option value="">Válassz...</option>
                            <?php foreach($activeProducts as $p): ?>
                                <option value="<?= $p['ID'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field" style="margin-bottom:12px;">
                        <label>Raktár</label>
                        <select name="warehouse_id" required>
                            <option value="">Válassz...</option>
                            <?php foreach($warehouses as $w): ?>
                                <option value="<?= $w['ID'] ?>"><?= htmlspecialchars($w['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field" style="margin-bottom:12px;">
                        <label>Mennyiség (0-9999)</label>
                        <input type="number" name="quantity" required placeholder="0" min="0" max="9999">
                    </div>
                    <div class="field actions" style="justify-content: flex-end;">
                        <button type="submit" name="create_inventory" class="btn btn-small">Hozzáadás</button>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-header"><h2>📄 CSV Import</h2></div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="field" style="margin-bottom:12px;">
                        <label>CSV Fájl</label>
                        <input type="file" name="csv_file" accept=".csv" required style="padding: 6px;">
                    </div>
                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 12px;">
                        <strong>Formátum:</strong> Név, RaktárID, Mennyiség
                    </div>
                    <div class="field actions" style="justify-content: flex-end;">
                        <button type="submit" name="csv_submit" class="btn btn-outline">Feltöltés</button>
                    </div>
                </form>
            </div>
        </div>

        <section class="card">
            <details>
                <summary style="cursor:pointer; font-weight:700; outline:none; padding:10px;">
                    <img class="icon" src="./img/create_117333.png"> Készlet Módosítása
                </summary>
                <div style="margin-top: 20px;">
                    <div class="field" style="margin-bottom: 15px;">
                        <input type="text" id="searchInput" onkeyup="filterList()" placeholder="🔍 Keresés terméknévre vagy raktárra...">
                    </div>
                    
                    <form method="POST">
                        <div style="display:flex; justify-content:flex-end; margin-bottom:10px;">
                            <button type="submit" name="update_inventory_bulk" class="btn">💾 Összes módosítás mentése</button>
                        </div>

                        <div class="filters" style="background: #f1f5f9; border-bottom: 2px solid #e2e8f0; font-weight:bold; padding:10px;">
                            <div class="col-4">Termék & Raktár</div>
                            <div class="col-4">Jelenlegi DB (Max 9999)</div>
                            <div class="col-4">Min. Limit (Max 9999)</div>
                        </div>
                        <div id="inventoryListContainer">
                            <?php foreach ($inventoryList as $inv): ?>
                                <div class="inventory-row">
                                    <div class="filters" style="margin-bottom: 8px; align-items: center; padding: 5px 10px;">
                                        <div class="col-4 field info-text">
                                            <div style="font-weight:600;"><?= htmlspecialchars($inv['p_name']) ?></div>
                                            <div style="font-size:0.8rem; color: var(--text-muted);"><?= htmlspecialchars($inv['w_name']) ?></div>
                                        </div>
                                        <div class="col-4 field">
                                            <input type="number" name="inventory[<?= $inv['ID'] ?>][quantity]" value="<?= $inv['quantity'] ?>" min="0" max="9999" required>
                                        </div>
                                        <div class="col-4 field">
                                            <input type="number" name="inventory[<?= $inv['ID'] ?>][min_quantity]" value="<?= $inv['min_quantity'] ?>" min="0" max="9999" placeholder="Min">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </form>
                </div>
            </details>
        </section>

    </main>

    <script>
        // Új kategória mező megjelenítése/elrejtése
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

        function filterList() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let rows = document.getElementsByClassName('inventory-row');
            for (let i = 0; i < rows.length; i++) {
                let txt = rows[i].querySelector('.info-text').textContent || rows[i].querySelector('.info-text').innerText;
                rows[i].style.display = txt.toLowerCase().includes(filter) ? "" : "none";
            }
        }

        function filterProducts() {
            let input = document.getElementById('productSearchInput');
            let filter = input.value.toLowerCase();
            let rows = document.getElementsByClassName('product-row');
            for (let i = 0; i < rows.length; i++) {
                let name = rows[i].querySelector('.prod-name input').value;
                let num = rows[i].querySelector('.prod-item-num input').value;
                rows[i].style.display = (name + " " + num).toLowerCase().includes(filter) ? "" : "none";
            }
        }
    </script>
</body>
</html>