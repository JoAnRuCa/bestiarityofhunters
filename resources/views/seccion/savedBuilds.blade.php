@if(!isset($only_content))
@extends('layouts.master')
@section('title', 'Saved Builds')

@section('content')
<div class="w-[95%] md:w-[70%] max-w-7xl mx-auto mt-12 mb-20 p-8 bg-[#F4EBD0] rounded-3xl shadow-2xl border border-[#6B8E23]/20">
    
    <div class="mb-8 border-b-2 border-[#6B8E23]/20 pb-6">
        <h1 class="text-5xl font-black tracking-tighter uppercase italic text-[#2F2F2F]">
            Saved <span class="text-[#6B8E23]">Builds</span>
        </h1>
        <p class="text-[10px] font-bold uppercase tracking-widest text-[#C67C48] mt-2 italic">Your personal armory of specialized hunting gear.</p>
    </div>

    <div class="mb-12">
        <x-filter-panel :action="route('saved.builds')" :activeTags="$activeTags">
            <div class="flex items-center">
                <input type="text" name="autor" placeholder="Creator..." 
                       value="{{ request('autor') }}" 
                       class="bg-white border border-[#C67C48]/30 px-4 py-2 rounded text-xs font-bold tracking-tighter text-gray-700 focus:ring-1 focus:ring-[#6B8E23] outline-none placeholder:text-gray-500 w-full md:w-44 shadow-sm h-[38px] transition-all">
            </div>
        </x-filter-panel>
    </div>

    <div id="builds-wrapper" class="transition-opacity duration-300">
@endif

        {{-- CONTENIDO DINÁMICO --}}
        @if($savedData->count() === 0)
            <div class="py-20 text-center border-2 border-dashed border-[#6B8E23]/10 rounded-lg">
                <p class="text-xl text-gray-600 italic font-serif">Your archive is currently empty or no matches found.</p>
                <a href="{{ route('builds.index') }}" class="mt-4 inline-block text-[#C67C48] font-bold uppercase hover:underline tracking-widest text-xs">
                    Explore the Smithy →
                </a>
            </div>
        @else
            <div id="guides-container" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($savedData as $item)
                    @php $build = $item->build; @endphp
                    
                    <div id="build-card-{{ $build->id }}" class="group p-6 flex justify-between items-stretch relative bg-white/40 border border-[#6B8E23]/10 rounded-2xl transition-all hover:bg-[#6B8E23]/5 duration-300 shadow-sm hover:shadow-md min-h-[160px]">
                        
                        <div class="flex-1 pr-4 flex flex-col">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] font-black text-[#6B8E23] uppercase tracking-tighter italic">Saved Build</span>
                            </div>
                            <h2 class="text-2xl font-bold mb-2 leading-tight">
                                <a href="{{ route('builds.show', $build->slug) }}" class="text-[#2F2F2F] hover:text-[#6B8E23] transition-colors tracking-tight font-serif">
                                    {{ $build->titulo }}
                                </a>
                            </h2>
                            
                            <div class="flex flex-wrap gap-2 mb-4">
                                @foreach($build->tags as $tag)
                                    <span class="px-2 py-0.5 bg-[#C67C48] text-white text-[10px] font-bold uppercase rounded shadow-sm">
                                        {{ $tag->name }}
                                    </span>
                                @endforeach
                            </div>

                            <p class="mt-auto text-[11px] text-[#2F2F2F] font-bold tracking-wider opacity-80 uppercase">
                                By <span class="text-[#C67C48]">{{ $build->user->name }}</span>
                            </p>
                        </div>

                        <div class="flex flex-col items-end justify-between min-w-[80px]">
                            <div class="flex flex-col items-center gap-4">
                                <div class="save-container">
                                    <button type="button" 
                                            class="save-btn flex items-center justify-center w-10 h-10 rounded-full bg-[#6B8E23] text-[#2F2F2F] shadow-sm transition-all hover:scale-110"
                                            data-url="{{ route('saved.toggle', ['type' => 'build', 'id' => $build->id]) }}" 
                                            data-type="build">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="transform scale-90">
                                    <x-vote-block :item="$build" type="build" />
                                </div>
                            </div>

                            {{-- LÓGICA DE ADMIN ACTUALIZADA --}}
                            @if(auth()->check() && (auth()->id() === $build->user_id || auth()->user()->role === 'admin'))
                                <div class="flex flex-row items-center justify-end gap-2 w-full mt-auto">
                                    <x-edit-button :url="route('builds.edit', $build->slug)" :editable="true" />
                                    <x-delete-button :action="route('builds.destroy', $build->slug)" :id="$build->slug" />
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach 
            </div>
            
            <div class="mt-12 pagination-ajax flex justify-end">
                {{ $savedData->links() }}
            </div>
        @endif

@if(!isset($only_content))
    </div>
</div>
@endsection

@section('scripts')
    <script src="{{ asset('js/votes.js') }}"></script>
    <script src="{{ asset('js/universal-save.js') }}"></script>
    <script src="{{ asset('js/list.js') }}"></script>
    <script src="{{ asset('js/borrar.js') }}"></script>
@endsection
@endif