@extends('layouts.master')
@section('title', 'Build — ' . $build->titulo)

@section('content')
<div class="w-[95%] max-w-7xl mx-auto mt-12 mb-12 p-8 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20 text-[#2F2F2F]">
    
    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-8">
        <div class="w-full md:w-auto">
            <h1 class="text-5xl font-black tracking-tighter uppercase italic leading-none">
                Build <span class="text-[#6B8E23]">Review</span>
            </h1>
            <div class="mt-6 flex flex-col gap-4">
                <div class="w-full">
                    <label class="text-[10px] uppercase font-black text-[#C67C48] tracking-widest mb-1 block ml-1">Build's Name</label>
                    <div class="text-4xl font-black tracking-tight leading-none mb-6">{{ $build->titulo }}</div>
                    
                    <div class="flex flex-wrap gap-3">
                        @forelse($build->tags as $tag)
                            <div class="flex items-center gap-2 bg-[#C67C48] text-white px-4 py-2 rounded-xl shadow-sm">
                                <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                                <span class="text-xs font-black uppercase italic tracking-tight">{{ $tag->name }}</span>
                            </div>
                        @empty
                            <span class="text-[10px] font-bold italic opacity-30 uppercase tracking-widest ml-1">No tags assigned</span>
                        @endforelse
                    </div>
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
                            {{-- Ahora la etiqueta viene lista del controlador --}}
                            <span class="text-[10px] uppercase font-black text-[#6B8E23] tracking-wider mb-1 opacity-70 italic block">
                                {{ $eq->tipo_label }}
                            </span>
                            <span class="font-bold text-lg leading-none">{{ $eq->real_name }}</span>

                            @if(!empty($eq->attached_decos))
                            <div class="mt-4 flex flex-col gap-2 pt-3 border-t border-[#6B8E23]/10">
                                @foreach($eq->attached_decos as $deco)
                                    @if($deco['is_empty'])
                                        <div class="flex items-center gap-3 w-full px-3 py-2 rounded-xl border border-[#6B8E23]/10 bg-white/20">
                                            <div class="flex items-center justify-center w-6 h-6 rounded-full border-2 border-[#6B8E23]/30 bg-transparent">
                                                <span class="text-[10px] font-black text-[#6B8E23]/50">{{ $deco['level'] }}</span>
                                            </div>
                                            <span class="text-[10px] font-bold italic text-[#6B8E23]/40 uppercase tracking-widest">
                                                Empty Slot (Lv{{ $deco['level'] }})
                                            </span>
                                        </div>
                                    @else
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

        {{-- COLUMNA DERECHA: SKILLS (LIMPIEZA TOTAL) --}}
        <div class="space-y-6">
            <div class="bg-white/40 border-2 border-[#6B8E23]/20 rounded-3xl p-6 shadow-inner">
                <h3 class="font-black uppercase text-sm tracking-widest mb-6 flex items-center">
                    <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Active Skills
                </h3>
                
                <div class="space-y-6">
                    @forelse($totalSkills as $skill)
                        <div class="skill-row">
                            <div class="flex justify-between items-end mb-1">
                                <span class="font-black uppercase text-[11px] tracking-wider">{{ $skill['name'] }}</span>
                                <span class="text-[#6B8E23] font-black text-xs">Lv {{ $skill['lvl'] }}/{{ $skill['max'] }}</span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-200/50 rounded-full overflow-hidden mb-2">
                                <div class="h-full bg-[#6B8E23] transition-all duration-500" style="width: {{ $skill['percent'] }}%"></div>
                            </div>
                            <p class="text-[10px] leading-tight font-bold uppercase opacity-80">
                                {{ $skill['desc'] }}
                            </p>
                        </div>
                    @empty
                        <p class="py-10 text-center italic text-xs opacity-50 font-bold uppercase tracking-widest">No Skills Detected</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if($build->playstyle)
        <div class="w-full h-px bg-[#6B8E23]/30 my-8"></div>
        <section>
            <h3 class="font-black uppercase text-sm tracking-widest mb-6 flex items-center">
                <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Playstyle & Strategy
            </h3>
            <div class="bg-white/30 border border-[#6B8E23]/10 p-6 rounded-2xl">
                <p class="text-sm font-bold italic leading-relaxed opacity-80 whitespace-pre-line">{{ $build->playstyle }}</p>
            </div>
        </section>
    @endif
</div>
@endsection