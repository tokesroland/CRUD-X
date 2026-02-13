// Dinamikus készletadatok betöltése és megjelenítése
async function showStockDetails(productId, event) {
    event.stopPropagation();

    const popup = document.getElementById('common-stock-popup');
    const body = document.getElementById('popup-body');

    // Pozicionálás a gomb mellé
    const rect = event.target.getBoundingClientRect();
    popup.style.top = (rect.bottom + window.scrollY + 5) + 'px';
    popup.style.left = (rect.left + window.scrollX) + 'px';

    body.innerHTML = '<p class="muted">Betöltés...</p>';
    popup.style.display = 'block';

    try {
        const response = await fetch(`${BASE_URL}api/products/${productId}`);
        if (!response.ok) throw new Error('Hiba a lekérés során');

        const json = await response.json();
        if (json.status !== 'success') throw new Error(json.message);

        const inventory = json.data.inventory;
        let html = '';

        if (inventory.length === 0) {
            html = '<div class="row">Nincs készleten</div>';
        } else {
            inventory.forEach(wh => {
                html += `<div class="row">
                            <strong>${escapeHtml(wh.warehouse)}</strong>
                            <span>${wh.quantity} db</span>
                         </div>`;
            });
        }

        // Összes készlet kiszámítása
        const productData = json.data.product;
        const globalTotal = productData.display_qty || 0;
        html += `<div class="muted">Összesen: ${globalTotal} db</div>`;

        body.innerHTML = html;
    } catch (error) {
        body.innerHTML = `<p style="color:red;">Hiba: ${error.message}</p>`;
    }
}

function escapeHtml(text) {
    if (!text) return text;
    return text.toString()
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

// Bárhová kattintasz az oldalon → minden popup bezár
document.addEventListener('click', function (e) {
    const commonPopup = document.getElementById('common-stock-popup');
    if (commonPopup && !commonPopup.contains(e.target)) {
        commonPopup.style.display = 'none';
    }
});

document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('arriveDate');
    if (!el) return;

    function openPicker() {
        if (typeof el.showPicker === 'function') {
            el.showPicker();
        } else {
            el.focus();
            try { el.click(); } catch (e) { }
        }
    }

    // Open picker when clicking/focusing the input
    el.addEventListener('click', openPicker);
    el.addEventListener('focus', openPicker);

    // Also open when clicking anywhere inside the surrounding .field
    var container = el.closest('.field');
    if (container) {
        container.addEventListener('click', function (e) {
            if (e.target !== el) openPicker();
        });
    }
});
function updateMaxQty() {
    const select = document.getElementById('productSelect');
    if (!select) return;
    const opt = select.options[select.selectedIndex];
    const max = opt.getAttribute('data-qty');
    const qtyInp = document.getElementById('qtyInput');
    const hint = document.getElementById('maxQtyHint');
    const productHidden = document.getElementById('productNameHidden');
    if (productHidden) productHidden.value = opt.getAttribute('data-name');
    if (max && qtyInp) {
        qtyInp.max = max;
        qtyInp.value = 1;
        if (hint) hint.textContent = "Max: " + max + " db";
    }
}
