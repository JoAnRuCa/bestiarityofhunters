@if($editable)
<a href="{{ $url }}" 
   class="group relative flex items-center justify-center w-8 h-8 rounded-full 
          bg-transparent border border-[#6B8E23]/20 
          text-[#6B8E23]/60 transition-all duration-300
          hover:bg-[#6B8E23] hover:text-white hover:border-[#6B8E23] 
          hover:scale-110 active:scale-90"
   title="Edit Scroll">
    
    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition-transform group-hover:-rotate-12" 
         fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
              d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
    </svg>

    <span class="absolute -top-8 left-1/2 -translate-x-1/2 bg-[#2F2F2F] text-white text-[10px] 
                 px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none uppercase font-bold whitespace-nowrap shadow-md">
        Edit
    </span>
</a>
@endif