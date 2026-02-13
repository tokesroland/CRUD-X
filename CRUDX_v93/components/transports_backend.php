<?php

// --------------------------------------------------------
// 1. KOSÁR MŰVELETEK (SESSION KEZELÉS)

if (!isset($_SESSION['transport_cart'])) {
    $_SESSION['transport_cart'] = [
        'source_wh' => null, 
        'items' => []        
    ];
}

// 1.A - Forrás raktár kiválasztása
if (isset($_POST['set_source'])) {
    $sourceId = (int)$_POST['source_wh_id'];
    
    if (!in_array($sourceId, $allowedWarehouseIds) && $userRole !== 'owner') {
        $message = "Nincs jogosultságod ebből a raktárból indítani!";
        $msgType = "danger";
    } elseif (empty($_SESSION['transport_cart']['items'])) {
        $_SESSION['transport_cart']['source_wh'] = $sourceId;
    } else {
        $message = "A forrás raktár nem módosítható, amíg van termék a listában!";
        $msgType = "danger";
    }
}

// 1.B - Termék hozzáadása a listához
if (isset($_POST['add_item'])) {
    $prodId = (int)$_POST['product_id'];
    $qty = (int)$_POST['quantity'];
    $prodName = $_POST['product_name']; 

    $stmt = $pdo->prepare("SELECT quantity FROM inventory WHERE product_ID = ? AND warehouse_ID = ?");
    $stmt->execute([$prodId, $_SESSION['transport_cart']['source_wh']]);
    $stock = $stmt->fetchColumn();

    if ($stock >= $qty && $qty > 0) {
        if (isset($_SESSION['transport_cart']['items'][$prodId])) {
            $_SESSION['transport_cart']['items'][$prodId]['qty'] += $qty;
        } else {
            $_SESSION['transport_cart']['items'][$prodId] = [
                'name' => $prodName,
                'qty' => $qty
            ];
        }
        if ($_SESSION['transport_cart']['items'][$prodId]['qty'] > $stock) {
            $_SESSION['transport_cart']['items'][$prodId]['qty'] = $stock; 
            $message = "A mennyiséget a maximális készletre korlátoztuk.";
            $msgType = "warning";
        }
    } else {
        $message = "Nincs ennyi termék a forrás raktárban!";
        $msgType = "danger";
    }
}

// 1.C - Tétel törlése
if (isset($_GET['remove_item'])) {
    $remId = (int)$_GET['remove_item'];

    if (isset($_SESSION['transport_cart']['items'][$remId])) {
        unset($_SESSION['transport_cart']['items'][$remId]);
    }

    header("Location: transports.php");
    exit;
}

// 1.D - Kosár ürítése
if (isset($_POST['clear_cart'])) {
    $_SESSION['transport_cart'] = ['source_wh' => null, 'items' => []];
    header("Location: transports.php");
    exit;
}

// --------------------------------------------------------
// 2. TRANZAKCIÓ INDÍTÁSA (FÜGGŐ ÁLLAPOT) - ADATBÁZIS MŰVELETEK

