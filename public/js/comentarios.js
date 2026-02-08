/**
 * Toggles the visibility of child replies
 */
function toggleChildren(btn) {
    const container = btn.closest('.flex-1').querySelector('.replies-container');
    const icon = btn.querySelector('.icon');
    const isHidden = container.classList.contains('hidden');

    if (isHidden) {
        container.classList.remove('hidden');
        btn.innerHTML = `<span class="icon">▼</span> Hide Replies`;
    } else {
        container.classList.add('hidden');
        btn.innerHTML = `<span class="icon">▶</span> Show Replies`;
    }
}

/**
 * Toggles the reply form visibility
 */
function toggleReply(id) {
    const form = document.getElementById(`reply-form-${id}`);
    if (form) {
        form.classList.toggle('hidden');
        if (!form.classList.contains('hidden')) {
            form.querySelector('textarea').focus();
        }
    }
}

/**
 * Handles AJAX comment submission
 */
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
                container.classList.remove('hidden');
                form.classList.add('hidden');

                // Opcional: Si el botón de "Show Replies" existe, actualizarlo a "Hide"
                const toggleBtn = parentBody.querySelector('button[onclick="toggleChildren(this)"]');
                if (toggleBtn) {
                    toggleBtn.innerHTML = `<span class="icon">▼</span> Hide Replies`;
                }
            } else {
                const wrapperList = document.getElementById('comments-wrapper');
                // Eliminar el mensaje de "No comments yet" si existe
                const noCommentsMsg = document.getElementById('no-comments-msg');
                if (noCommentsMsg) noCommentsMsg.remove();

                wrapperList.prepend(newElement);
            }

            form.reset();
        }
    } catch (err) {
        console.error("Error en la petición:", err);
        alert("The guild's messenger failed to deliver your comment. Try again!");
    }

    return false;
}
