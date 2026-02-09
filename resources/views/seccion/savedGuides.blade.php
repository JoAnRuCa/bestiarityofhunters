@extends('layouts.master')

@section('title', 'Saved Guides')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 mb-20">
    
    {{-- Título de la sección --}}
    <div class="mb-10 border-b-2 border-[#6B8E23]/20 pb-4">
        <h1 class="text-4xl font-extrabold text-[#6B8E23] uppercase tracking-tighter">
            Saved <span class="text-[#C67C48]">Guides</span>
        </h1>
        <p class="text-gray-600 italic">Your personal collection of hunting scrolls and tactics.</p>
    </div>

    {{-- Panel de Filtros Reutilizado --}}
    <x-filter-panel 
        :action="route('saved.guides')" 
        :allTags="$allTags" 
        :activeTags="$activeTags" 
        :isTagActive="$isTagActive"
    >
        {{-- Slot vacío o podrías añadir un buscador por autor si quisieras --}}
    </x-filter-panel>

    @if($savedData->count() === 0)
        <div class="bg-white/30 rounded-lg p-12 text-center border-2 border-dashed border-[#6B8E23]/10">
            <p class="text-lg text-gray-600 italic">Your archives are empty or no matches found.</p>
            <a href="{{ route('guides.index') }}" class="mt-4 inline-block text-[#C67C48] font-bold uppercase hover:underline text-xs tracking-widest">
                Return to Bestiary
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @foreach($savedData as $item)
                @php $guide = $item->guide; @endphp
                
                {{-- Usamos tu estructura de guide-grid pero adaptada al loop de savedData --}}
                <div class="p-6 bg-transparent flex justify-between items-start border-b border-[#6B8E23]/10 md:border-none transition-all hover:bg-white/20 rounded-xl relative group">
                    
                    {{-- Botón de Guardado (SaveButton) --}}
                    {{-- Nota: Lo ponemos aquí para que el usuario pueda "des-archivar" --}}
                    <x-save-button :id="$guide->id" type="guide" />

                    <div class="flex-1 pr-4">
                        <h2 class="text-2xl font-bold mb-2">
                            <a href="{{ route('guides.show', ['slug' => $guide->slug]) }}" class="text-[#6B8E23] hover:text-[#C67C48] transition-colors uppercase tracking-tight">
                                {{ $guide->titulo }}
                            </a>
                        </h2>
                        
                        <p class="text-gray-700 mb-4 leading-snug text-sm">
                            {{ Str::limit($guide->contenido, 120) }}
                        </p>

                        <div class="flex flex-wrap gap-2 mb-4">
                            @foreach($guide->tags as $tag)
                                <span class="px-2 py-0.5 bg-[#C67C48] text-white text-[10px] font-bold uppercase rounded shadow-sm">
                                    {{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                        
                        <p class="text-[11px] text-[#2F2F2F] font-medium tracking-wider">
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
            {{ $savedData->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/votes.js') }}"></script>
    <script src="{{ asset('js/universal-save.js') }}"></script>
@endsection