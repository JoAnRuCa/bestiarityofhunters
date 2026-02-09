@extends('layouts.master')
@section('title', $armor['name'])

@section('content')
<div class="w-[90%] md:w-[70%] max-w-5xl mx-auto mt-12 mb-20 p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
    
    {{-- Encabezado: Nombre y Rareza --}}
    <div class="border-b-2 border-[#6B8E23]/20 pb-6 mb-8">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-[#6B8E23] leading-none mb-2">
                    {{ $armor['name'] }}
                </h1>
                <p class="text-gray-600 italic leading-relaxed text-sm">"{{ $armor['description'] }}"</p>
            </div>
            <div class="flex flex-col items-end gap-2">
                <span class="px-3 py-1 bg-[#C67C48] text-white text-[10px] font-bold uppercase rounded shadow-sm">
                    Rarity {{ $armor['rarity'] }}
                </span>
                <span class="text-[#2F2F2F] font-bold text-xs uppercase tracking-widest opacity-60">
                    {{ ucfirst($armor['kind']) }} / {{ ucfirst($armor['rank']) }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Columna Izquierda: Estadísticas (Defensa y Resistencias) --}}
        <div class="lg:col-span-1 space-y-8">
            {{-- Defensa --}}
            @if(isset($armor['defense']))
                <div class="bg-white/30 p-4 rounded-lg border border-[#6B8E23]/5">
                    <h3 class="text-[#6B8E23] font-bold uppercase tracking-widest text-xs mb-4">Defense</h3>
                    <div class="flex justify-between items-center border-b border-[#6B8E23]/10 pb-2 mb-2">
                        <span class="text-gray-600 text-sm font-medium">Base</span>
                        <span class="text-lg font-bold text-[#2F2F2F]">{{ $armor['defense']['base'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm font-medium">Maximum</span>
                        <span class="text-lg font-bold text-[#6B8E23]">{{ $armor['defense']['max'] }}</span>
                    </div>
                </div>
            @endif

            {{-- Resistencias --}}
            @if(isset($armor['resistances']))
                <div class="bg-white/30 p-4 rounded-lg border border-[#6B8E23]/5">
                    <h3 class="text-[#6B8E23] font-bold uppercase tracking-widest text-xs mb-4">Resistances</h3>
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($armor['resistances'] as $element => $value)
                            <div class="flex flex-col border-b border-[#6B8E23]/5 pb-1">
                                <span class="text-[10px] uppercase font-bold text-gray-500">{{ $element }}</span>
                                <span class="font-bold {{ $value > 0 ? 'text-[#6B8E23]' : ($value < 0 ? 'text-red-800' : 'text-gray-700') }}">
                                    {{ $value > 0 ? '+' : '' }}{{ $value }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Slots --}}
            @if(isset($armor['slots']) && count($armor['slots']) > 0)
                <div class="bg-white/30 p-4 rounded-lg border border-[#6B8E23]/5">
                    <h3 class="text-[#6B8E23] font-bold uppercase tracking-widest text-xs mb-3">Decoration Slots</h3>
                    <div class="flex gap-2">
                        @foreach ($armor['slots'] as $slot)
                            <span class="w-8 h-8 flex items-center justify-center bg-[#2F2F2F] text-[#F4EBD0] rounded-full text-xs font-bold shadow-md">
                                {{ $slot }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Columna Derecha: Skills y Set --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Skills --}}
            @if(isset($armor['skills']) && count($armor['skills']) > 0)
                <div>
                    <h3 class="text-[#6B8E23] font-bold uppercase tracking-widest text-xs mb-4">Equipment Skills</h3>
                    <div class="space-y-3">
                        @foreach ($armor['skills'] as $skill)
                            <div class="bg-white/50 border-l-4 border-[#C67C48] p-4 rounded-r-lg shadow-sm transition-all hover:bg-white/70">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-bold text-[#2F2F2F]">{{ $skill['skill']['name'] }}</span>
                                    <span class="text-[#C67C48] font-bold text-sm uppercase">Lv {{ $skill['level'] }}</span>
                                </div>
                                <p class="text-gray-600 text-xs leading-snug">{{ $skill['description'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Armor Set --}}
            @if(isset($armor['armorSet']))
                <div class="pt-6 border-t border-[#6B8E23]/10">
                    <h3 class="text-[#2F2F2F] font-bold uppercase tracking-widest text-[10px] mb-2 opacity-60">Armor Set</h3>
                    <div class="inline-flex items-center px-4 py-2 bg-white/40 border border-[#6B8E23]/20 rounded-md">
                        <span class="text-[#6B8E23] font-bold">{{ $armor['armorSet']['name'] }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Botón de Retorno --}}
    <div class="mt-12 pt-6 border-t border-[#6B8E23]/10">
        <a href="{{ route('armors.index') }}" class="inline-flex items-center text-[#6B8E23] text-sm font-bold hover:text-[#C67C48] transition-colors group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            Back to Armors
        </a>
    </div>
</div>
@endsection