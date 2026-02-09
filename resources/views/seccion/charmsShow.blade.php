@extends('layouts.master')
@section('title', $selectedRank['name'])

@section('content')
<div class="w-[90%] md:w-[60%] max-w-4xl mx-auto mt-12 mb-20 p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
    
    {{-- Encabezado: Nombre y Rareza --}}
    <div class="border-b-2 border-[#6B8E23]/20 pb-6 mb-8">
        <div class="flex items-center justify-between gap-4">
            <h1 class="text-4xl md:text-5xl font-extrabold text-[#6B8E23] leading-none">
                {{ $selectedRank['name'] }}
            </h1>
            <div class="flex flex-col items-end gap-2 text-right">
                <span class="px-3 py-1 bg-[#C67C48] text-white text-[10px] font-bold uppercase rounded shadow-sm">
                    Rarity {{ $selectedRank['rarity'] }}
                </span>
                <span class="text-[#2F2F2F] font-bold text-xs uppercase tracking-widest opacity-60">
                    Rank Level {{ $selectedRank['level'] }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
        
        {{-- Columna Izquierda: Información de Forja/Estado --}}
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white/30 p-6 rounded-lg border border-[#6B8E23]/5 shadow-sm flex flex-col items-center justify-center min-h-[150px]">
                <h3 class="text-[#6B8E23] font-bold uppercase tracking-widest text-[10px] mb-4 text-center">Charm Power</h3>
                
                {{-- Icono representativo de Talismán (Nivel de Rank) --}}
                <div class="grid place-items-center w-16 h-16 bg-[#6B8E23] rounded-lg shadow-lg border-2 border-[#F4EBD0]/20 rotate-45">
                    <span class="text-3xl font-bold text-[#F4EBD0] -rotate-45">
                        {{ $selectedRank['level'] }}
                    </span>
                </div>
                <p class="text-[10px] text-gray-500 mt-6 uppercase font-bold tracking-tighter">Artisan Rank</p>
            </div>
        </div>

        {{-- Columna Derecha: Descripción y Habilidades --}}
        <div class="md:col-span-2 space-y-8">
            
            {{-- Descripción del Talismán --}}
            @if(!empty($selectedRank['description']))
                <div>
                    <h3 class="text-[#2F2F2F] font-bold uppercase tracking-widest text-[10px] mb-3 opacity-70">Artisan's Note</h3>
                    <div class="bg-white/30 border-l-4 border-[#C67C48] p-4 rounded-r-lg shadow-sm">
                        <p class="text-gray-800 text-lg leading-relaxed italic">
                            "{{ $selectedRank['description'] }}"
                        </p>
                    </div>
                </div>
            @endif

            {{-- Habilidades otorgadas --}}
            @if(isset($selectedRank['skills']) && count($selectedRank['skills']) > 0)
                <div>
                    <h3 class="text-[#6B8E23] font-bold uppercase tracking-widest text-[10px] mb-4">Charm Skills</h3>
                    <div class="space-y-4">
                        @foreach ($selectedRank['skills'] as $skill)
                            <div class="bg-white/50 border-l-4 border-[#C67C48] p-4 rounded-r-lg shadow-sm">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-bold text-[#2F2F2F] text-lg">{{ $skill['skill']['name'] }}</span>
                                    <span class="bg-[#C67C48] text-white px-2 py-0.5 rounded text-xs font-bold">
                                        Lv {{ $skill['level'] }}
                                    </span>
                                </div>
                                <p class="text-gray-700 text-sm leading-snug">
                                    <strong class="text-[#2F2F2F]">Level {{ $skill['level'] }}:</strong> {{ $skill['description'] }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Botón de Retorno --}}
    <div class="mt-12 pt-6 border-t border-[#6B8E23]/10 text-center">
        <a href="{{ route('charms.index') }}" class="inline-flex items-center text-[#6B8E23] text-sm font-bold hover:text-[#C67C48] transition-colors group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            Back to Charms
        </a>
    </div>
</div>
@endsection