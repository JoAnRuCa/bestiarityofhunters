<form action="{{ $action }}" method="POST" class="inline-block delete-form-ajax">
    @csrf
    @method('DELETE')
    
    <button type="submit" 
            class="group relative flex items-center justify-center w-8 h-8 rounded-full 
                   bg-transparent border border-[#2F2F2F]/20 
                   text-[#2F2F2F]/40 transition-all duration-300
                   hover:bg-red-900 hover:text-white hover:border-red-900 
                   hover:scale-110 active:scale-90"
            title="Discard Scroll">
        
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:rotate-12" 
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg>

        <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-[#2F2F2F] text-white text-[10px] 
                     px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none uppercase font-bold whitespace-nowrap">
            Discard
        </span>
    </button>
</form>