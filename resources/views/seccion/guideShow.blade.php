@extends('layouts.master')
@section('title', $guide->titulo)

@section('content')
<div class="flex flex-col gap-10 mt-12 mb-12">
    
   {{-- BLOQUE 1: LA GUÍA --}}
<div class="relative w-[90%] md:w-[60%] max-w-4xl mx-auto p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10" style="position: relative;">
    
    <x-save-button :id="$guide->id" type="guide" />

    <div class="flex items-start gap-6 mb-6 border-b pb-4 border-[#6B8E23]/20">
        <div class="flex-shrink-0">
            <x-vote-block :item="$guide" type="guide" />
        </div>
        {{-- Añadimos padding derecho para que el título no choque con el botón --}}
        <h1 class="text-4xl md:text-5xl font-extrabold text-[#6B8E23] pr-28">
            {{ $guide->titulo }}
        </h1>
    </div>
    
    <div class="prose max-w-none text-gray-900 leading-relaxed mb-4">
        {!! nl2br(e($guide->contenido)) !!}
    </div>
</div>

    {{-- BLOQUE 2: COMENTARIOS --}}
    <div class="w-[90%] md:w-[60%] max-w-4xl mx-auto p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
        <x-comments-section :item="$guide" type="guide" />
    </div>

    <div class="w-[90%] md:w-[60%] max-w-4xl mx-auto">
        <a href="{{ url('/guides') }}" class="inline-flex items-center px-4 py-2 bg-[#6B8E23] text-white font-bold rounded hover:bg-[#556b1c] shadow-md uppercase text-xs transition-colors">
            ← Back to Guides
        </a>
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/votes.js') }}"></script>
    <script src="{{ asset('js/comentarios.js') }}"></script>
    <script src="{{ asset('js/universal-save.js') }}"></script>
@endsection