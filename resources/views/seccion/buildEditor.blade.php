@extends('layouts.master')
@section('title', 'Build Architect')

@section('content')
<form id="forgeForm" action="{{ route('builds.store') }}" method="POST">
    @csrf
    <input type="hidden" name="build_data" id="buildDataInput">
    <input type="hidden" name="decorations_data" id="decoDataInput">

    <div class="w-[95%] max-w-7xl mx-auto mt-12 mb-12 p-8 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20 text-[#2F2F2F]">
        
        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-8">
            <div class="w-full md:w-auto">
                <h1 class="text-5xl font-black tracking-tighter uppercase italic leading-none">
                    Build <span class="text-[#6B8E23]">Architect</span>
                </h1>
                <div class="mt-6 flex flex-col gap-6">
                    <div class="w-full sm:w-80">
                        <label class="text-[10px] uppercase font-black text-[#6B8E23] tracking-widest mb-1 block ml-1">Build Designation</label>
                        <input type="text" name="name" id="buildName" 
                            class="w-full bg-white border-2 border-[#6B8E23]/30 rounded-xl py-3 px-4 font-bold text-[#2F2F2F] outline-none focus:border-[#6B8E23] transition-all"
                            placeholder="Enter build name...">
                        {{-- ERROR NOMBRE --}}
                        <p id="error-name" class="text-red-600 text-xs font-bold mt-2 hidden italic"></p>
                    </div>
                    <div id="tagContainer" class="bg-white/40 p-5 rounded-2xl border border-[#6B8E23]/10">
                        <x-tag-selector :showAll="false" />
                        {{-- ERROR TAGS --}}
                        <p id="error-tags" class="text-red-600 text-xs font-bold mt-2 hidden italic"></p>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="bg-[#6B8E23] text-white px-10 py-5 rounded-2xl font-black uppercase shadow-[0_5px_0_0_#4A6318] active:translate-y-1 active:shadow-none transition-all">
                Forge Build
            </button>
        </div>

        <div class="w-full h-px bg-[#6B8E23]/30 my-8"></div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
            <div class="lg:col-span-2 space-y-12">
                <section>
                    <h3 class="font-black uppercase text-sm tracking-widest mb-6 flex items-center">
                        <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Equipment Loadout
                    </h3>
                    
                    <div id="selected" class="grid gap-4">
                        @foreach (['weapon1', 'weapon2', 'head', 'chest', 'arms', 'waist', 'legs', 'charm'] as $slot)
                            <div> {{-- Wrapper para el error --}}
                                <div id="slot_container_{{ $slot }}" class="bg-white/50 border border-[#6B8E23]/10 p-5 rounded-2xl shadow-sm transition-all duration-300 hover:border-[#6B8E23]/40">
                                    <div class="flex items-center justify-between">
                                        <div class="flex flex-col flex-1 group cursor-pointer" onclick="openSelector('{{ $slot }}')">
                                            <span class="text-[10px] uppercase font-black text-[#6B8E23] tracking-wider mb-1 opacity-70 italic block">
                                                {{ str_replace(['1','2'], [' Primary',' Secondary'], $slot) }}
                                            </span>
                                            <span id="{{ $slot }}_name" class="font-bold text-lg leading-none">
                                                — Select Piece —
                                            </span>
                                        </div>
                                        <button type="button" onclick="event.stopPropagation(); clearSlot('{{ $slot }}')" 
                                            class="text-gray-300 hover:text-red-500 p-2 transition-colors">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div id="{{ $slot }}_slots" class="mt-4 flex flex-col gap-2 pt-3 border-t border-[#6B8E23]/10 hidden"></div>
                                </div>
                                {{-- ERROR PIEZA ESPECÍFICA (ID dinámico para el script) --}}
                                <p id="error-{{ $slot }}" class="text-red-600 text-[10px] font-black uppercase mt-2 hidden italic ml-2"></p>
                            </div>
                        @endforeach
                    </div>
                </section>

                <div class="w-full h-px bg-[#6B8E23]/30 my-8"></div>

                <section>
                    <h3 class="font-black uppercase text-sm tracking-widest mb-6 flex items-center">
                        <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Hunter's Strategy
                    </h3>
                    <div id="playstyle_container" class="bg-white border-2 border-[#6B8E23]/10 p-6 rounded-2xl shadow-sm transition-all">
                        <textarea id="playstyleField" name="playstyle" rows="6" 
                            placeholder="Describe how to play this build..." 
                            class="w-full bg-transparent outline-none font-medium text-[#2F2F2F] text-lg resize-none placeholder:opacity-30"></textarea>
                    </div>
                    {{-- ERROR PLAYSTYLE --}}
                    <p id="error-playstyle" class="text-red-600 text-xs font-bold mt-2 hidden italic"></p>
                </section>
            </div>

            <div class="space-y-6">
                <div class="bg-white/40 border-2 border-[#6B8E23]/20 rounded-3xl p-6 shadow-inner sticky top-6">
                    <h3 class="font-black uppercase text-sm tracking-widest mb-6 flex items-center">
                        <span class="w-10 h-1 bg-[#6B8E23] mr-3"></span> Active Skills
                    </h3>
                    <div id="skillTotals" class="space-y-6 max-h-[65vh] overflow-y-auto custom-scrollbar pr-2">
                        <p class="py-10 text-center italic text-xs opacity-50 font-bold uppercase tracking-widest">Equip items to see skills</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- MODAL --}}
<div id="modal" class="hidden fixed inset-0 bg-black/70 backdrop-blur-md z-[9999] flex items-center justify-center p-4">
    <div class="bg-[#F4EBD0] w-full max-w-xl rounded-[2.5rem] shadow-2xl border-4 border-[#6B8E23] flex flex-col overflow-hidden max-h-[85vh]">
        <div class="bg-[#6B8E23] p-6 text-white flex justify-between items-center">
            <h3 class="text-2xl font-black uppercase italic">Inventory</h3>
            <button type="button" onclick="closeModal()" class="text-white font-bold text-3xl hover:scale-110">&times;</button>
        </div>
        <div class="p-5 bg-white/30 border-b border-[#6B8E23]/10">
            <input id="searchInput" type="text" placeholder="Filter..." class="w-full bg-white border-2 border-[#6B8E23]/30 rounded-2xl py-4 px-6 font-bold outline-none text-[#2F2F2F]">
        </div>
        <div id="modalList" class="overflow-y-auto p-6 space-y-3 flex-1 bg-[#F4EBD0] custom-scrollbar"></div>
    </div>
</div>

<script src="{{ asset('js/build-editor.js') }}"></script>
@endsection