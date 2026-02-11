@extends('layouts.master')
@section('title', $build->titulo)

@section('content')
<div class="w-[95%] max-w-7xl mx-auto mt-12 mb-12 p-8 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20">
    
    {{-- HEADER & BUILD NAME (Estilo Editor) --}}
    <div class="flex flex-col md:flex-row items-center justify-between mb-10 border-b border-[#6B8E23]/30 pb-8 gap-6">
        <div class="w-full md:w-auto">
            <h1 class="text-5xl font-black text-[#2F2F2F] tracking-tighter uppercase italic leading-none">
                Build <span class="text-[#6B8E23]">Architect</span>
            </h1>
            
            <div class="mt-6">
                <div class="w-full sm:w-80">
                    <label class="text-[10px] uppercase font-black text-[#6B8E23] tracking-widest mb-1 block ml-1">
                        Build Designation
                    </label>
                    <div class="w-full bg-white border-2 border-[#6B8E23]/30 rounded-xl py-3 px-4 font-bold text-[#2F2F2F] shadow-sm">
                        {{ $build->titulo }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        {{-- COLUMNA LOADOUT --}}
        <div class="lg:col-span-2 space-y-8">
            <section>
                <h3 class="text-[#2F2F2F] font-black uppercase text-sm tracking-widest mb-4 flex items-center">
                    <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Equipment Loadout
                </h3>
                
                <div class="grid gap-3">
                    @foreach($equipments as $eq)
                        <div class="bg-white border border-[#6B8E23]/10 p-5 rounded-2xl shadow-sm">
                            <div class="flex flex-col">
                                <span class="text-[10px] uppercase font-black text-[#6B8E23] tracking-wider mb-1 opacity-70 italic">
                                    @php
                                        $labels = [1 => 'Weapon', 2 => 'Armor Piece', 3 => 'Charm'];
                                        echo $labels[$eq->tipo] ?? 'Equipment';
                                    @endphp
                                </span>
                                <span class="text-[#2F2F2F] font-bold text-lg leading-none">
                                    {{ $eq->real_name ?? '— Empty Slot —' }}
                                </span>
                            </div>

                            @if(!empty($eq->attached_decos))
                            <div class="mt-4 space-y-1.5 border-t border-gray-100 pt-3">
                                @foreach($eq->attached_decos as $deco)
                                    <div class="flex items-center gap-3 p-2 rounded-xl border border-dashed bg-gray-50 border-gray-200">
                                        <div class="w-5 h-5 rounded-full border-2 border-[#6B8E23] flex items-center justify-center text-[9px] font-black text-[#6B8E23] bg-white shadow-sm">
                                            L
                                        </div>
                                        <span class="text-xs font-bold text-[#2F2F2F]">
                                            {{ $deco }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            <section>
                <h3 class="text-[#2F2F2F] font-black uppercase text-sm tracking-widest mb-4 flex items-center">
                    <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Hunter's Strategy
                </h3>
                <div class="bg-white border border-[#6B8E23]/10 p-6 rounded-2xl shadow-sm">
                    <p class="font-medium text-[#2F2F2F] whitespace-pre-line text-sm leading-relaxed">
                        {{ $build->playstyle ?: 'No strategic notes provided.' }}
                    </p>
                </div>
            </section>
        </div>

{{-- COLUMNA SKILLS (IDÉNTICA AL EDITOR) --}}
<div class="space-y-6 text-[#2F2F2F]">
    <div class="bg-white/40 border-2 border-[#6B8E23]/20 rounded-3xl p-6 shadow-inner sticky top-6">
        <h3 class="font-black uppercase text-sm tracking-widest mb-6 flex items-center">
            <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Active Skills
        </h3>
        
        <div id="skillTotals" class="space-y-3 max-h-[60vh] overflow-y-auto custom-scrollbar pr-2">
            @forelse($totalSkills as $name => $lvl)
                @php
                    // Lógica idéntica a tu JS: cappedLvl = Math.min(lvl, max)
                    $max = $skillMaxLevels[$name] ?? 5;
                    $cappedLvl = min($lvl, $max);
                    $percent = ($cappedLvl / $max) * 100;
                @endphp
                <div class="mb-4">
                    <div class="flex justify-between items-end mb-1">
                        <span class="font-black uppercase text-[11px] tracking-wider">{{ $name }}</span>
                        <span class="text-[#6B8E23] font-black text-xs">Lv {{ $cappedLvl }}/{{ $max }}</span>
                    </div>
                    <div class="w-full h-1.5 bg-gray-200 rounded-full overflow-hidden">
                        {{-- Transición y color idénticos --}}
                        <div class="h-full bg-[#6B8E23] transition-all duration-500" 
                             style="width: {{ $percent }}%"></div>
                    </div>
                </div>
            @empty
                <p class="text-xs italic opacity-50 text-center py-10 font-bold uppercase tracking-widest">
                    No Skills Detected
                </p>
            @endforelse
        </div>
    </div>
</div>

    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #6B8E23; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
</style>
@endsection