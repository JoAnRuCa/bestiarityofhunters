{{-- Eliminamos 'border-t' y quitamos la opacidad /95 para que el color sea sólido e idéntico al fondo --}}
<footer class="bg-[#F4EBD0] px-8 pt-12 pb-8 mt-auto">
  <div class="max-w-[1400px] mx-auto flex flex-col md:flex-row items-center justify-center gap-16">

    <div class="flex items-center gap-3 text-2xl font-bold text-[#6B8E23]">
        <a href="{{ route('home') }}">
            {{-- Añadida una opacidad sutil para que el logo no "grite" tanto en el footer --}}
            <img class="w-[260px] h-[150px] object-contain rounded opacity-80 hover:opacity-100 transition-opacity" 
                 src="{{ asset('images/logo.png') }}" 
                 alt="Logo"
                 onerror="this.style.display='none';">
        </a>
    </div>

    <div class="flex flex-col items-center gap-4">
      <div class="flex flex-wrap justify-center gap-8">
        <a href="{{ route('privacy') }}"
          class="text-[#2F2F2F] no-underline font-black uppercase italic text-xs tracking-widest px-4 py-2 rounded-lg transition-all duration-300 hover:bg-[#6B8E23]/10 hover:text-[#6B8E23]">
          Privacy Policy
        </a>
        <a href="{{ route('about') }}"
          class="text-[#2F2F2F] no-underline font-black uppercase italic text-xs tracking-widest px-4 py-2 rounded-lg transition-all duration-300 hover:bg-[#6B8E23]/10 hover:text-[#6B8E23]">
          About us
        </a>
        <a href="{{ route('disclaimer') }}"
          class="text-[#2F2F2F] no-underline font-black uppercase italic text-xs tracking-widest px-4 py-2 rounded-lg transition-all duration-300 hover:bg-[#6B8E23]/10 hover:text-[#6B8E23]">
          Disclaimer
        </a>
      </div>

      <div class="flex flex-wrap justify-center gap-8">
        <a href="{{ route('terms') }}"
          class="text-[#2F2F2F] no-underline font-black uppercase italic text-xs tracking-widest px-4 py-2 rounded-lg transition-all duration-300 hover:bg-[#6B8E23]/10 hover:text-[#6B8E23]">
          Terms of use
        </a>
        <a href="{{ route('contact') }}"
          class="text-[#2F2F2F] no-underline font-black uppercase italic text-xs tracking-widest px-4 py-2 rounded-lg transition-all duration-300 hover:bg-[#6B8E23]/10 hover:text-[#6B8E23]">
          Contact us
        </a>
      </div>
    </div>
  </div>

  {{-- Esta línea de abajo sí la mantenemos si quieres separar el Copyright, pero con una opacidad muy baja --}}
  <div class="mt-8 pt-4 border-t border-[#6B8E23]/10 text-center w-full">
    <p class="text-[#2F2F2F]/50 font-bold uppercase italic text-[10px] tracking-[0.2em]">
      © 2025-2026 Hunter's Bestiary. All rights reserved. Research Division.
    </p>
  </div>
</footer>