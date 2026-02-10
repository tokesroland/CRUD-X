        function toggleSection(id, header) {
            const content = document.getElementById(id);
            content.classList.toggle('is-hidden');
            header.classList.toggle('collapsed');
            header.parentElement.style.transform = 'scale(0.995)';
            setTimeout(() => {
                header.parentElement.style.transform = 'scale(1)';
            }, 100);
        }

        function filterUsers() {
            const input = document.getElementById('userSearch');
            const filter = input.value.toLowerCase();
            const rows = document.getElementsByClassName('user-row');
            for (let i = 0; i < rows.length; i++) {
                const usernameInput = rows[i].querySelector('input[name="username"]');
                if (usernameInput) {
                    const val = usernameInput.value.toLowerCase();
                    rows[i].style.display = val.includes(filter) ? "" : "none";
                }
            }
        }

        function filterWarehouses() {
            const input = document.getElementById('warehouseSearch');
            const filter = input.value.toLowerCase();
            const rows = document.getElementsByClassName('warehouse-row');
            for (let i = 0; i < rows.length; i++) {
                const nameInput = rows[i].querySelector('input[name="w_name"]');
                const addrInput = rows[i].querySelector('input[name="w_address"]');
                if (nameInput && addrInput) {
                    const val = (nameInput.value + addrInput.value).toLowerCase();
                    rows[i].style.display = val.includes(filter) ? "" : "none";
                }
            }
        }

        // JS Logika: Ha 'owner'-re vált, eltűnik a raktárválasztó
        function toggleWarehouseSelect(roleSelectId, whBoxId) {
            const roleSelect = document.getElementById(roleSelectId);
            const whBox = document.getElementById(whBoxId);
            const msgBox = document.getElementById(whBoxId + "_msg");

            if (roleSelect.value === 'owner') {
                whBox.style.display = 'none';
                if (msgBox) msgBox.style.display = 'block';
            } else {
                whBox.style.display = 'block';
                if (msgBox) msgBox.style.display = 'none';
            }
        }
