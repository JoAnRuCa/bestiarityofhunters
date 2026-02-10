// borrar.js
(function () {
    // Evita registrar el evento múltiples veces
    if (window.deleteScriptLoaded) return;
    window.deleteScriptLoaded = true;

    document.addEventListener('submit', function (e) {
        const form = e.target.closest('.delete-form-ajax');
        if (!form) return;

        e.preventDefault();
        e.stopImmediatePropagation(); // Detiene otros scripts que puedan estar escuchando

        if (!confirm('¿Seguro que quieres descartar este pergamino?')) return;

        const url = form.action;
        // Buscamos el contenedor que queremos que desaparezca
        const card = form.closest('[id^="guide-card-"]') || form.closest('.group') || form.parentElement.parentElement;

        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Si el borrado fue exitoso en la DB, lo quitamos de la vista
                    if (card) {
                        card.style.transition = 'all 0.4s ease';
                        card.style.opacity = '0';
                        card.style.transform = 'scale(0.9)';
                        setTimeout(() => card.remove(), 400);
                    }
                } else {
                    alert('Error: ' + (data.error || 'No se pudo eliminar.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Si el error es 404 significa que ya se borró, así que lo quitamos igual
                if (card) card.remove();
            });
    });
})();