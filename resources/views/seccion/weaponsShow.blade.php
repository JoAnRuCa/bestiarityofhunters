@extends('layouts.master')
@section('title', $weapon['name'])

@section('content')
<div class="w-[90%] md:w-[70%] max-w-5xl mx-auto mt-12 mb-20 p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
    
    {{-- Encabezado --}}
    <div class="border-b-2 border-[#6B8E23]/20 pb-6 mb-8">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-[#6B8E23] leading-none mb-2">
                    {{ $weapon['name'] }}
                </h1>
                <p class="text-gray-600 italic leading-relaxed text-sm">
                    "{{ $weapon['description'] ?? 'No description available for this relic.' }}"
                </p>
            </div>
            <div class="flex flex-col items-end gap-2 text-right">
                <span class="px-3 py-1 bg-[#C67C48] text-white text-[10px] font-bold uppercase rounded shadow-sm">
                    {{ str_replace('-', ' ', $weapon['kind'] ?? 'Unknown') }}
                </span>
                <span class="text-[#2F2F2F] font-bold text-xs uppercase tracking-widest opacity-60">
                    Rarity {{ $weapon['rarity'] ?? '—' }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- Columna Izquierda: Estadísticas de Combate --}}
        <div class="lg:col-span-1 space-y-6">
            
            {{-- Ataque Base --}}
            <div class="bg-white/30 p-4 rounded-lg border border-[#6B8E23]/5 shadow-sm">
                <h3 class="text-[#6B8E23] font-bold uppercase tracking-widest text-[10px] mb-4">Attack Power</h3>
                <div class="flex justify-between items-center mb-1">
                    <span class="text-gray-600 text-sm">Raw Damage</span>
                    <span class="text-lg font-bold text-[#2F2F2F]">{{ $weapon['damage']['raw'] ?? '—' }}</span>
                </div>
                <div class="flex justify-between items-center opacity-70">
                    <span class="text-xs italic">Display</span>
                    <span class="text-sm font-bold">{{ $weapon['damage']['display'] ?? '—' }}</span>
                </div>
            </div>

            {{-- Sharpness (Barra Visual con Números) --}}
            @if(isset($weapon['sharpness']) && count($weapon['sharpness']) > 0)
                <div class="bg-white/30 p-4 rounded-lg border border-[#6B8E23]/5 shadow-sm">
                    <h3 class="text-[#6B8E23] font-bold uppercase tracking-widest text-[10px] mb-3">Sharpness</h3>
                    
                    @php
                        $sharpnessColors = [
                            'red' => 'bg-red-600',
                            'orange' => 'bg-orange-500',
                            'yellow' => 'bg-yellow-400',
                            'green' => 'bg-green-500',
                            'blue' => 'bg-blue-600',
                            'white' => 'bg-slate-100',
                            'purple' => 'bg-purple-600'
                        ];
                    @endphp

                    {{-- Contenedor de la Barra --}}
                    <div class="flex h-4 w-full rounded-sm overflow-hidden border border-black/20 bg-gray-300 shadow-inner">
                        @foreach ($weapon['sharpness'] as $color => $value)
                            @if($value > 0)
                                <div class="{{ $sharpnessColors[strtolower($color)] ?? 'bg-gray-400' }}" 
                                    style="width: {{ $value }}%; min-width: 2px;" 
                                    title="{{ ucfirst($color) }}: {{ $value }}">
                                </div>
                            @endif
                        @endforeach
                    </div>

                    {{-- Contenedor de los Números (Alineados) --}}
                    <div class="flex w-full mt-1">
                        @foreach ($weapon['sharpness'] as $color => $value)
                            @if($value > 0)
                                <div style="width: {{ $value }}%;" class="text-[9px] text-center font-bold text-gray-600">
                                    {{ $value }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
@endif

            {{-- Afinidad y Defensa --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white/30 p-3 rounded-lg border border-[#6B8E23]/5 shadow-sm text-center">
                    <span class="text-[9px] uppercase font-bold text-gray-500 block mb-1">Affinity</span>
                    <span class="text-md font-bold {{ ($weapon['affinity'] ?? 0) > 0 ? 'text-[#6B8E23]' : (($weapon['affinity'] ?? 0) < 0 ? 'text-red-800' : 'text-gray-700') }}">
                        {{ $weapon['affinity'] ?? 0 }}%
                    </span>
                </div>
                <div class="bg-white/30 p-3 rounded-lg border border-[#6B8E23]/5 shadow-sm text-center">
                    <span class="text-[9px] uppercase font-bold text-gray-500 block mb-1">Def. Bonus</span>
                    <span class="text-md font-bold text-gray-700">+{{ $weapon['defenseBonus'] ?? 0 }}</span>
                </div>
            </div>

            {{-- Slots --}}
            @if(isset($weapon['slots']) && count($weapon['slots']) > 0)
                <div class="bg-white/30 p-4 rounded-lg border border-[#6B8E23]/5 shadow-sm">
                    <h3 class="text-[#6B8E23] font-bold uppercase tracking-widest text-[10px] mb-3 text-center lg:text-left">Decoration Slots</h3>
                    <div class="flex justify-center lg:justify-start gap-2">
                        @foreach ($weapon['slots'] as $slot)
                            <span class="w-8 h-8 flex items-center justify-center bg-[#2F2F2F] text-[#F4EBD0] rounded-full text-xs font-bold shadow-md border border-[#F4EBD0]/20">
                                {{ $slot }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Columna Derecha: Efectos Especiales y Skills --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Specials (Elementos/Estados) --}}
            @if(isset($weapon['specials']) && count($weapon['specials']) > 0)
                <div>
                    <h3 class="text-[#6B8E23] font-bold uppercase tracking-widest text-[10px] mb-4">Special Effects</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($weapon['specials'] as $special)
                            <div class="bg-white/50 p-4 rounded-lg border-l-4 border-[#6B8E23] shadow-sm">
                                <div class="flex justify-between items-start">
                                    <span class="font-bold text-[#2F2F2F]">{{ ucfirst($special['element'] ?? $special['status'] ?? 'Unknown') }}</span>
                                    <span class="text-[9px] uppercase font-bold opacity-60">({{ $special['kind'] ?? '—' }})</span>
                                </div>
                                <div class="mt-2 flex items-baseline gap-4">
                                    <span class="text-sm font-bold text-[#2F2F2F]">{{ $special['damage']['display'] ?? $special['damage']['raw'] ?? '—' }}</span>
                                    @if($special['hidden'] ?? false)
                                        <span class="text-red-800 font-bold text-[9px] uppercase tracking-widest bg-red-100 px-1 rounded">Hidden</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Weapon Skills --}}
            @if(isset($weapon['skills']) && count($weapon['skills']) > 0)
                <div>
                    <h3 class="text-[#6B8E23] font-bold uppercase tracking-widest text-[10px] mb-4">Weapon Skills</h3>
                    <div class="space-y-3">
                        @foreach ($weapon['skills'] as $skill)
                            <div class="bg-white/50 border-l-4 border-[#C67C48] p-4 rounded-r-lg shadow-sm">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="font-bold text-[#2F2F2F]">{{ $skill['skill']['name'] ?? 'Skill' }}</span>
                                    <span class="text-[#C67C48] font-bold text-sm uppercase">Lv {{ $skill['level'] ?? '—' }}</span>
                                </div>
                                <p class="text-gray-600 text-[11px] leading-tight italic mb-2">{{ $skill['skill']['description'] ?? '' }}</p>
                                <p class="text-gray-800 text-xs font-medium">{{ $skill['description'] ?? '' }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Series --}}
            @if(isset($weapon['series']))
                <div class="pt-6 border-t border-[#6B8E23]/10 text-right">
                    <h3 class="text-[#2F2F2F] font-bold uppercase tracking-widest text-[9px] mb-2 opacity-60">Part of the Series</h3>
                    <span class="inline-block px-4 py-2 bg-white/40 border border-[#6B8E23]/20 rounded text-[#6B8E23] font-bold text-sm">
                        {{ $weapon['series']['name'] ?? 'Unknown Series' }}
                    </span>
                </div>
            @endif
        </div>
    </div>

    {{-- Botón de Retorno --}}
    <div class="mt-12 pt-6 border-t border-[#6B8E23]/10 flex justify-center">
        <a href="{{ route('weapons.index') }}" class="inline-flex items-center text-[#6B8E23] text-sm font-bold hover:text-[#C67C48] transition-colors group">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12" />
            </svg>
            Back to Weapons
        </a>
    </div>
</div>
@endsection