<div class="flex flex-col items-center justify-center gap-1 vote-container bg-transparent"
     data-id="{{ $item->id }}"
     data-model="{{ $type }}"
     data-url="{{ route('votar') }}"
     data-voto="{{ $votoUsuario }}">

    <button class="vote-btn upvote transition-transform hover:scale-110 bg-transparent border-none outline-none p-0 cursor-pointer">
        <svg class="arrow-up w-8 h-8 md:w-10 md:h-10" viewBox="0 0 24 24"
             stroke="#6B8E23" stroke-width="2" fill="{{ $votoUsuario === 1 ? '#6B8E23' : 'none' }}">
            <path d="M12 6 L6 14 H18 Z"></path>
        </svg>
    </button>

    <div class="text-lg font-bold vote-score select-none"
         style="color: {{ $item->score() > 0 ? '#6B8E23' : ($item->score() < 0 ? '#2F2F2F' : '#555') }}">
        {{ $item->score() }}
    </div>

    <button class="vote-btn downvote transition-transform hover:scale-110 bg-transparent border-none outline-none p-0 cursor-pointer">
        <svg class="arrow-down w-8 h-8 md:w-10 md:h-10" viewBox="0 0 24 24"
             stroke="#2F2F2F" stroke-width="2" fill="{{ $votoUsuario === -1 ? '#2F2F2F' : 'none' }}">
            <path d="M12 18 L6 10 H18 Z"></path>
        </svg>
    </button>
</div>