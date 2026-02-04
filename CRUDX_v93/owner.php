<?php
session_start();
include 'config.php';
require "./components/auth_check.php";
authorize(['owner']);

$pageTitle = "Rendszer";
$activePage = "owner.php";
include './components/navbar.php'; 

$message = "";

// Raktárak betöltése
$warehouses_for_select = $pdo->query("SELECT * FROM warehouses WHERE active = 1 ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

/* 1. ÚJ FELHASZNÁLÓ */
if (isset($_POST['add_user'])) {
    $username = trim($_POST['username'] ?? '');
    $password_raw = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $wh_id = !empty($_POST['warehouse_id']) ? $_POST['warehouse_id'] : null;
    $now = date('Y-m-d H:i:s');

    if (!empty($username) && !empty($password_raw)) {
        $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, warehouse_id, active, created_at, login_at) VALUES (?, ?, ?, ?, 1, ?, ?)");
            $stmt->execute([$username, $password_hash, $role, $wh_id, $now, $now]);
            
            if($wh_id) {
                $uid = $pdo->lastInsertId();
                $stmtAccess = $pdo->prepare("INSERT INTO user_warehouse_access (user_id, warehouse_id) VALUES (?, ?)");
                $stmtAccess->execute([$uid, $wh_id]);
            }
            
            $message = "Sikeresen létrehozva: $username";
        } catch (PDOException $e) {
            $message = "Hiba: " . ($e->getCode() == 23000 ? "A felhasználónév már foglalt!" : $e->getMessage());
        }
    } else {
        $message = "Hiba: Minden kötelező mezőt ki kell tölteni!";
    }
}

