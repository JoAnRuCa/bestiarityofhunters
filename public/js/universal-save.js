// public/js/universal-save.js

function initUniversalSave() {
    // Buscamos botones que no hayan sido inicializados
    const saveButtons = document.querySelectorAll('.save-btn:not(.is-initialized)');

    saveButtons.forEach(btn => {
        btn.classList.add('is-initialized'); // Evita duplicar listeners

        btn.addEventListener('click', async function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Elementos visuales
            const container = btn.closest('.save-container');
            const msg = container ? container.querySelector('.save-msg') : null;
            const btnText = btn.querySelector('.btn-text');
            const svg = btn.querySelector('svg');
            const type = btn.dataset.type;
            const url = btn.dataset.url;

            // Bloquear botón durante la carga
            btn.disabled = true;
            btn.style.opacity = '0.7';

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

                // Si el servidor devuelve error (como el 400 que tenías)
                if (!response.ok) {
                    const errorData = await response.json();
                    console.error('Servidor respondió con error:', errorData);
                    throw new Error('Error en la petición al servidor');
                }

                const data = await response.json();

                if (data.status === 'added') {
                    // Estado Guardado
                    btn.classList.remove('bg-[#C67C48]');
                    btn.classList.add('bg-[#6B8E23]');
                    if (btnText) btnText.textContent = 'Saved';
                    if (svg) svg.setAttribute('fill', '#2F2F2F');
                    if (msg) msg.classList.remove('hidden');

                } else if (data.status === 'removed') {
                    // Estado No Guardado
                    btn.classList.remove('bg-[#6B8E23]');
                    btn.classList.add('bg-[#C67C48]');
                    const typeCapitalized = type.charAt(0).toUpperCase() + type.slice(1);
                    if (btnText) btnText.textContent = 'Save ' + typeCapitalized;
                    if (svg) svg.setAttribute('fill', 'none');
                    if (msg) msg.classList.add('hidden');

                    // Lógica para eliminar la card si estamos en la vista de "Mis Guardados"
                    if (window.location.pathname.includes('saved-')) {
                        const card = btn.closest('.group') || btn.closest('.guide-card');
                        if (card) {
                            card.style.transition = 'all 0.4s ease';
                            card.style.opacity = '0';
                            card.style.transform = 'scale(0.9)';

                            setTimeout(() => {
                                card.remove();
                                // Si no quedan más elementos, recargamos para mostrar el mensaje de "vacío"
                                const remaining = document.querySelectorAll('.save-btn').length;
                                if (remaining === 0) location.reload();
                            }, 400);
                        }
                    }
                }
            } catch (error) {
                console.error('Error fatal en universal-save.js:', error);
                alert('Could not save. Please try again.');
            } finally {
                // Reactivar botón
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        });
    });
}

// Inicializar al cargar el DOM
document.addEventListener('DOMContentLoaded', initUniversalSave);

// Por si usas Livewire o cargas contenido dinámico con AJAX
document.addEventListener('ajaxComplete', initUniversalSave);