function openModal(type) {
    const modal = document.getElementById('modalOverlay');
    const title = document.getElementById('modalTitle');
    const typeInput = document.getElementById('modalType');
    const container = document.getElementById('modalInputContainer');

    // Recuperamos los datos del puente invisible
    const userDataEl = document.getElementById('userData');
    const userName = userDataEl.dataset.name;
    const userEmail = userDataEl.dataset.email;

    modal.classList.remove('hidden');
    typeInput.value = type;
    container.innerHTML = '';

    // No usamos 'required' para que Laravel gestione los mensajes de error
    if (type === 'name') {
        title.innerText = 'Update Nickname';
        container.innerHTML = `
            <input type="text" name="name" value="${userName}" 
                   class="w-full p-2 border-2 border-[#C67C48]/20 rounded bg-white outline-none focus:border-[#6B8E23]">`;
    } else if (type === 'email') {
        title.innerText = 'Update Email';
        container.innerHTML = `
            <input type="email" name="email" value="${userEmail}" 
                   class="w-full p-2 border-2 border-[#C67C48]/20 rounded bg-white outline-none focus:border-[#6B8E23]">`;
    } else if (type === 'password') {
        title.innerText = 'Update Password';
        container.innerHTML = `
            <input type="password" name="current_password" placeholder="Current Password" 
                   class="w-full p-2 border-2 border-[#C67C48]/20 rounded bg-white outline-none focus:border-[#6B8E23]">
            <input type="password" name="new_password" placeholder="New Password" 
                   class="w-full p-2 mt-3 border-2 border-[#C67C48]/20 rounded bg-white outline-none focus:border-[#6B8E23]">
        `;
    }
}

function closeModal() {
    const modal = document.getElementById('modalOverlay');
    modal.classList.add('hidden');

    // Si cerramos y había errores, refrescamos para limpiar la sesión y los inputs
    if (modal.dataset.hasErrors === "true") {
        window.location.href = document.getElementById('userData').dataset.profileUrl;
    }
}

window.onclick = function (event) {
    const modal = document.getElementById('modalOverlay');
    if (event.target == modal) {
        closeModal();
    }
}