@if(request()->ajax())
    {{-- Respuesta simplificada para AJAX --}}
    <x-guide-grid :guides="$guides" />
@else
    @extends('layouts.master')
    @section('title', 'Guides')

    @section('content')
    <div class="w-[90%] md:w-[60%] max-w-6xl mx-auto mt-12 mb-20 p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-8 text-[#6B8E23] border-b-2 border-[#6B8E23]/20 pb-4">
            Guides
        </h1>

        {{-- 
            USO DEL COMPONENTE EXTERNALIZADO 
            Le pasamos la URL de guías y los tags activos desde la request.
        --}}
        <x-filter-panel :action="url('/guides')" :activeTags="request('tag', [])">
            {{-- Solo inyectamos el autor, porque el resto ya viene de serie en el componente --}}
            <input type="text" name="autor" placeholder="Author..." 
                   value="{{ request('autor') }}" 
                   class="bg-white border border-[#C67C48]/30 px-4 py-2 rounded text-xs font-bold tracking-tighter text-gray-700 focus:ring-1 focus:ring-[#6B8E23] outline-none placeholder:text-gray-500 w-full md:w-auto shadow-sm">
        </x-filter-panel>

        {{-- CONTENEDOR DE RESULTADOS --}}
        <div id="guides-wrapper" class="transition-opacity duration-300">
            {{-- Añadimos :editable="true" --}}
            <x-guide-grid :guides="$guides" :editable="true" />
        </div>
    </div>
    @endsection

    @section('scripts')
        <script src="{{ asset('js/votes.js') }}"></script>
        <script src="{{ asset('js/list.js') }}"></script>
        <script src="{{ asset('js/borrar.js') }}"></script>
    @endsection
@endif