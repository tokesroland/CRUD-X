<?php
if(!isset($pageTitle)) $pageTitle = "cím";
if(!isset($activePage)) $activePage = "";

// Raktárlista előkészítése a sessionből
$wh_list = $_SESSION['warehouse_names'] ?? [];
$wh_count = count($wh_list);
?>
<style>
    .nav-user-container { display: flex; align-items: center; gap: 15px; color: #e5e7eb; }
    .wh-tooltip-box { position: relative; display: inline-block; }
    .wh-badge { background: #2563eb; color: white; padding: 2px 8px; border-radius: 12px; font-size: 0.7rem; cursor: help; font-weight: 700; border: 1px solid rgba(255,255,255,0.2); }
    .wh-tooltip {
        visibility: hidden; width: 220px; background: #1e293b; color: #fff; text-align: left;
        border-radius: 8px; padding: 12px; position: absolute; z-index: 1000; right: 0; top: 35px;
        opacity: 0; transition: all 0.3s; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.5); border: 1px solid #334155;
    }
    .wh-tooltip::after { content: ""; position: absolute; bottom: 100%; right: 15px; border-width: 6px; border-style: solid; border-color: transparent transparent #1e293b transparent; }
    .wh-tooltip-box:hover .wh-tooltip { visibility: visible; opacity: 1; transform: translateY(5px); }
    .wh-tooltip ul { margin: 8px 0 0; padding: 0; list-style: none; font-size: 0.75rem; border-top: 1px solid #334155; }
    .wh-tooltip li { padding: 5px 0; border-bottom: 1px solid #334155; color: #cbd5e1; }
    .wh-tooltip li:last-child { border-bottom: none; }
</style>

<header style="user-select: none;" class="topbar">
    <div class="topbar-brand-container">
        <div class="logo">
            <a style="text-decoration: none; color:white;" href="index.php">CRUD-X</a>
        </div>
        
        <button class="hamburger-btn" onclick="toggleMenu()" aria-label="Menü">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
    
    <nav class="nav" id="mainNav">
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

    <div class="user-box" id="userBox">
        <div class="nav-user-container">
            <div class="wh-tooltip-box">
                <span class="wh-badge"><?= $wh_count > 0 ? $wh_count . ' helyszín' : 'Minden egység' ?></span>
                <?php if($wh_count > 0): ?>
                <div class="wh-tooltip">
                    <strong style="color:var(--primary); font-size:0.8rem;">Hozzárendelt raktárak:</strong>
                    <ul><?php foreach($wh_list as $name_): ?><li><?= htmlspecialchars($name_) ?></li><?php endforeach; ?></ul>
                </div>
                <?php endif; ?>
            </div>
            <div class="user-info">
                <span class="user-name" style="font-weight: bold;"><?= htmlspecialchars($_SESSION['username']) ?></span>
                <span class="user-role" style="font-size: 0.65rem; opacity: 0.7; text-transform: uppercase;"><?= htmlspecialchars($_SESSION['role']) ?></span>
            </div>
        </div>
        <a href="components/logout.php" class="btn btn-outline" style="padding: 6px 12px; font-size: 0.8rem; color:white; border-color:rgba(255,255,255,0.2);">Kijelentkezés</a>    
    </div>
</header>

<script>
    function toggleMenu() {
        const nav = document.getElementById('mainNav');
        const userBox = document.getElementById('userBox');
        nav.classList.toggle('open');
        userBox.classList.toggle('open');
    }
</script>