<?php
session_start();
include 'config.php';
require "./components/auth_check.php";
authorize(['owner']);

$pageTitle = "Rendszer";
$activePage = "owner.php";
include './components/navbar.php';

$message = "";

// Raktárak betöltése a legördülő listákhoz (Csak aktívak)
$warehouses_for_select = $pdo->query("SELECT * FROM warehouses WHERE active = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

/* |--------------------------------------------------------------------------
| 1. LOGIKA: ÚJ FELHASZNÁLÓ LÉTREHOZÁSA (Több raktárral + Owner logika)
|-------------------------------------------------------------------------- */
if (isset($_POST['add_user'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password_raw = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    // Checkboxokból érkező tömb
    $selected_whs = $_POST['warehouse_ids'] ?? [];

    $now = date('Y-m-d H:i:s');

    if (!empty($username) && !empty($password_raw) && !empty($email)) {
        try {
            $pdo->beginTransaction();

            $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);

            // 1. Felhasználó beszúrása
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role, active, created_at, login_at) VALUES (?, ?, ?, ?, 1, ?, ?)");
            $stmt->execute([$username, $email, $password_hash, $role, $now, $now]);

            $new_user_id = $pdo->lastInsertId();

            // 2. Kapcsolótábla feltöltése (CSAK HA NEM OWNER)
            // Az Ownernek programozottan van joga mindenhez, nem kell DB bejegyzés
            if ($role !== 'owner' && !empty($selected_whs)) {
                $stmtAccess = $pdo->prepare("INSERT INTO user_warehouse_access (user_id, warehouse_id) VALUES (?, ?)");
                foreach ($selected_whs as $wh_id) {
                    $stmtAccess->execute([$new_user_id, $wh_id]);
                }
            }

            $pdo->commit();
            $message = "Sikeresen létrehozva: $username";
        } catch (PDOException $e) {
            $pdo->rollBack();
            if ($e->getCode() == 23000) {
                $message = "Hiba: A felhasználónév vagy az email cím már foglalt!";
            } else {
                $message = "Hiba: " . $e->getMessage();
            }
        }
    } else {
        $message = "Hiba: Minden kötelező mezőt (Név, Email, Jelszó) ki kell tölteni!";
    }
}

/* |--------------------------------------------------------------------------
| 2. LOGIKA: FELHASZNÁLÓ MÓDOSÍTÁSA (Több raktárral + Owner logika)
|-------------------------------------------------------------------------- */
if (isset($_POST['update_user'])) {
    $id = $_POST['user_id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $active = isset($_POST['active']) ? 1 : 0;

    // A kiválasztott raktárak tömbje
    $selected_whs = $_POST['warehouse_ids'] ?? [];

    try {
        $pdo->beginTransaction();

        // 1. Alapadatok frissítése
        if (!empty($_POST['new_password'])) {
            $password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ?, password = ?, active = ? WHERE ID = ?");
            $stmt->execute([$username, $email, $role, $password, $active, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, role = ?, active = ? WHERE ID = ?");
            $stmt->execute([$username, $email, $role, $active, $id]);
        }

        // 2. Jogosultságok szinkronizálása
        // Először töröljük a felhasználó összes eddigi jogosultságát
        $delStmt = $pdo->prepare("DELETE FROM user_warehouse_access WHERE user_id = ?");
        $delStmt->execute([$id]);

        // Majd beszúrjuk az újakat (HA NEM OWNER)
        if ($role !== 'owner' && !empty($selected_whs)) {
            $insStmt = $pdo->prepare("INSERT INTO user_warehouse_access (user_id, warehouse_id) VALUES (?, ?)");
            foreach ($selected_whs as $wh_id) {
                $insStmt->execute([$id, $wh_id]);
            }
        }

        $pdo->commit();
        $message = "Adatok és jogosultságok frissítve!";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $message = "Hiba: " . $e->getMessage();
    }
}

/* |--------------------------------------------------------------------------
| 3. LOGIKA: ÚJ RAKTÁR
|-------------------------------------------------------------------------- */
if (isset($_POST['add_warehouse'])) {
    $name = trim($_POST['w_name'] ?? '');
    $type = $_POST['w_type'] ?? 'warehouse';
    $address = trim($_POST['w_address'] ?? '');
    $max_q = (int)($_POST['w_max_q'] ?? 0);

    if (!empty($name) && !empty($address)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO warehouses (name, type, address, max_quantity, active) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$name, $type, $address, $max_q]);
            $message = "Sikeresen létrehozva: $name";
        } catch (PDOException $e) {
            $message = "Hiba: " . $e->getMessage();
        }
    }
}

/* |--------------------------------------------------------------------------
| 4. LOGIKA: RAKTÁR MÓDOSÍTÁSA
|-------------------------------------------------------------------------- */
if (isset($_POST['update_warehouse'])) {
    $id = $_POST['w_id'];
    $name = trim($_POST['w_name']);
    $type = $_POST['w_type'];
    $address = trim($_POST['w_address']);
    $max_q = (int)$_POST['w_max_q'];
    $active = isset($_POST['w_active']) ? 1 : 0;

    try {
        $stmt = $pdo->prepare("UPDATE warehouses SET name = ?, type = ?, address = ?, max_quantity = ?, active = ? WHERE ID = ?");
        $stmt->execute([$name, $type, $address, $max_q, $active, $id]);
        $message = "Raktár adatai frissítve!";
    } catch (PDOException $e) {
        $message = "Hiba: " . $e->getMessage();
    }
}

/* |--------------------------------------------------------------------------
| 5. LOGIKA: HIBAJEGY LEZÁRÁSA
|-------------------------------------------------------------------------- */
if (isset($_POST['resolve_error'])) {
    $errID = (int)$_POST['error_id'];
    try {
        $stmt = $pdo->prepare("UPDATE user_error SET status = 'complete', completed_at = NOW() WHERE errID = ?");
        $stmt->execute([$errID]);
        $message = "Hibajegy (#$errID) lezárva.";
    } catch (PDOException $e) {
        $message = "Hiba: " . $e->getMessage();
    }
}

// --- LEKÉRDEZÉSEK ---

// Aktív hibajegyek
$errorReports = $pdo->query("
    SELECT e.*, u.username as registered_username 
    FROM user_error e 
    LEFT JOIN users u ON e.user_ID = u.ID 
    WHERE e.status = 'incomplete' 
    ORDER BY e.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Napló szűrők
$f_batch  = isset($_GET['f_batch']) ? trim($_GET['f_batch']) : '';
$f_user   = isset($_GET['f_user'])  ? (int)$_GET['f_user']  : 0;
$f_date   = isset($_GET['f_date'])  ? $_GET['f_date']       : '';
$f_status = isset($_GET['f_status']) ? $_GET['f_status']    : '';

// Dinamikusan nyitva tartjuk a fület, ha van szűrés
$isLogOpen = (!empty($f_batch) || $f_user > 0 || !empty($f_date) || !empty($f_status));

// Napló lekérdezés
$logQuery = "
    SELECT 
        t.batch_id, 
        t.date, 
        t.status,
        u.username,
        MAX(CASE WHEN t.type = 'export' THEN w.name END) as source_wh,
        MAX(CASE WHEN t.type = 'import' THEN w.name END) as target_wh
    FROM transports t
    LEFT JOIN users u ON t.user_ID = u.ID
    LEFT JOIN warehouses w ON t.warehouse_ID = w.ID
    WHERE 1=1
";

$logParams = [];
if (!empty($f_batch)) {
    $logQuery .= " AND t.batch_id LIKE ?";
    $logParams[] = "%$f_batch%";
}
if ($f_user > 0) {
    $logQuery .= " AND t.user_ID = ?";
    $logParams[] = $f_user;
}
if (!empty($f_date)) {
    $logQuery .= " AND DATE(t.date) = ?";
    $logParams[] = $f_date;
}
if (!empty($f_status)) {
    $logQuery .= " AND t.status = ?";
    $logParams[] = $f_status;
}

$logQuery .= " GROUP BY t.batch_id ORDER BY t.date DESC LIMIT 50";
$stmtLog = $pdo->prepare($logQuery);
$stmtLog->execute($logParams);
$logs = $stmtLog->fetchAll(PDO::FETCH_ASSOC);

// Felhasználók lekérdezése
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// **FONTOS**: Lekérjük a felhasználó-raktár kapcsolatokat a checkboxok bejelöléséhez
$user_access_map = [];
$access_rows = $pdo->query("SELECT user_id, warehouse_id FROM user_warehouse_access")->fetchAll(PDO::FETCH_ASSOC);
foreach ($access_rows as $row) {
    $user_access_map[$row['user_id']][] = $row['warehouse_id'];
}

$warehouses = $pdo->query("SELECT * FROM warehouses ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <title>Tulajdonosi Panel | CRUD-X</title>
    <link rel="stylesheet" href="./style/owner.css">
    <link rel="stylesheet" href="./style/style.css">
    <style>
    </style>
</head>

<body>

    <main class="container">
        <h1 style="margin: 20px 0;">Tulajdonosi Vezérlőpult</h1>
        <?php if ($message): ?>
            <div style="background: #f0fdf4; color: #1e40af; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bfdbfe;">
                <?= $message ?>
            </div>
        <?php endif; ?>

        <div class="management-grid">
            <div class="forms-stack">

                <section class="card">
                    <div style="padding: 20px; border-bottom: 1px solid #eee;">
                        <h2><img class="icon" src="./img/create-group-button_icon-icons.com_72792.png"> Új felhasználó</h2>
                    </div>
                    <div style="padding: 20px;">
                        <form method="POST">
                            <label style="display:block; margin-bottom:5px; font-weight:600;">Felhasználónév</label>
                            <input type="text" max="30" name="username" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:6px;">

                            <label style="display:block; margin-bottom:5px; font-weight:600;">Email cím</label>
                            <input type="email" max="255" name="email" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:6px;">

                            <label style="display:block; margin-bottom:5px; font-weight:600;">Jelszó</label>
                            <input type="password" max="30" name="password" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:6px;">

                            <label style="display:block; margin-bottom:5px; font-weight:600;">Szerepkör</label>
                            <select name="role" id="new_user_role" onchange="toggleWarehouseSelect('new_user_role', 'new_user_whs')" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:6px;">
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                                <option value="owner">Owner</option>
                            </select>

                            <div id="new_user_whs">
                                <label style="display:block; margin-bottom:5px; font-weight:600;">Hozzárendelt raktárak</label>
                                <div class="checkbox-list">
                                    <?php foreach ($warehouses_for_select as $wh): ?>
                                        <div class="checkbox-item">
                                            <input type="checkbox" name="warehouse_ids[]" value="<?= $wh['ID'] ?>" id="wh_new_<?= $wh['ID'] ?>">
                                            <label for="wh_new_<?= $wh['ID'] ?>"><?= htmlspecialchars($wh['name']) ?></label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div id="new_user_whs_msg" class="owner-access-info">
                                <strong>Global Admin:</strong> Teljes hozzáférés minden raktárhoz.
                            </div>

                            <button type="submit" name="add_user" class="btn-primary-blue" style="margin-top:15px;">Létrehozás</button>
                        </form>
                    </div>
                </section>

                <section class="card">
                    <div style="padding: 20px; border-bottom: 1px solid #eee;">
                        <h2><img class="icon" src="./img/warehouse_icon_180427.png"> Új raktár / üzlet</h2>
                    </div>
                    <div style="padding: 20px;">
                        <form method="POST">
                            <label style="display:block; margin-bottom:5px; font-weight:600;">Név</label>
                            <input type="text" max="255" name="w_name" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:6px;">
                            <label style="display:block; margin-bottom:5px; font-weight:600;">Típus</label>
                            <select name="w_type" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:6px;">
                                <option value="warehouse">Raktár</option>
                                <option value="store">Üzlet</option>
                            </select>
                            <label style="display:block; margin-bottom:5px; font-weight:600;">Cím</label>
                            <input type="text" max="255" name="w_address" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:6px;">
                            <label style="display:block; margin-bottom:5px; font-weight:600;">Max kapacitás (db)</label>
                            <input type="number" min="0" name="w_max_q" required style="width:100%; padding:10px; margin-bottom:20px; border:1px solid #ddd; border-radius:6px;">
                            <button type="submit" name="add_warehouse" class="btn-primary-blue">Rögzítés</button>
                        </form>
                    </div>
                </section>
            </div>

            <div class="lists-stack">

                <section class="card" style="border-left: 4px solid #f97316;">
                    <div class="card-header-toggle" onclick="toggleSection('errorContent', this)">
                        <h2><img class="icon" src="./img/icons8-error-48.png"> Aktív segítségkérések (<?= count($errorReports) ?>)</h2>
                        <span class="toggle-icon">▼</span>
                    </div>
                    <div id="errorContent">
                        <?php if (empty($errorReports)): ?>
                            <p style="padding:15px; color:#666;">Nincs aktív hibajelzés.</p>
                        <?php else: ?>
                            <div style="overflow-x: auto;">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Dátum</th>
                                            <th>Azonosított User</th>
                                            <th>Megadott Név</th>
                                            <th>Megadott Email</th>
                                            <th>Művelet</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($errorReports as $err): ?>
                                            <tr>
                                                <td><?= $err['created_at'] ?></td>
                                                <td><?= $err['registered_username'] ? '<strong>' . htmlspecialchars($err['registered_username']) . '</strong>' : '<span style="color:#aaa;">-</span>' ?></td>
                                                <td><?= htmlspecialchars($err['username'] ?? '-') ?></td>
                                                <td><?= htmlspecialchars($err['email'] ?? '-') ?></td>
                                                <td>
                                                    <form method="POST">
                                                        <input type="hidden" name="error_id" value="<?= $err['errID'] ?>">
                                                        <button type="submit" name="resolve_error" class="btn-success-sm">Elvégezve</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header-toggle collapsed" onclick="toggleSection('userContent', this)">
                        <h2><img class="icon" src="./img/users_icon_197608.png"> Felhasználók kezelése</h2>
                        <span class="toggle-icon">▼</span>
                    </div>
                    <div id="userContent" class="is-hidden">
                        <div class="search-container">
                            <input type="text" id="userSearch" class="search-input" placeholder="Keresés névre..." onkeyup="filterUsers()">
                        </div>
                        <div style="overflow-x: auto;">
                            <table class="data-table" id="userTable">
                                <thead>
                                    <tr>
                                        <th>Név / Email</th>
                                        <th>Jog</th>
                                        <th>Raktárak</th>
                                        <th>Jelszó</th>
                                        <th>Aktív</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $u): ?>
                                        <tr class="user-row">
                                            <form method="POST">
                                                <input type="hidden" name="user_id" value="<?= $u['ID'] ?>">
                                                <td>
                                                    <input type="text" name="username" value="<?= htmlspecialchars($u['username']) ?>" style="width:130px; padding:5px; border-radius:4px; border:1px solid #ddd; margin-bottom:4px;"><br>
                                                    <input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>" style="width:130px; padding:5px; border-radius:4px; border:1px solid #ddd; font-size:0.75rem;">
                                                </td>
                                                <td>
                                                    <select name="role" id="role_<?= $u['ID'] ?>" onchange="toggleWarehouseSelect('role_<?= $u['ID'] ?>', 'wh_box_<?= $u['ID'] ?>')" style="padding:5px; border-radius:4px; border:1px solid #ddd;">
                                                        <option value="user" <?= $u['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                                        <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                                        <option value="owner" <?= $u['role'] == 'owner' ? 'selected' : '' ?>>Owner</option>
                                                    </select>
                                                </td>
                                                <td style="min-width: 200px;">
                                                    <?php $isOwner = ($u['role'] === 'owner'); ?>

                                                    <div id="wh_box_<?= $u['ID'] ?>" style="display: <?= $isOwner ? 'none' : 'block' ?>;">
                                                        <div class="checkbox-list" style="max-height: 100px;">
                                                            <?php foreach ($warehouses_for_select as $wh):
                                                                // Ellenőrizzük, hogy a usernek van-e joga ehhez a raktárhoz
                                                                $checked = (isset($user_access_map[$u['ID']]) && in_array($wh['ID'], $user_access_map[$u['ID']])) ? 'checked' : '';
                                                            ?>
                                                                <div class="checkbox-item">
                                                                    <input type="checkbox" name="warehouse_ids[]" value="<?= $wh['ID'] ?>" <?= $checked ?>>
                                                                    <label><?= htmlspecialchars($wh['name']) ?></label>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    </div>

                                                    <div id="wh_box_<?= $u['ID'] ?>_msg" class="owner-access-info" style="display: <?= $isOwner ? 'block' : 'none' ?>;">
                                                        Teljes hozzáférés.
                                                    </div>
                                                </td>
                                                <td><input type="password" name="new_password" placeholder="Új jelszó..." style="width:80px; padding:5px; border-radius:4px; border:1px solid #ddd;"></td>
                                                <td style="text-align:center;"><input type="checkbox" name="active" <?= $u['active'] ? 'checked' : '' ?>></td>
                                                <td><button type="submit" name="update_user" class="btn-save-sm">Mentés</button></td>
                                            </form>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header-toggle collapsed" onclick="toggleSection('warehouseContent', this)">
                        <h2><img class="icon" src="./img/create_117333.png"> Raktárak és Üzletek kezelése</h2>
                        <span class="toggle-icon">▼</span>
                    </div>
                    <div id="warehouseContent" class="is-hidden">
                        <div class="search-container">
                            <input type="text" id="warehouseSearch" class="search-input" placeholder="Keresés névre vagy címre..." onkeyup="filterWarehouses()">
                        </div>
                        <div style="overflow-x: auto;">
                            <table class="data-table" id="warehouseTable">
                                <thead>
                                    <tr>
                                        <th>Név</th>
                                        <th>Típus</th>
                                        <th>Cím</th>
                                        <th>Kapacitás</th>
                                        <th>Aktív</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($warehouses as $w): ?>
                                        <tr class="warehouse-row">
                                            <form method="POST">
                                                <input type="hidden" name="w_id" value="<?= $w['ID'] ?>">
                                                <td><input type="text" name="w_name" value="<?= htmlspecialchars($w['name']) ?>" style="width:130px; padding:5px; border-radius:4px; border:1px solid #ddd;"></td>
                                                <td>
                                                    <select name="w_type" style="padding:5px; border-radius:4px; border:1px solid #ddd;">
                                                        <option value="warehouse" <?= $w['type'] == 'warehouse' ? 'selected' : '' ?>>Raktár</option>
                                                        <option value="store" <?= $w['type'] == 'store' ? 'selected' : '' ?>>Üzlet</option>
                                                    </select>
                                                </td>
                                                <td><input type="text" name="w_address" value="<?= htmlspecialchars($w['address']) ?>" style="width:150px; padding:5px; border-radius:4px; border:1px solid #ddd;"></td>
                                                <td><input type="number" name="w_max_q" value="<?= (int)$w['max_quantity'] ?>" style="width:70px; padding:5px; border-radius:4px; border:1px solid #ddd;"></td>
                                                <td style="text-align:center;"><input type="checkbox" name="w_active" <?= $w['active'] ? 'checked' : '' ?>></td>
                                                <td><button type="submit" name="update_warehouse" class="btn-save-sm">Mentés</button></td>
                                            </form>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header-toggle <?= $isLogOpen ? '' : 'collapsed' ?>" onclick="toggleSection('logContent', this)">
                        <h2><img class="icon" src="./img/logging_cloud_icon_215864.png"> Készletmozgási Napló</h2>
                        <span class="toggle-icon">▼</span>
                    </div>

                    <div id="logContent" class="<?= $isLogOpen ? '' : 'is-hidden' ?>">
                        <form method="GET" class="filter-bar">
                            <div class="filter-item">
                                <label>Batch ID</label>
                                <input type="text" name="f_batch" value="<?= htmlspecialchars($f_batch) ?>" placeholder="TR-...">
                            </div>
                            <div class="filter-item">
                                <label>User</label>
                                <select name="f_user">
                                    <option value="0">Összes</option>
                                    <?php foreach ($users as $user_opt): ?>
                                        <option value="<?= $user_opt['ID'] ?>" <?= $f_user == $user_opt['ID'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($user_opt['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label>Státusz</label>
                                <select name="f_status">
                                    <option value="">Összes</option>
                                    <option value="pending" <?= $f_status == 'pending' ? 'selected' : '' ?>>Függő</option>
                                    <option value="completed" <?= $f_status == 'completed' ? 'selected' : '' ?>>Befejezett</option>
                                    <option value="canceled" <?= $f_status == 'canceled' ? 'selected' : '' ?>>Törölt</option>
                                </select>
                            </div>
                            <div class="filter-item">
                                <label>Dátum</label>
                                <input type="date" name="f_date" value="<?= $f_date ?>">
                            </div>
                            <button type="submit" class="btn-save-sm" style="padding: 8px 15px;">Szűrés</button>
                            <a href="owner.php" style="font-size: 0.75rem; color: #64748b; margin-bottom: 8px;">Visszaállítás</a>
                        </form>

                        <div style="overflow-x: auto;">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Batch ID</th>
                                        <th>Időpont</th>
                                        <th>Státusz</th>
                                        <th>User</th>
                                        <th>Kiinduló raktár</th>
                                        <th>Cél raktár</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td class="batch-id-cell" onclick="location.href='transport.php?batch=<?= $log['batch_id'] ?>'">
                                                <a href="transport.php?batch=<?= $log['batch_id'] ?>"><?= htmlspecialchars($log['batch_id']) ?></a>
                                            </td>
                                            <td style="font-family: monospace; font-size: 0.8rem;"><?= $log['date'] ?></td>
                                            <td>
                                                <?php if ($log['status'] === 'pending'): ?>
                                                    <span class="pill-pending">Függő</span>
                                                <?php elseif ($log['status'] === 'completed'): ?>
                                                    <span class="pill-completed">Kész</span>
                                                <?php else: ?>
                                                    <span class="pill-completed" style="background:#eee; color:#666; border-color:#ccc;">Egyéb</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><strong><?= htmlspecialchars($log['username'] ?? 'Rendszer') ?></strong></td>
                                            <td><span class="status-badge status-inactive"><?= htmlspecialchars($log['source_wh'] ?? 'Ismeretlen') ?></span></td>
                                            <td><span class="status-badge status-active"><?= htmlspecialchars($log['target_wh'] ?? 'Ismeretlen') ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($logs)): ?>
                                        <tr>
                                            <td colspan="6" style="text-align:center; opacity:.6; padding: 20px;">Nincs találat a szűrőkre.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <?php include './components/footer.php'; ?>

    <script>
        function toggleSection(id, header) {
            const content = document.getElementById(id);
            content.classList.toggle('is-hidden');
            header.classList.toggle('collapsed');
            header.parentElement.style.transform = 'scale(0.995)';
            setTimeout(() => {
                header.parentElement.style.transform = 'scale(1)';
            }, 100);
        }

        function filterUsers() {
            const input = document.getElementById('userSearch');
            const filter = input.value.toLowerCase();
            const rows = document.getElementsByClassName('user-row');
            for (let i = 0; i < rows.length; i++) {
                const usernameInput = rows[i].querySelector('input[name="username"]');
                if (usernameInput) {
                    const val = usernameInput.value.toLowerCase();
                    rows[i].style.display = val.includes(filter) ? "" : "none";
                }
            }
        }

        function filterWarehouses() {
            const input = document.getElementById('warehouseSearch');
            const filter = input.value.toLowerCase();
            const rows = document.getElementsByClassName('warehouse-row');
            for (let i = 0; i < rows.length; i++) {
                const nameInput = rows[i].querySelector('input[name="w_name"]');
                const addrInput = rows[i].querySelector('input[name="w_address"]');
                if (nameInput && addrInput) {
                    const val = (nameInput.value + addrInput.value).toLowerCase();
                    rows[i].style.display = val.includes(filter) ? "" : "none";
                }
            }
        }

        // ÚJ JS Logika: Ha 'owner'-re vált, eltűnik a raktárválasztó
        function toggleWarehouseSelect(roleSelectId, whBoxId) {
            const roleSelect = document.getElementById(roleSelectId);
            const whBox = document.getElementById(whBoxId);
            const msgBox = document.getElementById(whBoxId + "_msg");

            if (roleSelect.value === 'owner') {
                whBox.style.display = 'none';
                if (msgBox) msgBox.style.display = 'block';
            } else {
                whBox.style.display = 'block';
                if (msgBox) msgBox.style.display = 'none';
            }
        }
    </script>
</body>

</html>