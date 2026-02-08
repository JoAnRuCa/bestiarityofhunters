<form id="filter-form" action="{{ $action }}" method="GET" class="mb-10 flex flex-col gap-6">
    <div id="active-tags-inputs">
        @foreach($activeTags as $tag)
            <input type="hidden" name="tag[]" value="{{ $tag }}">
        @endforeach
    </div>

    <div class="flex flex-wrap gap-4 items-center">
        {{-- Búsqueda General (Siempre está) --}}
        <input type="text" name="search" placeholder="Search..." 
               value="{{ request('search') }}" 
               class="bg-white border border-[#C67C48]/30 px-4 py-2 rounded text-xs font-bold tracking-tighter text-gray-700 focus:ring-1 focus:ring-[#6B8E23] outline-none placeholder:text-gray-500 w-full md:w-auto shadow-sm">

        {{-- Slot para cosas OPCIONALES (como el Autor) --}}
        {{ $slot }}

        {{-- Select Orden (Ahora siempre está aquí) --}}
        <select name="orden" class="bg-white border border-[#C67C48]/30 pl-4 pr-10 py-2 rounded text-xs font-bold tracking-tighter text-gray-700 focus:ring-1 focus:ring-[#6B8E23] outline-none cursor-pointer w-full md:w-auto min-w-[180px] appearance-none shadow-sm" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%23C67C48%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.7rem center; background-size: 1em;">
            <option value="recientes" {{ request('orden') == 'recientes' ? 'selected' : '' }}>Most recent</option>
            <option value="votados" {{ request('orden') == 'votados' ? 'selected' : '' }}>Most voted</option>
        </select>

        <button type="submit" class="bg-[#6B8E23] hover:bg-[#556b1c] text-white font-bold px-6 py-2 rounded transition-all shadow-md uppercase text-xs tracking-widest h-full">
            APPLY FILTERS
        </button>
    </div>

    {{-- Tags (Siempre están) --}}
    <div class="flex flex-wrap gap-2 w-full border-t border-[#6B8E23]/10 pt-4">
        @foreach($allTags as $tag)
            <button type="button" 
               data-tag="{{ $tag->name }}" 
               data-active="{{ $isTagActive($tag->name) ? 'true' : 'false' }}"
               class="tag-link px-3 py-1 text-[10px] font-bold uppercase rounded transition-all duration-200 border 
                      {{ $isTagActive($tag->name) 
                          ? 'bg-[#C67C48] text-white border-[#C67C48] shadow-md hover:bg-[#a1633a]' 
                          : 'bg-transparent text-[#C67C48] border-[#C67C48]/40 hover:bg-[#C67C48]/10' }}">
                {{ $tag->name }}
            </button>
        @endforeach
    </div>
</form>