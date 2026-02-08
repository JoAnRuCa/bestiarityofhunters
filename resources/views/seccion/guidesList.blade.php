@extends('layouts.master')
@section('title', 'Guides')

@section('content')
<div class="w-[90%] md:w-[60%] max-w-5xl mx-auto mt-12 mb-12 p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
    <h1 class="text-4xl md:text-5xl font-extrabold mb-8 text-[#6B8E23] border-b-2 border-[#6B8E23]/20 pb-4">
        Guides
    </h1>

    {{-- PANEL DE FILTROS --}}
    <form id="filter-form" action="{{ url('/guides') }}" method="GET" class="mb-10 flex flex-col gap-6">
        @php $activeTags = (array) request('tag', []); @endphp
        
        {{-- CONTENEDOR DE INPUTS OCULTOS --}}
        <div id="active-tags-inputs">
            @foreach($activeTags as $t)
                <input type="hidden" name="tag[]" value="{{ $t }}">
            @endforeach
        </div>

        <div class="flex flex-wrap gap-4">
            <input type="text" name="search" placeholder="SEARCH GUIDES..." 
                   value="{{ request('search') }}" 
                   class="bg-white/40 border border-[#C67C48]/30 px-4 py-2 rounded text-xs font-bold uppercase tracking-tighter text-gray-700 focus:ring-1 focus:ring-[#6B8E23] focus:bg-white outline-none placeholder:text-gray-500 w-full md:w-auto">

            <input type="text" name="autor" placeholder="AUTHOR..." 
                   value="{{ request('autor') }}" 
                   class="bg-white/40 border border-[#C67C48]/30 px-4 py-2 rounded text-xs font-bold uppercase tracking-tighter text-gray-700 focus:ring-1 focus:ring-[#6B8E23] focus:bg-white outline-none placeholder:text-gray-500 w-full md:w-auto">
            
            <select name="orden" class="bg-white/40 border border-[#C67C48]/30 px-4 py-2 rounded text-xs font-bold uppercase tracking-tighter text-gray-700 focus:ring-1 focus:ring-[#6B8E23] outline-none cursor-pointer w-full md:w-auto">
                <option value="recientes" {{ request('orden')=='recientes' ? 'selected' : '' }}>MOST RECENT</option>
                <option value="votados" {{ request('orden')=='votados' ? 'selected' : '' }}>MOST VOTED</option>
            </select>

            <button type="submit" class="bg-[#6B8E23] hover:bg-[#556b1c] text-white font-bold px-6 py-2 rounded transition-all shadow-md uppercase text-xs tracking-widest">
                APPLY FILTERS
            </button>
        </div>

        {{-- SECCIÓN DE TAGS --}}
        <div class="flex flex-wrap gap-2 w-full border-t border-[#6B8E23]/10 pt-4">
            @foreach(\App\Models\Tag::all() as $tag)
                @php
                    $isActive = in_array($tag->name, $activeTags);
                @endphp
                {{-- Eliminamos el href complejo y dejamos que el JS maneje la URL --}}
                <button type="button" 
                   class="tag-link px-3 py-1 text-[10px] font-bold uppercase rounded transition-all duration-200 border
                          {{ $isActive 
                              ? 'bg-[#C67C48] text-white border-[#C67C48] shadow-md' 
                              : 'bg-transparent text-[#C67C48] border-[#C67C48]/40 hover:bg-[#C67C48]/10' }}"
                   data-tag="{{ $tag->name }}" 
                   data-active="{{ $isActive ? 'true' : 'false' }}">
                    {{ $tag->name }}
                </button>
            @endforeach
        </div>
    </form>

    <div id="guides-wrapper" class="transition-opacity duration-300">
        @include('seccion.partials._guides_items')
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/votes.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.getElementById('guides-wrapper');
    const form = document.getElementById('filter-form');
    const tagsInputsContainer = document.getElementById('active-tags-inputs');

    // Función para obtener la URL actual con todos los filtros del formulario
    function getFilterUrl() {
        const formData = new FormData(form);
        const params = new URLSearchParams(formData);
        return `${form.action}?${params.toString()}`;
    }

    async function fetchGuides(url) {
        wrapper.style.opacity = '0.5';
        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await response.json();
            wrapper.innerHTML = data.html;
            window.history.pushState({}, '', url);
            if (typeof initVotes === 'function') initVotes();
        } catch (e) {
            console.error("Error:", e);
        }
        wrapper.style.opacity = '1';
    }

    // Manejo de clics en Tags
    document.addEventListener('click', function(e) {
        const tagBtn = e.target.closest('.tag-link');
        if (tagBtn) {
            const tagName = tagBtn.getAttribute('data-tag');
            const isActive = tagBtn.getAttribute('data-active') === 'true';

            if (isActive) {
                // DESACTIVAR
                tagBtn.classList.remove('bg-[#C67C48]', 'text-white', 'border-[#C67C48]', 'shadow-md');
                tagBtn.classList.add('bg-transparent', 'text-[#C67C48]', 'border-[#C67C48]/40');
                tagBtn.setAttribute('data-active', 'false');
                
                const input = tagsInputsContainer.querySelector(`input[value="${tagName}"]`);
                if (input) input.remove();
            } else {
                // ACTIVAR
                tagBtn.classList.remove('bg-transparent', 'text-[#C67C48]', 'border-[#C67C48]/40');
                tagBtn.classList.add('bg-[#C67C48]', 'text-white', 'border-[#C67C48]', 'shadow-md');
                tagBtn.setAttribute('data-active', 'true');

                const newInput = document.createElement('input');
                newInput.type = 'hidden';
                newInput.name = 'tag[]';
                newInput.value = tagName;
                tagsInputsContainer.appendChild(newInput);
            }

            // Tras actualizar los inputs, pedimos las guías con el estado actual del form
            fetchGuides(getFilterUrl());
        }

        // Paginación
        const pageLink = e.target.closest('.pagination-ajax a');
        if (pageLink) {
            e.preventDefault();
            fetchGuides(pageLink.href);
        }
    });

    // Filtros manuales
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        fetchGuides(getFilterUrl());
    });
});
</script>
@endsection