if (isset($_POST['finalize_transport'])) {
    if (empty($_SESSION['transport_cart']['items'])) {
        $message = "A lista üres!";
        $msgType = "danger";
    } elseif (empty($_POST['target_wh_id']) || $_POST['target_wh_id'] == $_SESSION['transport_cart']['source_wh']) {
        $message = "Érvénytelen célraktár!";
        $msgType = "danger";
    } else {
        try {
            $pdo->beginTransaction();

            $sourceWh = $_SESSION['transport_cart']['source_wh'];
            $targetWh = (int)$_POST['target_wh_id'];
            $description = $_POST['description'] ?? 'Átszállítás';
            $arriveDate = !empty($_POST['arrive_date']) ? $_POST['arrive_date'] : null;
            
            $batchId = generateBatchId(); 
            
            // JAVÍTÁS: Ellenőrizzük újra a készletet tranzakción belül!
            foreach ($_SESSION['transport_cart']['items'] as $pId => $item) {
                $qty = $item['qty'];

                // 1. Készlet ellenőrzése (Lockolhatnánk is FOR UPDATE-tel a precizitásért)
                $stmtCheck = $pdo->prepare("SELECT quantity, id FROM inventory WHERE product_ID = ? AND warehouse_ID = ?");
                $stmtCheck->execute([$pId, $sourceWh]);
                $invData = $stmtCheck->fetch(PDO::FETCH_ASSOC);

                if (!$invData || $invData['quantity'] < $qty) {
                    // Ha közben elfogyott, megszakítjuk a folyamatot
                    throw new Exception("Hiba: A '" . htmlspecialchars($item['name']) . "' termékből már nincs elegendő készlet!");
                }

                // 2. Forrás csökkentése (AZONNALI HATÁLY)
                $stmtSub = $pdo->prepare("UPDATE inventory SET quantity = quantity - ?, updated_at = NOW() WHERE id = ?");
                $stmtSub->execute([$qty, $invData['id']]);

                // 3. Naplózás (EXPORT) -> Status: COMPLETED
                $logExport = $pdo->prepare("
                    INSERT INTO transports (batch_id, product_ID, warehouse_ID, type, quantity, date, user_ID, description, arriveIn, status) 
                    VALUES (?, ?, ?, 'export', ?, NOW(), ?, ?, ?, 'completed')
                ");
                $descExport = "Kiszállítás cél: Raktár #$targetWh. ($description)";
                $logExport->execute([$batchId, $pId, $sourceWh, $qty, $userId, $descExport, $arriveDate]);

                // 4. Naplózás (IMPORT) -> Status: PENDING
                $logImport = $pdo->prepare("
                    INSERT INTO transports (batch_id, product_ID, warehouse_ID, type, quantity, date, user_ID, description, arriveIn, status) 
                    VALUES (?, ?, ?, 'import', ?, NOW(), ?, ?, ?, 'pending')
                ");
                $descImport = "Beérkezés forrás: Raktár #$sourceWh. ($description)";
                $logImport->execute([$batchId, $pId, $targetWh, $qty, $userId, $descImport, $arriveDate]);
            }

            $pdo->commit();
            $_SESSION['transport_cart'] = ['source_wh' => null, 'items' => []];
            $message = "A szállítás elindítva! Státusz: Függőben (Pending). Azonosító: $batchId";
            $msgType = "success";

        } catch (Exception $e) {
            $pdo->rollBack();
            $message = "Hiba: " . $e->getMessage();
            $msgType = "danger";
        }
    }
}


// --------------------------------------------------------
// 2.B - SAJÁT SZÁLLÍTÁS VISSZAVONÁSA (CANCEL)

if (isset($_POST['cancel_batch'])) {
    $cancelBatchId = $_POST['cancel_batch_id'];

    try {
        $pdo->beginTransaction();

        // 1. Ellenőrzés: Létezik-e, az enyém-e, és még pending-e?
        // Az 'import' sort nézzük, mert az hordozza a pending státuszt
        $stmtCheck = $pdo->prepare("
            SELECT ID FROM transports 
            WHERE batch_id = ? AND user_ID = ? AND type = 'import' AND status = 'pending'
        ");
        $stmtCheck->execute([$cancelBatchId, $userId]);
        
        if (!$stmtCheck->fetch()) {
            throw new Exception("A szállítmány nem található, nem te indítottad, vagy már átvették/törölték.");
        }

        // 2. Visszavételezés: Megkeressük az EXPORT sorokat, hogy tudjuk mit és hova kell visszarakni
        // Ezek voltak azok, amiket levontunk a forrásraktárból (status: completed)
        $stmtExports = $pdo->prepare("
            SELECT product_ID, warehouse_ID, quantity 
            FROM transports 
            WHERE batch_id = ? AND type = 'export'
        ");
        $stmtExports->execute([$cancelBatchId]);
        $itemsToRestore = $stmtExports->fetchAll(PDO::FETCH_ASSOC);

        foreach ($itemsToRestore as $item) {
            // Visszaadjuk a készletet a forrásraktárhoz
            $stmtRestock = $pdo->prepare("
                UPDATE inventory 
                SET quantity = quantity + ?, updated_at = NOW() 
                WHERE product_ID = ? AND warehouse_ID = ?
            ");
            $stmtRestock->execute([$item['quantity'], $item['product_ID'], $item['warehouse_ID']]);
            
            // Ha esetleg időközben törölték volna a sort az inventoryból (ritka), újra létre kéne hozni,
            // de a rendszer logikája szerint a 0-ás sorok megmaradnak, így az UPDATE elég.
        }

        // 3. Státusz frissítése CANCELED-re (minden sort a batch-ben)
        $stmtCancel = $pdo->prepare("UPDATE transports SET status = 'canceled' WHERE batch_id = ?");
        $stmtCancel->execute([$cancelBatchId]);

        $pdo->commit();
        $message = "A szállítás sikeresen visszavonva! A termékek visszakerültek a forrásraktárba.";
        $msgType = "success";

    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Hiba a visszavonáskor: " . $e->getMessage();
        $msgType = "danger";
    }
}


// --------------------------------------------------------
// 3. MEGJELENÍTÉSHEZ SZÜKSÉGES ADATOK

// Csak aktív raktárak legyenek a célállomás listában
$allWarehouses = $pdo->query("SELECT * FROM warehouses WHERE active = 1 ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$sourceInventory = [];
if ($_SESSION['transport_cart']['source_wh']) {
        $stmtSrc = $pdo->prepare("
            SELECT p.ID, p.name, p.item_number, i.quantity 
            FROM inventory i 
            JOIN products p ON i.product_ID = p.ID 
            JOIN warehouses w ON i.warehouse_ID = w.ID
            WHERE i.warehouse_ID = ? AND i.quantity > 0 AND w.active = 1
            ORDER BY p.name
        ");
    $stmtSrc->execute([$_SESSION['transport_cart']['source_wh']]);
    $sourceInventory = $stmtSrc->fetchAll(PDO::FETCH_ASSOC);
}

// BEJÖVŐ FÜGGŐ SZÁLLÍTMÁNYOK
$pendingQuery = "
    SELECT t.batch_id, t.date, t.arriveIn, t.description, w.name as target_wh_name, COUNT(t.ID) as item_count
    FROM transports t
    JOIN warehouses w ON t.warehouse_ID = w.ID
    WHERE t.type = 'import' AND t.status = 'pending'
";

if ($userRole !== 'owner') {
    if (!empty($allowedWarehouseIds)) {
        $inClause = implode(',', array_map('intval', $allowedWarehouseIds));
        $pendingQuery .= " AND t.warehouse_ID IN ($inClause)";
    } else {
        $pendingQuery .= " AND 1=0"; 
    }
}

$pendingQuery .= " GROUP BY t.batch_id ORDER BY t.date DESC";
$pendingTransports = $pdo->query($pendingQuery)->fetchAll(PDO::FETCH_ASSOC);


// SAJÁT INDÍTOTT, FÜGGŐBEN LÉVŐ SZÁLLÍTMÁNYOK (Visszavonható)
$myOutgoingQuery = "
    SELECT t.batch_id, t.date, t.arriveIn, w.name as target_wh_name, COUNT(t.ID) as item_count
    FROM transports t
    JOIN warehouses w ON t.warehouse_ID = w.ID
    WHERE t.user_ID = ? 
      AND t.type = 'import' 
      AND t.status = 'pending'
    GROUP BY t.batch_id 
    ORDER BY t.date DESC
";
$stmtMyOutgoing = $pdo->prepare($myOutgoingQuery);
$stmtMyOutgoing->execute([$userId]);
$myOutgoingTransports = $stmtMyOutgoing->fetchAll(PDO::FETCH_ASSOC);


?>