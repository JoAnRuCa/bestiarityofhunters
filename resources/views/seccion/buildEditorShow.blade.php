@extends('layouts.master')
@section('title', 'Build — ' . $build->titulo)

@section('content')
<div class="w-[95%] max-w-7xl mx-auto mt-12 mb-12 p-8 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20">
    
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-8">
        <div class="w-full md:w-auto">
            <h1 class="text-5xl font-black text-[#2F2F2F] tracking-tighter uppercase italic leading-none">
                Build <span class="text-[#6B8E23]">Architect</span>
            </h1>
            
            <div class="mt-6">
                <div class="w-full sm:w-80">
                    <label class="text-[10px] uppercase font-black text-[#6B8E23] tracking-widest mb-1 block ml-1">
                        Build's Name
                    </label>
                    <div class="text-4xl font-black text-[#2F2F2F] uppercase tracking-tight leading-none">
                        {{ $build->titulo }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- LÍNEA 1 --}}
    <div class="w-full h-px bg-[#6B8E23]/30 my-8"></div>

    {{-- SECCIÓN TAGS --}}
    @if($build->tags && $build->tags->count() > 0)
    <div class="px-2">
        <div class="flex flex-wrap gap-3">
            @foreach($build->tags as $tag)
                <span class="px-4 py-1.5 bg-[#6B8E23]/10 border border-[#6B8E23]/40 text-[#6B8E23] text-xs font-black uppercase tracking-widest rounded-lg">
                    # {{ $tag->name }}
                </span>
            @endforeach
        </div>
    </div>

    {{-- LÍNEA 2 --}}
    <div class="w-full h-px bg-[#6B8E23]/30 my-8"></div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        {{-- COLUMNA IZQUIERDA --}}
        <div class="lg:col-span-2 flex flex-col">
            
            <section class="mb-8">
                <h3 class="text-[#2F2F2F] font-black uppercase text-sm tracking-widest mb-6 flex items-center">
                    <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Equipment Loadout
                </h3>
                
                <div class="grid gap-4">
                    @foreach($equipments as $eq)
                        <div class="bg-white/50 border border-[#6B8E23]/10 p-5 rounded-2xl shadow-sm">
                            <div class="flex flex-col">
                                <span class="text-[10px] uppercase font-black text-[#6B8E23] tracking-wider mb-1 opacity-70 italic">
                                    @php
                                        $labels = [1 => 'Weapon', 2 => 'Armor Piece', 3 => 'Charm'];
                                        echo $labels[$eq->tipo] ?? 'Equipment';
                                    @endphp
                                </span>
                                <span class="text-[#2F2F2F] font-bold text-lg leading-none">
                                    {{ $eq->real_name }}
                                </span>
                            </div>

                            @if(!empty($eq->attached_decos))
                            <div class="mt-4 flex flex-wrap gap-2 pt-3 border-t border-[#6B8E23]/10">
                                @foreach($eq->attached_decos as $deco)
                                    <div class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-[#6B8E23]/20 bg-white/80">
                                        <div class="w-5 h-5 rounded-full border-2 border-[#6B8E23] flex items-center justify-center text-[10px] font-black text-[#6B8E23] bg-white">
                                            {{ $deco['level'] }}
                                        </div>
                                        <span class="text-[10px] font-bold text-[#2F2F2F] uppercase tracking-tight">
                                            {{ $deco['name'] }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- LÍNEA 3 --}}
            <div class="w-full h-px bg-[#6B8E23]/30 my-8"></div>

            <section class="mb-12">
                <h3 class="text-[#2F2F2F] font-black uppercase text-sm tracking-widest mb-4 flex items-center">
                    <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Hunter's Strategy
                </h3>
                <div class="px-2">
                    <p class="font-bold text-[#2F2F2F] text-xl italic leading-relaxed whitespace-pre-line opacity-80">
                        "{{ $build->playstyle ?: 'No strategic notes provided.' }}"
                    </p>
                </div>
            </section>
        </div>

        {{-- COLUMNA DERECHA --}}
        <div class="space-y-6 text-[#2F2F2F]">
            <div class="bg-white/40 border-2 border-[#6B8E23]/20 rounded-3xl p-6 shadow-inner sticky top-6">
                <h3 class="font-black uppercase text-sm tracking-widest mb-6 flex items-center">
                    <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Active Skills
                </h3>
                
                <div class="space-y-4 max-h-[60vh] overflow-y-auto custom-scrollbar pr-2">
                    @forelse($totalSkills as $name => $lvl)
                        @php
                            $max = $skillMaxLevels[$name] ?? 5;
                            $percent = (min($lvl, $max) / $max) * 100;
                        @endphp
                        <div>
                            <div class="flex justify-between items-end mb-1">
                                <span class="font-black uppercase text-[11px] tracking-wider">{{ $name }}</span>
                                <span class="text-[#6B8E23] font-black text-xs">Lv {{ min($lvl, $max) }}/{{ $max }}</span>
                            </div>
                            <div class="w-full h-2 bg-gray-200/50 rounded-full overflow-hidden">
                                <div class="h-full bg-[#6B8E23] transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="py-10 text-center italic text-xs opacity-50 font-bold uppercase tracking-widest">No Skills Detected</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #6B8E23; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(107, 142, 35, 0.1); }
</style>
@endsection