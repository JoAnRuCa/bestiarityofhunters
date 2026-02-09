document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.save-btn');
    if (!btn) return;

    // Elementos visuales
    const container = btn.closest('.save-container');
    const msg = container ? container.querySelector('.save-msg') : null;
    const btnText = btn.querySelector('.btn-text');
    const svg = btn.querySelector('svg');
    const type = btn.dataset.type;

    btn.disabled = true;

    try {
        const response = await fetch(btn.dataset.url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.status === 'added') {
            // Estado: Guardado
            btn.classList.replace('bg-[#C67C48]', 'bg-[#6B8E23]');
            if (btnText) btnText.textContent = 'Saved';
            if (svg) svg.setAttribute('fill', '#2F2F2F');
            if (msg) msg.classList.remove('hidden');

        } else if (data.status === 'removed') {
            // Estado: No guardado
            btn.classList.replace('bg-[#6B8E23]', 'bg-[#C67C48]');
            if (btnText) btnText.textContent = 'Save ' + type.charAt(0).toUpperCase() + type.slice(1);
            if (svg) svg.setAttribute('fill', 'none');
            if (msg) msg.classList.add('hidden');

            // --- Lógica Especial para la página de Archivos ---
            // Si la URL contiene "saved-", asumimos que estamos en el listado de guardados
            if (window.location.pathname.includes('saved-')) {
                const card = btn.closest('.group') || btn.closest('.guide-card');
                if (card) {
                    card.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                    card.style.opacity = '0';
                    card.style.transform = 'translateX(30px)';

                    setTimeout(() => {
                        card.remove();
                        // Opcional: Si no quedan tarjetas, recargar para mostrar el mensaje de "Vacío"
                        const remainingCards = document.querySelectorAll('.save-btn').length;
                        if (remainingCards === 0) location.reload();
                    }, 400);
                }
            }
        }
    } catch (error) {
        console.error('Error en la petición de guardado:', error);
    } finally {
        btn.disabled = false;
    }
});