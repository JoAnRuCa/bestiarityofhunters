document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.vote-container').forEach(container => {

        // Estado interno de cada componente
        let votoActual = parseInt(container.dataset.voto);
        const id = container.dataset.id;
        const model = container.dataset.model;
        const url = container.dataset.url;

        // Elementos del DOM
        const upSvg = container.querySelector('.arrow-up');
        const downSvg = container.querySelector('.arrow-down');
        const scoreBox = container.querySelector('.vote-score');
        const upBtn = container.querySelector('.upvote');
        const downBtn = container.querySelector('.downvote');

        // Función para actualizar colores de las flechas
        function pintarVoto() {
            upSvg.setAttribute("fill", votoActual === 1 ? "#6B8E23" : "none");
            downSvg.setAttribute("fill", votoActual === -1 ? "#2F2F2F" : "none");
        }

        // Función para actualizar el color y número del score
        function updateScore(score) {
            scoreBox.textContent = score;
            if (score > 0) {
                scoreBox.style.color = "#6B8E23";
            } else if (score < 0) {
                scoreBox.style.color = "#2F2F2F";
            } else {
                scoreBox.style.color = "#555";
            }
        }

        // Función principal de envío
        function ejecutarVoto(tipo) {
            fetch(url, {
                method: "POST",
                credentials: "same-origin",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "Accept": "application/json"
                },
                body: JSON.stringify({
                    id: id,
                    tipo: tipo,
                    model: model
                })
            })
                .then(res => {
                    if (res.status === 401) {
                        alert("You must be logged in to vote.");
                        return null;
                    }
                    return res.json();
                })
                .then(data => {
                    if (!data || data.error) return;

                    // Actualizamos estado y UI
                    votoActual = data.voto;
                    pintarVoto();
                    updateScore(data.score);
                })
                .catch(err => console.error("Error en la petición de voto:", err));
        }

        // Listeners
        upBtn.addEventListener('click', () => ejecutarVoto(1));
        downBtn.addEventListener('click', () => ejecutarVoto(-1));
    });
});