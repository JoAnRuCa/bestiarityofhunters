@props(['title', 'desc', 'link', 'color'])

<a href="{{ $link }}" class="relative group block h-48 overflow-hidden rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-500 {{ $color }}">
    {{-- Patrón de fondo decorativo --}}
    <div class="absolute inset-0 opacity-10 group-hover:scale-110 transition-transform duration-700 pointer-events-none flex items-center justify-center">
        <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14l-5-4.87 6.91-1.01L12 2z"/>
        </svg>
    </div>
    
    <div class="relative z-10 p-8 h-full flex flex-col justify-end">
        <h3 class="text-2xl font-black text-white uppercase italic leading-none mb-1 tracking-tighter">
            {{ $title }}
        </h3>
        <p class="text-white/80 text-[10px] font-bold uppercase tracking-widest italic">
            {{ $desc }}
        </p>
    </div>

    {{-- Degradado inferior para legibilidad --}}
    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-80"></div>
</a>