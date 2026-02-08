@if($shouldShow)
<div class="save-container mt-6">
    <button type="button"
            class="save-btn inline-flex items-center px-5 py-2.5 font-bold rounded shadow-md transition-all uppercase text-[10px] tracking-widest {{ $isSaved ? 'bg-[#6B8E23]' : 'bg-[#C67C48]' }} hover:opacity-90"
            data-url="{{ $url }}"
            data-saved="{{ $isSaved ? 'true' : 'false' }}">
        
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="h-4 w-4 mr-2 pointer-events-none text-[#2F2F2F]" 
             fill="{{ $isSaved ? '#2F2F2F' : 'none' }}" 
             viewBox="0 0 24 24" 
             stroke="#2F2F2F">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
        </svg>

        {{-- Color de texto #2F2F2F y eliminamos cualquier shadow/outline --}}
        <span class="btn-text pointer-events-none text-[#2F2F2F] antialiased">
            {{ $isSaved ? 'Saved' : 'Save Guide' }}
        </span>
    </button>
    
    <p class="save-msg text-[#6B8E23] text-xs font-bold {{ $isSaved ? '' : 'hidden' }} uppercase mt-2 tracking-tighter">
        Guide saved!
    </p>
</div>
@endif