@extends('layouts.master')
@section('title', 'Saved Guides')

@section('content')
<div class="w-[95%] md:w-[70%] max-w-7xl mx-auto mt-12 mb-20 p-8 bg-[#F4EBD0] rounded-3xl shadow-2xl border border-[#6B8E23]/20">
    
    <div class="mb-10 border-b-2 border-[#6B8E23]/20 pb-6">
        <h1 class="text-5xl font-black tracking-tighter uppercase italic text-[#2F2F2F]">
            Saved <span class="text-[#6B8E23]">Guides</span>
        </h1>
        <p class="text-[10px] font-bold uppercase tracking-widest text-[#C67C48] mt-2 italic">Your personal collection of hunting scrolls and tactics.</p>
    </div>

    <x-filter-panel :action="route('saved.guides')" :activeTags="request('tag', [])">
        <input type="text" name="autor" placeholder="Author..." 
               value="{{ request('autor') }}" 
               class="bg-white border-2 border-[#6B8E23]/20 px-4 py-2 rounded-xl text-xs font-bold text-[#2F2F2F] focus:border-[#6B8E23] outline-none placeholder:text-gray-400 w-full md:w-auto shadow-sm transition-all">
    </x-filter-panel>

    <div id="guides-wrapper" class="mt-8 transition-opacity duration-300">
        @if($savedData->count() === 0)
            <div class="py-20 text-center border-2 border-dashed border-[#6B8E23]/10 rounded-lg">
                <p class="text-xl text-gray-600 italic font-serif">Your archive is currently empty or no matches found.</p>
                <a href="{{ route('guides.index') }}" class="mt-4 inline-block text-[#C67C48] font-bold uppercase hover:underline tracking-widest text-xs">
                    Explore the Bestiary →
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($savedData as $item)
                    @php $guide = $item->guide; @endphp
                    <div id="guide-card-{{ $guide->id }}" class="group/card p-6 bg-white/40 flex justify-between items-stretch border border-[#6B8E23]/10 rounded-2xl transition-all hover:bg-[#6B8E23]/5 duration-300 shadow-sm hover:shadow-md min-h-[180px]">
                        
                        <div class="flex-1 pr-4">
                            <h2 class="text-2xl font-black uppercase italic leading-none mb-3">
                                <a href="{{ route('guides.show', $guide->slug) }}" class="text-[#2F2F2F] hover:text-[#6B8E23] transition-colors">
                                    {{ $guide->titulo }}
                                </a>
                            </h2>
                            <p class="text-gray-700 mb-4 leading-snug text-sm italic line-clamp-2">{{ Str::limit($guide->contenido, 120) }}</p>
                            
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach($guide->tags as $tag)
                                    <span class="px-2 py-0.5 bg-[#6B8E23] text-white text-[9px] font-black uppercase rounded shadow-sm">{{ $tag->name }}</span>
                                @endforeach
                            </div>

                            <p class="text-[11px] text-[#2F2F2F] font-bold tracking-wider uppercase opacity-70 mt-auto">
                                By <span class="text-[#C67C48]">{{ $guide->user->name }}</span> • {{ $guide->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <div class="flex flex-col items-end justify-between min-w-[60px] ml-4">
                            <div class="flex flex-col items-center gap-4">
                                <div class="save-container">
                                    {{-- Botón: Icono blanco fijo y fondo verde inicial --}}
                                    <button type="button" 
                                            class="save-btn flex items-center justify-center w-10 h-10 rounded-full bg-[#6B8E23] text-white shadow-sm transition-all hover:scale-110 active:scale-95"
                                            data-url="{{ route('saved.toggle', ['type' => 'guide', 'id' => $guide->id]) }}" 
                                            data-type="guide">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="white" viewBox="0 0 24 24">
                                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="transform scale-90 origin-right">
                                    <x-vote-block :item="$guide" type="guide" />
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach 
            </div>
            <div class="mt-12 pagination-ajax flex justify-end">{{ $savedData->links() }}</div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/universal-save.js') }}"></script>
@endsection