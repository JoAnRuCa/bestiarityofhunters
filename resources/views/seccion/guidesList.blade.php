@extends('layouts.master')
@section('title', 'Guides')

@section('content')

<div class="w-[60%] max-w-5xl mx-auto mt-12 mb-12 p-8 bg-[#F4EBD0] rounded-lg shadow-lg">

    <h1 class="text-4xl md:text-5xl font-extrabold mb-8 text-[#6B8E23] border-b pb-4">
        Guides
    </h1>

{{-- PANEL DE FILTROS --}}
<form method="GET" class="mb-8 flex flex-wrap gap-4">

    {{-- Mantener tags activos al enviar el formulario --}}
    @php
        $activeTags = (array) request('tag', []);
    @endphp

    @foreach($activeTags as $t)
        <input type="hidden" name="tag[]" value="{{ $t }}">
    @endforeach

    {{-- Buscar por título/contenido --}}
    <input type="text" name="search" placeholder="Search guides..."
           value="{{ request('search') }}"
           class="border px-3 py-2 rounded w-full md:w-auto">

    {{-- Buscar por autor --}}
    <input type="text" name="autor" placeholder="Author..."
           value="{{ request('autor') }}"
           class="border px-3 py-2 rounded w-full md:w-auto">

    {{-- TAGS COMO BOTONES --}}
    <div class="flex flex-wrap gap-2 w-full">

        @foreach(\App\Models\Tag::all() as $tag)
            @php
                $isActive = in_array($tag->name, $activeTags);

                // Construir nueva query con el tag añadido o quitado
                $newQuery = request()->all();
                $newQuery['tag'] = $activeTags;

                if (!$isActive) {
                    $newQuery['tag'][] = $tag->name;
                } else {
                    $newQuery['tag'] = array_filter($activeTags, fn($t) => $t !== $tag->name);
                }
            @endphp

            <a href="{{ url('/guides?' . http_build_query($newQuery)) }}"
               class="px-3 py-1 text-sm rounded transition
                        {{ $isActive 
                            ? 'bg-[#C67C48] text-white hover:bg-[#A8643C]' 
                            : 'bg-[#6B8E23] text-white hover:bg-[#556b1c]' }}">
                {{ $tag->name }}
            </a>
        @endforeach

    </div>

    {{-- Ordenar --}}
    <select name="orden"
            class="border px-3 py-2 rounded w-full md:w-auto min-w-[160px] pr-8">
        <option value="recientes" {{ request('orden')=='recientes' ? 'selected' : '' }}>Most Recent</option>
        <option value="votados" {{ request('orden')=='votados' ? 'selected' : '' }}>Most Voted</option>
    </select>

    <button class="bg-[#6B8E23] text-white px-4 py-2 rounded w-full md:w-auto">
        Apply Filters
    </button>

</form>

@if($guides->count() === 0)
    <p class="text-center text-lg text-gray-700">
        No guides match your search.
    </p>
@else

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        @foreach($guides as $guide)
            <div class="p-6 bg-white rounded-lg shadow border border-gray-200 flex justify-between items-center">

                {{-- CONTENIDO --}}
                <div class="flex-1 pr-4">

                    {{-- TÍTULO CLICABLE CON SLUG --}}
                    <h2 class="text-2xl font-bold mb-2">
                        <a href="{{ route('guides.show', ['slug' => $guide->slug]) }}"
                           class="text-[#6B8E23] hover:text-[#556b1c] transition">
                            {{ $guide->titulo }}
                        </a>
                    </h2>

                    <p class="text-gray-700 mb-3">
                        {{ Str::limit($guide->contenido, 150) }}
                    </p>

                    {{-- TAGS CLICABLES --}}
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($guide->tags as $tag)
                            @php
                                $newQuery = request()->all();
                                $newQuery['tag'] = [$tag->name];
                            @endphp

                            <a href="{{ url('/guides?' . http_build_query($newQuery)) }}"
                               class="px-3 py-1 bg-[#6B8E23] text-white text-sm rounded hover:bg-[#556b1c] transition">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>

                    {{-- AUTOR --}}
                    <p class="text-sm text-gray-600">
                        By <strong>{{ $guide->user->name }}</strong>
                        • {{ $guide->created_at->diffForHumans() }}
                    </p>
                </div>

                {{-- SISTEMA DE VOTOS --}}
                <div class="flex flex-col items-center justify-center gap-2 vote-container"
                     data-guide="{{ $guide->id }}">

                    <button class="vote-btn upvote">
                        <svg class="arrow-up" width="40" height="40" viewBox="0 0 24 24"
                             stroke="#6B8E23" stroke-width="2" fill="none">
                            <path d="M12 6 L6 14 H18 Z"></path>
                        </svg>
                    </button>

                    <div class="text-xl font-bold vote-score"
                         style="color: {{ $guide->score() > 0 ? '#6B8E23' : ($guide->score() < 0 ? '#2F2F2F' : '#555') }}">
                        {{ $guide->score() }}
                    </div>

                    <button class="vote-btn downvote">
                        <svg class="arrow-down" width="40" height="40" viewBox="0 0 24 24"
                             stroke="#2F2F2F" stroke-width="2" fill="none">
                            <path d="M12 18 L6 10 H18 Z"></path>
                        </svg>
                    </button>

                </div>

            </div>
        @endforeach

    </div>

    {{-- PAGINACIÓN --}}
    <div class="mt-8">
        {{ $guides->links() }}
    </div>

@endif

</div>

@endsection

@section('scripts')
<script src="{{ asset('js/votes.js') }}"></script>
@endsection
