@extends('layouts.master')
@section('title', 'Build — ' . $build->titulo)

@section('content')
<div class="w-[95%] max-w-7xl mx-auto mt-12 mb-12 p-8 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20 text-[#2F2F2F]">
    
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-8">
        <div class="w-full md:w-auto">
            <h1 class="text-5xl font-black tracking-tighter uppercase italic leading-none">
                Build <span class="text-[#6B8E23]">Architect</span>
            </h1>
            <div class="mt-6">
                <div class="w-full sm:w-80">
                    <label class="text-[10px] uppercase font-black text-[#6B8E23] tracking-widest mb-1 block ml-1">Build's Name</label>
                    <div class="text-4xl font-black uppercase tracking-tight leading-none">{{ $build->titulo }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full h-px bg-[#6B8E23]/30 my-8"></div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        {{-- COLUMNA IZQUIERDA: EQUIPAMIENTO --}}
        <div class="lg:col-span-2 space-y-8">
            <section>
                <h3 class="font-black uppercase text-sm tracking-widest mb-6 flex items-center">
                    <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Equipment Loadout
                </h3>
                <div class="grid gap-4">
                    @foreach($equipments as $eq)
                        <div class="bg-white/50 border border-[#6B8E23]/10 p-5 rounded-2xl shadow-sm">
                            <span class="text-[10px] uppercase font-black text-[#6B8E23] tracking-wider mb-1 opacity-70 italic block">
                                @php
                                    $labels = [1 => 'Weapon', 2 => 'Armor Piece', 3 => 'Charm'];
                                    echo $labels[$eq->tipo] ?? 'Equipment';
                                @endphp
                            </span>
                            <span class="font-bold text-lg leading-none">{{ $eq->real_name }}</span>

                            @if(!empty($eq->attached_decos))
                            {{-- Contenedor en columna para que salgan uno debajo del otro --}}
                            <div class="mt-4 flex flex-col gap-2 pt-3 border-t border-[#6B8E23]/10">
                                @foreach($eq->attached_decos as $deco)
                                    @if(isset($deco['is_empty']) && $deco['is_empty'])
                                        {{-- HUECO VACÍO --}}
                                        <div class="flex items-center gap-3 w-full px-3 py-2 rounded-xl border border-[#6B8E23]/10 bg-white/20">
                                            <div class="flex items-center justify-center w-6 h-6 rounded-full border-2 border-[#6B8E23]/30 bg-transparent">
                                                <span class="text-[10px] font-black text-[#6B8E23]/50">{{ $deco['level'] }}</span>
                                            </div>
                                            <span class="text-[10px] font-bold italic text-[#6B8E23]/40 uppercase tracking-widest">
                                                Empty Slot (Lv{{ $deco['level'] }})
                                            </span>
                                        </div>
                                    @else
                                        {{-- JOYA EQUIPADA --}}
                                        <div class="flex items-center gap-3 w-full px-3 py-2 rounded-xl border border-[#6B8E23]/20 bg-white/80 shadow-sm">
                                            <div class="w-6 h-6 rounded-full border-2 border-[#6B8E23] flex items-center justify-center text-[10px] font-black text-[#6B8E23]">
                                                {{ $deco['level'] }}
                                            </div>
                                            <span class="text-[10px] font-black uppercase tracking-tight text-[#2F2F2F]">
                                                {{ $deco['name'] }}
                                            </span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

{{-- COLUMNA DERECHA: SKILLS --}}
<div class="space-y-6">
    {{-- Eliminamos 'sticky' si no quieres que el cuadro se quede pegado, 
         o lo mantenemos si quieres que te siga mientras el resto de la página scrollea --}}
    <div class="bg-white/40 border-2 border-[#6B8E23]/20 rounded-3xl p-6 shadow-inner">
        <h3 class="font-black uppercase text-sm tracking-widest mb-6 flex items-center">
            <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Active Skills
        </h3>
        
        {{-- Eliminado: max-h-[60vh], overflow-y-auto y custom-scrollbar --}}
        <div class="space-y-6">
            @forelse($totalSkills as $name => $lvl)
                @php
                    $skillNameClean = trim($name);
                    $max = $skillMaxLevels[$skillNameClean] ?? 5;
                    $currentLvl = (int)min($lvl, $max);
                    $percent = ($currentLvl / $max) * 100;

                    $skillInfo = collect($skillsData)->first(function($item) use ($skillNameClean) {
                        return trim($item['name']) === $skillNameClean;
                    });

                    $descText = "Description not found.";
                    if ($skillInfo && isset($skillInfo['ranks'][$currentLvl - 1])) {
                        $rank = $skillInfo['ranks'][$currentLvl - 1];
                        $descText = $rank['description'] ?? $rank['desc'] ?? $descText;
                    }
                @endphp

                <div>
                    <div class="flex justify-between items-end mb-1">
                        <span class="font-black uppercase text-[11px] tracking-wider">{{ $skillNameClean }}</span>
                        <span class="text-[#6B8E23] font-black text-xs">Lv {{ $currentLvl }}/{{ $max }}</span>
                    </div>
                    <div class="w-full h-1.5 bg-gray-200/50 rounded-full overflow-hidden mb-2">
                        <div class="h-full bg-[#6B8E23] transition-all duration-500" style="width: {{ $percent }}%"></div>
                    </div>
                    <p class="text-[10px] leading-tight font-bold uppercase opacity-80">
                        {{ $descText }}
                    </p>
                </div>
            @empty
                <p class="py-10 text-center italic text-xs opacity-50 font-bold uppercase tracking-widest">No Skills Detected</p>
            @endforelse
        </div>
    </div>
</div>
    </div>
</div>
@endsection