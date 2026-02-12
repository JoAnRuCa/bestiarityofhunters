function initVotes() {
    document.querySelectorAll('.vote-container').forEach(container => {
        if (container.dataset.initialized) return;
        container.dataset.initialized = "true";

        const upBtn = container.querySelector('.upvote');
        const downBtn = container.querySelector('.downvote');
        const scoreBox = container.querySelector('.vote-score');
        const upSvg = container.querySelector('.arrow-up');
        const downSvg = container.querySelector('.arrow-down');

        const ejecutarVoto = (tipo) => {
            // 1. COMPROBACIÓN: Si no hay usuario en el body, abortamos la ejecución
            // Asegúrate de tener <body data-auth="{{ auth()->check() ? 'true' : 'false' }}">
            const isGuest = document.body.dataset.auth === 'false';
            if (isGuest) {
                return; // No hace nada, ni redirección ni fetch
            }

            const url = container.dataset.url || '/votar';

            fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    id: container.dataset.id,
                    tipo: tipo,
                    model: container.dataset.model
                })
            })
                .then(res => {
                    // Si el servidor responde 401 (aunque hayamos validado antes en JS)
                    if (res.status === 401) return;
                    return res.json();
                })
                .then(data => {
                    if (data && data.voto !== undefined) {
                        // Actualizar colores de las flechas
                        upSvg.setAttribute("fill", data.voto === 1 ? "#6B8E23" : "none");
                        downSvg.setAttribute("fill", data.voto === -1 ? "#2F2F2F" : "none");

                        // Actualizar número y su color
                        scoreBox.textContent = data.score;
                        if (data.score > 0) {
                            scoreBox.style.color = "#6B8E23";
                        } else if (data.score < 0) {
                            scoreBox.style.color = "#2F2F2F";
                        } else {
                            scoreBox.style.color = "#555";
                        }
                    }
                })
                .catch(err => {
                    // Opcional: silenciar errores de consola para invitados
                });
        };

        upBtn.onclick = (e) => { e.preventDefault(); ejecutarVoto(1); };
        downBtn.onclick = (e) => { e.preventDefault(); ejecutarVoto(-1); };
    });
}

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', initVotes);

// Re-inicializar si usas AJAX para cargar las listas
window.addEventListener('contentUpdated', initVotes);