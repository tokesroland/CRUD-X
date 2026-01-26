<header class="topbar">
    <div class="logo">
        <a style="text-decoration: none; color:white;" href="index.php">CRUD-X</a>
    </div>
    
    <nav class="nav">
        <a href="index.php" class="nav-link">Dashboard</a>
        <a href="products.php" class="nav-link">Termékek</a>
        <a href="inventory.php" class="nav-link">Raktárkészlet</a>

        <?php if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'owner'): ?>
            <a href="reports.php" class="nav-link">Jelentések</a>
            <a href="admin.php" class="nav-link">Kezelés</a>
            <a href="transports.php" class="nav-link">Szállítások</a>

        <?php endif; ?>

        <?php if ($_SESSION['role'] === 'owner'): ?>
            <a href="owner.php" class="nav-link">Rendszer</a>
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