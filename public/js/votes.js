function initVotes() {
    document.querySelectorAll('.vote-container').forEach(container => {
        // Evitar duplicar eventos si ya se inicializó
        if (container.dataset.initialized) return;
        container.dataset.initialized = "true";

        const upBtn = container.querySelector('.upvote');
        const downBtn = container.querySelector('.downvote');
        const scoreBox = container.querySelector('.vote-score');
        const upSvg = container.querySelector('.arrow-up');
        const downSvg = container.querySelector('.arrow-down');

        const ejecutarVoto = (tipo) => {
            fetch(container.dataset.url, {
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
                .then(res => res.json())
                .then(data => {
                    if (data.voto !== undefined) {
                        upSvg.setAttribute("fill", data.voto === 1 ? "#6B8E23" : "none");
                        downSvg.setAttribute("fill", data.voto === -1 ? "#2F2F2F" : "none");
                        scoreBox.textContent = data.score;
                        scoreBox.style.color = data.score > 0 ? "#6B8E23" : (data.score < 0 ? "#2F2F2F" : "#555");
                    }
                });
        };

        upBtn.onclick = () => ejecutarVoto(1);
        downBtn.onclick = () => ejecutarVoto(-1);
    });
}

document.addEventListener('DOMContentLoaded', initVotes);