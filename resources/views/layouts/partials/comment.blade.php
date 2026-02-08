<div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100 {{ $level > 0 ? 'mt-4 border-l-4 border-[#6B8E23]/20' : '' }}" 
     style="margin-left: {{ min($level * 30, 120) }}px;">
    
    <div class="flex flex-row-reverse items-start gap-4">
        {{-- VOTOS --}}
        <div class="flex flex-col items-center min-w-[40px] pt-1 {{ $level > 0 ? 'scale-90' : '' }}">
            <x-vote-block :item="$comment" type="comment" />
        </div>

        {{-- CONTENIDO --}}
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
                <span class="font-bold text-[#C67C48]">{{ $comment->user->name }}</span>
                <span class="text-[11px] text-gray-400 font-normal lowercase italic">
                    {{ $comment->created_at->diffForHumans() }}
                </span>
            </div>

            <p class="text-gray-800 mb-2 leading-snug text-sm">
                {{ $comment->comentario }}
            </p>

            @auth
                <button onclick="toggleReply('{{ $comment->id }}')" class="text-[10px] font-bold text-[#6B8E23] hover:underline uppercase">
                    Reply
                </button>

                <form id="reply-form-{{ $comment->id }}" action="{{ route('comments.store') }}" method="POST" class="hidden mt-4 bg-[#F4EBD0]/60 p-3 rounded-md border border-[#6B8E23]/10">
                    @csrf
                    <input type="hidden" name="item_id" value="{{ $guide->id }}">
                    <input type="hidden" name="type" value="guide">
                    <input type="hidden" name="padre" value="{{ $comment->id }}">
                    
                    {{-- Textarea vacío sin menciones --}}
                    <textarea name="comentario" rows="2" class="w-full p-2 rounded border border-gray-300 text-sm" 
                              placeholder="Write your reply..."></textarea>
                              
                    <div class="flex justify-end mt-3">
                        <button type="submit" class="text-[10px] bg-[#C67C48] text-white px-3 py-1 rounded font-bold uppercase shadow-sm hover:bg-[#a6683c]">
                            Send Reply
                        </button>
                    </div>
                </form>
            @endauth
        </div>
    </div>

    {{-- RECURSIVIDAD PARA ANIDACIÓN REAL --}}
    @if($comment->respuestas && $comment->respuestas->count() > 0)
        <div class="replies-container">
            @foreach($comment->respuestas as $respuesta)
                @include('layouts.partials.comment', ['comment' => $respuesta, 'guide' => $guide, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>