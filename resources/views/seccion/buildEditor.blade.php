@extends('layouts.master')
@section('title', 'Build Architect')

@section('content')
{{-- FORMULARIO PRINCIPAL --}}
<form id="forgeForm" action="{{ route('builds.store') }}" method="POST">
    @csrf
    {{-- Inputs ocultos para capturar los objetos JS --}}
    <input type="hidden" name="build_data" id="buildDataInput">
    <input type="hidden" name="decorations_data" id="decoDataInput">

    <div class="w-[95%] max-w-7xl mx-auto mt-12 mb-12 p-8 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20">
        
        {{-- HEADER & BUILD NAME --}}
        <div class="flex flex-col md:flex-row items-center justify-between mb-10 border-b border-[#6B8E23]/30 pb-8 gap-6">
            <div class="w-full md:w-auto">
                <h1 class="text-5xl font-black text-[#2F2F2F] tracking-tighter uppercase italic leading-none">
                    Build <span class="text-[#6B8E23]">Architect</span>
                </h1>
                
                <div class="mt-6 flex flex-col gap-6">
                    <div class="w-full sm:w-80">
                        <label for="buildName" class="text-[10px] uppercase font-black text-[#6B8E23] tracking-widest mb-1 block ml-1">
                            Build Designation
                        </label>
                        <input type="text" name="name" id="buildName" required 
                            placeholder="E.g. Rathalos Hunter v1" 
                            class="w-full bg-white border-2 border-[#6B8E23]/30 rounded-xl py-3 px-4 outline-none focus:ring-2 focus:ring-[#6B8E23]/20 focus:border-[#6B8E23] font-bold text-[#2F2F2F] transition-all shadow-sm">
                    </div>

                    {{-- TAG SELECTOR COMPONENT --}}
                    <div id="tagContainer" class="bg-white/40 p-5 rounded-2xl border border-[#6B8E23]/10 shadow-inner">
                        <x-tag-selector :showAll="false" />
                    </div>
                </div>
            </div>
            
            <button type="submit" class="bg-[#6B8E23] hover:bg-[#58751C] text-white px-10 py-5 rounded-2xl font-black uppercase tracking-tighter transition-all shadow-[0_5px_0_0_#4A6318] active:translate-y-1 active:shadow-none shrink-0 group">
                <span class="flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M13 10V3L4 14h7v7l9-11h-7z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Forge Build
                </span>
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            
            {{-- LOADOUT COLUMN --}}
            <div class="lg:col-span-2 space-y-8">
                <section>
                    <h3 class="text-[#2F2F2F] font-black uppercase text-sm tracking-widest mb-4 flex items-center">
                        <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Equipment Loadout
                    </h3>
                    
                    <div id="selected" class="grid gap-3">
                        @foreach (['weapon1', 'weapon2', 'head', 'chest', 'arms', 'waist', 'legs', 'charm'] as $slot)
                            <div class="bg-white border border-[#6B8E23]/10 p-5 rounded-2xl shadow-sm transition-all duration-300 hover:shadow-md">
                                <div class="flex items-center justify-between group cursor-pointer" onclick="openSelector('{{ $slot }}')">
                                    <div class="flex flex-col flex-1">
                                        <span class="text-[10px] uppercase font-black text-[#6B8E23] tracking-wider mb-1 opacity-70 group-hover:opacity-100 italic transition-opacity">
                                            {{ str_replace('1', ' Primary', str_replace('2', ' Secondary', $slot)) }}
                                        </span>
                                        <span id="{{ $slot }}_name" class="text-[#2F2F2F] font-bold text-lg leading-none group-hover:text-[#6B8E23] transition-colors">
                                            — Select Piece —
                                        </span>
                                    </div>
                                    <button type="button" onclick="event.stopPropagation(); clearSlot('{{ $slot }}')" 
                                        class="text-gray-300 hover:text-red-500 transition-colors p-2 rounded-lg hover:bg-red-50" title="Clear Piece">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                                {{-- Contenedor de decoraciones dinámico --}}
                                <div id="{{ $slot }}_slots" class="mt-4 space-y-1.5 border-t border-gray-100 pt-3 hidden"></div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section>
                    <h3 class="text-[#2F2F2F] font-black uppercase text-sm tracking-widest mb-4 flex items-center">
                        <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Hunter's Strategy
                    </h3>
                    <div class="bg-white border border-[#6B8E23]/10 p-6 rounded-2xl shadow-sm">
                        <textarea name="playstyle" id="buildPlaystyle" rows="4" 
                            placeholder="Describe how to play this build, combo tips, or situational use..." 
                            class="w-full bg-white outline-none focus:ring-0 font-medium text-[#2F2F2F] placeholder-[#2F2F2F]/30 resize-none transition-all"></textarea>
                    </div>
                </section>
            </div>

            {{-- SKILLS & STATS COLUMN --}}
            <div class="space-y-6 text-[#2F2F2F]">
                <div class="bg-white/40 border-2 border-[#6B8E23]/20 rounded-3xl p-6 shadow-inner sticky top-6">
                    <h3 class="font-black uppercase text-sm tracking-widest mb-6 flex items-center">
                        <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Active Skills
                    </h3>
                    <div id="skillTotals" class="space-y-3 max-h-[60vh] overflow-y-auto custom-scrollbar pr-2">
                        <p class="text-xs italic opacity-50 text-center py-10 font-bold uppercase tracking-widest">
                            Equip items to see skills
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- MODAL SELECTOR (Fuera del form para evitar interferencias) --}}
<div id="modal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-md z-[9999] flex items-center justify-center p-4 md:p-6">
    <div class="bg-[#F4EBD0] w-full max-w-xl rounded-[2.5rem] shadow-2xl border-4 border-[#6B8E23] flex flex-col overflow-hidden max-h-[85vh]">
        <div class="bg-[#6B8E23] p-6 text-white flex justify-between items-center shrink-0">
            <h3 id="modalTitle" class="text-2xl font-black uppercase italic leading-none tracking-tighter">Forge Inventory</h3>
            <button type="button" onclick="closeModal()" class="text-white hover:rotate-90 transition-transform duration-200 font-bold text-3xl">&times;</button>
        </div>
        <div class="p-5 border-b border-[#6B8E23]/20 bg-white/30 shrink-0">
            <input id="searchInput" type="text" placeholder="Filter by name or skill..." 
                class="w-full bg-white border-2 border-[#6B8E23]/30 rounded-2xl py-4 px-6 outline-none focus:border-[#6B8E23] font-bold text-lg shadow-sm">
        </div>
        <div id="modalList" class="overflow-y-auto p-6 space-y-3 flex-1 bg-[#F4EBD0] custom-scrollbar">
            </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #6B8E23; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    
    .deco-row:hover { background-color: #fdfaf0 !important; border-color: #6B8E23 !important; }
    .deco-row:hover .deco-text { color: #6B8E23 !important; opacity: 1 !important; font-style: normal !important; }

    /* Tags ocultos para el selector de armas */
    .tag-hidden { display: none; }
</style>

<script src="{{ asset('js/build-editor.js') }}"></script>
@endsection