@extends('layouts.master')
@section('title', 'Guides')

@section('content')
<div class="w-[90%] md:w-[60%] max-w-5xl mx-auto mt-12 mb-12 p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
    <h1 class="text-4xl md:text-5xl font-extrabold mb-8 text-[#6B8E23] border-b-2 border-[#6B8E23]/20 pb-4">Guides</h1>

    <form id="filter-form" method="GET" class="mb-10 flex flex-wrap gap-4">
        @php $activeTags = (array) request('tag', []); @endphp
        <div id="active-tags-inputs">
            @foreach($activeTags as $t)
                <input type="hidden" name="tag[]" value="{{ $t }}">
            @endforeach
        </div>

        <input type="text" name="search" placeholder="Search guides..." value="{{ request('search') }}" class="bg-white/40 border-none px-4 py-2 rounded shadow-inner w-full md:w-auto focus:ring-1 focus:ring-[#6B8E23] outline-none">
        <input type="text" name="autor" placeholder="Author..." value="{{ request('autor') }}" class="bg-white/40 border-none px-4 py-2 rounded shadow-inner w-full md:w-auto focus:ring-1 focus:ring-[#6B8E23] outline-none">
        
        <select name="orden" class="bg-white/40 border-none px-4 py-2 rounded shadow-inner w-full md:w-auto focus:ring-1 focus:ring-[#6B8E23] outline-none">
            <option value="recientes" {{ request('orden')=='recientes' ? 'selected' : '' }}>Most Recent</option>
            <option value="votados" {{ request('orden')=='votados' ? 'selected' : '' }}>Most Voted</option>
        </select>

        <button type="submit" class="bg-[#6B8E23] text-white font-bold px-6 py-2 rounded shadow-md uppercase text-sm">Apply Filters</button>

        <div class="flex flex-wrap gap-2 w-full mt-2">
            @foreach(\App\Models\Tag::all() as $tag)
                @php
                    $isActive = in_array($tag->name, $activeTags);
                    $newQuery = request()->all();
                    $newQuery['tag'] = $activeTags;
                    if (!$isActive) { $newQuery['tag'][] = $tag->name; } 
                    else { $newQuery['tag'] = array_filter($activeTags, fn($t) => $t !== $tag->name); }
                @endphp
                <a href="{{ url('/guides?' . http_build_query($newQuery)) }}" 
                   class="tag-link px-2 py-0.5 text-[10px] font-bold uppercase rounded shadow-sm {{ $isActive ? 'bg-[#6B8E23] text-white' : 'bg-[#C67C48]/20 text-[#C67C48]' }}"
                   data-tag="{{ $tag->name }}" data-active="{{ $isActive ? 'true' : 'false' }}">
                    {{ $tag->name }}
                </a>
            @endforeach
        </div>
    </form>

    <div id="guides-wrapper">
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
        } catch (e) { console.error(e); }
        wrapper.style.opacity = '1';
    }

    // Clic en Tags
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('tag-link')) {
            e.preventDefault();
            fetchGuides(e.target.href);
            // Aquí podrías añadir lógica para cambiar el color del tag instantáneamente
        }
        
        // Clic en Paginación (opcional pero recomendado)
        if (e.target.closest('.pagination-ajax a')) {
            e.preventDefault();
            fetchGuides(e.target.closest('a').href);
        }
    });

    // Filtros por formulario (Search/Select)
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new URLSearchParams(new FormData(form)).toString();
        fetchGuides(`${form.action}?${formData}`);
    });
});
</script>
@endsection