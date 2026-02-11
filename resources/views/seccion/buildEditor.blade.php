@extends('layouts.master')
@section('title', 'Build Editor')

@section('content')
<div class="w-[95%] max-w-7xl mx-auto mt-12 mb-12 p-8 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20">
    
    <div class="flex flex-col md:flex-row items-center justify-between mb-10 border-b border-[#6B8E23]/30 pb-6 gap-4">
        <div>
            <h1 class="text-5xl font-black text-[#2F2F2F] tracking-tighter uppercase italic leading-none">
                Build <span class="text-[#6B8E23]">Architect</span>
            </h1>
            <p class="text-[#C67C48] font-bold uppercase tracking-[0.3em] text-xs mt-2">Professional Hunting Gear Configurator</p>
        </div>
        <button onclick="saveBuild()" class="bg-[#6B8E23] hover:bg-[#58751C] text-white px-8 py-4 rounded-xl font-bold transition-all shadow-[0_4px_0_0_#4A6318] active:translate-y-1 active:shadow-none">
            Forge Build
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <div class="lg:col-span-2 space-y-4">
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

                        <div id="{{ $slot }}_slots" class="mt-4 space-y-1.5 border-t border-gray-100 pt-3 hidden">
                            </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-6 text-[#2F2F2F]">
            <div class="bg-white/40 border-2 border-[#6B8E23]/20 rounded-3xl p-6 shadow-inner sticky top-6">
                <h3 class="font-black uppercase text-sm tracking-widest mb-6 flex items-center">
                    <span class="w-10 h-1 bg-[#C67C48] mr-3"></span> Active Skills
                </h3>
                <div id="skillTotals" class="space-y-2 max-h-[60vh] overflow-y-auto custom-scrollbar">
                    <p class="text-xs italic opacity-50">Empty build...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] flex items-center justify-center p-6">
    <div class="bg-[#F4EBD0] w-full max-w-md rounded-[2.5rem] shadow-2xl border-4 border-[#6B8E23] flex flex-col overflow-hidden max-h-[85vh]">
        <div class="bg-[#6B8E23] p-5 text-white flex justify-between items-center shrink-0">
            <h3 id="modalTitle" class="text-xl font-black uppercase italic leading-none text-white">Select Item</h3>
            <button onclick="closeModal()" class="text-white hover:opacity-70 font-bold text-2xl">×</button>
        </div>
        <div class="p-4 border-b border-[#6B8E23]/20 bg-white/30 shrink-0">
            <input id="searchInput" type="text" placeholder="Search name, kind or skill..." 
                class="w-full bg-white border-2 border-[#6B8E23]/30 rounded-xl py-3 px-4 focus:border-[#C67C48] outline-none font-bold text-sm">
        </div>
        <div id="modalList" class="overflow-y-auto p-4 space-y-2 flex-1 bg-[#F4EBD0]"></div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #6B8E23; border-radius: 10px; }

    /* --- FIX HOVER SLOTS --- */
    .deco-row {
        transition: all 0.2s ease;
    }

    .deco-row:hover {
        background-color: #F4EBD0 !important;
        border-color: #6B8E23 !important;
    }

    /* Forzamos que el texto dentro del hover sea verde, tenga o no opacidad previa */
    .deco-row:hover .deco-text {
        color: #6B8E23 !important;
        opacity: 1 !important;
        font-style: normal !important;
    }

    .delete-btn:hover {
        color: #ef4444 !important;
        background-color: #fee2e2 !important;
    }
</style>

<script src="{{ asset('js/build-editor.js') }}"></script>
@endsection