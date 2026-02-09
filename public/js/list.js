document.addEventListener('DOMContentLoaded', function () {
    // Intentamos encontrar el contenedor de resultados (soporta guías o builds)
    const wrapper = document.getElementById('guides-wrapper') ||
        document.getElementById('builds-wrapper') ||
        document.querySelector('.results-container');

    const form = document.getElementById('filter-form');
    const tagsInputsContainer = document.getElementById('active-tags-inputs');

    // Si no hay contenedor o formulario, el script se desactiva silenciosamente
    if (!wrapper || !form) return;

    /**
     * Construye la URL con todos los filtros actuales
     */
    function getFilterUrl() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        // Filtramos parámetros vacíos para limpiar la URL
        for (const [key, value] of [...params.entries()]) {
            if (!value) params.delete(key);
        }
        return `${form.action}?${params.toString()}`;
    }

    /**
     * Realiza la petición AJAX y actualiza el DOM
     */
    async function fetchContent(url) {
        wrapper.style.opacity = '0.5';
        wrapper.style.pointerEvents = 'none'; // Evita clics dobles mientras carga

        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const html = await response.text();

            // Actualizamos el contenido
            wrapper.innerHTML = html;

            // Sincronizamos la URL en el navegador
            window.history.pushState({ path: url }, '', url);

            // --- REINICIALIZACIÓN ---
            // Esto es vital para que los botones de votar/guardar funcionen en el nuevo HTML
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

    // --- DELEGACIÓN DE EVENTOS ---
    document.addEventListener('click', function (e) {

        // 1. Manejo de Tags (dentro del panel de filtros)
        const tagBtn = e.target.closest('.tag-link');
        if (tagBtn) {
            e.preventDefault();
            const tagName = tagBtn.getAttribute('data-tag');
            const isActive = tagBtn.getAttribute('data-active') === 'true';

            if (isActive) {
                // Desactivar
                tagBtn.classList.remove('bg-[#C67C48]', 'text-white');
                tagBtn.classList.add('bg-transparent', 'text-[#C67C48]');
                tagBtn.setAttribute('data-active', 'false');

                const input = tagsInputsContainer.querySelector(`input[value="${tagName}"]`);
                if (input) input.remove();
            } else {
                // Activar
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

        // 2. Manejo de Paginación AJAX
        const pageLink = e.target.closest('.pagination-ajax a');
        if (pageLink) {
            e.preventDefault();
            fetchContent(pageLink.href);
            // Scroll suave hacia arriba para que el usuario vea los nuevos resultados
            wrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    // 3. Manejo de Inputs (Search, Autor, Orden)
    // Escuchamos el cambio en el selector de orden y el submit del buscador
    form.addEventListener('change', function (e) {
        if (e.target.name === 'orden') {
            fetchContent(getFilterUrl());
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        fetchContent(getFilterUrl());
    });
});