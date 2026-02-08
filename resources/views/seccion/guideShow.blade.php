@extends('layouts.master')
@section('title', $guide->titulo)

@section('content')

<div class="w-[60%] max-w-4xl mx-auto mt-12 mb-12 p-8 bg-[#F4EBD0] rounded-lg shadow-lg">

    {{-- TÍTULO + VOTOS --}}
    <div class="flex items-center justify-between mb-6 border-b pb-4 border-[#6B8E23]/20">
        <h1 class="text-4xl md:text-5xl font-extrabold text-[#6B8E23]">
            {{ $guide->titulo }}
        </h1>
        <x-vote-block :item="$guide" type="guide" />
    </div>

    {{-- Autor y fecha --}}
    <p class="text-gray-700 mb-4">
        By <strong>{{ $guide->user->name }}</strong> • {{ $guide->created_at->diffForHumans() }}
    </p>

    {{-- Tags --}}
    <div class="flex flex-wrap gap-2 mb-6">
        @foreach($guide->tags as $tag)
            <span class="px-3 py-1 bg-[#6B8E23] text-white text-sm rounded">
                {{ $tag->name }}
            </span>
        @endforeach
    </div>

    {{-- Contenido --}}
    <div class="prose max-w-none text-gray-900 leading-relaxed mb-12">
        {!! nl2br(e($guide->contenido)) !!}
    </div>

    <hr class="border-t border-[#6B8E23]/30 my-8">

    {{-- SECCIÓN DE COMENTARIOS --}}
    <section class="comments-area">
        <h2 class="text-2xl font-bold text-[#6B8E23] mb-6">Discussion</h2>

        {{-- Formulario para nuevo comentario (Solo Logeados) --}}
        @auth
            <form action="{{ route('comments.store') }}" method="POST" class="mb-10 bg-white/50 p-4 rounded-lg">
                @csrf
                <input type="hidden" name="item_id" value="{{ $guide->id }}">
                <input type="hidden" name="type" value="guide">
                
                <textarea name="comentario" rows="3" required
                    class="w-full p-3 rounded border border-gray-300 focus:ring-2 focus:ring-[#6B8E23] focus:outline-none"
                    placeholder="Share your thoughts or hunter tips..."></textarea>
                
                <button type="submit" class="mt-2 px-6 py-2 bg-[#6B8E23] text-white font-bold rounded hover:bg-[#556b1c] transition">
                    Post Comment
                </button>
            </form>
        @else
            <div class="mb-10 p-4 bg-white/30 border border-dashed border-[#6B8E23] rounded-lg text-center">
                <p class="text-gray-700">You must be <a href="{{ route('login') }}" class="text-[#6B8E23] font-bold underline">logged in</a> to post a comment.</p>
            </div>
        @endauth

        {{-- Listado de Comentarios --}}
        <div class="space-y-6">
            @forelse($guide->comments->where('padre', null) as $comment)
                <div class="flex gap-4 p-4 bg-white rounded-lg shadow-sm">
                    {{-- Votos del Comentario --}}
                    <x-vote-block :item="$comment" type="comment" />

                    <div class="flex-1">
                        <div class="flex justify-between items-baseline mb-1">
                            <span class="font-bold text-[#C67C48]">{{ $comment->user->name }}</span>
                            <span class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-gray-800 mb-3">{{ $comment->comentario }}</p>

                        @auth
                            <button onclick="toggleReply('{{ $comment->id }}')" class="text-sm font-semibold text-[#6B8E23] hover:text-[#556b1c]">
                                Reply
                            </button>

                            {{-- Formulario Respuesta Oculto --}}
                            <form id="reply-form-{{ $comment->id }}" action="{{ route('comments.store') }}" method="POST" class="hidden mt-4 bg-[#F4EBD0] p-3 rounded">
                                @csrf
                                <input type="hidden" name="item_id" value="{{ $guide->id }}">
                                <input type="hidden" name="type" value="guide">
                                <input type="hidden" name="padre" value="{{ $comment->id }}">
                                <textarea name="comentario" rows="2" class="w-full p-2 rounded border" placeholder="Write a reply..."></textarea>
                                <button type="submit" class="mt-2 text-xs bg-[#C67C48] text-white px-3 py-1 rounded">Send Reply</button>
                            </form>
                        @endauth

                        {{-- Respuestas (Hijos) --}}
                        @if($comment->respuestas->count() > 0)
                            <div class="mt-4 space-y-4 border-l-2 border-[#6B8E23]/20 pl-4">
                                @foreach($comment->respuestas as $reply)
                                    <div class="flex gap-3 text-sm">
                                        <x-vote-block :item="$reply" type="comment" />
                                        <div class="flex-1">
                                            <span class="font-bold text-[#C67C48]">{{ $reply->user->name }}</span>
                                            <p class="text-gray-700">{{ $reply->comentario }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 italic">No comments yet. Be the first!</p>
            @endforelse
        </div>
    </section>

    {{-- Botón volver --}}
    <div class="mt-12 pt-6 border-t border-[#6B8E23]/20">
        <a href="{{ url('/guides') }}"
           class="px-4 py-2 bg-[#6B8E23] text-white rounded hover:bg-[#556b1c] transition inline-block">
            ← Back to Guides
        </a>
    </div>

</div>

@endsection

@section('scripts')
<script src="{{ asset('js/votes.js') }}"></script>
<script>
    // Pequeño JS para mostrar/ocultar formularios de respuesta
    function toggleReply(id) {
        const form = document.getElementById(`reply-form-${id}`);
        form.classList.toggle('hidden');
    }
</script>
@endsection