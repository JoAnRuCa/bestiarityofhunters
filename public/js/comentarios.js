window.toggleChildren = function (btn) {
    const node = btn.closest('.comment-node');
    const container = node.querySelector('.replies-container');
    if (!container) return;
    const isHidden = container.classList.contains('hidden');
    if (isHidden) {
        container.classList.remove('hidden');
        btn.innerHTML = `<span class="icon inline-block">▼</span> Hide Replies`;
    } else {
        container.classList.add('hidden');
        btn.innerHTML = `<span class="icon inline-block">▶</span> Show Replies`;
    }
};

window.toggleReply = function (id) {
    const form = document.getElementById(`reply-form-${id}`);
    const editForm = document.getElementById(`edit-form-${id}`);
    const body = document.getElementById(`comment-body-${id}`);
    if (form) {
        form.classList.toggle('hidden');
        if (!form.classList.contains('hidden')) {
            if (editForm) { editForm.classList.add('hidden'); body.classList.remove('hidden'); }
            form.querySelector('textarea').focus();
        }
    }
};

window.toggleEdit = function (id) {
    const form = document.getElementById(`edit-form-${id}`);
    const body = document.getElementById(`comment-body-${id}`);
    const replyForm = document.getElementById(`reply-form-${id}`);
    if (form && body) {
        form.classList.toggle('hidden');
        body.classList.toggle('hidden');
        if (!form.classList.contains('hidden')) {
            if (replyForm) replyForm.classList.add('hidden');
            form.querySelector('textarea').focus();
        }
    }
};

window.enviarEdicion = async function (e, form, id) {
    e.preventDefault();
    const formData = new FormData(form);
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await response.json();
        if (data.success) {
            const bodyDiv = document.getElementById(`comment-body-${id}`);
            bodyDiv.querySelector('p').innerText = formData.get('comentario');
            window.toggleEdit(id);
        }
    } catch (err) { console.error("Error edit:", err); }
};

window.borrarComentario = async function (e, form) {
    e.preventDefault();
    if (!confirm('Are you sure you want to delete this comment?')) return;
    const formData = new FormData(form);
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (response.ok) {
            window.location.reload();
        }
    } catch (err) { console.error("Error delete:", err); }
};

window.enviarComentario = async function (e, form) {
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
                const mainWrapper = document.getElementById('comments-wrapper');
                if (mainWrapper) mainWrapper.prepend(newElement);
            }
            if (typeof initVotes === "function") initVotes();
            form.reset();
        }
    } catch (err) { console.error("Error critico:", err); }
    return false;
};