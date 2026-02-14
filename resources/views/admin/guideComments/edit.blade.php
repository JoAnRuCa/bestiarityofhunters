@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-white py-12">
    <div class="w-[95%] max-w-3xl mx-auto">
        
        {{-- Enlace para volver --}}
        <div class="mb-6">
            <a href="{{ route('admin.guideComments.index') }}" class="text-[#6B8E23] font-black uppercase text-xs tracking-widest hover:text-[#2F2F2F] transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Echoes
            </a>
        </div>

        {{-- Formulario de Edición --}}
        <form action="{{ route('admin.guideComments.update', $comment->id) }}" method="POST" class="p-8 md:p-12 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20 text-[#2F2F2F]">
            @csrf
            @method('PUT')

            <div class="mb-10 pb-6 border-b-2 border-[#6B8E23]/10">
                <h1 class="text-3xl font-black uppercase italic tracking-tighter text-[#2F2F2F]">
                    Edit <span class="text-[#6B8E23]">Hunter Echo</span>
                </h1>
                <p class="text-[#6B8E23] font-bold text-xs uppercase tracking-[0.3em] mt-1">Modifying record #{{ $comment->id }}</p>
            </div>

            {{-- Ficha de Contexto (Solo lectura) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white/50 p-4 rounded-2xl border border-[#6B8E23]/10">
                    <label class="text-[10px] uppercase font-black text-[#6B8E23] tracking-widest block mb-1">Author</label>
                    <p class="font-bold text-[#2F2F2F]">{{ $comment->user->name }}</p>
                </div>
                <div class="bg-white/50 p-4 rounded-2xl border border-[#6B8E23]/10">
                    <label class="text-[10px] uppercase font-black text-[#6B8E23] tracking-widest block mb-1">Origin Guide</label>
                    <p class="font-bold text-[#2F2F2F] italic">{{ $comment->guide->titulo ?? 'Archive lost' }}</p>
                </div>
            </div>

            {{-- Campo de Edición --}}
            <div class="mb-8">
                <label for="comentario" class="text-[10px] uppercase font-black text-[#6B8E23] tracking-widest mb-2 block ml-1">Commentary Content</label>
                <textarea 
                    name="comentario" 
                    id="comentario" 
                    rows="6" 
                    class="w-full bg-white border-2 border-[#6B8E23]/30 rounded-2xl py-4 px-6 font-medium text-[#2F2F2F] outline-none focus:border-[#6B8E23] transition-all resize-none shadow-inner"
                    placeholder="Escribe el nuevo contenido del mensaje..."
                >{{ old('comentario', $comment->comentario) }}</textarea>
                
                @error('comentario')
                    <p class="text-red-500 text-xs font-bold mt-2 uppercase italic tracking-widest">{{ $message }}</p>
                @enderror
            </div>

            {{-- Botón de Acción --}}
            <div class="flex justify-end">
                <button type="submit" 
                    class="bg-[#6B8E23] hover:bg-[#C67C48] text-white px-10 py-4 rounded-2xl font-black uppercase shadow-[0_5px_0_0_#4A6318] hover:shadow-[0_5px_0_0_#A05E31] transition-all duration-300 text-sm tracking-widest">
                    Update Scroll
                </button>
            </div>
        </form>
    </div>
</div>
@endsection