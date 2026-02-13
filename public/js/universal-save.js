// public/js/universal-save.js

function initUniversalSave() {
    const saveButtons = document.querySelectorAll('.save-btn:not(.is-initialized)');

    saveButtons.forEach(btn => {
        btn.classList.add('is-initialized');

        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            e.stopPropagation();

            const type = btn.dataset.type;
            const url = btn.dataset.url;
            const btnText = btn.querySelector('.btn-text');
            const svg = btn.querySelector('svg');

            btn.disabled = true;

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Error en el servidor');
                const data = await response.json();

                // Detectamos si estamos en las páginas de "Mis Guardados"
                const isSavedPage = window.location.pathname.includes('/saved/guides') ||
                    window.location.pathname.includes('/saved/builds');

                if (isSavedPage) {
                    // --- LÓGICA SOLO PARA PÁGINAS DE GUARDADOS ---
                    if (data.status === 'added') {
                        btn.classList.replace('bg-[#C67C48]', 'bg-[#6B8E23]');
                    } else {
                        btn.classList.replace('bg-[#6B8E23]', 'bg-[#C67C48]');
                    }
                    // En estas páginas no tocamos ni texto ni icono, se queda blanco.
                } else {
                    // --- LÓGICA PARA EL RESTO DE LA WEB (COMO ESTABA ANTES) ---
                    if (data.status === 'added') {
                        btn.classList.replace('bg-[#C67C48]', 'bg-[#6B8E23]');
                        if (btnText) btnText.textContent = 'Saved';
                        if (svg) svg.setAttribute('fill', '#2F2F2F'); // O el color que usaras antes
                    } else {
                        btn.classList.replace('bg-[#6B8E23]', 'bg-[#C67C48]');
                        const typeCap = type.charAt(0).toUpperCase() + type.slice(1);
                        if (btnText) btnText.textContent = 'Save ' + typeCap;
                        if (svg) svg.setAttribute('fill', 'none');
                    }
                }

            } catch (error) {
                console.error('Error:', error);
            } finally {
                btn.disabled = false;
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', initUniversalSave);