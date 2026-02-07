document.addEventListener('DOMContentLoaded', () => {

    document.querySelectorAll('.vote-container').forEach(container => {

        // Cada contenedor tiene su propio estado
        let votoActual = parseInt(container.dataset.voto);

        const upBtn = container.querySelector('.upvote');
        const downBtn = container.querySelector('.downvote');
        const upSvg = container.querySelector('.arrow-up');
        const downSvg = container.querySelector('.arrow-down');
        const scoreBox = container.querySelector('.vote-score');
        const guideId = container.dataset.guide;
        const url = container.dataset.url;

        function pintarVoto() {
            upSvg.setAttribute("fill", votoActual === 1 ? "#6B8E23" : "none");
            downSvg.setAttribute("fill", votoActual === -1 ? "#2F2F2F" : "none");
        }

        // 🔥 PINTAR AL CARGAR
        pintarVoto();

        function updateScore(score) {
            scoreBox.textContent = score;
            scoreBox.style.color =
                score > 0 ? "#6B8E23" :
                    score < 0 ? "#2F2F2F" :
                        "#555";
        }

        function votar(tipo) {
            return fetch(url, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    guide_id: guideId,
                    tipo: tipo
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (!data) return;

                    // 🔥 ACTUALIZAR SOLO ESTE COMPONENTE
                    votoActual = data.voto;

                    // 🔥 REPINTAR FLECHAS
                    pintarVoto();

                    // 🔥 ACTUALIZAR CONTADOR
                    updateScore(data.score);
                });
        }

        // 🔼 CLICK EN UPVOTE
        upBtn.addEventListener('click', () => {
            votar(1);
        });

        // 🔽 CLICK EN DOWNVOTE
        downBtn.addEventListener('click', () => {
            votar(-1);
        });

    });

});
