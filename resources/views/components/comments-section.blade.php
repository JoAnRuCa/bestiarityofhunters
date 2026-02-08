<section class="comments-area">
    <h2 class="text-2xl font-bold text-[#6B8E23] mb-6">Discussion</h2>

    @auth
        <form action="{{ route('comments.store') }}" method="POST" onsubmit="return enviarComentario(event, this)" class="mb-12">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <input type="hidden" name="type" value="{{ $type }}">
            <textarea name="comentario" rows="3" required 
                class="w-full p-3 rounded border border-gray-300 bg-white/90 shadow-inner focus:ring-2 focus:ring-[#6B8E23] outline-none" 
                placeholder="Share your hunting tips..."></textarea>
            
            <div class="flex justify-end mt-4"> 
                <button type="submit" class="px-6 py-2 bg-[#6B8E23] text-white font-bold rounded-md hover:bg-[#556b1c] shadow-md uppercase text-sm transition">
                    Post Comment
                </button>
            </div>
        </form>
    @endauth

    <div id="comments-wrapper" class="space-y-6">
        @forelse(($item->comments ?? collect())->where('padre', null) as $comment)
            <x-comment-item :comment="$comment" :item="$item" :type="$type" :level="0" />
        @empty
            <div id="no-comments-msg" class="text-center py-8 bg-white/20 rounded-lg border border-dashed border-gray-400">
                <p class="text-gray-500 italic">No comments yet. Be the first to start the hunt!</p>
            </div>
        @endforelse
    </div>
</section>