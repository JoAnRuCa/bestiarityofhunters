/**
 * Toggles the visibility of child replies
 */
function toggleChildren(btn) {
    // Buscamos el nodo de comentario más cercano y de ahí el contenedor de respuestas
    const node = btn.closest('.comment-node');
    const container = node.querySelector('.replies-container');

    if (!container) return;

    const isHidden = container.classList.contains('hidden');

    if (isHidden) {
        container.classList.remove('hidden');
        btn.innerHTML = `<span class="icon">▼</span> Hide Replies`;
    } else {
        container.classList.add('hidden');
        btn.innerHTML = `<span class="icon">▶</span> Show Replies`;
    }
}

function toggleReply(id) {
    const form = document.getElementById(`reply-form-${id}`);
    if (form) {
        form.classList.toggle('hidden');
        if (!form.classList.contains('hidden')) {
            form.querySelector('textarea').focus();
        }
    }
}

async function enviarComentario(e, form) {
    e.preventDefault();
    const formData = new FormData(form);
    const isReply = form.id.includes('reply-form-');

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
                const parentNode = form.closest('.comment-node');
                const container = parentNode.querySelector('.replies-container');
                container.appendChild(newElement);
                container.classList.remove('hidden');
                form.classList.add('hidden');
            } else {
                document.getElementById('comments-wrapper').prepend(newElement);
            }

            // ¡IMPORTANTE! Volver a activar los votos en el nuevo comentario
            if (typeof initVotes === "function") { initVotes(); }
            form.reset();
        }
    } catch (err) {
        console.error("Error:", err);
    }
    return false;
}