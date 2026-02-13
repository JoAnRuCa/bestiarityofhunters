@extends('layouts.master')
@section('title', 'Saved Builds')

@section('content')
<div class="w-[95%] md:w-[70%] max-w-7xl mx-auto mt-12 mb-20 p-8 bg-[#F4EBD0] rounded-3xl shadow-2xl border border-[#6B8E23]/20">
    
    {{-- Header de la sección --}}
    <div class="mb-10 border-b-2 border-[#6B8E23]/20 pb-6">
        <h1 class="text-5xl font-black tracking-tighter uppercase italic text-[#2F2F2F]">
            Saved <span class="text-[#6B8E23]">Builds</span>
        </h1>
        <p class="text-[10px] font-bold uppercase tracking-widest text-[#C67C48] mt-2 italic">Your personal armory of specialized hunting gear.</p>
    </div>

    {{-- Panel de Filtros --}}
    <x-filter-panel :action="route('saved.builds')" :activeTags="$activeTags">
        <input type="text" name="autor" placeholder="Creator..." 
               value="{{ request('autor') }}" 
               class="bg-white border-2 border-[#6B8E23]/20 px-4 py-2 rounded-xl text-xs font-bold text-[#2F2F2F] focus:border-[#6B8E23] outline-none placeholder:text-gray-400 w-full md:w-auto shadow-sm transition-all">
    </x-filter-panel>

    {{-- Listado de Builds --}}
    <div id="builds-wrapper" class="mt-8 transition-opacity duration-300">
        @if($savedData->count() === 0)
            <div class="py-20 text-center border-2 border-dashed border-[#6B8E23]/10 rounded-lg">
                <p class="text-xl text-gray-600 italic font-serif">Your archive is currently empty or no matches found.</p>
                <a href="{{ route('builds.index') }}" class="mt-4 inline-block text-[#C67C48] font-bold uppercase hover:underline tracking-widest text-xs">
                    Explore the Smithy →
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($savedData as $item)
                    @php $build = $item->build; @endphp
                    <div id="build-card-{{ $build->id }}" class="group/card p-6 bg-white/40 flex justify-between items-stretch border border-[#6B8E23]/10 rounded-2xl transition-all hover:bg-[#6B8E23]/5 duration-300 shadow-sm hover:shadow-md min-h-[160px]">
                        
                        {{-- Contenido de la Build (Izquierda) --}}
                        <div class="flex-1 pr-4 flex flex-col">
                            <h2 class="text-2xl font-black uppercase italic leading-tight mb-2">
                                <a href="{{ route('builds.show', $build->slug) }}" class="text-[#2F2F2F] hover:text-[#6B8E23] transition-colors">
                                    {{ $build->titulo }}
                                </a>
                            </h2>
                            
                            {{-- Etiquetas --}}
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach($build->tags as $tag)
                                    <span class="px-2 py-0.5 bg-[#6B8E23] text-white text-[9px] font-black uppercase rounded shadow-sm">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>

                            {{-- Info de Autor y Fecha --}}
                            <p class="text-[11px] text-[#2F2F2F] font-bold tracking-wider uppercase opacity-70 mt-auto">
                                By <span class="text-[#C67C48]">{{ $build->user->name }}</span> • {{ $build->created_at->diffForHumans() }}
                            </p>
                        </div>

                        {{-- Sección de Interacción (Derecha) - SIN LÍNEA VERTICAL --}}
                        <div class="flex flex-col items-end justify-between min-w-[60px] ml-4">
                            <div class="flex flex-col items-center gap-4">
                                {{-- Botón Guardar --}}
                                <div class="save-container">
                                    <button type="button" 
                                            class="save-btn flex items-center justify-center w-10 h-10 rounded-full bg-[#6B8E23] text-white shadow-sm transition-all hover:scale-110 active:scale-95"
                                            data-url="{{ route('saved.toggle', ['type' => 'build', 'id' => $build->id]) }}" 
                                            data-type="build">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Bloque de Votos --}}
                                <div class="transform scale-90 origin-right">
                                    <x-vote-block :item="$build" type="build" />
                                </div>
                            </div>
                        </div>

                    </div>
                @endforeach 
            </div>
            
            {{-- Paginación --}}
            <div class="mt-12 pagination-ajax flex justify-end">
                {{ $savedData->links() }}
            </div>
        @endif
    </div>
</div>
@endsection