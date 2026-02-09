@extends('layouts.master')
@section('title', 'Decorations')

@section('content')
<div class="w-[90%] md:w-[60%] max-w-6xl mx-auto mt-12 mb-20 p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
    
    {{-- Título --}}
    <h1 class="text-4xl md:text-5xl font-extrabold mb-8 text-[#6B8E23] border-b-2 border-[#6B8E23]/20 pb-4">
        Decorations
    </h1>

    {{-- Buscador --}}
    <div class="mb-10">
        <form method="GET" action="{{ route('decorations.index') }}" class="flex flex-col md:flex-row gap-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search jewel or skill..." 
                   class="flex-1 bg-white border border-[#C67C48]/30 px-4 py-2 rounded text-sm font-bold tracking-tight text-gray-700 focus:ring-2 focus:ring-[#6B8E23] outline-none shadow-sm transition-all">
            
            <button type="submit" 
                    class="bg-[#6B8E23] hover:bg-[#C67C48] text-white font-bold py-2 px-6 rounded shadow-md transition-colors duration-300 uppercase text-sm tracking-widest">
                Search
            </button>
        </form>
    </div>

    {{-- Lista en Grid --}}
    @if($paginatedDecorations->count() === 0)
        <p class="text-center text-lg text-gray-600 italic py-10">No decorations found.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($paginatedDecorations as $decoration)
                <div class="group">
                    <a href="{{ route('decorations.show', $decoration['slug']) }}" 
                       class="flex items-center justify-between p-4 bg-white/50 border border-[#6B8E23]/10 rounded-lg transition-all duration-300 hover:bg-[#6B8E23] hover:text-white hover:shadow-md hover:-translate-y-1">
                        
                        <span class="text-lg font-bold tracking-tight leading-tight pr-4">
                            {{ $decoration['name'] }}
                        </span>

                        {{-- Círculo con Centrado de Rejilla (Grid Center) --}}
                        @if(isset($decoration['slot']))
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <span class="text-[9px] font-bold uppercase opacity-60 group-hover:text-white/80 tracking-tighter italic">Slot</span>
                                
                                {{-- Contenedor Grid: Fuerza el centro sin importar la fuente --}}
                                <div class="grid place-items-center w-7 h-7 bg-[#2F2F2F] group-hover:bg-white rounded-full shadow-sm transition-colors border border-white/10">
                                    <span class="text-xs font-bold leading-none text-[#F4EBD0] group-hover:text-[#2F2F2F] flex items-center justify-center {{ $decoration['slot'] == 1 ? 'w-full text-center' : '' }}">
                                        {{ $decoration['slot'] }}
                                    </span>
                                </div>
                            </div>
                        @endif
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-12 flex justify-center pagination-ajax">
        {{ $paginatedDecorations->links() }}
    </div>
</div>
@endsection