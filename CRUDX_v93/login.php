<?php
session_start();

include "config.php";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    if ($user === "" || $pass === "") {
        $error = "Kérlek tölts ki minden mezőt!";
    } else {
        // Módosított lekérdezés: JOIN-oljuk a raktár nevét is
        $stmt = $pdo->prepare("
            SELECT u.*, w.name as warehouse_name 
            FROM users u 
            LEFT JOIN warehouses w ON u.warehouse_id = w.ID 
            WHERE u.username = :u LIMIT 1
        ");
        $stmt->execute(['u' => $user]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($pass, $row['password'])) {
            if ($row['active'] == 0) {
                $error = "A fiók inaktív. Fordulj az adminisztrátorhoz!";
            } else {
                $_SESSION['user_id'] = $row['ID'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                // Új session adatok
                $_SESSION['warehouse_id'] = $row['warehouse_id'];
                $_SESSION['warehouse_name'] = $row['warehouse_name'] ?? 'Minden egység';

                $pdo->prepare("UPDATE users SET login_at = NOW() WHERE ID = ?")->execute([$row['ID']]);

                header("Location: index.php");
                exit;
            }
        } else {
            $error = "Hibás felhasználónév vagy jelszó!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>CRUD-WMS – Bejelentkezés</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css">

    <style>
        /* Login oldal saját stílusai */
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: var(--bg);
        }

        .login-card {
            width: 100%;
            max-width: 420px;
            background: var(--bg-card);
            padding: 2rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border);
            text-align: center;
        }

        .login-logo {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--primary);
            letter-spacing: 0.04em;
        }

        .login-field {
            text-align: left;
            margin-bottom: 1rem;
        }

        .login-field label {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .login-field input {
            width: 100%;
            padding: 0.55rem;
            margin-top: 0.3rem;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            background: #f9fafb;
        }

        .login-error {
            background: rgba(239, 68, 68, 0.12);
            color: #dc2626;
            padding: 0.6rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
    </style>

</head>
<body>


<div class="login-card">

    <div class="login-logo">CRUD-X</div>

    <h2 style="margin-bottom: 1.5rem;">Bejelentkezés</h2>

    <?php if ($error): ?>
        <div class="login-error">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form action="#" method="POST">

        <div class="login-field">
            <label for="username">Felhasználónév</label>
            <input type="text" name="username" id="username" placeholder="pl.: raktar_kezelo">
        </div>

        <div class="login-field">
            <label for="password">Jelszó</label>
            <input type="password" name="password" id="password" placeholder="••••••••">
        </div>

        <button type="submit" class="btn" style="width: 100%; margin-top: 0.5rem;">Bejelentkezés</button>
    </form>

</div>

</body>
</html>
