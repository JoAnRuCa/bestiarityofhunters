@extends('layouts.master')
@section('title', $guide->titulo)

@section('content')
<div class="w-[60%] max-w-4xl mx-auto mt-12 mb-12 p-8 bg-[#F4EBD0] rounded-lg shadow-lg">
    {{-- Contenido de la guía --}}
    <div class="flex items-center justify-between mb-6 border-b pb-4 border-[#6B8E23]/20">
        <h1 class="text-4xl md:text-5xl font-extrabold text-[#6B8E23]">{{ $guide->titulo }}</h1>
        <x-vote-block :item="$guide" type="guide" />
    </div>

    <section class="comments-area">
        <h2 class="text-2xl font-bold text-[#6B8E23] mb-6">Discussion</h2>

        @auth
            {{-- Formulario Principal --}}
            <form action="{{ route('comments.store') }}" method="POST" onsubmit="return enviarComentario(event, this)" class="mb-12">
                @csrf
                <input type="hidden" name="item_id" value="{{ $guide->id }}">
                <input type="hidden" name="type" value="guide">
                <textarea name="comentario" rows="3" required class="w-full p-3 rounded border border-gray-300 bg-white/90 shadow-inner" placeholder="Share your hunting tips..."></textarea>
                <div class="flex justify-end mt-4"> 
                    <button type="submit" class="px-6 py-2 bg-[#6B8E23] text-white font-bold rounded-md hover:bg-[#556b1c] shadow-md uppercase text-sm">Post Comment</button>
                </div>
            </form>
        @endauth

        <div id="comments-wrapper" class="space-y-6">
            @foreach(($guide->comments ?? collect())->where('padre', null) as $comment)
                @include('layouts.partials.comment', ['comment' => $comment, 'guide' => $guide, 'level' => 0])
            @endforeach
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/votes.js') }}"></script>
<script>
    // Función para mostrar/ocultar respuestas
    function toggleChildren(btn) {
        const container = btn.closest('.flex-1').querySelector('.replies-container');
        const icon = btn.querySelector('.icon');
        const isHidden = container.classList.contains('hidden');

        if (isHidden) {
            container.classList.remove('hidden');
            btn.innerHTML = `<span class="icon">▼</span> Hide Replies`;
        } else {
            container.classList.add('hidden');
            // Intentamos recuperar el conteo si es posible o simplemente Reset
            btn.innerHTML = `<span class="icon">▶</span> Show Replies`;
        }
    }

    // Actualización de enviarComentario
    async function enviarComentario(e, form) {
        e.preventDefault();
        e.stopPropagation();

        const formData = new FormData(form);
        const isReply = form.id.includes('reply-form-');
        
        if(isReply) {
            const node = form.closest('.comment-node');
            const currentLevel = parseInt(node.getAttribute('data-level')) || 0;
            formData.append('level', currentLevel + 1);
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await response.json();

            if (data.success) {
                const wrapper = document.createElement('div');
                wrapper.innerHTML = data.comment_html.trim();
                const newElement = wrapper.firstElementChild;

                if (isReply) {
                    const parentBody = form.closest('.flex-1');
                    const container = parentBody.querySelector('.replies-container');
                    
                    container.appendChild(newElement);
                    container.classList.remove('hidden'); // Mostramos automáticamente al responder
                    form.classList.add('hidden');
                    
                    // Si el botón de "Show Replies" no existía (era la primera respuesta), podrías crearlo aquí o simplemente mostrar el contenedor
                } else {
                    document.getElementById('comments-wrapper').prepend(newElement);
                }
                
                form.reset();
            }
        } catch (err) {
            console.error("Error:", err);
        }
        return false;
    }

    // ... mantener toggleReply
</script>
@endsection