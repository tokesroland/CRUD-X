<?php
session_start();

include "config.php";
$error = "";

// --- Password/username recovery ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recover'])) {
    $input = trim($_POST['recover_input'] ?? '');
    
    if ($input === "") {
        $error = "Kérlek töltsd ki a mezőt!";
    } else {
        // Try to find a matching user by email or username
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :val OR email = :val LIMIT 1");
        $stmt->execute(['val' => $input]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $user_ID = $user['ID'] ?? null;
        $username_snapshot = $user['username'] ?? null;
        $email_snapshot = $user['email'] ?? (filter_var($input, FILTER_VALIDATE_EMAIL) ? $input : null);

        // Insert into user_error
        $stmt = $pdo->prepare("
            INSERT INTO user_error (user_ID, input_value, username, email, status)
            VALUES (:user_ID, :input_value, :username, :email, 'incomplete')
        ");
        $stmt->execute([
            'user_ID' => $user_ID,
            'input_value' => $input,
            'username' => $username_snapshot,
            'email' => $email_snapshot
        ]);

        // If email exists, optionally send an email reminder
        if ($email_snapshot && $user_ID) {
            $subject = "Felhasználónév / jelszó emlékeztető";
            $message = "Szia " . htmlspecialchars($username_snapshot) . ",\n\n"
                     . "Kértél emlékeztetőt. A felhasználóneved: " . htmlspecialchars($username_snapshot) . "\n\n"
                     . "Kérjük, vedd fel a kapcsolatot az adminnal, hogy jelszót változtathass.";
            // mail($email_snapshot, $subject, $message); // Uncomment if mail() is configured
        }

        $error = "A kérésedet rögzítettük. Az admin hamarosan segíteni fog.";
    }
}

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
        <hr style="margin: 1.5rem 0;">
        <h3>Elfelejtett jelszó / felhasználónév?</h3>
        <form action="#" method="POST">
            <div class="login-field">
                <label for="recover_input">Felhasználónév vagy email</label>
                <input type="text" name="recover_input" id="recover_input"
                    placeholder="Írd be a felhasználóneved vagy email címed">
            </div>
            <button type="submit" name="recover" class="btn" style="width: 100%; margin-top: 0.5rem;">Segítség
                kérése</button>
        </form>


    </div>

</body>

</html>