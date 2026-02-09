@if(!isset($only_content))
@extends('layouts.master')
@section('title', 'Saved Archives')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12 mb-20">
    <div class="bg-[#F4EBD0] rounded-xl shadow-2xl border border-[#6B8E23]/30 overflow-hidden p-8 md:p-12">
        <div class="mb-10 border-b-2 border-[#6B8E23]/20 pb-6">
            <h1 class="text-5xl font-extrabold text-[#6B8E23] uppercase tracking-tighter">
                Saved <span class="text-[#C67C48]">Archives</span>
            </h1>
        </div>

        <div class="mb-12">
            <x-filter-panel :action="route('saved.guides')" :allTags="$allTags" :activeTags="$activeTags" :isTagActive="$isTagActive">
                <input type="text" name="autor" placeholder="Author..." value="{{ request('autor') }}" 
                       class="bg-white border border-[#C67C48]/30 px-4 py-2 rounded text-xs font-bold text-gray-700 w-full md:w-auto shadow-sm">
            </x-filter-panel>
        </div>

        <div id="guides-wrapper" class="transition-opacity duration-300">
@endif

            @if($savedData->count() === 0)
                <p class="py-20 text-center italic">Your archive is empty.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    @foreach($savedData as $item)
                        @php $guide = $item->guide; @endphp
                        <div class="group p-6 border border-[#6B8E23]/10 rounded-lg bg-white/30 flex justify-between items-start">
                            <div>
                                <h2 class="text-2xl font-bold font-serif text-[#6B8E23] uppercase">
                                    <a href="{{ route('guides.show', $guide->slug) }}">{{ $guide->titulo }}</a>
                                </h2>
                                <p class="text-[11px] font-bold mt-2">By <span class="text-[#C67C48]">{{ $guide->user->name }}</span></p>
                            </div>
                            <button type="button" class="save-btn w-10 h-10 rounded-full bg-[#6B8E23] text-[#2F2F2F] flex items-center justify-center"
                                    data-url="{{ route('saved.toggle', ['type' => 'guide', 'id' => $guide->id]) }}" data-type="guide">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" /></svg>
                            </button>
                        </div>
                    @endforeach
                </div>
                <div class="mt-12 pagination-ajax">{{ $savedData->links() }}</div>
            @endif

@if(!isset($only_content))
        </div>
    </div>
</div>
@endsection
@section('scripts')
    <script src="{{ asset('js/universal-save.js') }}"></script>
    <script src="{{ asset('js/guide-list.js') }}"></script>
@endsection
@endif