@extends('layouts.master')
@section('title', 'Armors')

@section('content')
<div class="w-[90%] md:w-[60%] max-w-6xl mx-auto mt-12 mb-20 p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
    
    {{-- Título --}}
    <h1 class="text-4xl md:text-5xl font-extrabold mb-8 text-[#6B8E23] border-b-2 border-[#6B8E23]/20 pb-4">
        Armors
    </h1>

    {{-- Buscador Estilizado --}}
    <div class="mb-10">
        <form method="GET" action="{{ route('armors.index') }}" class="flex flex-col md:flex-row gap-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search armor piece or set..." 
                   class="flex-1 bg-white border border-[#C67C48]/30 px-4 py-2 rounded text-sm font-bold tracking-tight text-gray-700 focus:ring-2 focus:ring-[#6B8E23] outline-none placeholder:text-gray-500 shadow-sm transition-all">
            
            <button type="submit" 
                    class="bg-[#6B8E23] hover:bg-[#C67C48] text-white font-bold py-2 px-6 rounded shadow-md transition-colors duration-300 uppercase text-sm tracking-widest">
                Search
            </button>
        </form>
    </div>

    {{-- Lista de Armaduras en Grid --}}
    @if($paginatedArmor->count() === 0)
        <p class="text-center text-lg text-gray-600 italic py-10">No armor found in the armory archives.</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach ($paginatedArmor as $armor)
                <div class="group">
                    <a href="{{ route('armors.show', $armor['slug']) }}" 
                       class="flex items-center p-4 bg-white/50 border border-[#6B8E23]/10 rounded-lg transition-all duration-300 hover:bg-[#6B8E23] hover:text-white hover:shadow-md hover:-translate-y-1">
                        
                        <div class="flex flex-col">
                            <span class="text-lg font-bold tracking-tight leading-tight">
                                {{ $armor['name'] }}
                            </span>
                            {{-- Ejemplo de subtexto si tienes el tipo de pieza (head, chest, etc) --}}
                            @if(isset($armor['type']))
                                <span class="text-[10px] uppercase tracking-widest opacity-70 mt-1 font-bold">
                                    {{ $armor['type'] }}
                                </span>
                            @endif
                        </div>
                        
                        {{-- Icono de flecha decorativa --}}
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-auto opacity-0 group-hover:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Paginación Centrada --}}
    <div class="mt-12 flex justify-center pagination-ajax">
        {{ $paginatedArmor->links() }}
    </div>
</div>
@endsection