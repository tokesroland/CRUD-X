// Új kategória mező megjelenítése/elrejtése
function checkNewCategory(val) {
    const field = document.getElementById('newCategoryField');
    const input = field.querySelector('input');
    if (val === 'NEW') {
        field.style.display = 'block';
        input.required = true;
        input.focus();
    } else {
        field.style.display = 'none';
        input.required = false;
    }
}