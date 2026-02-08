@extends('layouts.master')
@section('title', $guide->titulo)

@section('content')

<div class="w-[60%] max-w-4xl mx-auto mt-12 mb-12 p-8 bg-[#F4EBD0] rounded-lg shadow-lg">

    {{-- TÍTULO Y CONTENIDO --}}
    <div class="flex items-center justify-between mb-6 border-b pb-4 border-[#6B8E23]/20">
        <h1 class="text-4xl md:text-5xl font-extrabold text-[#6B8E23]">{{ $guide->titulo }}</h1>
        <x-vote-block :item="$guide" type="guide" />
    </div>

    <p class="text-gray-700 mb-4 text-sm">
        By <span class="font-bold text-[#C67C48]">{{ $guide->user->name }}</span> • {{ $guide->created_at->diffForHumans() }}
    </p>

    <div class="prose max-w-none text-gray-900 leading-relaxed mb-12 bg-white/30 p-4 rounded-md">
        {!! nl2br(e($guide->contenido)) !!}
    </div>

    <hr class="border-t border-[#6B8E23]/30 my-8">

    {{-- SECCIÓN DE COMENTARIOS --}}
    <section class="comments-area">
        <h2 class="text-2xl font-bold text-[#6B8E23] mb-6">Discussion</h2>

        @auth
            <form action="{{ route('comments.store') }}" method="POST" class="mb-12">
                @csrf
                <input type="hidden" name="item_id" value="{{ $guide->id }}">
                <input type="hidden" name="type" value="guide">
                <textarea name="comentario" rows="3" required class="w-full p-3 rounded border border-gray-300 bg-white/90 shadow-inner" placeholder="Share your hunting tips..."></textarea>
                <div class="flex justify-end mt-4"> 
                    <button type="submit" class="px-6 py-2 bg-[#6B8E23] text-white font-bold rounded-md hover:bg-[#556b1c] shadow-md uppercase text-sm">Post Comment</button>
                </div>
            </form>
        @endauth

        <div class="space-y-6">
            @foreach(($guide->comments ?? collect())->where('padre', null) as $comment)
                @include('layouts.partials.comment', ['comment' => $comment, 'guide' => $guide, 'level' => 0])
            @endforeach
        </div>
    </section>

    <div class="mt-12 pt-6 border-t border-[#6B8E23]/20">
        <a href="{{ url('/guides') }}" class="inline-flex items-center px-4 py-2 bg-[#6B8E23] text-white font-bold rounded hover:bg-[#556b1c] shadow-md uppercase text-xs">← Back to Guides</a>
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
                const textarea = form.querySelector('textarea');
                textarea.value = ''; // Forzamos que esté vacío al abrir
                textarea.focus();
            }
        }
    }
</script>
@endsection