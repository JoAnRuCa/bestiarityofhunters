@extends('layouts.master')
@section('title', $guide->titulo)

@section('content')
<div class="w-[60%] max-w-4xl mx-auto mt-12 mb-12 p-8 bg-[#F4EBD0] rounded-lg shadow-lg">
    
    {{-- Encabezado de la guía --}}
    <div class="flex items-center justify-between mb-6 border-b pb-4 border-[#6B8E23]/20">
        <h1 class="text-4xl md:text-5xl font-extrabold text-[#6B8E23]">{{ $guide->titulo }}</h1>
        <x-vote-block :item="$guide" type="guide" />
    </div>

    {{-- Contenido de la guía (Asegúrate de mostrar el contenido aquí si lo necesitas) --}}
    <div class="prose max-w-none text-gray-900 leading-relaxed mb-12">
        {!! nl2br(e($guide->contenido)) !!}
    </div>

    <hr class="border-t border-[#6B8E23]/30 my-8">

    {{-- SECCIÓN DE COMENTARIOS REUTILIZABLE --}}
    {{-- Llamamos al componente padre pasándole el objeto y el discriminador --}}
    <x-comments-section :item="$guide" type="guide" />

    <div class="mt-12 pt-6 border-t border-[#6B8E23]/20">
        <a href="{{ url('/guides') }}" class="inline-flex items-center px-4 py-2 bg-[#6B8E23] text-white font-bold rounded hover:bg-[#556b1c] shadow-md uppercase text-xs">← Back to Guides</a>
    </div>
</div>
@endsection

@section('scripts')
    {{-- Scripts externos unificados --}}
    <script src="{{ asset('js/votes.js') }}"></script>
    <script src="{{ asset('js/comentarios.js') }}"></script>
@endsection