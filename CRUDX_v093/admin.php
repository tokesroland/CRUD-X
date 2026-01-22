<?php
include 'config.php';

$message = "";
$msgType = ""; // success | danger

/*
|--------------------------------------------------------------------------
| 1️⃣ ÚJ TERMÉK LÉTREHOZÁSA
|--------------------------------------------------------------------------
*/
if (isset($_POST['create_product'])) {
    try {
        // Validáció
        if (empty($_POST['name']) || empty($_POST['category_id'])) {
            throw new Exception("Név és Kategória kötelező!");
        }

        $stmt = $pdo->prepare("
            INSERT INTO products 
            (name, item_number, description, category_ID, active) 
            VALUES (?, ?, ?, ?, ?)
        ");

        // Ha nincs cikkszám, generálunk (timestamp alapú)
        $itemNumber = !empty($_POST['item_number']) ? $_POST['item_number'] : time();
        
        // Ha nincs leírás, alapértelmezett érték (NOT NULL miatt)
        $desc = !empty($_POST['description']) ? $_POST['description'] : '-';

        $stmt->execute([
            $_POST['name'],
            $itemNumber,
            $desc,
            $_POST['category_id'],
            isset($_POST['active']) ? 1 : 0
        ]);

        $message = "Termék sikeresen létrehozva!";
        $msgType = "success";
    } catch (Exception $e) {
        $message = "Hiba: " . $e->getMessage();
        if ($e->getCode() == 23000) $message = "Hiba: Ez a cikkszám már létezik!";
        $msgType = "danger";
    }
}

/*
|--------------------------------------------------------------------------
| 2️⃣ ÚJ KÉSZLET HOZZÁRENDELÉS
|--------------------------------------------------------------------------
*/
if (isset($_POST['create_inventory'])) {
    try {
        $productId   = (int)$_POST['product_id'];
        $warehouseId = (int)$_POST['warehouse_id'];
        $quantity    = (int)$_POST['quantity'];

        // Ellenőrzés: létezik-e már?
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
| 3️⃣ KÉSZLET MÓDOSÍTÁS
|--------------------------------------------------------------------------
*/
if (isset($_POST['update_inventory'])) {
    try {
        $inventoryId = (int)$_POST['inventory_id'];
        $quantity    = (int)$_POST['quantity'];
        $minQty      = $_POST['min_quantity'] !== "" ? (int)$_POST['min_quantity'] : null;

        $stmt = $pdo->prepare("
            UPDATE inventory 
            SET quantity = ?, min_quantity = ?, updated_at = NOW() 
            WHERE ID = ?
        ");
        $stmt->execute([$quantity, $minQty, $inventoryId]);

        $message = "Készlet frissítve.";
        $msgType = "success";
    } catch (Exception $e) {
        $message = "Hiba a mentéskor.";
        $msgType = "danger";
    }
}

/*
|--------------------------------------------------------------------------
| 4️⃣ CSV IMPORT
|--------------------------------------------------------------------------
*/
if (isset($_POST['csv_submit']) && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if (($handle = fopen($file, "r")) !== false) {
        $pdo->beginTransaction();
        $row = 0;
        $imported = 0;

        try {
            // Megkeressük az első létező kategóriát fallback-nek
            $defaultCat = $pdo->query("SELECT ID FROM categories LIMIT 1")->fetchColumn();
            if (!$defaultCat) throw new Exception("Nincs kategória az adatbázisban, előbb hozz létre egyet!");

            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                $row++;
                if ($row === 1) continue; // Fejléc skip

                // CSV: Név, RaktárID, Mennyiség
                [$productName, $warehouseId, $quantity] = $data;
                
                $productName = trim($productName ?? '');
                $warehouseId = (int)($warehouseId ?? 0);
                $quantity    = (int)($quantity ?? 0);

                if ($productName === "") continue;

                // 1. Termék keresés / Létrehozás
                $stmt = $pdo->prepare("SELECT ID FROM products WHERE name = ?");
                $stmt->execute([$productName]);
                $pId = $stmt->fetchColumn();

                if (!$pId) {
                    // ÚJ TERMÉK LÉTREHOZÁSA (Kötelező mezők kitöltésével!)
                    $stmt = $pdo->prepare("
                        INSERT INTO products (name, item_number, description, category_ID, active)
                        VALUES (?, ?, 'CSV Importált', ?, 1)
                    ");
                    // Cikkszám generálás: timestamp + sorszám
                    $stmt->execute([$productName, time() + $row, $defaultCat]);
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

/*
|--------------------------------------------------------------------------
| ADATOK LEKÉRÉSE A MEGJELENÍTÉSHEZ
|--------------------------------------------------------------------------
*/
$categories = $pdo->query("SELECT * FROM categories ORDER BY category_name")->fetchAll(PDO::FETCH_ASSOC);
$warehouses = $pdo->query("SELECT * FROM warehouses ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$products   = $pdo->query("SELECT ID, name FROM products WHERE active = 1 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

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

<?php include './components/navbar_admin.php'; ?>

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
                <h2>🧩 Új Termék Létrehozása</h2>
            </div>
            
            <form method="POST">
                <div class="filters">
                    
                    <div class="field col-4">
                        <label>Termék neve *</label>
                        <input type="text" name="name" required placeholder="Pl. USB Kábel">
                    </div>

                    <div class="field col-4">
                        <label>Cikkszám</label>
                        <input type="number" name="item_number" placeholder="Hagy üresen generáláshoz">
                    </div>

                    <div class="field col-4">
                        <label>Kategória *</label>
                        <select name="category_id" required>
                            <option value="">Válassz...</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?= $cat['ID'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field col-12">
                        <label>Leírás</label>
                        <input type="text" name="description" placeholder="Rövid leírás a termékről...">
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


        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));">
            
            <div class="card">
                <div class="card-header">
                    <h2>📦 Új Készlet Hozzárendelés</h2>
                </div>
                <form method="POST">
                    <div class="field" style="margin-bottom:12px;">
                        <label>Termék</label>
                        <select name="product_id" required>
                            <option value="">Válassz terméket...</option>
                            <?php foreach($products as $p): ?>
                                <option value="<?= $p['ID'] ?>"><?= htmlspecialchars($p['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field" style="margin-bottom:12px;">
                        <label>Raktár</label>
                        <select name="warehouse_id" required>
                            <option value="">Válassz raktárat...</option>
                            <?php foreach($warehouses as $w): ?>
                                <option value="<?= $w['ID'] ?>"><?= htmlspecialchars($w['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field" style="margin-bottom:12px;">
                        <label>Kezdő Mennyiség</label>
                        <input type="number" name="quantity" required placeholder="0">
                    </div>
                    <div class="field actions" style="justify-content: flex-end;">
                        <button type="submit" name="create_inventory" class="btn btn-small">Hozzáadás</button>
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>📄 Tömeges Import (CSV)</h2>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <div class="field" style="margin-bottom:12px;">
                        <label>CSV Fájl kiválasztása</label>
                        <input type="file" name="csv_file" accept=".csv" required style="padding: 6px;">
                    </div>
                    <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 12px; line-height: 1.4;">
                        <strong>Formátum:</strong> Terméknév, RaktárID, Mennyiség<br>
                        <em>Ha a termék nem létezik, automatikusan létrejön az adatbázisban.</em>
                    </div>
                    <div class="field actions" style="justify-content: flex-end;">
                        <button type="submit" name="csv_submit" class="btn btn-outline">Feltöltés</button>
                    </div>
                    <br>
                    <div class="field actions"  style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 12px; line-height: 1.4;">
                       <a style="text-decoration: underline!important;" href="path/to/your/template.csv" target="_blank">CSV Sablon Letöltése</a> <em>Letölthető CSV sablon a megfelelő formátumhoz.</em>
                    </div>

                </form>
            </div>

        </div>


        <section class="card">
            <details>
                <summary style="cursor:pointer; font-weight:700; outline:none;">
                    ✏️ Készlet Kezelés és Módosítás (Kattints a lenyitáshoz)
                </summary>

                <div style="margin-top: 20px;">
                    <div class="field" style="margin-bottom: 15px;">
                        <input type="text" id="searchInput" onkeyup="filterList()" placeholder="🔍 Keresés terméknévre vagy raktárra...">
                    </div>

                    <div class="filters" style="background: #f1f5f9; border-bottom: 2px solid #e2e8f0; font-weight:bold;">
                        <div class="col-4">Termék & Raktár</div>
                        <div class="col-3">Jelenlegi DB</div>
                        <div class="col-3">Min. Limit</div>
                        <div class="col-2" style="text-align:right;">Művelet</div>
                    </div>

                    <div id="inventoryListContainer">
                        <?php foreach ($inventoryList as $inv): ?>
                            <form method="POST" class="inventory-row">
                                <input type="hidden" name="inventory_id" value="<?= $inv['ID'] ?>">
                                
                                <div class="filters" style="margin-bottom: 8px; align-items: center;">
                                    
                                    <div class="col-4 field info-text">
                                        <div style="font-weight:600;"><?= htmlspecialchars($inv['p_name']) ?></div>
                                        <div style="font-size:0.8rem; color: var(--text-muted);"><?= htmlspecialchars($inv['w_name']) ?></div>
                                    </div>

                                    <div class="col-3 field">
                                        <input type="number" name="quantity" value="<?= $inv['quantity'] ?>" required>
                                    </div>

                                    <div class="col-3 field">
                                        <input type="number" name="min_quantity" value="<?= $inv['min_quantity'] ?>" placeholder="Min">
                                    </div>

                                    <div class="col-2 field actions" style="justify-content:flex-end;">
                                        <button type="submit" name="update_inventory" class="btn btn-outline btn-small">Mentés</button>
                                    </div>

                                </div>
                            </form>
                        <?php endforeach; ?>
                    </div>
                </div>
            </details>
        </section>

    </main>

    <footer class="footer">
        CRUD-X Raktárkezelő &copy; <?= date('Y') ?>
    </footer>

    <script>
        function filterList() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let container = document.getElementById('inventoryListContainer');
            let rows = container.getElementsByClassName('inventory-row');

            for (let i = 0; i < rows.length; i++) {
                // A .info-text osztályú div tartalmát keressük
                let infoDiv = rows[i].querySelector('.info-text');
                if (infoDiv) {
                    let txtValue = infoDiv.textContent || infoDiv.innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }
            }
        }
    </script>

</body>
</html>