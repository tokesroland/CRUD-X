<header class="topbar">
    <div class="logo"><a style="text-decoration: none; color:white;" href="index.php">CRUD-X Admin</a></div>
    <nav class="nav">
        <a href="index.php" class="nav-link">Vissza a főoldalra</a>
        <a href="reports.php" class="nav-link">Jelentések</a>
        <a href="admin.php" class="nav-link">Kezelés</a>

    </nav>
    <div class="user-box">
        <span class="user-name"><?= $_SESSION['username'] ?></span>
        <a href="components/logout.php" class="btn btn-outline">Kijelentkezés</a>
    </div>
</header>