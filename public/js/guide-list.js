document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('guides-wrapper');
    const form = document.getElementById('filter-form');
    const tagsInputsContainer = document.getElementById('active-tags-inputs');

    function getFilterUrl() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        return `${form.action}?${params.toString()}`;
    }

    async function fetchGuides(url) {
        wrapper.style.opacity = '0.5';
        try {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) throw new Error('Error en la petición');

            // LEEMOS COMO TEXTO (HTML), NO COMO JSON
            const html = await response.text();

            wrapper.innerHTML = html;
            window.history.pushState({}, '', url);

            if (typeof initVotes === 'function') initVotes();

        } catch (e) {
            console.error("Error al cargar guías:", e);
            wrapper.innerHTML = '<p class="text-center text-red-500 py-10">Error loading guides.</p>';
        }
        wrapper.style.opacity = '1';
    }

    document.addEventListener('click', function (e) {
        const tagBtn = e.target.closest('.tag-link');
        if (tagBtn) {
            const tagName = tagBtn.getAttribute('data-tag');
            const isActive = tagBtn.getAttribute('data-active') === 'true';

            if (isActive) {
                tagBtn.classList.remove('bg-[#C67C48]', 'text-white', 'border-[#C67C48]', 'shadow-md', 'hover:bg-[#a1633a]');
                tagBtn.classList.add('bg-transparent', 'text-[#C67C48]', 'border-[#C67C48]/40', 'hover:bg-[#C67C48]/10');
                tagBtn.setAttribute('data-active', 'false');

                const input = tagsInputsContainer.querySelector(`input[value="${tagName}"]`);
                if (input) input.remove();
            } else {
                tagBtn.classList.remove('bg-transparent', 'text-[#C67C48]', 'border-[#C67C48]/40', 'hover:bg-[#C67C48]/10');
                tagBtn.classList.add('bg-[#C67C48]', 'text-white', 'border-[#C67C48]', 'shadow-md', 'hover:bg-[#a1633a]');
                tagBtn.setAttribute('data-active', 'true');

                const newInput = document.createElement('input');
                newInput.type = 'hidden';
                newInput.name = 'tag[]';
                newInput.value = tagName;
                tagsInputsContainer.appendChild(newInput);
            }
            fetchGuides(getFilterUrl());
        }

        const pageLink = e.target.closest('.pagination-ajax a');
        if (pageLink) {
            e.preventDefault();
            fetchGuides(pageLink.href);
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        fetchGuides(getFilterUrl());
    });
});