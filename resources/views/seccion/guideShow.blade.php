@extends('layouts.master')
@section('title', $guide->titulo)

@section('content')

<div class="w-[60%] max-w-4xl mx-auto mt-12 mb-12 p-8 bg-[#F4EBD0] rounded-lg shadow-lg">

    {{-- TÍTULO + VOTOS DE LA GUÍA --}}
    <div class="flex items-center justify-between mb-6 border-b pb-4 border-[#6B8E23]/20">
        <h1 class="text-4xl md:text-5xl font-extrabold text-[#6B8E23]">
            {{ $guide->titulo }}
        </h1>
        <x-vote-block :item="$guide" type="guide" />
    </div>

    {{-- Autor y fecha de la guía --}}
    <p class="text-gray-700 mb-4 text-sm">
        By <span class="font-bold text-[#C67C48]">{{ $guide->user->name }}</span> • {{ $guide->created_at->diffForHumans() }}
    </p>

    {{-- Contenido de la Guía --}}
    <div class="prose max-w-none text-gray-900 leading-relaxed mb-12 bg-white/30 p-4 rounded-md">
        {!! nl2br(e($guide->contenido)) !!}
    </div>

    <hr class="border-t border-[#6B8E23]/30 my-8">

    {{-- SECCIÓN DE COMENTARIOS --}}
    <section class="comments-area">
        <h2 class="text-2xl font-bold text-[#6B8E23] mb-6 flex items-center gap-2">
            Discussion
        </h2>

        {{-- Formulario para nuevo comentario --}}
        @auth
            <form action="{{ route('comments.store') }}" method="POST" class="mb-10 bg-white/40 p-4 rounded-lg border border-[#6B8E23]/10">
                @csrf
                <input type="hidden" name="item_id" value="{{ $guide->id }}">
                <input type="hidden" name="type" value="guide">
                
                <textarea name="comentario" rows="3" required
                    class="w-full p-3 rounded border border-gray-300 focus:ring-2 focus:ring-[#6B8E23] focus:outline-none bg-white/90"
                    placeholder="Share your hunting tips..."></textarea>
                
                <div class="flex justify-end mt-2">
                    <button type="submit" class="px-6 py-2 bg-[#6B8E23] text-white font-bold rounded-md hover:bg-[#556b1c] transition shadow-md">
                        Post Comment
                    </button>
                </div>
            </form>
        @endauth

        {{-- Listado de Comentarios --}}
        <div class="space-y-6">
            @forelse(($guide->comments ?? collect())->where('padre', null) as $comment)
                <div class="p-4 bg-white rounded-lg shadow-sm border border-gray-100">
                    
                    {{-- Contenedor principal: Votos a la derecha --}}
                    <div class="flex flex-row-reverse items-start gap-4">
                        
                        {{-- 1. BLOQUE DE VOTOS (DERECHA) --}}
                        <div class="flex flex-col items-center min-w-[40px] pt-1">
                            <x-vote-block :item="$comment" type="comment" />
                        </div>

                        {{-- 2. CONTENIDO (IZQUIERDA) --}}
                        <div class="flex-1">
                            {{-- Encabezado: Nombre y tiempo pegados y en minúsculas --}}
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-bold text-[#C67C48]">{{ $comment->user->name }}</span>
                                <span class="text-[10px] text-gray-400 font-normal lowercase italic">
                                    {{ $comment->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <p class="text-gray-800 mb-2 leading-snug">{{ $comment->comentario }}</p>

                            @auth
                                <button onclick="toggleReply('{{ $comment->id }}')" class="text-xs font-bold text-[#6B8E23] hover:underline uppercase tracking-tighter">
                                    Reply
                                </button>

                                {{-- Formulario Respuesta Oculto --}}
                                <form id="reply-form-{{ $comment->id }}" action="{{ route('comments.store') }}" method="POST" class="hidden mt-4 bg-[#F4EBD0]/60 p-3 rounded-md border border-[#6B8E23]/10">
                                    @csrf
                                    <input type="hidden" name="item_id" value="{{ $guide->id }}">
                                    <input type="hidden" name="type" value="guide">
                                    <input type="hidden" name="padre" value="{{ $comment->id }}">
                                    <textarea name="comentario" rows="2" class="w-full p-2 rounded border border-gray-300 text-sm" placeholder="Write a reply..."></textarea>
                                    <div class="flex justify-end mt-2">
                                        <button type="submit" class="text-xs bg-[#C67C48] text-white px-3 py-1 rounded font-bold shadow-sm hover:bg-[#a6683c]">Send Reply</button>
                                    </div>
                                </form>
                            @endauth

                            {{-- Renderizado de Respuestas (Anidadas) --}}
                            @if($comment->respuestas->count() > 0)
                                <div class="mt-4 space-y-4 border-l-2 border-[#6B8E23]/20 pl-4 bg-gray-50/50 rounded-r-md">
                                    @foreach($comment->respuestas as $reply)
                                        <div class="flex flex-row-reverse items-start gap-3 py-2">
                                            {{-- Votos respuesta --}}
                                            <div class="scale-75 opacity-80 pt-1">
                                                <x-vote-block :item="$reply" type="comment" />
                                            </div>
                                            {{-- Texto respuesta --}}
                                            <div class="flex-1 text-sm">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="font-bold text-[#C67C48]">{{ $reply->user->name }}</span>
                                                    <span class="text-[9px] text-gray-400 font-normal lowercase italic">
                                                        {{ $reply->created_at->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <p class="text-gray-700 leading-tight">{{ $reply->comentario }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-8 bg-white/20 rounded-lg border border-dashed border-gray-400">
                    <p class="text-gray-500 italic">No comments yet. Be the first to start the hunt!</p>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Botón Volver --}}
    <div class="mt-12 pt-6 border-t border-[#6B8E23]/20">
        <a href="{{ url('/guides') }}"
           class="inline-flex items-center px-4 py-2 bg-[#6B8E23] text-white font-bold rounded hover:bg-[#556b1c] transition shadow-md">
            ← Back to Guides
        </a>
    </div>

</div>

@endsection

@section('scripts')
<script src="{{ asset('js/votes.js') }}"></script>
<script>
    function toggleReply(id) {
        const form = document.getElementById(`reply-form-${id}`);
        if(form) {
            form.classList.toggle('hidden');
            if(!form.classList.contains('hidden')) {
                form.querySelector('textarea').focus();
            }
        }
    }
</script>
@endsection