function filterTable() {
    const batchFilter = document.getElementById('batchFilter').value.toLowerCase();
    const dateFilter = document.getElementById('dateFilter').value;
    const rows = document.querySelectorAll('.pending-card tbody tr');
    
    rows.forEach(row => {
        const batchId = row.cells[0].textContent.toLowerCase();
        const date = row.cells[2].textContent.split(' ')[0];
        
        const batchMatch = batchId.includes(batchFilter);
        const dateMatch = !dateFilter || date.includes(dateFilter.replace(/-/g, '.'));
        
        row.style.display = batchMatch && dateMatch ? '' : 'none';
    });
}

function resetFilters() {
    document.getElementById('batchFilter').value = '';
    document.getElementById('dateFilter').value = '';
    filterTable();
}

document.getElementById('batchFilter').addEventListener('input', filterTable);
document.getElementById('dateFilter').addEventListener('change', filterTable);

document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('dateFilter');
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