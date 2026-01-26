@extends('layouts.master')
@section('title', 'Build Editor')

@section('content')

<div>
    <h1>Build Editor</h1>

    {{-- Botones para elegir qué mostrar --}}
    <div>
        <button data-type="weapons">Mostrar armas</button>
        <button data-type="armor-head">Mostrar cascos</button>
        <button data-type="armor-chest">Mostrar pecheras</button>
        <button data-type="armor-arms">Mostrar brazos</button>
        <button data-type="armor-waist">Mostrar cintura</button>
        <button data-type="armor-legs">Mostrar piernas</button>
    </div>

    <hr>

    {{-- Contenedor donde se mostrarán los resultados --}}
    <div id="results"></div>
</div>

{{-- Datos enviados desde el controlador --}}
<script>
    const armors = @json($armors);
    const weapons = @json($weapons);
    const skills = @json($skills);
    const decorations = @json($decorations);
    const charms = @json($charms);
</script>

{{-- Lógica funcional --}}
<script>
    document.querySelectorAll("button[data-type]").forEach(btn => {
        btn.addEventListener("click", () => {
            const type = btn.dataset.type;
            let filtered = [];

            // Mostrar armas
            if (type === "weapons") {
                filtered = weapons;
            }

            // Mostrar armaduras por tipo
            if (type.startsWith("armor-")) {
                const kind = type.replace("armor-", ""); // head, chest, arms, waist, legs
                filtered = armors.filter(a => a.kind === kind);
            }

            renderResults(filtered);
        });
    });

    function renderResults(items) {
        const container = document.getElementById("results");
        container.innerHTML = "";

        if (items.length === 0) {
            container.textContent = "No hay resultados.";
            return;
        }

