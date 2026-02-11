@if(request()->ajax())
    {{-- Respuesta simplificada para AJAX --}}
    @if($guides->count() > 0)
        <x-guide-grid :guides="$guides" :editable="true" />
    @else
        <div class="py-12 text-center border-2 border-dashed border-[#6B8E23]/10 rounded-lg">
            @if(request()->filled('tag'))
                {{-- Mensaje cuando hay filtros activos pero no hay resultados --}}
                <p class="text-gray-600 italic font-serif text-lg">No scrolls found with the selected tags.</p>
                <a href="{{ route('my.guides') }}" class="mt-4 inline-block text-[#6B8E23] font-bold uppercase hover:underline tracking-widest text-xs">
                    Clear Filters
                </a>
            @else
                {{-- Mensaje cuando la librería está realmente vacía --}}
                <p class="text-gray-600 italic font-serif text-lg">You haven't shared any of your hunting scrolls with the community yet.</p>
                <a href="{{ url('/guide-editor') }}" class="mt-4 inline-block text-[#C67C48] font-bold uppercase hover:underline tracking-widest text-xs">
                    Go to Guide Editor →
                </a>
            @endif
        </div>
    @endif
@else
    @extends('layouts.master')
    @section('title', 'My Library')

    @section('content')
    <div class="w-[90%] md:w-[60%] max-w-6xl mx-auto mt-12 mb-20 p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
        
        {{-- Cabecera --}}
        <div class="mb-8 border-b-2 border-[#6B8E23]/20 pb-4">
            <h1 class="text-4xl md:text-5xl font-extrabold text-[#6B8E23] uppercase tracking-tighter">
                My <span class="text-[#C67C48]">Guides</span>
            </h1>
            <p class="text-gray-700 italic font-serif text-sm mt-2">Manage your published hunting scrolls.</p>
        </div>

        {{-- Panel de Filtros --}}
        <x-filter-panel :action="route('my.guides')" :activeTags="request('tag', [])">
        </x-filter-panel>

        {{-- CONTENEDOR DE RESULTADOS --}}
        <div id="guides-wrapper" class="transition-opacity duration-300 mt-8">
            @if($guides->count() > 0)
                <x-guide-grid :guides="$guides" :editable="true" />
            @else
                <div class="py-12 text-center border-2 border-dashed border-[#6B8E23]/10 rounded-lg">
                    @if(request()->filled('tag'))
                        {{-- Mensaje para filtros sin éxito --}}
                        <p class="text-gray-600 italic font-serif text-lg">No scrolls found with the selected tags.</p>
                        <button onclick="window.location.href='{{ route('my.guides') }}'" class="mt-4 inline-block text-[#6B8E23] font-bold uppercase hover:underline tracking-widest text-xs">
                            Clear Filters
                        </button>
                    @else
                        {{-- Mensaje para librería vacía --}}
                        <p class="text-gray-600 italic font-serif text-lg">You haven't shared any of your hunting scrolls with the community yet.</p>
                        <a href="{{ url('/guide-editor') }}" class="mt-4 inline-block text-[#C67C48] font-bold uppercase hover:underline tracking-widest text-xs">
                            Go to Guide Editor →
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- Paginación --}}
        <div class="mt-12 pagination-ajax">
            {{ $guides->links() }}
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