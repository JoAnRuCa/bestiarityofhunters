<div class="flex flex-col items-center justify-center gap-2 vote-container"
     data-guide="{{ $guide->id }}"
     data-url="{{ route('votar') }}">

    <button class="vote-btn upvote">
        <svg class="arrow-up" width="40" height="40" viewBox="0 0 24 24"
             stroke="#6B8E23" stroke-width="2" fill="none">
            <path d="M12 6 L6 14 H18 Z"></path>
        </svg>
    </button>

    <div class="text-xl font-bold vote-score"
         style="color: {{ $guide->score() > 0 ? '#6B8E23' : ($guide->score() < 0 ? '#2F2F2F' : '#555') }}">
        {{ $guide->score() }}
    </div>

    <button class="vote-btn downvote">
        <svg class="arrow-down" width="40" height="40" viewBox="0 0 24 24"
             stroke="#2F2F2F" stroke-width="2" fill="none">
            <path d="M12 18 L6 10 H18 Z"></path>
        </svg>
    </button>

</div>
