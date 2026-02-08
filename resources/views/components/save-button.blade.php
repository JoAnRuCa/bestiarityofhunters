@if($shouldShow)
{{-- Forzamos absolute y coordenadas --}}
<div class="save-container" style="position: absolute; top: 24px; right: 24px; z-index: 50;">
    <button type="button"
            class="save-btn flex items-center px-4 py-2 font-bold rounded shadow-sm transition-all uppercase text-[10px] tracking-widest {{ $isSaved ? 'bg-[#6B8E23]' : 'bg-[#C67C48]' }} hover:opacity-90"
            style="border: none;"
            data-url="{{ $url }}">
        
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="h-4 w-4 mr-1.5 pointer-events-none text-[#2F2F2F]" 
             fill="{{ $isSaved ? '#2F2F2F' : 'none' }}" 
             viewBox="0 0 24 24" 
             stroke="#2F2F2F">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
        </svg>

        <span class="btn-text pointer-events-none text-[#2F2F2F]">
            {{ $isSaved ? 'Saved' : 'Save Guide' }}
        </span>
    </button>
    
    <p class="save-msg text-[#6B8E23] text-[9px] font-bold {{ $isSaved ? '' : 'hidden' }} uppercase mt-1 tracking-tighter text-right">
        Guide saved!
    </p>
</div>
@endif