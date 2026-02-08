<div class="flex flex-col items-center justify-center gap-1 vote-container" {{-- Reducido gap a 1 --}}
     data-id="{{ $item->id }}"
     data-model="{{ $type }}"
     data-url="{{ route('votar') }}"
     data-voto="{{ $votoUsuario }}">

    {{-- Botón Upvote --}}
    <button class="vote-btn upvote transition-transform hover:scale-110">
        <svg class="arrow-up w-8 h-8 md:w-10 md:h-10" viewBox="0 0 24 24" {{-- Tamaño responsivo --}}
             stroke="#6B8E23" stroke-width="2" fill="{{ $votoUsuario === 1 ? '#6B8E23' : 'none' }}">
            <path d="M12 6 L6 14 H18 Z"></path>
        </svg>
    </button>

    {{-- Marcador de Score --}}
    <div class="text-lg font-bold vote-score" {{-- Bajado de text-xl a text-lg --}}
         style="color: {{ $item->score() > 0 ? '#6B8E23' : ($item->score() < 0 ? '#2F2F2F' : '#555') }}">
        {{ $item->score() }}
    </div>

    {{-- Botón Downvote --}}
    <button class="vote-btn downvote transition-transform hover:scale-110">
        <svg class="arrow-down w-8 h-8 md:w-10 md:h-10" viewBox="0 0 24 24"
             stroke="#2F2F2F" stroke-width="2" fill="{{ $votoUsuario === -1 ? '#2F2F2F' : 'none' }}">
            <path d="M12 18 L6 10 H18 Z"></path>
        </svg>
    </button>
</div>