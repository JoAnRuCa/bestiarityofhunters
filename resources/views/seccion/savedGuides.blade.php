@if(!isset($only_content))
@extends('layouts.master')

@section('title', 'Saved Archives')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 mb-20">
    
    <div class="bg-[#F4EBD0] rounded-xl shadow-2xl border border-[#6B8E23]/30 overflow-hidden p-8 md:p-12">
        
        {{-- Cabecera --}}
        <div class="mb-10 border-b-2 border-[#6B8E23]/20 pb-6">
            <h1 class="text-5xl font-extrabold text-[#6B8E23] uppercase tracking-tighter">
                Saved <span class="text-[#C67C48]">Archives</span>
            </h1>
            <p class="text-gray-700 italic font-serif text-lg mt-2">Your personal collection of hunting scrolls and tactics.</p>
        </div>

{{-- Panel de Filtros --}}
        <div class="mb-12">
            {{-- 
               IMPORTANTE: Si el input de Autor no está dentro del componente x-filter-panel, 
               asegúrate de que el JS 'guide-list.js' recoja los datos de este input. 
               Lo ideal es que el componente x-filter-panel tenga un espacio para él.
            --}}
            <x-filter-panel 
                :action="route('saved.guides')" 
                :allTags="$allTags" 
                :activeTags="$activeTags" 
                :isTagActive="$isTagActive"
            >
                {{-- Si tu componente permite slots, pon el input aquí. 
                   Si no, tendrás que editar el componente x-filter-panel para incluirlo al lado del search --}}
                <input type="text" name="autor" placeholder="Author..." 
                       value="{{ request('autor') }}" 
                       class="bg-white border border-[#C67C48]/30 px-4 py-2 rounded text-xs font-bold tracking-tighter text-gray-700 focus:ring-1 focus:ring-[#6B8E23] outline-none placeholder:text-gray-500 w-full md:w-auto shadow-sm">
            </x-filter-panel>
        </div>

        {{-- CONTENEDOR PARA AJAX --}}
        <div id="guides-wrapper" class="transition-opacity duration-300">
@endif

            {{-- ESTA PARTE ES LA QUE SE REEMPLAZA POR AJAX --}}
            @if($savedData->count() === 0)
                <div class="py-20 text-center border-2 border-dashed border-[#6B8E23]/10 rounded-lg">
                    <p class="text-xl text-gray-600 italic font-serif">Your archive is currently empty or no matches found.</p>
                    <a href="{{ route('guides.index') }}" class="mt-4 inline-block text-[#C67C48] font-bold uppercase hover:underline tracking-widest text-xs">
                        Explore the Bestiary →
                    </a>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    @foreach($savedData as $item)
                        @php $guide = $item->guide; @endphp
                        
                        <div class="group p-6 flex justify-between items-start border border-[#6B8E23]/10 rounded-lg transition-all hover:bg-[#6B8E23]/5 relative bg-white/30">
                            
                            <div class="flex-1 pr-4">
                                <h2 class="text-2xl font-bold mb-2 leading-tight">
                                    <a href="{{ route('guides.show', $guide->slug) }}" class="text-[#6B8E23] hover:text-[#C67C48] transition-colors uppercase tracking-tight font-serif">
                                        {{ $guide->titulo }}
                                    </a>
                                </h2>
                                
                                <p class="text-gray-800 mb-4 leading-snug text-sm font-medium">
                                    {{ Str::limit($guide->contenido, 120) }}
                                </p>
                                
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @foreach($guide->tags as $tag)
                                        <span class="px-2 py-0.5 bg-[#C67C48] text-white text-[10px] font-bold uppercase rounded shadow-sm">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                                
                                <p class="text-[11px] text-[#2F2F2F] font-bold tracking-wider uppercase opacity-80">
                                    By <span class="text-[#C67C48]">{{ $guide->user->name }}</span> • 
                                    <span class="text-gray-600">{{ $guide->created_at->diffForHumans() }}</span>
                                </p>
                            </div>

                            <div class="flex flex-col items-center gap-6 min-w-[60px]">
                                <div class="save-container" style="position: static !important;">
                                    <button type="button" 
                                            class="save-btn flex items-center justify-center w-10 h-10 rounded-full bg-[#6B8E23] text-[#2F2F2F] shadow-sm transition-all hover:scale-110"
                                            data-url="{{ route('saved.toggle', ['type' => 'guide', 'id' => $guide->id]) }}"
                                            data-type="guide">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="transform scale-90">
                                    <x-vote-block :item="$guide" type="guide" />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-12 pagination-ajax">
                    {{ $savedData->links() }}
                </div>
            @endif

@if(!isset($only_content))
        </div> {{-- Fin #guides-wrapper --}}
    </div>
</div>

<style>
    .save-container { position: static !important; }
</style>
@endsection

@section('scripts')
    <script src="{{ asset('js/votes.js') }}"></script>
    <script src="{{ asset('js/universal-save.js') }}"></script>
    <script src="{{ asset('js/guide-list.js') }}"></script>
@endsection
@endif