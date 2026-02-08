document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.save-btn');
    if (!btn) return;

    const container = btn.closest('.save-container');
    const msg = container.querySelector('.save-msg');
    const btnText = btn.querySelector('.btn-text');
    const svg = btn.querySelector('svg');

    // Bloqueo temporal para evitar spam
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
            // Estado Guardado
            btn.classList.replace('bg-[#C67C48]', 'bg-gray-500');
            btn.classList.remove('hover:bg-[#a1633a]');
            btnText.textContent = 'Saved';
            svg.setAttribute('fill', 'currentColor');
            msg.classList.remove('hidden');
        } else if (data.status === 'removed') {
            // Estado No Guardado
            btn.classList.replace('bg-gray-500', 'bg-[#C67C48]');
            btn.classList.add('hover:bg-[#a1633a]');
            btnText.textContent = 'Save Guide';
            svg.setAttribute('fill', 'none');
            msg.classList.add('hidden');
        }
    } catch (error) {
        console.error('Error:', error);
    } finally {
        btn.disabled = false;
    }
});