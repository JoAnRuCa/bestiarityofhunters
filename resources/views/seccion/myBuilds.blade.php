@if(request()->ajax())
    <x-build-grid :builds="$builds" :editable="true" />
@else
    @extends('layouts.master')
    @section('title', 'My Armory')

    @section('content')
    <div class="w-[95%] md:w-[70%] max-w-7xl mx-auto mt-12 mb-20 p-8 bg-[#F4EBD0] rounded-3xl shadow-2xl border border-[#6B8E23]/20">
        
        <div class="mb-8 border-b-2 border-[#6B8E23]/20 pb-6">
            <h1 class="text-5xl font-black tracking-tighter uppercase italic text-[#2F2F2F]">
                My <span class="text-[#6B8E23]">Builds</span>
            </h1>
            <p class="text-[10px] font-bold uppercase tracking-widest text-[#C67C48] mt-2 italic">Manage your custom equipment loadouts.</p>
        </div>

        {{-- El action apunta a la ruta de My Builds --}}
        <x-filter-panel :action="route('my.builds')" :activeTags="request('tag', [])">
        </x-filter-panel>

        {{-- Importante: El ID builds-wrapper es el que suele usar list.js para actualizar --}}
        <div id="builds-wrapper" class="mt-8 transition-opacity duration-300">
            <x-build-grid :builds="$builds" :editable="true" />
        </div>
    </div>
    @endsection

    @section('scripts')
        <script src="{{ asset('js/votes.js') }}"></script>
        <script src="{{ asset('js/universal-save.js') }}"></script>
        <script src="{{ asset('js/list.js') }}"></script>
        <script src="{{ asset('js/borrar.js') }}"></script>
    @endsection
@endif