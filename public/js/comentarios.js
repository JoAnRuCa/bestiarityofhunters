// Función para mostrar/ocultar respuestas
function toggleChildren(btn) {
    const container = btn.closest('.flex-1').querySelector('.replies-container');
    const icon = btn.querySelector('.icon');
    const isHidden = container.classList.contains('hidden');

    if (isHidden) {
        container.classList.remove('hidden');
        btn.innerHTML = `<span class="icon">▼</span> Hide Replies`;
    } else {
        container.classList.add('hidden');
        // Intentamos recuperar el conteo si es posible o simplemente Reset
        btn.innerHTML = `<span class="icon">▶</span> Show Replies`;
    }
}

// Actualización de enviarComentario
async function enviarComentario(e, form) {
    e.preventDefault();
    e.stopPropagation();

    const formData = new FormData(form);
    const isReply = form.id.includes('reply-form-');

    if (isReply) {
        const node = form.closest('.comment-node');
        const currentLevel = parseInt(node.getAttribute('data-level')) || 0;
        formData.append('level', currentLevel + 1);
    }

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });

        const data = await response.json();

        if (data.success) {
            const wrapper = document.createElement('div');
            wrapper.innerHTML = data.comment_html.trim();
            const newElement = wrapper.firstElementChild;

            if (isReply) {
                const parentBody = form.closest('.flex-1');
                const container = parentBody.querySelector('.replies-container');

                container.appendChild(newElement);
                container.classList.remove('hidden'); // Mostramos automáticamente al responder
                form.classList.add('hidden');

                // Si el botón de "Show Replies" no existía (era la primera respuesta), podrías crearlo aquí o simplemente mostrar el contenedor
            } else {
                document.getElementById('comments-wrapper').prepend(newElement);
            }

            form.reset();
        }
    } catch (err) {
        console.error("Error:", err);
    }
    return false;
}

