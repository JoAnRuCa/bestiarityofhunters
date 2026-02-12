@if(request()->ajax())
    <x-guide-grid :guides="$guides" />
@else
    @extends('layouts.master')
    @section('title', 'Guides')

    @section('content')
    <div class="w-[95%] md:w-[70%] max-w-7xl mx-auto mt-12 mb-20 p-8 bg-[#F4EBD0] rounded-3xl shadow-2xl border border-[#6B8E23]/20">
        
        <div class="flex flex-col md:flex-row justify-between items-end mb-8 border-b-2 border-[#6B8E23]/20 pb-6 gap-4">
            <div>
                <h1 class="text-5xl font-black tracking-tighter uppercase italic text-[#2F2F2F]">
                    Hunter <span class="text-[#6B8E23]">Guides</span>
                </h1>
                <p class="text-[10px] font-bold uppercase tracking-widest text-[#C67C48] mt-2 italic">
                    Master the hunt with ancient knowledge from the community
                </p>
            </div>
        </div>

        <x-filter-panel :action="url('/guides')" :activeTags="request('tag', [])">
            <input type="text" name="autor" placeholder="Author..." 
                   value="{{ request('autor') }}" 
                   class="bg-white border-2 border-[#6B8E23]/20 px-4 py-2 rounded-xl text-xs font-bold text-[#2F2F2F] focus:border-[#6B8E23] outline-none placeholder:text-gray-400 w-full md:w-auto shadow-sm transition-all">
        </x-filter-panel>

        <div id="guides-wrapper" class="mt-8 transition-opacity duration-300">
            <x-guide-grid :guides="$guides" />
        </div>
    </div>
    @endsection

    @section('scripts')
        <script src="{{ asset('js/votes.js') }}"></script>
        <script src="{{ asset('js/list.js') }}"></script>
        <script src="{{ asset('js/borrar.js') }}"></script>
    @endsection
@endif