<div class="comment-node py-4 {{ $level > 0 ? 'ml-6 md:ml-12 border-l-2 border-[#6B8E23]/20 pl-4' : '' }}" data-level="{{ $level }}">
    <div class="flex flex-row items-start gap-4">
        
        <div class="flex-shrink-0">
            <x-vote-block :item="$comment" type="comment" />
        </div>

        <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
                <span class="font-bold text-[#C67C48]">{{ $comment->user->name }}</span>
                <span class="text-xs text-gray-500 italic">{{ $comment->created_at->diffForHumans() }}</span>
            </div>

            <p class="text-gray-800 text-[15px]">{{ $comment->comentario }}</p>

            <div class="flex items-center gap-4 mt-2">
                @auth
                    <button onclick="toggleReply('{{ $comment->id }}')" class="text-[11px] font-bold text-[#6B8E23] uppercase hover:text-[#556b1c] transition-colors">Reply</button>
                @endauth
                
                @if($comment->respuestas->count() > 0)
                    {{-- BOTÓN ACTUALIZADO --}}
                    <button onclick="toggleChildren(this)" class="text-[11px] font-bold text-[#2F2F2F] uppercase hover:text-[#6B8E23] transition-colors duration-200">
                        <span class="icon">▶</span> Show Replies
                    </button>
                @endif
            </div>

            <form id="reply-form-{{ $comment->id }}" action="{{ route('comments.store') }}" method="POST" onsubmit="return enviarComentario(event, this)" class="hidden mt-4 p-3 bg-white border border-[#6B8E23]/20 rounded">
                @csrf
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="padre" value="{{ $comment->id }}">
                
                <textarea name="comentario" rows="2" required class="w-full p-2 bg-white border-none outline-none text-sm focus:ring-0" placeholder="Write a reply..."></textarea>
                
                <div class="flex justify-end mt-2">
                    <button type="submit" class="bg-[#C67C48] text-white px-3 py-1 rounded text-[11px] font-bold uppercase hover:bg-[#a36236] transition-colors">
                        Send
                    </button>
                </div>
            </form>

            <div class="replies-container hidden mt-4">
                @foreach($comment->respuestas as $respuesta)
                    <x-comment-item :comment="$respuesta" :item="$item" :type="$type" :level="$level + 1" />
                @endforeach
            </div>
        </div>
    </div>
</div>