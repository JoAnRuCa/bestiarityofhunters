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

        {{-- GRID: 2 columnas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            @foreach($guides as $guide)
                <div class="p-6 bg-white rounded-lg shadow border border-gray-200">

                    {{-- Título --}}
                    <h2 class="text-2xl font-bold text-[#6B8E23] mb-2">
                        {{ $guide->titulo }}
                    </h2>

                    {{-- Contenido resumido --}}
                    <p class="text-gray-700 mb-3">
                        {{ Str::limit($guide->contenido, 150) }}
                    </p>

                    {{-- Tags --}}
                    <div class="flex flex-wrap gap-2 mb-3">
                        @foreach($guide->tags as $tag)
                            <span class="px-3 py-1 bg-[#6B8E23] text-white text-sm rounded">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>

                    {{-- Autor y fecha --}}
                    <p class="text-sm text-gray-600">
                        By <strong>{{ $guide->user->name }}</strong>
                        • {{ $guide->created_at->diffForHumans() }}
                    </p>

                </div>
            @endforeach

        </div>

        {{-- Paginación --}}
        <div class="mt-8">
            {{ $guides->links() }}
        </div>

    @endif

</div>

@endsection
