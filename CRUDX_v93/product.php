<?php
session_start();
require 'config.php'; // Itt definiálódik a $BASE_URL
require "./components/auth_check.php";
authorize(['admin', 'owner', 'user']);

// Az ID-t csak azért olvassuk ki, hogy átadjuk a JS-nek
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

?>
<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termék betöltése...</title>
    <link rel="stylesheet" href="<?= $BASE_URL?>style/style.css">
    <link rel="stylesheet" href="<?= $BASE_URL?>style/product.css">
    <style>
        /* Skeleton loading stílus */
        .skeleton {
            background-color: #e2e8f0;
            color: transparent !important;
            border-radius: 4px;
            animation: pulse 2s infinite;
            user-select: none;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }

        .hidden {
            display: none;
        }
    </style>
</head>

<body>

    <main class="container">

        <div id="error-container" class="hidden" style="text-align:center; padding: 40px; color: #ef4444;">
            <h2 id="error-message">Hiba történt</h2>
            <a href="<?= $BASE_URL?>products" class="btn btn-outline">Vissza a listához</a>
        </div>

        <section class="card" id="main-content">
            <div class="card-header">
                <h2>
                    <img class="icon" src="<?= $BASE_URL?>img/products_box.png">
                    <span id="p-name" class="skeleton">Termék Neve...</span>
                </h2>
                <a href="<?= $BASE_URL?>products" class="btn btn-outline btn-small">Vissza a listához</a>
            </div>

            <div style="display:flex; gap:32px; flex-wrap:wrap; margin-bottom: 20px;">
                <div style="flex: 0 0 280px;">
                    <img id="p-image" src="<?= $BASE_URL?>images/products/no-image.png"
                        style="width:100%; border-radius:12px; border: 1px solid var(--border);" alt="Termék kép">
                </div>

                <div style="flex: 1; min-width: 300px;">
                    <div style="display: grid; grid-template-columns: auto 1fr; gap: 10px 20px; align-items: center;">
                        <span class="muted">Azonosító:</span> <strong id="p-id" class="skeleton">#000</strong>
                        <span class="muted">Cikkszám:</span> <strong id="p-item-number"
                            class="skeleton">-------</strong>
                        <span class="muted">Kategória:</span> <strong id="p-category" class="skeleton">--------</strong>
                        <span class="muted">Státusz:</span>
                        <div id="p-status" class="skeleton">-----</div>
                        <span class="muted">Rögzítve:</span> <small id="p-created" class="skeleton">----.--.--</small>
                        <span class="muted">Utolsó mozgás:</span> <small id="p-updated"
                            class="skeleton">----.--.--</small>
                    </div>
                </div>
            </div>

            <hr>

            <h3><img class="icon" src="<?= $BASE_URL?>img/document_23966.png"> Leírás</h3>
            <div id="p-description" style="padding: 10px 0; line-height: 1.6;">
                <span class="skeleton">Ez itt a leírás helye, ami éppen töltődik...</span>
            </div>

            <hr>

            <h3><img class="icon" src="<?= $BASE_URL?>img/products_box.png"> Jelenlegi készletek</h3>
            <div id="inventory-list">
                <p class="skeleton">Készletek betöltése...</p>
            </div>

        </section>
    </main>

    <?php require './components/footer.php'; ?>

    <script>
        const PRODUCT_ID = <?= $productId ?>;
        const BASE_URL = "<?= $BASE_URL?>";

        document.addEventListener("DOMContentLoaded", () => {
            // Ellenőrizzük a konzolon a generált URL-t (Debug célból)
            // console.log("Adatlekérés erről az URL-ről:", `${BASE_URL}api/v1/product_details.php?id=${PRODUCT_ID}`);
            loadProductData();
        });

        async function loadProductData() {
            try {
                // Itt állítjuk össze a pontos URL-t (RESTful formátum)
                const response = await fetch(`${BASE_URL}api/products/${PRODUCT_ID}`);

                if (!response.ok) throw new Error(`Hálózati hiba: ${response.status}`);

                const json = await response.json();
                if (json.status === 'error') throw new Error(json.message);

                const data = json.data.product;
                const inventory = json.data.inventory;

                // Adatok betöltése a DOM-ba
                document.title = data.name;
                setText('p-name', data.name);
                setText('p-id', `#${data.ID}`);
                setText('p-item-number', data.item_number);
                setText('p-category', data.category_name || '—');
                setText('p-created', data.created_at);
                setText('p-updated', data.updated_at || 'Nincs adat');

                const descEl = document.getElementById('p-description');
                descEl.innerHTML = data.description ? data.description.replace(/\n/g, '<br>') : '<em class="muted">Nincs leírás.</em>';

                const statusEl = document.getElementById('p-status');
                statusEl.innerHTML = data.active == 1
                    ? '<span class="badge badge-success">Aktív</span>'
                    : '<span class="badge badge-muted">Inaktív</span>';
                statusEl.classList.remove('skeleton');

                renderInventory(inventory);
                loadImage(data.name);

            } catch (error) {
                console.error("Fetch hiba:", error);
                document.getElementById('main-content').classList.add('hidden');
                document.getElementById('error-container').classList.remove('hidden');
                document.getElementById('error-message').textContent = "Nem sikerült betölteni az adatokat: " + error.message;
            }
        }

        function renderInventory(inventory) {
            const container = document.getElementById('inventory-list');
            if (!inventory || inventory.length === 0) {
                container.innerHTML = `<div style="padding: 20px; text-align: center; background: #f8fafc; border-radius: 8px; color: #64748b;">Nincs készleten.</div>`;
                return;
            }

            let html = `<table class="data-table"><thead><tr><th>Raktár</th><th style="text-align: right;">Mennyiség</th></tr></thead><tbody>`;
            inventory.forEach(item => {
                html += `<tr>
                            <td><strong>${escapeHtml(item.warehouse)}</strong></td>
                            <td style="text-align: right; font-family: monospace; font-weight: bold;">${item.quantity} db</td>
                         </tr>`;
            });
            html += `</tbody></table>`;
            container.innerHTML = html;
        }

        async function loadImage(query) {
            try {
                // Kép API hívás (szintén BASE_URL-lel)
                const res = await fetch(`${BASE_URL}api/api.php?query=${encodeURIComponent(query)}`);
                const data = await res.json();
                if (data.image_url) {
                    document.getElementById('p-image').src = data.image_url;
                }
            } catch (e) {
                console.warn("Kép betöltési hiba", e);
            }
        }

        function setText(id, text) {
            const el = document.getElementById(id);
            if (el) {
                el.textContent = text;
                el.classList.remove('skeleton');
            }
        }

        function escapeHtml(text) {
            if (!text) return text;
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }
    </script>
</body>

</html>