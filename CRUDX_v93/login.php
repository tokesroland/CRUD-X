<?php
session_start();
include "config.php";
$error = "";

// Segítségkérés feldolgozása
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recover'])) {
    $input = trim($_POST['recover_input'] ?? '');
    if ($input !== "") {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :val OR email = :val LIMIT 1");
        $stmt->execute(['val' => $input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $user_ID = $user['ID'] ?? null;
        $username_to_save = $user['username'] ?? ($user ? null : $input);
        $email_to_save = $user['email'] ?? (filter_var($input, FILTER_VALIDATE_EMAIL) ? $input : null);

        $pdo->prepare("INSERT INTO user_error (user_ID, username, email, status) VALUES (?, ?, ?, 'incomplete')")
            ->execute([$user_ID, $username_to_save, $email_to_save]);
        $error = "Kérésedet rögzítettük. Az adminisztrátor hamarosan felveszi veled a kapcsolatot.";
    }
}

// Bejelentkezés feldolgozása
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['recover'])) {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($user !== "" && $pass !== "") {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :u LIMIT 1");
        $stmt->execute(['u' => $user]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($pass, $row['password'])) {
            if ($row['active'] == 0) {
                $error = "A fiók inaktív. Fordulj az adminisztrátorhoz!";
            } else {
                $_SESSION['user_id'] = $row['ID'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];

                // --- JOGOSULTSÁG LOGIKA MÓDOSÍTÁS ---
                if ($row['role'] === 'owner') {
                    // Ha Owner, minden aktív raktárat megkap programozottan
                    $stmtW = $pdo->query("SELECT ID, name FROM warehouses WHERE active = 1 ORDER BY name ASC");
                    $warehouses = $stmtW->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    // Ha nem Owner, a kapcsolótáblából kérjük le
                    $stmtW = $pdo->prepare("
                        SELECT w.ID, w.name 
                        FROM warehouses w 
                        JOIN user_warehouse_access uwa ON w.ID = uwa.warehouse_id 
                        WHERE uwa.user_id = ?
                    ");
                    $stmtW->execute([$row['ID']]);
                    $warehouses = $stmtW->fetchAll(PDO::FETCH_ASSOC);
                }
                
                $_SESSION['warehouse_names'] = array_column($warehouses, 'name');
                $_SESSION['warehouse_ids'] = array_column($warehouses, 'ID');
                $_SESSION['warehouse_name'] = !empty($warehouses) ? $warehouses[0]['name'] : 'Nincs hozzárendelt raktár';

                $pdo->prepare("UPDATE users SET login_at = NOW() WHERE ID = ?")->execute([$row['ID']]);
                header("Location: index.php");
                exit;
            }
        } else { $error = "Hibás felhasználónév vagy jelszó!"; }
    }
}
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8"><title>CRUD-X – Bejelentkezés</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css">
    <style>
        body { display: flex; justify-content: center; align-items: center; height: 100vh; background: var(--bg); }
        .login-card { width: 100%; max-width: 420px; background: var(--bg-card); padding: 2rem; border-radius: 12px; box-shadow: var(--shadow-soft); border: 1px solid var(--border); text-align: center; }
        .login-field { text-align: left; margin-bottom: 1rem; }
        .login-field input { width: 100%; padding: 0.6rem; margin-top: 0.3rem; border-radius: 6px; border: 1px solid var(--border); }
        .login-error { background: #fee2e2; color: #dc2626; padding: 0.6rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.9rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <div style="font-size: 1.6rem; font-weight: 700; color: var(--primary); margin-bottom: 1rem;">CRUD-X</div>
        <h2 style="margin-bottom: 1.5rem;">Bejelentkezés</h2>
        <?php if ($error): ?><div class="login-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST">
            <div class="login-field"><label>Felhasználónév</label><input type="text" name="username" placeholder="felhasználónév"></div>
            <div class="login-field"><label>Jelszó</label><input type="password" name="password" placeholder="••••••••"></div>
            <button type="submit" class="btn" style="width: 100%;">Bejelentkezés</button>
        </form>
        <hr style="margin: 1.5rem 0;">
        <h3>Segítségre van szükséged?</h3>
        <form method="POST">
            <div class="login-field"><label>Felhasználónév vagy email</label><input type="text" name="recover_input" placeholder="adataid ide..."></div>
            <button type="submit" name="recover" class="btn" style="width: 100%;">Segítség kérése</button>
        </form>
    </div>
</body>
</html>