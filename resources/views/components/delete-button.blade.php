<form action="{{ $action }}" method="POST" class="inline-block delete-form-ajax">
    @csrf
    @method('DELETE')
    
    <div class="relative flex items-center justify-center">
        <button type="submit" 
                class="peer flex items-center justify-center w-8 h-8 rounded-full 
                       bg-transparent border border-[#2F2F2F]/20 
                       text-[#2F2F2F]/40 opacity-40 hover:opacity-100 transition-all duration-300
                       hover:bg-red-900 hover:text-white hover:border-red-900 
                       hover:scale-110 active:scale-90"
                title="Discard Scroll">
            
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>

        {{-- Tooltip: Reacciona solo al hover del botón --}}
        <span class="absolute -top-10 left-1/2 -translate-x-1/2 bg-[#2F2F2F] text-white text-[10px] 
                     px-2 py-1 rounded pointer-events-none uppercase font-bold whitespace-nowrap shadow-md z-50
                     opacity-0 scale-95 peer-hover:opacity-100 peer-hover:scale-100 transition-all duration-200">
            Discard
        </span>
    </div>
</form>