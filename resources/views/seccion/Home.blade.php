@extends('layouts.master')

@section('title', 'Hunter Hub')

@section('content')
<div class="min-h-screen bg-white py-4">
    
    {{-- 1. BLOQUE HERO: Centrado --}}
    <div class="w-[95%] max-w-7xl mx-auto mt-8 mb-8 p-12 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20 text-[#2F2F2F] relative overflow-hidden text-center">
        <div class="relative z-10">
            <span class="text-[#6B8E23] font-black uppercase tracking-[0.4em] text-xs italic">Research Division</span>
            <h1 class="text-5xl md:text-7xl font-black tracking-tighter uppercase italic leading-none mt-4">
                BESTIARY <span class="text-[#C67C48]">FOR</span> HUNTERS
            </h1>
            <p class="mt-6 text-gray-700 max-w-2xl mx-auto font-medium italic text-lg leading-relaxed">
                The definitive archive for tracking, studying, and overcoming the world's most formidable creatures.
            </p>
        </div>
    </div>

    {{-- 2. BLOQUE STRATEGIC --}}
    <div class="w-[95%] max-w-7xl mx-auto mb-8 p-10 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20 text-[#2F2F2F]">
        {{-- Título Centrado con borde inferior sutil --}}
        <div class="text-center mb-10">
            <h2 class="inline-block text-2xl font-black text-[#2F2F2F] uppercase italic border-b-4 border-[#6B8E23] pb-2 tracking-tight">
                Strategic Planning
            </h2>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-home-card title="Build Forge" desc="Engineer your perfect gear set" :link="route('build.editor')" color="bg-[#6B8E23]" />
            <x-home-card title="Armor Sets" desc="Browse community equipment" :link="route('builds.index')" color="bg-[#2F2F2F]" />
            <x-home-card title="Write Scroll" desc="Document your hunting tactics" :link="route('guide.editor')" color="bg-[#2F2F2F]" />
            <x-home-card title="Tactical Guides" desc="Read the ancient field notes" :link="route('guides.index')" color="bg-[#C67C48]" />
        </div>
    </div>

{{-- 3. BLOQUE DATABASE: Estilo escalonado (Zig-Zag) --}}
    <div class="w-[95%] max-w-7xl mx-auto mb-12 p-12 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20 text-[#2F2F2F]">
        {{-- Título Centrado --}}
        <div class="text-center mb-12">
            <h2 class="inline-block text-3xl font-black text-[#2F2F2F] uppercase italic border-b-4 border-[#C67C48] pb-2 tracking-tight">
                The Field Database
            </h2>
        </div>

        <div class="flex flex-col gap-6">
            {{-- Fila 1: 3 Botones --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <a href="{{ url('/weapons') }}" class="flex flex-col items-center justify-center h-28 bg-[#C67C48] border-2 border-[#6B8E23]/20 rounded-2xl hover:border-[#6B8E23] hover:bg-[#6B8E23]/5 transition-all group shadow-sm">
                    <span class="text-sm font-black uppercase italic text-white group-hover:text-[#6B8E23] tracking-widest">Weapons</span>
                    <span class="text-[8px] text-white uppercase font-bold mt-1 group-hover:text-[#6B8E23]/60 tracking-[0.2em]">Offensive Gear</span>
                </a>

                <a href="{{ url('/armors') }}" class="flex flex-col items-center justify-center h-28 bg-[#C67C48] border-2 border-[#6B8E23]/20 rounded-2xl hover:border-[#6B8E23] hover:bg-[#6B8E23]/5 transition-all group shadow-sm">
                    <span class="text-sm font-black uppercase italic text-white group-hover:text-[#6B8E23] tracking-widest">Armors</span>
                    <span class="text-[8px] text-white uppercase font-bold mt-1 group-hover:text-[#6B8E23]/60 tracking-[0.2em]">Protective Sets</span>
                </a>

                <a href="{{ url('/skills') }}" class="flex flex-col items-center justify-center h-28 bg-[#C67C48] border-2 border-[#6B8E23]/20 rounded-2xl hover:border-[#6B8E23] hover:bg-[#6B8E23]/5 transition-all group shadow-sm">
                    <span class="text-sm font-black uppercase italic text-white group-hover:text-[#6B8E23] tracking-widest">Skills</span>
                    <span class="text-[8px] text-white uppercase font-bold mt-1 group-hover:text-[#6B8E23]/60 tracking-[0.2em]">Combat Abilities</span>
                </a>
            </div>

            {{-- Fila 2: 2 Botones centrados en los huecos de arriba --}}
            {{-- Usamos un ancho del 66% (2/3) y lo centramos para que coincidan los ejes --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full md:w-[66%] mx-auto">
                <a href="{{ url('/decorations') }}" class="flex flex-col items-center justify-center h-28 bg-[#C67C48] border-2 border-[#6B8E23]/20 rounded-2xl hover:border-[#6B8E23] hover:bg-[#6B8E23]/5 transition-all group shadow-sm">
                    <span class="text-sm font-black uppercase italic text-white group-hover:text-[#6B8E23] tracking-widest">Decorations</span>
                    <span class="text-[8px] text-white uppercase font-bold mt-1 group-hover:text-[#6B8E23]/60 tracking-[0.2em]">Jewel Slots</span>
                </a>

                <a href="{{ url('/charms') }}" class="flex flex-col items-center justify-center h-28 bg-[#C67C48] border-2 border-[#6B8E23]/20 rounded-2xl hover:border-[#6B8E23] hover:bg-[#6B8E23]/5 transition-all group shadow-sm">
                    <span class="text-sm font-black uppercase italic text-white group-hover:text-[#6B8E23] tracking-widest">Charms</span>
                    <span class="text-[8px] text-white uppercase font-bold mt-1 group-hover:text-[#6B8E23]/60 tracking-[0.2em]">Talismans</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection