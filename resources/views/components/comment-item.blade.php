<div class="comment-node py-4 {{ $level > 0 ? 'ml-6 md:ml-12 border-l-2 border-[#6B8E23]/20 pl-4' : '' }}" data-level="{{ $level }}">
    <div class="flex flex-row items-start gap-4">
        
        <div class="flex-shrink-0">
            @if($comment->comentario !== 'This text has been deleted')
                <x-vote-block :item="$comment" :type="$type === 'build' ? 'build_comment' : 'comment'" />
            @else
                {{-- Espaciador para mantener alineación cuando no hay votos --}}
                <div class="w-8 md:w-10"></div> 
            @endif
        </div>

        <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
                <span class="font-bold text-[#C67C48]">{{ $comment->user->name }}</span>
                <span class="text-xs text-gray-500 italic">{{ $comment->created_at->diffForHumans() }}</span>
            </div>

            {{-- Contenido del Texto --}}
            <div id="comment-body-{{ $comment->id }}">
                @if($comment->comentario === 'This text has been deleted')
                    {{-- Forzamos color oscuro y quitamos cualquier opacidad heredada --}}
                    <p class="text-[15px] italic" style="color: #2f2f2f !important; opacity: 1 !important;">
                        This comment has been deleted
                    </p>
                @else
                    <p class="text-gray-800 text-[15px]">{{ $comment->comentario }}</p>
                @endif
            </div>

            {{-- Botones de Acción: flex e items-center para alinear perfectamente --}}
            <div class="flex items-center gap-4 mt-2">
                @if($comment->comentario !== 'This text has been deleted')
                    @auth
                        <button onclick="toggleReply('{{ $comment->id }}')" 
                                class="text-[11px] font-bold text-[#6B8E23] uppercase hover:text-[#2f2f2f] transition-colors">
                            Reply
                        </button>

                        @if(auth()->id() === $comment->user_id)
                            <button onclick="toggleEdit('{{ $comment->id }}')" 
                                    class="text-[11px] font-bold text-[#6B8E23] uppercase hover:text-[#2f2f2f] transition-colors">
                                Edit
                            </button>
                            
                            {{-- Formulario inline para que el botón Delete no se desplace --}}
                            <form onsubmit="borrarComentario(event, this)" action="{{ route('comments.soft-delete', $comment->id) }}" method="POST" class="inline-block m-0 p-0">
                                @csrf @method('PATCH')
                                <input type="hidden" name="type" value="{{ $type }}">
                                <button type="submit" class="text-[11px] font-bold text-red-600 uppercase hover:text-red-800 transition-colors">
                                    Delete
                                </button>
                            </form>
                        @endif
                    @endauth
                @endif

                @if($comment->respuestas->count() > 0)
                    <button onclick="toggleChildren(this)" 
                            class="text-[11px] font-bold text-[#2f2f2f] uppercase hover:text-[#6B8E23] transition-colors">
                        <span class="icon inline-block">▶</span> Show Replies
                    </button>
                @endif
            </div>

            {{-- Formulario Edición --}}
            <form id="edit-form-{{ $comment->id }}" onsubmit="enviarEdicion(event, this, '{{ $comment->id }}')" action="{{ route('comments.update', $comment->id) }}" method="POST" class="hidden mt-4 p-3 bg-white border border-[#6B8E23]/20 rounded shadow-sm">
                @csrf @method('PUT')
                <input type="hidden" name="type" value="{{ $type }}">
                <textarea name="comentario" rows="2" class="w-full p-2 text-sm border-none focus:ring-0 bg-gray-50 rounded" required>{{ $comment->comentario }}</textarea>
                <div class="flex justify-end gap-2 mt-2">
                    <button type="button" onclick="toggleEdit('{{ $comment->id }}')" class="text-[10px] uppercase font-bold text-gray-400">Cancel</button>
                    <button type="submit" class="bg-[#6B8E23] text-white px-3 py-1 rounded text-[11px] font-bold uppercase">Update</button>
                </div>
            </form>

            {{-- Formulario Respuesta --}}
            <form id="reply-form-{{ $comment->id }}" action="{{ route('comments.store') }}" method="POST" onsubmit="return enviarComentario(event, this)" class="hidden mt-4 p-3 bg-white border border-[#6B8E23]/20 rounded shadow-sm">
                @csrf
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="padre" value="{{ $comment->id }}">
                <textarea name="comentario" rows="2" required class="w-full p-2 text-sm border-none focus:ring-0 bg-gray-50 rounded" placeholder="Write a reply..."></textarea>
                <div class="flex justify-end mt-2">
                    <button type="submit" class="bg-[#C67C48] text-white px-3 py-1 rounded text-[11px] font-bold uppercase">Send</button>
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