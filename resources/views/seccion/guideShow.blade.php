@extends('layouts.master')
@section('title', $guide->titulo)

@section('content')
<div class="flex flex-col gap-10 mt-12 mb-12">
    
    {{-- BLOQUE 1: LA GUÍA (Mantiene su estilo de tarjeta) --}}
    <div class="w-[60%] max-w-4xl mx-auto p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
        <div class="flex items-center justify-between mb-6 border-b pb-4 border-[#6B8E23]/20">
            <h1 class="text-4xl md:text-5xl font-extrabold text-[#6B8E23]">{{ $guide->titulo }}</h1>
            <x-vote-block :item="$guide" type="guide" />
        </div>

        <div class="prose max-w-none text-gray-900 leading-relaxed mb-4">
            {!! nl2br(e($guide->contenido)) !!}
        </div>
    </div>

    {{-- BLOQUE 2: COMENTARIOS (Sin sombras pesadas ni bordes grises) --}}
    <div class="w-[60%] max-w-4xl mx-auto p-8 bg-[#F4EBD0] rounded-lg shadow-sm">
        <x-comments-section :item="$guide" type="guide" />
    </div>

    {{-- BOTÓN VOLVER --}}
    <div class="w-[60%] max-w-4xl mx-auto">
        <a href="{{ url('/guides') }}" class="inline-flex items-center px-4 py-2 bg-[#6B8E23] text-white font-bold rounded hover:bg-[#556b1c] shadow-md uppercase text-xs transition-colors">
            ← Back to Guides
        </a>
    </div>

</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/votes.js') }}"></script>
    <script src="{{ asset('js/comentarios.js') }}"></script>
@endsection