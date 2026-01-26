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
