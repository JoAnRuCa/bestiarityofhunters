@extends('layouts.master')

@section('content')
<div class="w-[95%] max-w-7xl mx-auto mt-12 mb-12 p-8 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20">
    
    {{-- HEADER: TÍTULO --}}
    <div class="mb-10 border-b border-[#6B8E23]/30 pb-8">
        <h1 class="text-5xl font-black text-[#2F2F2F] tracking-tighter uppercase italic leading-none">
            {{ $build->titulo }}
        </h1>
        <div class="mt-4 flex flex-wrap gap-2">
            @if(isset($build->tags))
                @foreach($build->tags as $tag)
                    <span class="bg-[#6B8E23] text-white text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-widest">
                        {{ $tag->name }}
                    </span>
                @endforeach
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
        
        {{-- COLUMNA IZQUIERDA: EQUIPAMIENTO Y PLAYSTYLE --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- SECCIÓN EQUIPAMIENTO --}}
            <section class="space-y-3">
                <h3 class="text-[#2F2F2F] font-black uppercase text-sm tracking-widest mb-4 flex items-center">
                    <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Equipment Loadout
                </h3>

                @foreach($equipments as $eq)
                <div class="bg-white border border-[#6B8E23]/10 p-5 rounded-2xl shadow-sm flex items-center gap-4 {{ !$eq->real_name ? 'border-dashed opacity-50' : '' }}">
                    <div class="flex-grow">
                        <span class="text-[10px] font-black text-[#6B8E23] uppercase tracking-[0.2em] italic opacity-70">
                            {{ str_replace(['1', '2'], [' Primary', ' Secondary'], ($eq->tipo == 1 ? 'Weapon' : ($eq->tipo == 2 ? 'Armor' : 'Charm'))) }}
                        </span>
                        <h3 class="font-bold text-[#2F2F2F] text-lg leading-tight">
                            {{ $eq->real_name ?? '— Empty Slot —' }}
                        </h3>

                        {{-- DECORACIONES --}}
                        @if(!empty($eq->attached_decos))
                        <div class="mt-3 flex flex-wrap gap-1.5 border-t border-gray-50 pt-3">
                            @foreach($eq->attached_decos as $deco)
                                <span class="px-2 py-0.5 bg-[#F4EBD0]/30 text-[#2F2F2F] text-[10px] font-black rounded-lg border border-[#6B8E23]/10 flex items-center">
                                    <span class="mr-1.5 text-[#6B8E23] opacity-70">💎</span> {{ $deco }}
                                </span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </section>

            {{-- SECCIÓN PLAYSTYLE (DEBAJO DE EQUIPAMIENTO) --}}
            <section>
                <h3 class="text-[#2F2F2F] font-black uppercase text-sm tracking-widest mb-4 flex items-center">
                    <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Playstyle
                </h3>
                <div class="bg-white border border-[#6B8E23]/10 p-6 rounded-2xl shadow-sm min-h-[100px]">
                    <p class="text-[#2F2F2F] font-medium leading-relaxed whitespace-pre-line">
                        {{ $build->playstyle ?: 'No strategic notes provided for this build.' }}
                    </p>
                </div>
            </section>
        </div>

        {{-- COLUMNA DERECHA: HABILIDADES (FIJA) --}}
        <div class="space-y-6">
            <div class="bg-white/40 border-2 border-[#6B8E23]/20 rounded-3xl p-6 shadow-inner sticky top-6">
                <h3 class="text-[#2F2F2F] font-black uppercase text-sm tracking-widest mb-6 flex items-center">
                    <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Active Skills
                </h3>
                
                @forelse($totalSkills as $name => $level)
                    <div class="mb-4 last:mb-0">
                        <div class="flex justify-between items-end mb-1">
                            <span class="text-[#2F2F2F] font-black text-xs uppercase tracking-tight">{{ $name }}</span>
                            <span class="text-[#6B8E23] font-black text-xs italic">LV. {{ $level }}</span>
                        </div>
                        <div class="w-full bg-white rounded-full h-2 p-0.5 border border-[#6B8E23]/10 shadow-sm">
                            <div class="bg-[#6B8E23] h-full rounded-full transition-all duration-700" 
                                 style="width: {{ min(($level / 7) * 100, 100) }}%">
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-[10px] font-black uppercase tracking-widest opacity-30 py-10 italic">
                        No skills detected
                    </p>
                @endforelse

                <div class="mt-8 pt-4 border-t border-[#6B8E23]/20 text-center">
                    <p class="text-[9px] text-[#6B8E23] uppercase tracking-[0.3em] font-black italic">Hunters Bestiary Forge</p>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    /* Scrollbar personalizada para el panel de habilidades */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #6B8E23; border-radius: 10px; }
</style>
@endsection