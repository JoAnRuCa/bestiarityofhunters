document.getElementById('recovery-form').addEventListener('submit', function (e) {
    e.preventDefault(); // <--- ESTO evita que Laravel recargue la página y salten errores

    const emailField = document.getElementById('email-field');
    const statusMsg = document.getElementById('status-msg');
    const btn = document.getElementById('submit-btn');
    const validationErrors = document.querySelector('.mb-4.text-red-600'); // Por si hubiera errores previos

    if (emailField.value !== "") {
        // Ocultamos errores de Laravel si existieran
        if (validationErrors) validationErrors.style.display = 'none';

        // Mostramos nuestro mensaje de éxito
        statusMsg.classList.remove('hidden');

        // Limpiamos el texto
        emailField.value = "";

        // Cambiamos el estado del botón
        btn.innerText = "LINK DISPATCHED";
        btn.classList.add('opacity-50', 'pointer-events-none');
    }
});