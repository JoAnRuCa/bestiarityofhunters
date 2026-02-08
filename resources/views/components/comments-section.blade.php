<section class="comments-area">
    <h2 class="text-2xl font-bold text-[#6B8E23] mb-8 border-b border-[#6B8E23]/20 pb-2 inline-block">Discussion</h2>

    @auth
        <form action="{{ route('comments.store') }}" method="POST" onsubmit="return enviarComentario(event, this)" class="mb-10">
            @csrf
            <input type="hidden" name="item_id" value="{{ $item->id }}">
            <input type="hidden" name="type" value="{{ $type }}">
            <textarea name="comentario" rows="3" required 
                class="w-full p-4 rounded-lg bg-white/20 border-none shadow-inner focus:ring-1 focus:ring-[#6B8E23] outline-none transition-all placeholder:text-gray-400" 
                placeholder="Share your hunting tips..."></textarea>
            
            <div class="flex justify-end mt-4"> 
                <button type="submit" class="px-6 py-2 bg-[#6B8E23] text-white font-bold rounded shadow-md hover:opacity-90 uppercase text-sm">
                    Post Comment
                </button>
            </div>
        </form>
    @endauth

    {{-- Contenedor sin ninguna clase de borde ni división automática --}}
    <div id="comments-wrapper" class="flex flex-col">
        @forelse(($item->comments ?? collect())->where('padre', null) as $comment)
            <x-comment-item :comment="$comment" :item="$item" :type="$type" :level="0" />
        @empty
            <div id="no-comments-msg" class="text-center py-10 opacity-50 italic text-gray-500">
                No comments yet. Start the conversation!
            </div>
        @endforelse
    </div>
</section>