/* 2. FELHASZNÁLÓ MÓDOSÍTÁSA */
if (isset($_POST['update_user'])) {
    $id = $_POST['user_id'];
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $active = isset($_POST['active']) ? 1 : 0;
    
    try {
        if (!empty($_POST['new_password'])) {
            $password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ?, password = ?, active = ? WHERE ID = ?");
            $stmt->execute([$username, $role, $password, $active, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ?, role = ?, active = ? WHERE ID = ?");
            $stmt->execute([$username, $role, $active, $id]);
        }
        $message = "Adatok frissítve!";
    } catch (PDOException $e) {
        $message = "Hiba: " . $e->getMessage();
    }
}

/* 3. ÚJ RAKTÁR */
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

/* 4. RAKTÁR MÓDOSÍTÁSA */
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

// --- NAPLÓ SZŰRŐK ---
$f_batch  = isset($_GET['f_batch']) ? trim($_GET['f_batch']) : '';
$f_user   = isset($_GET['f_user'])  ? (int)$_GET['f_user']  : 0;
$f_date   = isset($_GET['f_date'])  ? $_GET['f_date']       : '';
$f_status = isset($_GET['f_status']) ? $_GET['f_status']    : '';

// JAVÍTÁS: Ellenőrizzük, van-e aktív szűrés. Ha igen, nyitva hagyjuk a fület.
$isLogOpen = (!empty($f_batch) || $f_user > 0 || !empty($f_date) || !empty($f_status));

// --- NAPLÓ LEKÉRDEZÉS ---
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

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$warehouses = $pdo->query("SELECT * FROM warehouses ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>Tulajdonosi Panel | CRUD-X</title>
    <link rel="stylesheet" href="./style/style.css">
    <style>
        .management-grid { display: grid; grid-template-columns: 350px 1fr; gap: 20px; margin-top: 20px; align-items: start; }
        .card { background: white; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 20px; border: none; }
        .data-table { width: 100%; border-collapse: collapse; background: white; }
        .data-table thead th { background-color: #0f172a; color: #ffffff; padding: 15px; text-align: left; font-weight: 600; font-size: 0.9rem; }
        .data-table tbody td { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        .card-header-toggle { padding: 20px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; }
        .toggle-icon { transition: transform 0.3s; }
        .collapsed .toggle-icon { transform: rotate(-90deg); }
        .is-hidden { display: none; }
        .search-container { padding: 10px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        .search-input { width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; }
        .btn-primary-blue { background: #0f172a; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; width: 100%; font-weight: 600; }
        .btn-save-sm { background: #0f172a; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }
        .batch-id-cell { position: relative; cursor: pointer; transition: background 0.2s; }
        .batch-id-cell:hover { background: #f1f5f9 !important; }
        .batch-id-cell a { text-decoration: none; color: #2563eb; font-family: monospace; font-weight: bold; }
        .batch-id-cell::after { content: '→'; position: absolute; right: 15px; top: 50%; transform: translateY(-50%); opacity: 0; transition: all 0.2s; color: #2563eb; }
        .batch-id-cell:hover::after { opacity: 1; right: 8px; }
        .filter-bar { display: flex; flex-wrap: wrap; gap: 10px; padding: 15px 20px; background: #f1f5f9; border-bottom: 1px solid #e2e8f0; align-items: flex-end; }
        .filter-item { display: flex; flex-direction: column; gap: 4px; }
        .filter-item label { font-size: 0.75rem; font-weight: bold; color: #64748b; }
        .filter-item input, .filter-item select { padding: 6px; border: 1px solid #cbd5e1; border-radius: 4px; }
        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }
        .status-active { background: #dcfce7; color: #16a34a; }
        .status-inactive { background: #fee2e2; color: #dc2626; }
        .pill-pending { background: #fffbeb; color: #d97706; border: 1px solid #fcd34d; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem;}
        .pill-completed { background: #dcfce7; color: #16a34a; border: 1px solid #86efac; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem;}
        @media (max-width: 900px) { .management-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<main class="container">
    <h1 style="margin: 20px 0;">Tulajdonosi Vezérlőpult</h1>
    <?php if($message): ?>
        <div style="background: #f0fdf4; color: #1e40af; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #bfdbfe;">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <div class="management-grid">
        <div class="forms-stack">
            <section class="card">
                <div style="padding: 20px; border-bottom: 1px solid #eee;"><h2><img class="icon" src="./img/create-group-button_icon-icons.com_72792.png">  Új felhasználó</h2></div>
                <div style="padding: 20px;">
                    <form method="POST">
                        <label style="display:block; margin-bottom:5px; font-weight:600;">Felhasználónév</label>
                        <input type="text" max="30" name="username" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:6px;">
                        <label style="display:block; margin-bottom:5px; font-weight:600;">Jelszó</label>
                        <input type="password" max="30" name="password" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:6px;">
                        <label style="display:block; margin-bottom:5px; font-weight:600;">Szerepkör</label>
                        <select name="role" style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:6px;">
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                            <option value="owner">Owner</option>
                        </select>
                        <label style="display:block; margin-bottom:5px; font-weight:600;">Elsődleges Raktár (Opcionális)</label>
                        <select name="warehouse_id" style="width:100%; padding:10px; margin-bottom:20px; border:1px solid #ddd; border-radius:6px;">
                            <option value="">-- Nincs --</option>
                            <?php foreach($warehouses_for_select as $wh): ?>
                                <option value="<?= $wh['ID'] ?>"><?= htmlspecialchars($wh['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button type="submit" name="add_user" class="btn-primary-blue">Létrehozás</button>
                    </form>
                </div>
            </section>

            <section class="card">
                <div style="padding: 20px; border-bottom: 1px solid #eee;"><h2><img class="icon" src="./img/warehouse_icon_180427.png"> Új raktár / üzlet</h2></div>
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
                            <thead><tr><th>Név</th><th>Jog</th><th>Jelszó</th><th>Aktív</th><th></th></tr></thead>
                            <tbody>
                                <?php foreach ($users as $u): ?>
                                <tr class="user-row">
                                    <form method="POST">
                                        <input type="hidden" name="user_id" value="<?= $u['ID'] ?>">
                                        <td><input type="text" name="username" value="<?= htmlspecialchars($u['username']) ?>" style="width:120px; padding:5px; border-radius:4px; border:1px solid #ddd;"></td>
                                        <td>
                                            <select name="role" style="padding:5px; border-radius:4px; border:1px solid #ddd;">
                                                <option value="user" <?= $u['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                                <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                                <option value="owner" <?= $u['role'] == 'owner' ? 'selected' : '' ?>>Owner</option>
                                            </select>
                                        </td>
                                        <td><input type="password" name="new_password" placeholder="Új jelszó..." style="width:100px; padding:5px; border-radius:4px; border:1px solid #ddd;"></td>
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
                            <thead><tr><th>Név</th><th>Típus</th><th>Cím</th><th>Kapacitás</th><th>Aktív</th><th></th></tr></thead>
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
                                <?php foreach($users as $user_opt): ?>
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
                                        <?php if($log['status'] === 'pending'): ?>
                                            <span class="pill-pending">Függő</span>
                                        <?php elseif($log['status'] === 'completed'): ?>
                                            <span class="pill-completed">Kész</span>
                                        <?php else: ?>
                                            <span class="pill-completed" style="background:#eee; color:#666; border-color:#ccc;">Egyéb</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><strong><?= htmlspecialchars($log['username'] ?? 'Rendszer') ?></strong></td>
                                    <td><span class="status-badge status-inactive" style="font-size: 0.7rem;"><?= htmlspecialchars($log['source_wh'] ?? 'Ismeretlen') ?></span></td>
                                    <td><span class="status-badge status-active" style="font-size: 0.7rem;"><?= htmlspecialchars($log['target_wh'] ?? 'Ismeretlen') ?></span></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($logs)): ?>
                                    <tr><td colspan="6" style="text-align:center; opacity:.6; padding: 20px;">Nincs találat a szűrőkre.</td></tr>
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
    setTimeout(() => { header.parentElement.style.transform = 'scale(1)'; }, 100);
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
</script>
</body>
</html>