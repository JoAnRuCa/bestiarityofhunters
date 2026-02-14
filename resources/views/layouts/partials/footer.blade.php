<footer class="bg-[#F4EBD0]/95 px-8 pt-12 pb-8 mt-auto border-t border-[#6B8E23]/20">
  <div class="max-w-[1400px] mx-auto flex flex-col md:flex-row items-center justify-center gap-16">

    <div class="flex items-center gap-3 text-2xl font-bold text-[#6B8E23]">
        <a href="{{ route('home') }}">
            <img class="w-[260px] h-[150px] object-contain rounded" src="{{ asset('images/logo.png') }}" alt="Logo"
              onerror="this.style.display='none';">
        </a>
    </div>

    <div class="flex flex-col items-center gap-4">
      <div class="flex flex-wrap justify-center gap-8">
        <a href="{{ route('privacy') }}"
          class="text-[#2F2F2F] no-underline font-medium px-4 py-2 rounded-lg transition-all duration-300 hover:bg-[#6B8E23]/10 hover:text-[#6B8E23]">
          Privacy Policy
        </a>
        <a href="{{ route('about') }}"
          class="text-[#2F2F2F] no-underline font-medium px-4 py-2 rounded-lg transition-all duration-300 hover:bg-[#6B8E23]/10 hover:text-[#6B8E23]">
          About us
        </a>
        <a href="{{ route('disclaimer') }}"
          class="text-[#2F2F2F] no-underline font-medium px-4 py-2 rounded-lg transition-all duration-300 hover:bg-[#6B8E23]/10 hover:text-[#6B8E23]">
          Disclaimer
        </a>
      </div>

      <div class="flex flex-wrap justify-center gap-8">
        <a href="{{ route('terms') }}"
          class="text-[#2F2F2F] no-underline font-medium px-4 py-2 rounded-lg transition-all duration-300 hover:bg-[#6B8E23]/10 hover:text-[#6B8E23]">
          Terms of use
        </a>
        <a href="{{ route('contact') }}"
          class="text-[#2F2F2F] no-underline font-medium px-4 py-2 rounded-lg transition-all duration-300 hover:bg-[#6B8E23]/10 hover:text-[#6B8E23]">
          Contact us
        </a>
      </div>
    </div>
  </div>

  <div class="mt-4 pt-4 border-t border-[#6B8E23]/20 text-center w-full">
    <p class="text-[#2F2F2F] my-2 text-sm">
      © 2025-2026 Hunter's Bestiary. All rights reserved.
    </p>
  </div>
</footer>