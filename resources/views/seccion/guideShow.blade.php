@extends('layouts.master')
@section('title', $guide->titulo)

@section('content')

<div class="w-[60%] max-w-4xl mx-auto mt-12 mb-12 p-8 bg-[#F4EBD0] rounded-lg shadow-lg">

    {{-- TÍTULO + VOTOS (CORREGIDO: YA NO ESTÁ DENTRO DEL H1) --}}
    <div class="flex items-center justify-between mb-6 border-b pb-4">
        <h1 class="text-4xl md:text-5xl font-extrabold text-[#6B8E23]">
            {{ $guide->titulo }}
        </h1>

        <x-vote-block :item="$guide" type="guide" />
    </div>

    {{-- Autor y fecha --}}
    <p class="text-gray-700 mb-4">
        By <strong>{{ $guide->user->name }}</strong> • {{ $guide->created_at->diffForHumans() }}
    </p>

    {{-- Tags --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach($guide->tags as $tag)
            <span class="px-3 py-1 bg-[#6B8E23] text-white text-sm rounded">
                {{ $tag->name }}
            </span>
        @endforeach
    </div>

    {{-- Contenido --}}
    <div class="prose max-w-none text-gray-900 leading-relaxed">
        {!! nl2br(e($guide->contenido)) !!}
    </div>

    {{-- Botón volver --}}
    <div class="mt-8">
        <a href="{{ url('/guides') }}"
           class="px-4 py-2 bg-[#6B8E23] text-white rounded hover:bg-[#556b1c] transition">
            ← Back to Guides
        </a>
    </div>

</div>

@endsection

@section('scripts')
<script src="{{ asset('js/votes.js') }}"></script>
@endsection
