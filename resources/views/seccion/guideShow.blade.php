@extends('layouts.master')
@section('title', $guide->titulo)

@section('content')
<div class="flex flex-col gap-10 mt-12 mb-12">
    
    {{-- BLOQUE 1: LA GUÍA --}}
    <div class="relative w-[90%] md:w-[60%] max-w-4xl mx-auto p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
        
        {{-- Llamada al componente universal --}}
        <x-save-button :id="$guide->id" type="guide" />

        <div class="flex items-center gap-6 mb-6 border-b pb-4 border-[#6B8E23]/20">
            <div class="flex-shrink-0">
                <x-vote-block :item="$guide" type="guide" />
            </div>
            
            <div class="flex flex-col gap-3">
                <h1 class="text-4xl md:text-5xl font-extrabold text-[#6B8E23] pr-32 leading-tight">
                    {{ $guide->titulo }}
                </h1>

                {{-- SECCIÓN DE TAGS (ESTILO #C67C48) --}}
                <div class="flex flex-wrap gap-2">
                    @forelse($guide->tags as $tag)
                        <div class="flex items-center gap-2 bg-[#C67C48] text-white px-3 py-1.5 rounded-xl shadow-sm">
                            <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-[10px] font-black uppercase italic tracking-tight">
                                {{ $tag->name }}
                            </span>
                        </div>
                    @empty
                        {{-- Opcional: podrías dejarlo vacío o con el mensaje de "No tags" --}}
                    @endforelse
                </div>
            </div>
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