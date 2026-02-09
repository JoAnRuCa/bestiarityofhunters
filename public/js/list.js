document.addEventListener('DOMContentLoaded', function () {
    // Contenedor de resultados (Guías o Builds)
    const wrapper = document.getElementById('guides-wrapper') ||
        document.getElementById('builds-wrapper') ||
        document.querySelector('.results-container');

    const form = document.getElementById('filter-form');
    const tagsInputsContainer = document.getElementById('active-tags-inputs');

    if (!wrapper || !form) return;

    /**
     * Construye la URL con filtros
     */
    function getFilterUrl() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        for (const [key, value] of [...params.entries()]) {
            if (!value) params.delete(key);
        }
        return `${form.action}?${params.toString()}`;
    }

    /**
     * Petición AJAX principal
     */
    async function fetchContent(url) {
        wrapper.style.opacity = '0.5';
        wrapper.style.pointerEvents = 'none';

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const html = await response.text();
            wrapper.innerHTML = html;

            window.history.pushState({ path: url }, '', url);

            // Reinicializar otros componentes
            if (typeof initVotes === 'function') initVotes();
            if (typeof initUniversalSave === 'function') initUniversalSave();

        } catch (e) {
            console.error("AJAX Error:", e);
            wrapper.innerHTML = `
                <div class="py-20 text-center">
                    <p class="text-red-500 font-bold uppercase tracking-widest text-xs">Error loading content</p>
                    <button onclick="location.reload()" class="text-[#C67C48] underline text-[10px] mt-2">Retry</button>
                </div>
            `;
        } finally {
            wrapper.style.opacity = '1';
            wrapper.style.pointerEvents = 'auto';
        }
    }

    // --- DELEGACIÓN DE EVENTOS (CLICK) ---
    document.addEventListener('click', function (e) {
        // 1. Manejo de Tags
        const tagBtn = e.target.closest('.tag-link');
        if (tagBtn) {
            e.preventDefault();
            const tagName = tagBtn.getAttribute('data-tag');
            const isActive = tagBtn.getAttribute('data-active') === 'true';

            if (isActive) {
                tagBtn.classList.remove('bg-[#C67C48]', 'text-white');
                tagBtn.classList.add('bg-transparent', 'text-[#C67C48]');
                tagBtn.setAttribute('data-active', 'false');
                const input = tagsInputsContainer.querySelector(`input[value="${tagName}"]`);
                if (input) input.remove();
            } else {
                tagBtn.classList.remove('bg-transparent', 'text-[#C67C48]');
                tagBtn.classList.add('bg-[#C67C48]', 'text-white');
                tagBtn.setAttribute('data-active', 'true');
                const newInput = document.createElement('input');
                newInput.type = 'hidden';
                newInput.name = 'tag[]';
                newInput.value = tagName;
                tagsInputsContainer.appendChild(newInput);
            }
            fetchContent(getFilterUrl());
        }

        // 2. Manejo de Paginación
        const pageLink = e.target.closest('.pagination-ajax a');
        if (pageLink) {
            e.preventDefault();
            fetchContent(pageLink.href);
            wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    // --- MANEJO DE ELIMINACIÓN AJAX ---
    document.addEventListener('submit', async function (e) {
        const deleteForm = e.target.closest('.delete-form-ajax');

        // Si es el formulario de filtros, dejamos que lo maneje el listener de abajo
        if (!deleteForm) return;

        e.preventDefault();
        if (!confirm('Do you want to discard this scroll forever?')) return;

        const url = deleteForm.action;
        const formData = new FormData(deleteForm);
        const itemContainer = deleteForm.closest('.group'); // Busca el contenedor de la card

        try {
            const response = await fetch(url, {
                method: 'POST', // Laravel usa POST + _method DELETE
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Animación de salida
                itemContainer.style.transition = 'all 0.4s ease';
                itemContainer.style.opacity = '0';
                itemContainer.style.transform = 'scale(0.95)';

                setTimeout(() => {
                    itemContainer.remove();
                    // Si ya no quedan elementos, recargamos filtros o mostramos mensaje vacío
                    if (wrapper.querySelectorAll('.group').length === 0) {
                        wrapper.innerHTML = `
                            <div class="py-12 text-center">
                                <p class="text-gray-600 italic font-serif text-lg">Your library is currently empty.</p>
                            </div>
                        `;
                    }
                }, 400);
            }
        } catch (error) {
            console.error('Delete Error:', error);
            alert('Could not discard the item.');
        }
    });

    // --- FORMULARIO DE FILTROS ---
    form.addEventListener('change', function (e) {
        if (e.target.name === 'orden') fetchContent(getFilterUrl());
    });

    form.addEventListener('submit', function (e) {
        if (e.target.id === 'filter-form') {
            e.preventDefault();
            fetchContent(getFilterUrl());
        }
    });
});