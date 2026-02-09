document.addEventListener('click', async function (e) {
    const btn = e.target.closest('.save-btn');
    if (!btn) return;

    const container = btn.closest('.save-container');
    const msg = container.querySelector('.save-msg');
    const btnText = btn.querySelector('.btn-text');
    const svg = btn.querySelector('svg');
    const type = btn.dataset.type; // 'guide' o 'build'

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
            btn.classList.replace('bg-[#C67C48]', 'bg-[#6B8E23]');
            btnText.textContent = 'Saved';
            svg.setAttribute('fill', '#2F2F2F');
            msg.classList.remove('hidden');
        } else if (data.status === 'removed') {
            btn.classList.replace('bg-[#6B8E23]', 'bg-[#C67C48]');
            // Dinamismo: 'Save Guide' o 'Save Build'
            btnText.textContent = 'Save ' + type.charAt(0).toUpperCase() + type.slice(1);
            svg.setAttribute('fill', 'none');
            msg.classList.add('hidden');
        }
    } catch (error) {
        console.error('Error:', error);
    } finally {
        btn.disabled = false;
    }
});