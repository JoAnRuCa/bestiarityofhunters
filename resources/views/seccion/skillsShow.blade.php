@extends('layouts.master')
@section('title', $skill['name'])

@section('content')
<div class="w-[90%] md:w-[60%] max-w-4xl mx-auto mt-12 mb-20 p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
    
    {{-- Encabezado: Nombre y Tipo (Centrado Vertical) --}}
    <div class="border-b-2 border-[#6B8E23]/20 pb-6 mb-8">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-4xl md:text-5xl font-extrabold text-[#6B8E23] leading-none">
                {{ $skill['name'] }}
            </h1>
            {{-- Badge centrado verticalmente respecto al título --}}
            <span class="inline-flex items-center justify-center px-3 py-2 bg-[#C67C48] text-white text-xs font-bold uppercase rounded shadow-sm h-fit">
                {{ ucfirst($skill['kind']) }} Skill
            </span>
        </div>
    </div>

    {{-- Descripción General --}}
    @if(!empty($skill['description']))
        <div class="mb-10">
            <h3 class="text-[#2F2F2F] font-bold uppercase tracking-widest text-xs mb-3 opacity-70">Description</h3>
            <p class="text-gray-800 text-lg leading-relaxed italic">
                "{{ $skill['description'] }}"
            </p>
        </div>
    @endif

    {{-- Sección de Ranks (Sin flecha en el título) --}}
    @if(isset($skill['ranks']) && count($skill['ranks']) > 0)
        <div class="mt-10">
            <h3 class="text-[#6B8E23] font-bold uppercase tracking-widest text-xs mb-6">
                Skill Ranks
            </h3>

            <div class="space-y-3">
                @foreach ($skill['ranks'] as $rank)
                    <div class="bg-white/40 border-l-4 border-[#C67C48] p-4 rounded-r-lg shadow-sm hover:bg-white/60 transition-colors flex items-center">
                        <p class="text-gray-700 leading-snug">
                            <span class="font-bold text-[#2F2F2F] mr-2">Lv {{ $rank['level'] }}:</span>
                            {{ $rank['description'] }}
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Botón de Retorno --}}
    <div class="mt-12 pt-6 border-t border-[#6B8E23]/10">
        <a href="{{ route('skills.index') }}" class="inline-flex items-center text-[#6B8E23] text-sm font-bold hover:text-[#C67C48] transition-colors group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            Back to Skills
        </a>
    </div>
</div>
@endsection