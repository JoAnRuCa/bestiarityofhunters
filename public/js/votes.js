document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.vote-container').forEach(container => {

        const upBtn = container.querySelector('.upvote');
        const downBtn = container.querySelector('.downvote');
        const upSvg = container.querySelector('.arrow-up');
        const downSvg = container.querySelector('.arrow-down');
        const scoreBox = container.querySelector('.vote-score');
        const guideId = container.dataset.guide;

        function updateScore(score) {
            scoreBox.textContent = score;

            if (score > 0) scoreBox.style.color = "#6B8E23";
            else if (score < 0) scoreBox.style.color = "#2F2F2F";
            else scoreBox.style.color = "#555";
        }

        function resetArrows() {
            upSvg.setAttribute("fill", "none");
            downSvg.setAttribute("fill", "none");
        }

        function votar(tipo) {
            return fetch("votar", {
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
                .then(async res => {
                    console.log("STATUS:", res.status);

                    // Si el backend devuelve HTML (error 500), evitamos el crash
                    const text = await res.text();
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error("Respuesta no JSON:", text);
                        return null;
                    }
                })
                .then(data => {
                    if (!data) return;
                    console.log("DATA:", data);
                    updateScore(data.score);
                })
                .catch(err => console.error("Error en fetch:", err));
        }

        upBtn.addEventListener('click', () => {
            const active = upSvg.getAttribute("fill") !== "none";

            resetArrows();

            if (!active) {
                upSvg.setAttribute("fill", "#6B8E23");
                votar(1);
            } else {
                votar(1);
            }
        });

        downBtn.addEventListener('click', () => {
            const active = downSvg.getAttribute("fill") !== "none";

            resetArrows();

            if (!active) {
                downSvg.setAttribute("fill", "#2F2F2F");
                votar(-1);
            } else {
                votar(-1);
            }
        });

    });
});
