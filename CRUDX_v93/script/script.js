// Kattintásra nyit / zár egy termékhez tartozó popupot
function toggleStockPopup(productId, event) {
    event.stopPropagation(); // ne fusson le az egész oldalas "click close" azonnal

    const popup = document.getElementById('stock-popup-' + productId);

    // Először zárjuk be az összes többit
    document.querySelectorAll('.popup-card').forEach(el => {
        if (el !== popup) {
            el.style.display = 'none';
        }
    });

    // A sajátját toggle-oljuk
    if (popup.style.display === 'block') {
        popup.style.display = 'none';        
    } else {
        popup.style.display = 'block';
    }
}

// Bárhová kattintasz az oldalon → minden popup bezár
document.addEventListener('click', function () {
    document.querySelectorAll('.popup-card').forEach(el => {
        el.style.display = 'none';
    });
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
    const opt = select.options[select.selectedIndex];
    const max = opt.getAttribute('data-qty');
    const qtyInp = document.getElementById('qtyInput');
    const hint = document.getElementById('maxQtyHint');
    document.getElementById('productNameHidden').value = opt.getAttribute('data-name');
    if (max) { qtyInp.max = max; qtyInp.value = 1; hint.textContent = "Max: " + max + " db"; }
}

