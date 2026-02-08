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
        
        <div id="active-tags-inputs">
            @foreach($activeTags as $t)
                <input type="hidden" name="tag[]" value="{{ $t }}">
            @endforeach
        </div>

        <div class="flex flex-wrap gap-4 items-center">
           {{-- Search: Sin la clase 'uppercase' --}}
<input type="text" name="search" placeholder="Search guides..." 
       value="{{ request('search') }}" 
       class="bg-white border border-[#C67C48]/30 px-4 py-2 rounded text-xs font-bold tracking-tighter text-gray-700 focus:ring-1 focus:ring-[#6B8E23] outline-none placeholder:text-gray-500 w-full md:w-auto shadow-sm">

{{-- Author: Sin la clase 'uppercase' --}}
<input type="text" name="autor" placeholder="Author..." 
       value="{{ request('autor') }}" 
       class="bg-white border border-[#C67C48]/30 px-4 py-2 rounded text-xs font-bold tracking-tighter text-gray-700 focus:ring-1 focus:ring-[#6B8E23] outline-none placeholder:text-gray-500 w-full md:w-auto shadow-sm">
            
            {{-- Orden: Un poco más ancho (pr-10) para que la flecha no pise el texto --}}
            <select name="orden" class="bg-white border border-[#C67C48]/30 pl-4 pr-10 py-2 rounded text-xs font-bold tracking-tighter text-gray-700 focus:ring-1 focus:ring-[#6B8E23] outline-none cursor-pointer w-full md:w-auto min-w-[180px] appearance-none shadow-sm" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23C67C48%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.7rem center; background-size: 1em;">
                <option value="recientes" {{ request('orden')=='recientes' ? 'selected' : '' }}>Most recent</option>
                <option value="votados" {{ request('orden')=='votados' ? 'selected' : '' }}>Most voted</option>
            </select>

            <button type="submit" class="bg-[#6B8E23] hover:bg-[#556b1c] text-white font-bold px-6 py-2 rounded transition-all shadow-md uppercase text-xs tracking-widest h-full">
                APPLY FILTERS
            </button>
        </div>

        {{-- SECCIÓN DE TAGS --}}
        <div class="flex flex-wrap gap-2 w-full border-t border-[#6B8E23]/10 pt-4">
            @foreach(\App\Models\Tag::all() as $tag)
                @php $isActive = in_array($tag->name, $activeTags); @endphp
                <button type="button" 
                   class="tag-link px-3 py-1 text-[10px] font-bold uppercase rounded transition-all duration-200 border
                          {{ $isActive 
                              ? 'bg-[#C67C48] text-white border-[#C67C48] shadow-md hover:bg-[#a1633a]' 
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
<script src="{{ asset('js/guide-list.js') }}"></script>
@endsection