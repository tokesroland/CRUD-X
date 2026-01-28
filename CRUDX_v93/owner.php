<?php
session_start();
include 'config.php';
require "./components/auth_check.php";
authorize(['owner']);

$pageTitle = "Rendszer";
$activePage = "owner.php";
include './components/navbar.php'; 


$message = "";

/* |--------------------------------------------------------------------------
| 1. LOGIKA: ÚJ FELHASZNÁLÓ LÉTREHOZÁSA
|-------------------------------------------------------------------------- */
if (isset($_POST['add_user'])) {
    $username = trim($_POST['username'] ?? '');
    $password_raw = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $now = date('Y-m-d H:i:s');

    if (!empty($username) && !empty($password_raw)) {
        $password_hash = password_hash($password_raw, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, password, role, active, created_at, login_at) VALUES (?, ?, ?, 1, ?, ?)");
            $stmt->execute([$username, $password_hash, $role, $now, $now]);
            $message = "Sikeresen létrehozva: $username";
        } catch (PDOException $e) {
            $message = "Hiba: " . ($e->getCode() == 23000 ? "A felhasználónév már foglalt!" : $e->getMessage());
        }
    } else {
        $message = "Hiba: Minden mezőt ki kell tölteni!";
    }
}

/* |--------------------------------------------------------------------------
| 2. LOGIKA: FELHASZNÁLÓ MÓDOSÍTÁSA
|-------------------------------------------------------------------------- */
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

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
$logs = $pdo->query("SELECT t.*, u.username, p.name as product_name, w.name as warehouse_name FROM transports t LEFT JOIN users u ON t.user_ID = u.ID LEFT JOIN products p ON t.product_ID = p.ID LEFT JOIN warehouses w ON t.warehouse_ID = w.ID ORDER BY t.date DESC LIMIT 50")->fetchAll(PDO::FETCH_ASSOC);
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
        
        /* Navy Blue Table Header */
        .data-table { width: 100%; border-collapse: collapse; background: white; }
        .data-table thead th { 
            background-color: #0f172a; 
            color: #ffffff; 
            padding: 15px; 
            text-align: left; 
            font-weight: 600; 
            font-size: 0.9rem;
        }
        .data-table tbody td { padding: 12px 15px; border-bottom: 1px solid #f1f5f9; color: #334155; }

        .card-header-toggle { padding: 20px; display: flex; justify-content: space-between; align-items: center; cursor: pointer; }
        .toggle-icon { transition: transform 0.3s; }
        .collapsed .toggle-icon { transform: rotate(-90deg); }
        .is-hidden { display: none; }

        .search-container { padding: 10px 20px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
        .search-input { width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; }

        .btn-primary-blue { background: #0f172a; color: white; border: none; padding: 10px 15px; border-radius: 6px; cursor: pointer; width: 100%; font-weight: 600; }
        .btn-save-sm { background: #0f172a; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }

        .status-badge { padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }
        .status-active { background: #dcfce7; color: #16a34a; }
        .status-inactive { background: #fee2e2; color: #dc2626; }
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
        <section class="card">
            <div style="padding: 20px; border-bottom: 1px solid #eee;"><h2>➕ Új felhasználó</h2></div>
            <div style="padding: 20px;">
                <form method="POST">
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Felhasználónév</label>
                    <input type="text" name="username" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:6px;">
                    
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Jelszó</label>
                    <input type="password" name="password" required style="width:100%; padding:10px; margin-bottom:15px; border:1px solid #ddd; border-radius:6px;">
                    
                    <label style="display:block; margin-bottom:5px; font-weight:600;">Szerepkör</label>
                    <select name="role" style="width:100%; padding:10px; margin-bottom:20px; border:1px solid #ddd; border-radius:6px;">
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                        <option value="owner">Owner</option>
                    </select>
                    
                    <button type="submit" name="add_user" class="btn-primary-blue">Létrehozás</button>
                </form>
            </div>
        </section>

        <section class="card">
            <div class="card-header-toggle" onclick="toggleSection('userContent', this)">
                <h2>👥 Felhasználók kezelése</h2>
                <span class="toggle-icon">▼</span>
            </div>
            
            <div id="userContent">
                <div class="search-container">
                    <input type="text" id="userSearch" class="search-input" placeholder="Keresés névre..." onkeyup="filterUsers()">
                </div>
                <div style="overflow-x: auto;">
                    <table class="data-table" id="userTable">
                        <thead>
                            <tr>
                                <th>Név</th>
                                <th>Jog</th>
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
    </div>

    <section class="card">
        <div style="padding: 20px; border-bottom: 1px solid #eee;"><h2>📜 Készletmozgási Napló</h2></div>
        <div style="overflow-x: auto;">
            <table class="data-table" style="min-width: 900px;">
                <thead>
                    <tr>
                        <th>Dátum</th>
                        <th>User</th>
                        <th>Típus</th>
                        <th>Termék</th>
                        <th>Raktár</th>
                        <th>Leírás</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                    <tr>
                        <td style="font-family: monospace; font-size: 0.8rem;"><?= $log['date'] ?></td>
                        <td><strong><?= htmlspecialchars($log['username'] ?? 'Rendszer') ?></strong></td>
                        <td><span class="status-badge <?= $log['type'] == 'import' ? 'status-active' : 'status-inactive' ?>"><?= strtoupper($log['type']) ?></span></td>
                        <td><?= htmlspecialchars($log['product_name']) ?></td>
                        <td><?= htmlspecialchars($log['warehouse_name']) ?></td>
                        <td style="font-size: 0.85rem; font-style: italic; color: #64748b;"><?= htmlspecialchars($log['description']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

<script>
function toggleSection(id, header) {
    const content = document.getElementById(id);
    content.classList.toggle('is-hidden');
    header.classList.toggle('collapsed');
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
</script>
</body>
</html>