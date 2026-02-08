<div class="comment-node py-4 {{ $level > 0 ? 'ml-6 md:ml-12 pl-4 mt-2' : '' }}" 
     data-level="{{ $level }}">
    
    <div class="flex flex-row-reverse items-start gap-4">
        {{-- Votos --}}
        <div class="flex flex-col items-center min-w-[35px] {{ $level > 0 ? 'scale-90' : '' }}">
            <x-vote-block :item="$comment" type="comment" />
        </div>

        <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
                <span class="font-bold text-[#C67C48] text-sm">{{ $comment->user->name }}</span>
                <span class="text-[11px] text-gray-500 font-normal italic">
                    {{ $comment->created_at->diffForHumans() }}
                </span>
            </div>

            <p class="text-gray-800 mb-2 leading-relaxed text-[15px]">
                {{ $comment->comentario }}
            </p>

            <div class="flex items-center gap-4 mt-2">
                @auth
                    <button onclick="toggleReply('{{ $comment->id }}')" class="text-[11px] font-bold text-[#6B8E23] hover:underline uppercase transition-colors">Reply</button>
                @endauth

                @if($comment->respuestas->count() > 0)
                    {{-- Cambiado text-gray-400 por text-[#2F2F2F] --}}
                    <button onclick="toggleChildren(this)" class="text-[11px] font-bold text-[#2F2F2F] hover:text-[#6B8E23] uppercase flex items-center gap-1 transition-colors">
                        <span class="icon text-[9px]">▶</span> 
                        <span class="label">Show {{ $comment->respuestas->count() }} Replies</span>
                    </button>
                @endif
            </div>

            {{-- Formulario de respuesta --}}
            @auth
                <form id="reply-form-{{ $comment->id }}" action="{{ route('comments.store') }}" method="POST" 
                      onsubmit="return enviarComentario(event, this)" class="hidden mt-4 bg-[#FEF9E7]/40 p-3 rounded-md">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $item->id }}">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="padre" value="{{ $comment->id }}">
                    <textarea name="comentario" rows="2" class="w-full p-2 bg-white/20 border-none rounded text-sm outline-none shadow-inner" placeholder="Write a reply..."></textarea>
                    <div class="flex justify-end mt-2">
                        <button type="submit" class="text-[11px] bg-[#C67C48] text-white px-3 py-1 rounded font-bold uppercase hover:bg-[#a36236]">Send Reply</button>
                    </div>
                </form>
            @endauth

            <div class="replies-container hidden">
                @foreach($comment->respuestas as $respuesta)
                    <x-comment-item :comment="$respuesta" :item="$item" :type="$type" :level="$level + 1" />
                @endforeach
            </div>
        </div>
    </div>
</div>