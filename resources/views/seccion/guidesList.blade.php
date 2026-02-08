@extends('layouts.master')
@section('title', 'Guides')

@section('content')

<div class="w-[90%] md:w-[60%] max-w-5xl mx-auto mt-12 mb-12 p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">

    <h1 class="text-4xl md:text-5xl font-extrabold mb-8 text-[#6B8E23] border-b-2 border-[#6B8E23]/20 pb-4">
        Guides
    </h1>

    {{-- PANEL DE FILTROS --}}
    <form method="GET" class="mb-10 flex flex-wrap gap-4">
        @php
            $activeTags = (array) request('tag', []);
        @endphp

        @foreach($activeTags as $t)
            <input type="hidden" name="tag[]" value="{{ $t }}">
        @endforeach

        {{-- Inputs con fondo crema suave --}}
        <input type="text" name="search" placeholder="Search guides..."
               value="{{ request('search') }}"
               class="bg-white/40 border-none px-4 py-2 rounded shadow-inner w-full md:w-auto focus:ring-1 focus:ring-[#6B8E23] outline-none placeholder:text-gray-500">

        <input type="text" name="autor" placeholder="Author..."
               value="{{ request('autor') }}"
               class="bg-white/40 border-none px-4 py-2 rounded shadow-inner w-full md:w-auto focus:ring-1 focus:ring-[#6B8E23] outline-none placeholder:text-gray-500">

        {{-- Ordenar --}}
        <select name="orden"
                class="bg-white/40 border-none px-4 py-2 rounded shadow-inner w-full md:w-auto min-w-[160px] focus:ring-1 focus:ring-[#6B8E23] outline-none text-gray-700">
            <option value="recientes" {{ request('orden')=='recientes' ? 'selected' : '' }}>Most Recent</option>
            <option value="votados" {{ request('orden')=='votados' ? 'selected' : '' }}>Most Voted</option>
        </select>

        <button class="bg-[#6B8E23] hover:bg-[#556b1c] text-white font-bold px-6 py-2 rounded transition-all shadow-md uppercase text-sm tracking-wide">
            Apply Filters
        </button>

        {{-- TAGS DEL FILTRO (Sin fondo gris) --}}
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
                   class="px-3 py-1 text-xs font-bold rounded transition-all uppercase tracking-tighter border
                            {{ $isActive 
                                ? 'bg-[#C67C48] text-white border-[#C67C48] shadow-md' 
                                : 'bg-transparent text-[#6B8E23] border-[#6B8E23]/40 hover:bg-[#6B8E23]/10' }}">
                    {{ $tag->name }}
                </a>
            @endforeach
        </div>
    </form>

    @if($guides->count() === 0)
        <p class="text-center text-lg text-gray-600 italic py-10">
            No guides match your search.
        </p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($guides as $guide)
                {{-- TARJETA (Sin fondo gris/crema y sin bordes pesados) --}}
                <div class="p-6 bg-transparent flex justify-between items-start border-b border-[#6B8E23]/10 md:border-none transition-all">

                    <div class="flex-1 pr-4">
                        <h2 class="text-2xl font-bold mb-2">
                            <a href="{{ route('guides.show', ['slug' => $guide->slug]) }}"
                               class="text-[#6B8E23] hover:text-[#C67C48] transition-colors">
                                {{ $guide->titulo }}
                            </a>
                        </h2>

                        <p class="text-gray-700 mb-4 leading-snug text-sm">
                            {{ Str::limit($guide->contenido, 120) }}
                        </p>

                        {{-- TAGS DE LA GUÍA (Sin fondo) --}}
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($guide->tags as $tag)
                                <span class="px-2 py-0.5 bg-transparent text-[#6B8E23] text-[10px] font-bold uppercase rounded border border-[#6B8E23]/40">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>

                        <p class="text-[11px] text-gray-500 font-medium uppercase tracking-wider">
                            By <span class="text-[#C67C48]">{{ $guide->user->name }}</span>
                            <span class="mx-1">•</span> {{ $guide->created_at->diffForHumans() }}
                        </p>
                    </div>

                    {{-- Votos --}}
                    <div class="pt-1">
                        <x-vote-block :item="$guide" type="guide" />
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $guides->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/votes.js') }}"></script>
@endsection