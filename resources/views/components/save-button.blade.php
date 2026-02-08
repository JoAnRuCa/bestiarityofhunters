@if($shouldShow)
<div class="save-container mt-6">
    <button type="button"
            class="save-btn inline-flex items-center px-5 py-2.5 font-bold rounded shadow-md transition-all uppercase text-[10px] tracking-widest {{ $isSaved ? 'bg-gray-500 text-white' : 'bg-[#C67C48] text-white hover:bg-[#a1633a]' }}"
            data-url="{{ $url }}"
            data-saved="{{ $isSaved ? 'true' : 'false' }}">
        
        <svg xmlns="http://www.w3.org/2000/svg" 
             class="h-4 w-4 mr-2 pointer-events-none" 
             fill="{{ $isSaved ? 'currentColor' : 'none' }}" 
             viewBox="0 0 24 24" 
             stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
        </svg>

        <span class="btn-text pointer-events-none">
            {{ $isSaved ? 'Saved' : 'Save Guide' }}
        </span>
    </button>
    
    <p class="save-msg text-[#6B8E23] text-xs font-bold {{ $isSaved ? '' : 'hidden' }} uppercase mt-2 tracking-tighter">
        Guide saved!
    </p>
</div>
@endif