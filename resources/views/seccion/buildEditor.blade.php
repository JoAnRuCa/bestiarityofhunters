@extends('layouts.master')
@section('title', 'Build Architect')

@section('content')
<form id="forgeForm" action="{{ route('builds.store') }}" method="POST">
    @csrf
    <input type="hidden" name="build_data" id="buildDataInput">
    <input type="hidden" name="decorations_data" id="decoDataInput">

    <div class="w-[95%] max-w-7xl mx-auto mt-12 mb-12 p-8 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20">
        
        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row items-center justify-between mb-10 border-b border-[#6B8E23]/30 pb-8 gap-6">
            <div class="w-full md:w-auto">
                <h1 class="text-5xl font-black text-[#2F2F2F] tracking-tighter uppercase italic leading-none">
                    Build <span class="text-[#6B8E23]">Architect</span>
                </h1>
                <div class="mt-6 flex flex-col gap-6">
                    <div class="w-full sm:w-80">
                        <label class="text-[10px] uppercase font-black text-[#6B8E23] tracking-widest mb-1 block">Build Designation</label>
                        <input type="text" name="name" id="buildName" required class="w-full bg-white border-2 border-[#6B8E23]/30 rounded-xl py-3 px-4 font-bold text-[#2F2F2F] outline-none">
                    </div>
                    <div id="tagContainer" class="bg-white/40 p-5 rounded-2xl border border-[#6B8E23]/10">
                        <x-tag-selector :showAll="false" />
                    </div>
                </div>
            </div>
            <button type="submit" class="bg-[#6B8E23] text-white px-10 py-5 rounded-2xl font-black uppercase shadow-[0_5px_0_0_#4A6318] active:translate-y-1 active:shadow-none">
                Forge Build
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            {{-- LOADOUT --}}
            <div class="lg:col-span-2 space-y-8">
                <div id="selected" class="grid gap-3">
                    @foreach (['weapon1', 'weapon2', 'head', 'chest', 'arms', 'waist', 'legs', 'charm'] as $slot)
                        <div class="bg-white border border-[#6B8E23]/10 p-5 rounded-2xl shadow-sm">
                            <div class="flex items-center justify-between group cursor-pointer" onclick="openSelector('{{ $slot }}')">
                                <div class="flex flex-col flex-1">
                                    <span class="text-[10px] uppercase font-black text-[#6B8E23] italic">
                                        {{ str_replace(['1','2'], [' Primary',' Secondary'], $slot) }}
                                    </span>
                                    <span id="{{ $slot }}_name" class="text-[#2F2F2F] font-bold text-lg">— Select Piece —</span>
                                </div>
                                <button type="button" onclick="event.stopPropagation(); clearSlot('{{ $slot }}')" class="text-gray-300 hover:text-red-500 p-2">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </div>
                            <div id="{{ $slot }}_slots" class="mt-4 space-y-1.5 border-t border-gray-100 pt-3 hidden"></div>
                        </div>
                    @endforeach
                </div>

                {{-- PLAYSTYLE (OPCIONAL) --}}
                <div class="bg-white border border-[#6B8E23]/10 p-6 rounded-2xl shadow-sm">
                    <label class="text-[10px] uppercase font-black text-[#6B8E23] tracking-widest mb-3 block">Hunter's Strategy</label>
                    <textarea name="playstyle" rows="4" placeholder="Describe how to play this build..." class="w-full bg-white outline-none font-medium text-[#2F2F2F] resize-none"></textarea>
                </div>
            </div>

            {{-- SKILLS SIDEBAR --}}
            <div class="space-y-6">
                <div class="bg-white/40 border-2 border-[#6B8E23]/20 rounded-3xl p-6 sticky top-6">
                    <h3 class="font-black uppercase text-sm tracking-widest mb-6 text-[#2F2F2F]">Active Skills</h3>
                    <div id="skillTotals" class="space-y-3 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                        <p class="text-xs italic opacity-50 text-center py-10 font-bold uppercase">Equip items to see skills</p>
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
            <button type="button" onclick="closeModal()" class="text-white font-bold text-3xl">&times;</button>
        </div>
        <div class="p-5 bg-white/30">
            <input id="searchInput" type="text" placeholder="Filter..." class="w-full bg-white border-2 border-[#6B8E23]/30 rounded-2xl py-4 px-6 font-bold">
        </div>
        <div id="modalList" class="overflow-y-auto p-6 space-y-3 flex-1 bg-[#F4EBD0]"></div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #6B8E23; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
</style>

<script src="{{ asset('js/build-editor.js') }}"></script>
@endsection