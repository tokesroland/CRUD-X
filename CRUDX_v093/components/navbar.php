<header class="topbar">
    <div class="logo"><a style="text-decoration: none; color:white;" href="index.php">CRUD-X</a></div>
    <nav class="nav">
        <a href="products.php" class="nav-link">Termékek</a>
        <a href="inventory.php" class="nav-link">Áttekintés</a>
        <?= $_SESSION['role'] == 'admin' || $_SESSION['role'] == 'owner' ? '<a href="admin.php" class="nav-link">Admin</a>' : '' ?>
        <?= $_SESSION['role'] == 'owner' ? '<a href="owner.php" class="nav-link">Tulajdonos</a>' : '' ?>
    </nav>
    <div class="user-box">
        <span class="user-name"><?= $_SESSION['username'] ?></span>
        <a href="components/logout.php" class="btn btn-outline">Kijelentkezés</a>    
    </div>
</header>