@if(request()->ajax())
    {{-- BLOQUE 1: RESPUESTA EXCLUSIVA PARA AJAX (SOLO EL LISTADO)--}}
    @if($guides->count() === 0)
        <p class="text-center text-lg text-gray-600 italic py-10">No guides match your search.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($guides as $guide)
                <div class="p-6 bg-transparent flex justify-between items-start border-b border-[#6B8E23]/10 md:border-none transition-all">
                    <div class="flex-1 pr-4">
                        <h2 class="text-2xl font-bold mb-2">
                            <a href="{{ route('guides.show', ['slug' => $guide->slug]) }}" class="text-[#6B8E23] hover:text-[#C67C48] transition-colors">
                                {{ $guide->titulo }}
                            </a>
                        </h2>
                        <p class="text-gray-700 mb-4 leading-snug text-sm">{{ Str::limit($guide->contenido, 120) }}</p>
                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($guide->tags as $tag)
                                <span class="px-2 py-0.5 bg-[#C67C48] text-white text-[10px] font-bold uppercase rounded shadow-sm">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                        <p class="text-[11px] text-gray-500 font-medium uppercase tracking-wider">
                            By <span class="text-[#C67C48]">{{ $guide->user->name }}</span> • {{ $guide->created_at->diffForHumans() }}
                        </p>
                    </div>
                    <div class="pt-1">
                        <x-vote-block :item="$guide" type="guide" />
                    </div>
                </div>
            @endforeach
        </div>
        <div class="mt-12 pagination-ajax">
            {{ $guides->links() }}
        </div>
    @endif

@else

    {{-- BLOQUE 2: ESTRUCTURA COMPLETA (CARGA INICIAL DE LA PÁGINA)--}}

    @extends('layouts.master')
    @section('title', 'Guides')

    @section('content')
    <div class="w-[90%] md:w-[60%] max-w-5xl mx-auto mt-12 mb-12 p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
        <h1 class="text-4xl md:text-5xl font-extrabold mb-8 text-[#6B8E23] border-b-2 border-[#6B8E23]/20 pb-4">
            Guides
        </h1>

        {{-- PANEL DE FILTROS --}}
        <form id="filter-form" action="{{ url('/guides') }}" method="GET" class="mb-10 flex flex-col gap-6">
            @php $activeTags = (array) request('tag', []); @endphp
            
            <div id="active-tags-inputs">
                @foreach($activeTags as $t)
                    <input type="hidden" name="tag[]" value="{{ $t }}">
                @endforeach
            </div>

            <div class="flex flex-wrap gap-4 items-center">
                {{-- Búsqueda --}}
                <input type="text" name="search" placeholder="Search guides..." 
                       value="{{ request('search') }}" 
                       class="bg-white border border-[#C67C48]/30 px-4 py-2 rounded text-xs font-bold tracking-tighter text-gray-700 focus:ring-1 focus:ring-[#6B8E23] outline-none placeholder:text-gray-500 w-full md:w-auto shadow-sm">

                {{-- Autor --}}
                <input type="text" name="autor" placeholder="Author..." 
                       value="{{ request('autor') }}" 
                       class="bg-white border border-[#C67C48]/30 px-4 py-2 rounded text-xs font-bold tracking-tighter text-gray-700 focus:ring-1 focus:ring-[#6B8E23] outline-none placeholder:text-gray-500 w-full md:w-auto shadow-sm">
                
                {{-- Orden --}}
                <select name="orden" class="bg-white border border-[#C67C48]/30 pl-4 pr-10 py-2 rounded text-xs font-bold tracking-tighter text-gray-700 focus:ring-1 focus:ring-[#6B8E23] outline-none cursor-pointer w-full md:w-auto min-w-[180px] appearance-none shadow-sm" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23C67C48%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.7rem center; background-size: 1em;">
                    <option value="recientes" {{ request('orden')=='recientes' ? 'selected' : '' }}>Most recent</option>
                    <option value="votados" {{ request('orden')=='votados' ? 'selected' : '' }}>Most voted</option>
                </select>

                <button type="submit" class="bg-[#6B8E23] hover:bg-[#556b1c] text-white font-bold px-6 py-2 rounded transition-all shadow-md uppercase text-xs tracking-widest h-full">
                    APPLY FILTERS
                </button>
            </div>

            {{-- TAGS --}}
            <div class="flex flex-wrap gap-2 w-full border-t border-[#6B8E23]/10 pt-4">
                @foreach(\App\Models\Tag::all() as $tag)
                    @php $isActive = in_array($tag->name, $activeTags); @endphp
                    <button type="button" 
                       class="tag-link px-3 py-1 text-[10px] font-bold uppercase rounded transition-all duration-200 border
                              {{ $isActive 
                                  ? 'bg-[#C67C48] text-white border-[#C67C48] shadow-md hover:bg-[#a1633a]' 
                                  : 'bg-transparent text-[#C67C48] border-[#C67C48]/40 hover:bg-[#C67C48]/10' }}"
                       data-tag="{{ $tag->name }}" 
                       data-active="{{ $isActive ? 'true' : 'false' }}">
                        {{ $tag->name }}
                    </button>
                @endforeach
            </div>
        </form>

        <div id="guides-wrapper" class="transition-opacity duration-300">
            {{-- BLOQUE REPETIDO PARA LA CARGA INICIAL (Evita recursión infinita) --}}
            @if($guides->count() === 0)
                <p class="text-center text-lg text-gray-600 italic py-10">No guides match your search.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @foreach($guides as $guide)
                        <div class="p-6 bg-transparent flex justify-between items-start border-b border-[#6B8E23]/10 md:border-none transition-all">
                            <div class="flex-1 pr-4">
                                <h2 class="text-2xl font-bold mb-2">
                                    <a href="{{ route('guides.show', ['slug' => $guide->slug]) }}" class="text-[#6B8E23] hover:text-[#C67C48] transition-colors">
                                        {{ $guide->titulo }}
                                    </a>
                                </h2>
                                <p class="text-gray-700 mb-4 leading-snug text-sm">{{ Str::limit($guide->contenido, 120) }}</p>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    @foreach($guide->tags as $tag)
                                        <span class="px-2 py-0.5 bg-[#C67C48] text-white text-[10px] font-bold uppercase rounded shadow-sm">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>
                                <p class="text-[11px] text-gray-500 font-medium uppercase tracking-wider">
                                    By <span class="text-[#C67C48]">{{ $guide->user->name }}</span> • {{ $guide->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="pt-1">
                                <x-vote-block :item="$guide" type="guide" />
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-12 pagination-ajax">
                    {{ $guides->links() }}
                </div>
            @endif
        </div>
    </div>
    @endsection

    @section('scripts')
        <script src="{{ asset('js/votes.js') }}"></script>
        <script src="{{ asset('js/guide-list.js') }}"></script>
    @endsection
@endif