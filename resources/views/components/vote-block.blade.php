<div class="flex flex-col items-center justify-center gap-2 vote-container"
     data-id="{{ $item->id }}"
     data-model="{{ $type }}"
     data-url="{{ route('votar') }}"
     data-voto="{{ $votoUsuario }}">

    {{-- Botón Upvote --}}
    <button class="transition-transform duration-200 transform vote-btn upvote hover:scale-110 active:scale-95">
        <svg class="arrow-up" width="40" height="40" viewBox="0 0 24 24"
             stroke="#6B8E23" stroke-width="2" fill="{{ $votoUsuario === 1 ? '#6B8E23' : 'none' }}">
            <path d="M12 6 L6 14 H18 Z"></path>
        </svg>
    </button>

    {{-- Marcador de Score --}}
    <div class="text-xl font-bold vote-score"
         style="color: {{ $item->score() > 0 ? '#6B8E23' : ($item->score() < 0 ? '#2F2F2F' : '#555') }}">
        {{ $item->score() }}
    </div>

    {{-- Botón Downvote --}}
    <button class="transition-transform duration-200 transform vote-btn downvote hover:scale-110 active:scale-95">
        <svg class="arrow-down" width="40" height="40" viewBox="0 0 24 24"
             stroke="#2F2F2F" stroke-width="2" fill="{{ $votoUsuario === -1 ? '#2F2F2F' : 'none' }}">
            <path d="M12 18 L6 10 H18 Z"></path>
        </svg>
    </button>
</div>