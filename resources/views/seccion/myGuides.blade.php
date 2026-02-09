@if(request()->ajax())
    {{-- Respuesta simplificada para AJAX --}}
    <x-guide-grid :guides="$guides" />
@else
    @extends('layouts.master')
    @section('title', 'My Library')

    @section('content')
    <div class="w-[90%] md:w-[60%] max-w-6xl mx-auto mt-12 mb-20 p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
        
        {{-- Cabecera con estilo unificado --}}
        <div class="mb-8 border-b-2 border-[#6B8E23]/20 pb-4">
            <h1 class="text-4xl md:text-5xl font-extrabold text-[#6B8E23] uppercase tracking-tighter">
                My <span class="text-[#C67C48]">Guides</span>
            </h1>
            <p class="text-gray-700 italic font-serif text-sm mt-2">Manage your published hunting scrolls.</p>
        </div>

        {{-- Panel de Filtros: Apunta a la ruta 'my.guides' y hereda el ID del script 'filter-form' --}}
        <x-filter-panel :action="route('my.guides')" :activeTags="request('tag', [])">
            {{-- Sin input de autor para esta vista personal --}}
        </x-filter-panel>

        {{-- CONTENEDOR DE RESULTADOS --}}
        <div id="guides-wrapper" class="transition-opacity duration-300 mt-8">
            @if($guides->count() > 0)
                {{-- 
                    Importante: Asegúrate de que dentro de x-guide-grid 
                    estés usando el componente <x-delete-button /> 
                    con la clase 'delete-form-ajax'.
                --}}
                <x-guide-grid :guides="$guides" :editable="true" />
            @else
                <div class="py-12 text-center border-2 border-dashed border-[#6B8E23]/10 rounded-lg">
                    <p class="text-gray-600 italic font-serif text-lg">You haven't written any guides yet.</p>
                    <a href="{{ route('guides.create') }}" class="mt-4 inline-block text-[#C67C48] font-bold uppercase hover:underline tracking-widest text-xs">
                        Write your first scroll →
                    </a>
                </div>
            @endif
        </div>

        {{-- Paginación con clase para AJAX --}}
        <div class="mt-12 pagination-ajax">
            {{ $guides->links() }}
        </div>
    </div>
    @endsection

    @section('scripts')
        {{-- Cargamos los scripts necesarios para la funcionalidad de la lista --}}
        <script src="{{ asset('js/votes.js') }}"></script>
        <script src="{{ asset('js/universal-save.js') }}"></script>
        {{-- list.js ahora contiene la lógica de filtros y el borrado AJAX --}}
        <script src="{{ asset('js/list.js') }}"></script>
    @endsection
@endif