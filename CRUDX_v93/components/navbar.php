<?php

if(!isset($pageTitle)){
    $pageTitle = "cím";
}
if(!isset($activePage)){
    $activePage = "";
}
?>
<header class="topbar">
    <div class="logo">
        <a style="text-decoration: none; color:white;" href="index.php">CRUD-X</a>
    </div>
    
    <nav class="nav">
        <a class="nav-link <?= $activePage == "index.php" ? 'active': '' ?>" href="index.php">Dashboard</a>
        <a class="nav-link <?= $activePage == "products.php" ? 'active': '' ?>" href="products.php">Termékek</a>
        <a class="nav-link <?= $activePage == "inventory.php" ? 'active': '' ?>" href="inventory.php">Raktárkészlet</a>

        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'owner'): ?>
            <a class="nav-link <?= $activePage == "reports.php" ? 'active': '' ?>" href="reports.php">Jelentések</a>
            <a class="nav-link <?= $activePage == "admin.php" ? 'active': '' ?>" href="admin.php">Kezelés</a>
            <a class="nav-link <?= $activePage == "transports.php" ? 'active': '' ?>" href="transports.php">Szállítások</a>

        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'owner'): ?>
            <a href="owner.php" class="nav-link <?= $activePage == "owner.php" ? 'active': '' ?>">Rendszer</a>
        <?php endif; ?>
    </nav>

    <div class="user-box">
        <div class="user-info" style="display: flex; flex-direction: column; text-align: right; margin-right: 15px;">
            <span class="user-name" style="font-weight: bold;"><?= htmlspecialchars($_SESSION['username']) ?></span>
            <span class="user-role" style="font-size: 0.75rem; opacity: 0.8; text-transform: uppercase;">
                <?= htmlspecialchars($_SESSION['role']) ?>
            </span>
        </div>
        <a href="components/logout.php" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.85rem;">Kijelentkezés</a>    
    </div>
</header>