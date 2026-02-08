document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.save-btn');
    if (!btn) return;

    const container = btn.closest('.save-container');
    const msg = container.querySelector('.save-msg');
    const btnText = btn.querySelector('.btn-text');
    const svg = btn.querySelector('svg');

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
            // Cambiar Marrón -> Verde
            btn.classList.replace('bg-[#C67C48]', 'bg-[#6B8E23]');
            btnText.textContent = 'Saved';
            svg.setAttribute('fill', '#2F2F2F');
            msg.classList.remove('hidden');
        } else if (data.status === 'removed') {
            // Cambiar Verde -> Marrón
            btn.classList.replace('bg-[#6B8E23]', 'bg-[#C67C48]');
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