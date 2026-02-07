@extends('layouts.master')
@section('title', 'Guides')

@section('content')

<div class="w-[60%] max-w-5xl mx-auto mt-12 mb-12 p-8 bg-[#F4EBD0] rounded-lg shadow-lg">

    <h1 class="text-4xl md:text-5xl font-extrabold mb-8 text-[#6B8E23] border-b pb-4">
        Guides
    </h1>

    @if($guides->count() === 0)
        <p class="text-center text-lg text-gray-700">
            No guides have been created yet.
        </p>
    @else

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            @foreach($guides as $guide)
                <div class="p-6 bg-white rounded-lg shadow border border-gray-200 flex justify-between items-center">

                    {{-- CONTENIDO --}}
                    <div class="flex-1 pr-4">
                        <h2 class="text-2xl font-bold text-[#6B8E23] mb-2">
                            {{ $guide->titulo }}
                        </h2>

                        <p class="text-gray-700 mb-3">
                            {{ Str::limit($guide->contenido, 150) }}
                        </p>

                        {{-- TAGS --}}
                        <div class="flex flex-wrap gap-2 mb-3">
                            @foreach($guide->tags as $tag)
                                <span class="px-3 py-1 bg-[#6B8E23] text-white text-sm rounded">
                                    {{ $tag->name }}
                                </span>
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

                        {{-- Flecha arriba --}}
                        <button class="vote-btn upvote">
                            <svg class="arrow-up" width="40" height="40" viewBox="0 0 24 24"
                                 stroke="#6B8E23" stroke-width="2" fill="none">
                                <path d="M12 6 L6 14 H18 Z"></path>
                            </svg>
                        </button>

                        {{-- Contador --}}
                        <div class="text-xl font-bold vote-score"
                             style="color: {{ $guide->score() > 0 ? '#6B8E23' : ($guide->score() < 0 ? '#2F2F2F' : '#555') }}">
                            {{ $guide->score() }}
                        </div>

                        {{-- Flecha abajo --}}
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
