<?php
// components/admin_backend.php

$message = ""; 
$msgType = ""; 

// --- JOGOSULTSÁGOK ÉS RAKTÁRAK LEKÉRÉSE ---
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['role'] ?? 'user';
$warehouses = [];

if ($userRole === 'owner') {
    $stmtWh = $pdo->query("SELECT * FROM warehouses WHERE active = 1 ORDER BY name ASC");
    $warehouses = $stmtWh->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmtWh = $pdo->prepare("
        SELECT w.* FROM warehouses w
        JOIN user_warehouse_access uwa ON w.ID = uwa.warehouse_id
        WHERE uwa.user_id = ? AND w.active = 1
        ORDER BY w.name ASC
    ");
    $stmtWh->execute([$userId]);
    $warehouses = $stmtWh->fetchAll(PDO::FETCH_ASSOC);
}

$allowedWhIds = array_column($warehouses, 'ID');
if (empty($allowedWhIds)) {
    $allowedWhIds = [0];
}

/* |--------------------------------------------------------------------------
   | ÚJ TERMÉK LÉTREHOZÁSA
   |-------------------------------------------------------------------------- */
if (isset($_POST['create_product'])) {
    try {
        $categoryID = $_POST['category_id'];
        $name = trim($_POST['name']);
        $itemNumber = !empty($_POST['item_number']) ? trim($_POST['item_number']) : (string)time();
        $desc = !empty($_POST['description']) ? $_POST['description'] : '-';

        if (mb_strlen($name) > 100) {
            throw new Exception("Hiba: A termék neve túl hosszú!");
        }
        if (mb_strlen($itemNumber) > 100) {
            throw new Exception("Hiba: A cikkszám túl hosszú!");
        }
        if (empty($name) || empty($categoryID)) {
            throw new Exception("Név és Kategória kötelező!");
        }

        $stmtCheckName = $pdo->prepare("SELECT ID FROM products WHERE name = ?");
        $stmtCheckName->execute([$name]);
        if ($stmtCheckName->fetch()) {
            throw new Exception("Hiba: Ezzel a névvel már létezik termék!");
        }

        $stmtCheckItem = $pdo->prepare("SELECT ID FROM products WHERE item_number = ?");
        $stmtCheckItem->execute([$itemNumber]);
        if ($stmtCheckItem->fetch()) {
            throw new Exception("Hiba: Ez a cikkszám már foglalt!");
        }

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

    $stmt = $pdo->prepare("INSERT INTO products (name, item_number, description, category_ID, active) VALUES (?, ?, ?, ?, ?)");
    // Default newly created products to active = 1
    $stmt->execute([$name, $itemNumber, $desc, $categoryID, 1]);

        $message = "Termék sikeresen létrehozva!";
        $msgType = "success";

    } catch (Exception $e) {
        $message = $e->getMessage();
        $msgType = "danger";
    }
}

/* |--------------------------------------------------------------------------
   | TERMÉK TÖMEGES MÓDOSÍTÁS
   |-------------------------------------------------------------------------- */
if (isset($_POST['update_products_bulk']) && isset($_POST['products'])) {
    $pdo->beginTransaction();
    try {
        $count = 0;
        $stmt = $pdo->prepare("
            UPDATE products 
            SET name = ?, item_number = ?, description = ?, category_ID = ?, active = ?, updated_at = NOW() 
            WHERE ID = ?
        ");

        $checkNameUpd = $pdo->prepare("SELECT ID FROM products WHERE name = ? AND ID != ?");
        $checkNumUpd = $pdo->prepare("SELECT ID FROM products WHERE item_number = ? AND ID != ?");

        foreach ($_POST['products'] as $id => $data) {
            $id = (int)$id;
            $name = trim($data['name']);
            $itemNum = trim($data['item_number']);
            $desc = isset($data['description']) ? trim($data['description']) : '';

            if (empty($name)) {
                throw new Exception("Név kötelező (ID: $id)");
            }

            $checkNameUpd->execute([$name, $id]);
            if ($checkNameUpd->fetch()) {
                throw new Exception("A '$name' név már foglalt!");
            }

            if (!empty($itemNum)) {
                $checkNumUpd->execute([$itemNum, $id]);
                if ($checkNumUpd->fetch()) {
                    throw new Exception("A '$itemNum' cikkszám már foglalt!");
                }
            }

            $stmt->execute([
                $name,
                !empty($itemNum) ? $itemNum : (string)time(),
                $desc,
                $data['category_id'],
                isset($data['active']) ? 1 : 0,
                $id
            ]);
            $count++;
        }

        $pdo->commit();
        $message = "$count termék frissítve!";
        $msgType = "success";

    } catch (Exception $e) {
        $pdo->rollBack();
        $message = $e->getMessage();
        $msgType = "danger";
    }
}

/* |--------------------------------------------------------------------------
   | ÚJ KÉSZLET HOZZÁRENDELÉS
   |-------------------------------------------------------------------------- */
if (isset($_POST['create_inventory'])) {
    try {
        $productId = (int)$_POST['product_id'];
        $warehouseId = (int)$_POST['warehouse_id'];
        $quantity = (int)$_POST['quantity'];

        if ($quantity < 0) {
            throw new Exception("Hiba: A mennyiség nem lehet negatív!");
        }
        if ($quantity > 9999) {
            throw new Exception("Hiba: A maximális bevihető mennyiség 9999 db!");
        }

        // Ellenőrizzük, hogy van-e elég hely a cél raktárban (ha a raktárhoz tartozik max_quantity)
        $stmtWhCap = $pdo->prepare("SELECT max_quantity FROM warehouses WHERE ID = ? LIMIT 1");
        $stmtWhCap->execute([$warehouseId]);
        $maxQty = (int)$stmtWhCap->fetchColumn();
        if ($maxQty > 0) {
            $stmtTot = $pdo->prepare("SELECT COALESCE(SUM(quantity),0) FROM inventory WHERE warehouse_ID = ?");
            $stmtTot->execute([$warehouseId]);
            $currentTotal = (int)$stmtTot->fetchColumn();

            if (($currentTotal + $quantity) > $maxQty) {
                $available = $maxQty - $currentTotal;
                throw new Exception("Hiba: Nincs elég hely a raktárban. Szabad hely: $available db.");
            }
        }

        $stmt = $pdo->prepare("SELECT ID FROM inventory WHERE product_ID = ? AND warehouse_ID = ?");
        $stmt->execute([$productId, $warehouseId]);
        if ($stmt->fetch()) {
            throw new Exception("Ez a termék már szerepel ebben a raktárban!");
        }

        $stmt = $pdo->prepare("INSERT INTO inventory (product_ID, warehouse_ID, quantity, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$productId, $warehouseId, $quantity]);
        
        $message = "Készlet sikeresen hozzáadva.";
        $msgType = "success";
    } catch (Exception $e) {
        $message = $e->getMessage();
        $msgType = "danger";
    }
}

/* |--------------------------------------------------------------------------
   | KÉSZLET TÖMEGES MÓDOSÍTÁS
   |-------------------------------------------------------------------------- */
if (isset($_POST['bulk_update_inventory']) && isset($_POST['inventory'])) {
    $pdo->beginTransaction();
    try {
        $count = 0;
        $whListStr = implode(',', $allowedWhIds);
        if (empty($whListStr)) {
            $whListStr = '0';
        }
        
        $checkStmt = $pdo->prepare("SELECT ID FROM inventory WHERE ID = ? AND warehouse_ID IN ($whListStr)");
        $updateStmt = $pdo->prepare("UPDATE inventory SET quantity = ?, min_quantity = ?, updated_at = NOW() WHERE ID = ?");

        foreach ($_POST['inventory'] as $invID => $data) {
            $invID = (int)$invID;
            $checkStmt->execute([$invID]);
            if (!$checkStmt->fetch()) {
                continue;
            }

            $qty = (int)$data['quantity'];
            $minQty = (int)$data['min_quantity'];

            // Ne engedjünk negatív értékeket a készletnél
            if ($qty < 0) {
                throw new Exception("Hiba: A mennyiség nem lehet negatív (inventory ID: $invID)");
            }
            if ($minQty < 0) {
                throw new Exception("Hiba: A minimális mennyiség nem lehet negatív (inventory ID: $invID)");
            }

            // Konzisztencia: ugyanaz a felső korlát, mint az egyedi beszúrásnál
            if ($qty > 9999) {
                throw new Exception("Hiba: A mennyiség túl nagy (inventory ID: $invID). Max: 9999");
            }

            $updateStmt->execute([$qty, $minQty, $invID]);
            $count++;
        }

        $pdo->commit();
        $message = "$count készlet frissítve!";
        $msgType = "success";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Hiba: " . $e->getMessage();
        $msgType = "danger";
    }
}

/* |--------------------------------------------------------------------------
   | CSV IMPORT
   |-------------------------------------------------------------------------- */
if (isset($_POST['csv_submit']) && isset($_FILES['csv_file'])) {
    $file = $_FILES['csv_file']['tmp_name'];

    if ($handle = fopen($file, "r")) {
        $pdo->beginTransaction();
        $row = 0;
        $imported = 0;

        try {
            $defaultCat = $pdo->query("SELECT ID FROM categories LIMIT 1")->fetchColumn();
            if (!$defaultCat) {
                throw new Exception("Nincs kategória az adatbázisban!");
            }

            while ($data = fgetcsv($handle, 1000, ",")) {
                $row++;
                if ($row === 1) continue;

                $productName = trim($data[0] ?? '');
                $warehouseId = (int)($data[1] ?? 0);
                $quantity = (int)($data[2] ?? 0);
                $description = !empty($data[3]) ? trim($data[3]) : 'CSV Importált termék';
                $categoryInput = !empty($data[4]) ? trim($data[4]) : '';

                if ($productName === "") continue;

                if (mb_strlen($productName) > 100) {
                    throw new Exception("Hiba a $row. sorban: A terméknév túl hosszú!");
                }
                if ($quantity < 0 || $quantity > 9999) {
                    throw new Exception("Hiba a $row. sorban: A mennyiség értéke érvénytelen!");
                }

                $stmt = $pdo->prepare("SELECT ID FROM products WHERE name = ?");
                $stmt->execute([$productName]);
                $pId = $stmt->fetchColumn();

                if (!$pId) {
                    $catIdToUse = $defaultCat;

                    if ($categoryInput !== '') {
                        $stmtC = $pdo->prepare("SELECT ID FROM categories WHERE category_name = ?");
                        $stmtC->execute([$categoryInput]);
                        $foundCat = $stmtC->fetchColumn();

                        if ($foundCat) {
                            $catIdToUse = $foundCat;
                        } else {
                            $foundCatId = false;

                            if (is_numeric($categoryInput)) {
                                $stmtC2 = $pdo->prepare("SELECT ID FROM categories WHERE ID = ?");
                                $stmtC2->execute([$categoryInput]);
                                $foundCatId = $stmtC2->fetchColumn();
                                if ($foundCatId) {
                                    $catIdToUse = $foundCatId;
                                }
                            }
                            
                            if (!$foundCat && !$foundCatId) {
                                $stmtNewC = $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)");
                                $stmtNewC->execute([$categoryInput]);
                                $catIdToUse = $pdo->lastInsertId();
                            }
                        }
                    }

                    $stmt = $pdo->prepare("INSERT INTO products (name, item_number, description, category_ID, active) VALUES (?, ?, ?, ?, 1)");
                    $stmt->execute([$productName, (string)(time() + $row), $description, $catIdToUse]);
                    $pId = $pdo->lastInsertId();
                }

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

// --- ADATLEKÉRÉSEK A MEGJELENÍTÉSHEZ ---
$categories = $pdo->query("SELECT * FROM categories ORDER BY category_name")->fetchAll(PDO::FETCH_ASSOC);
$activeProducts = $pdo->query("SELECT ID, name FROM products WHERE active = 1 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Készlet Keresés
$inventoryList = [];
$searchInv = trim($_GET['search_inv'] ?? '');
if ($searchInv !== '') {
    $whPlaceholders = implode(',', array_fill(0, count($allowedWhIds), '?'));
    $params = ["%$searchInv%", "%$searchInv%", ...$allowedWhIds];
    $sqlInv = "SELECT i.*, p.name as p_name, p.item_number, w.name as w_name 
               FROM inventory i
               JOIN products p ON i.product_ID = p.ID
               JOIN warehouses w ON i.warehouse_ID = w.ID
               WHERE (p.name LIKE ? OR p.item_number LIKE ?) AND w.ID IN ($whPlaceholders)
               ORDER BY p.name ASC LIMIT 40";
    $stmtInv = $pdo->prepare($sqlInv);
    $stmtInv->execute($params);
    $inventoryList = $stmtInv->fetchAll(PDO::FETCH_ASSOC);
}

// Termék Keresés
$allProducts = [];
$searchProd = trim($_GET['search_prod'] ?? '');
if ($searchProd !== '') {
    $stmtProd = $pdo->prepare("SELECT * FROM products WHERE name LIKE ? OR item_number LIKE ? ORDER BY name ASC LIMIT 40");
    $stmtProd->execute(["%$searchProd%", "%$searchProd%"]);
    $allProducts = $stmtProd->fetchAll(PDO::FETCH_ASSOC);
}
?>