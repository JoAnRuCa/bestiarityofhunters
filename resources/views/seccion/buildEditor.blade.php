@extends('layouts.master')
@section('title', 'Build Editor')

@section('content')
<div class="w-[95%] max-w-7xl mx-auto mt-12 mb-12 p-8 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20">
    
    {{-- HEADER & BUILD NAME --}}
    <div class="flex flex-col md:flex-row items-center justify-between mb-10 border-b border-[#6B8E23]/30 pb-8 gap-6">
        <div class="w-full md:w-auto">
            <h1 class="text-5xl font-black text-[#2F2F2F] tracking-tighter uppercase italic leading-none">
                Build <span class="text-[#6B8E23]">Architect</span>
            </h1>
            <div class="mt-4 flex flex-col sm:flex-row gap-4 items-end sm:items-center">
                <div class="w-full sm:w-80">
                    <label for="buildName" class="text-[10px] uppercase font-black text-[#6B8E23] tracking-widest mb-1 block ml-1">Build Designation</label>
                    <input type="text" id="buildName" placeholder="E.g. Rathalos Hunter v1" 
                        class="w-full bg-white border-2 border-[#6B8E23]/30 rounded-xl py-3 px-4 outline-none focus:ring-0 focus:border-[#6B8E23]/30 font-bold text-[#2F2F2F] transition-all shadow-sm">
                </div>
            </div>
        </div>
        
        <button onclick="saveBuild()" class="bg-[#6B8E23] hover:bg-[#58751C] text-white px-10 py-5 rounded-2xl font-black uppercase tracking-tighter transition-all shadow-[0_5px_0_0_#4A6318] active:translate-y-1 active:shadow-none shrink-0">
            Forge Build
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        {{-- LEFT COLUMN: LOADOUT & PLAYSTYLE --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- EQUIPMENT --}}
            <section>
                <h3 class="text-[#2F2F2F] font-black uppercase text-sm tracking-widest mb-4 flex items-center">
                    <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Equipment Loadout
                </h3>
                <div id="selected" class="grid gap-3">
                    @foreach (['weapon1', 'weapon2', 'head', 'chest', 'arms', 'waist', 'legs', 'charm'] as $slot)
                        <div class="bg-white border border-[#6B8E23]/10 p-5 rounded-2xl shadow-sm transition-all duration-300">
                            <div class="flex items-center justify-between group cursor-pointer" onclick="openSelector('{{ $slot }}')">
                                <div class="flex flex-col flex-1">
                                    <span class="text-[10px] uppercase font-black text-[#6B8E23] tracking-wider mb-1 opacity-70 group-hover:opacity-100 italic transition-opacity">
                                        {{ $slot }}
                                    </span>
                                    <span id="{{ $slot }}_name" class="text-[#2F2F2F] font-bold text-lg leading-none group-hover:text-[#6B8E23] transition-colors">
                                        — Select Piece —
                                    </span>
                                </div>
                                <button onclick="event.stopPropagation(); clearSlot('{{ $slot }}')" class="text-gray-300 hover:text-red-500 transition-colors p-2 rounded-lg hover:bg-red-50" title="Clear Piece">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                            <div id="{{ $slot }}_slots" class="mt-4 space-y-1.5 border-t border-gray-100 pt-3 hidden"></div>
                        </div>
                    @endforeach
                </div>
            </section>

            {{-- PLAYSTYLE SECTION --}}
            <section>
                <h3 class="text-[#2F2F2F] font-black uppercase text-sm tracking-widest mb-4 flex items-center">
                    <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Playstyle
                </h3>
                <div class="bg-white border border-[#6B8E23]/10 p-6 rounded-2xl shadow-sm">
                    <textarea id="buildPlaystyle" rows="4" placeholder="Describe the hunter's strategy, combos or specific monster matchups..." 
                        class="w-full bg-white outline-none focus:ring-0 font-medium text-[#2F2F2F] placeholder-[#2F2F2F]/30 resize-none transition-all"></textarea>
                </div>
            </section>
        </div>

        {{-- RIGHT COLUMN: SKILLS --}}
        <div class="space-y-6 text-[#2F2F2F]">
            <div class="bg-white/40 border-2 border-[#6B8E23]/20 rounded-3xl p-6 shadow-inner sticky top-6">
                <h3 class="font-black uppercase text-sm tracking-widest mb-6 flex items-center">
                    <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Active Skills
                </h3>
                <div id="skillTotals" class="space-y-2 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <p class="text-xs italic opacity-50 text-center py-10 font-bold uppercase">No Skills Detected</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL --}}
<div id="modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] flex items-center justify-center p-6">
    <div class="bg-[#F4EBD0] w-full max-w-md rounded-[2.5rem] shadow-2xl border-4 border-[#6B8E23] flex flex-col overflow-hidden max-h-[85vh]">
        <div class="bg-[#6B8E23] p-5 text-white flex justify-between items-center shrink-0">
            <h3 id="modalTitle" class="text-xl font-black uppercase italic leading-none text-white">Select Item</h3>
            <button onclick="closeModal()" class="text-white hover:opacity-70 font-bold text-2xl">×</button>
        </div>
        <div class="p-4 border-b border-[#6B8E23]/20 bg-white/30 shrink-0">
            <input id="searchInput" type="text" placeholder="Search name, kind or skill..." 
                class="w-full bg-white border-2 border-[#6B8E23]/30 rounded-xl py-3 px-4 outline-none focus:ring-0 focus:border-[#6B8E23]/30 font-bold text-sm">
        </div>
        <div id="modalList" class="overflow-y-auto p-4 space-y-2 flex-1 bg-[#F4EBD0]"></div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #6B8E23; border-radius: 10px; }
    .deco-row { transition: all 0.2s ease; }
    .deco-row:hover { background-color: #F4EBD0 !important; border-color: #6B8E23 !important; }
    .deco-row:hover .deco-text { color: #6B8E23 !important; opacity: 1 !important; font-style: normal !important; }
    .delete-btn:hover { color: #ef4444 !important; background-color: #fee2e2 !important; }
</style>

<script src="{{ asset('js/build-editor.js') }}"></script>
@endsection