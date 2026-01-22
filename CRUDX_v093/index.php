<?php 
    session_start();
    include './config.php';
    require "./components/auth_check.php";
    authorize(['admin','owner','user']);

    include './components/navbar.php'; 
?>


<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>CRUD-X – Raktárkezelő rendszer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/style.css">
</head>
<body>

<main class="container">

    <!-- Felső statisztika kártyák -->
    <section class="stats-grid">
        <div class="card stat-card">
            <div class="stat-label">Összes termék</div>
            <div class="stat-value">152</div>
            <div class="stat-sub">Aktív: 140 • Inaktív: 12</div>
        </div>
        <div class="card stat-card">
            <div class="stat-label">Raktárak száma</div>
            <div class="stat-value">3</div>
            <div class="stat-sub">Budapest • Győr • Debrecen</div>
        </div>
        <div class="card stat-card critical">
            <div class="stat-label">Alacsony készletszint</div>
            <div class="stat-value">8</div>
            <div class="stat-sub">Azonnó figyelmet igényel</div>
        </div>
    </section>

    <!-- Keresés / szűrés -->
    <section class="card filter-card">
        <div class="filter-row">
            <div class="field">
                <label for="search">Keresés név vagy cikkszám alapján</label>
                <input type="text" id="search" placeholder="Pl.: csavar, ABC-1234">
            </div>
            <div class="field">
                <label for="category">Kategória</label>
                <select id="category">
                    <option>Összes kategória</option>
                    <option>Csavarok</option>
                    <option>Faanyag</option>
                    <option>Elektromos</option>
                </select>
            </div>
            <div class="field">
                <label for="status">Státusz</label>
                <select id="status">
                    <option>Összes</option>
                    <option>Aktív</option>
                    <option>Inaktív</option>
                </select>
            </div>
            <div class="field buttons">
                <button class="btn">Szűrés</button>
                <button class="btn btn-outline">Szűrők törlése</button>
            </div>
        </div>
    </section>

    <!-- Terméklista -->
    <section class="card">
        <div class="card-header">
            <h2>📦 Terméklista</h2>
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Név</th>
                        <th>Cikkszám</th>
                        <th>Kategória</th>
                        <th>Státusz</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dummy adatok – később backend tölti -->
                    <tr>
                        <td>1</td>
                        <td>6x40 facsavar</td>
                        <td>CSAV-640-01</td>
                        <td>Csavarok</td>
                        <td><span class="badge badge-success">Aktív</span></td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>10x50 facsavar</td>
                        <td>CSAV-1050-02</td>
                        <td>Csavarok</td>
                        <td><span class="badge badge-success">Aktív</span></td>
                    </tr>
                    <tr class="inactive-row">
                        <td>3</td>
                        <td>Régi típusú fénycső</td>
                        <td>FENY-OLD-01</td>
                        <td>Elektromos</td>
                        <td><span class="badge badge-muted">Inaktív</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </section>

</main>

<footer class="footer">
        CRUD-X Raktárkezelő &copy; <?= date('Y') ?>
</footer>

</body>
</html>
