<div class="comment-node py-4 {{ $level === 1 ? 'ml-6 md:ml-12 border-l-2 border-[#6B8E23]/20 pl-4' : '' }}" data-level="{{ $level }}">
    <div class="flex flex-row items-start gap-4">
        
        {{-- Bloque de Votos --}}
        <div class="flex-shrink-0">
            @if($comment->comentario !== 'This text has been deleted')
                <x-vote-block :item="$comment" :type="$type === 'build' ? 'build_comment' : 'comment'" />
            @else
                <div class="w-8 md:w-10"></div> 
            @endif
        </div>

        <div class="flex-1 min-w-0">
            {{-- Cabecera --}}
            <div class="flex items-center gap-2 mb-1">
                <span class="font-bold text-[#C67C48]">{{ $comment->user->name }}</span>
                @if($comment->user->role === 'admin')
                    <span class="text-[9px] bg-[#6B8E23] text-white px-1.5 py-0.5 rounded uppercase font-black tracking-tighter">Staff</span>
                @endif

            </div>

            {{-- Contenido --}}
            <div id="comment-body-{{ $comment->id }}">
                @if($comment->comentario === 'This text has been deleted')
                    <p class="text-[15px] italic text-[#2f2f2f] opacity-100">This comment has been deleted</p>
                @else
                    <p class="text-gray-800 text-[15px] leading-relaxed break-all">{{ $comment->comentario }}</p>
                @endif
            </div>

            {{-- Botones de Acción --}}
            <div class="flex items-center gap-4 mt-2">
                @auth
                    {{-- Si el comentario está borrado, NO se puede ni Responder, ni Editar, ni volver a Borrar --}}
                    @if($comment->comentario !== 'This text has been deleted')
                        
                        <button onclick="toggleReply('{{ $comment->id }}')" 
                                class="text-[11px] font-bold text-[#6B8E23] uppercase hover:text-[#2f2f2f] transition-colors leading-none">
                            Reply
                        </button>

                        @if(auth()->id() === $comment->user_id || auth()->user()->role === 'admin')
                            
                            {{-- Edit: Ahora solo disponible si NO está borrado --}}
                            <button onclick="toggleEdit('{{ $comment->id }}')" 
                                    class="text-[11px] font-bold text-[#6B8E23] uppercase hover:text-[#2f2f2f] transition-colors leading-none">
                                Edit
                            </button>
                            
                            {{-- Delete --}}
                            <form onsubmit="borrarComentario(event, this)" action="{{ route('comments.soft-delete', $comment->id) }}" method="POST" class="flex items-center m-0 p-0">
                                @csrf @method('PATCH')
                                <input type="hidden" name="type" value="{{ $type }}">
                                <button type="submit" class="text-[11px] font-bold text-red-600 uppercase hover:text-red-800 transition-colors leading-none">
                                    Delete
                                </button>
                            </form>

                        @endif
                    @endif
                @endauth

                @if($comment->respuestas->count() > 0)
                    <button onclick="toggleChildren(this)" 
                            class="text-[11px] font-bold text-[#2f2f2f] uppercase hover:text-[#6B8E23] transition-colors leading-none flex items-center gap-1">
                        <span class="icon text-[8px]">▶</span> 
                        Show {{ $comment->respuestas->count() }} {{ Str::plural('Reply', $comment->respuestas->count()) }}
                    </button>
                @endif
            </div>

            {{-- Formulario Edición (Sigue aquí por si se activa) --}}
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
                <input type="hidden" name="level" value="{{ $level + 1 }}">
                <textarea name="comentario" rows="2" required class="w-full p-2 text-sm border-none focus:ring-0 bg-gray-50 rounded" placeholder="Write a reply..."></textarea>
                <div class="flex justify-end mt-2">
                    <button type="submit" class="bg-[#C67C48] text-white px-3 py-1 rounded text-[11px] font-bold uppercase">Send Reply</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Respuestas --}}
    <div class="replies-container hidden mt-4">
        @foreach($comment->respuestas as $respuesta)
            <x-comment-item :comment="$respuesta" :item="$item" :type="$type" :level="$level + 1" />
        @endforeach
    </div>
</div>