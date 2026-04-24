@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-white py-12">
    <div class="max-w-5xl mx-auto p-8 md:p-12 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20 text-[#2F2F2F]">
        
        <div class="flex flex-col md:flex-row justify-between items-start mb-10 border-b-2 border-[#6B8E23]/10 pb-6 gap-4">
            <div>
                <h1 class="text-3xl font-black uppercase italic tracking-tighter text-[#2F2F2F]">Edit Hunting Guide</h1>
                <p class="text-[#6B8E23] font-bold text-xs uppercase tracking-[0.3em] mt-2">Updating: {{ $guide->titulo }}</p>
            </div>
            
            {{-- Botón rápido para eliminar desde el edit --}}
            <form action="{{ route('admin.guides.destroy', $guide) }}" method="POST" 
                  onsubmit="return confirm('¿Seguro que quieres eliminar permanentemente esta guía de los archivos?')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-400 hover:text-red-600 text-[10px] font-black uppercase tracking-widest border-2 border-red-400/20 px-4 py-2 rounded-xl transition-all">
                    Discard Archive
                </button>
            </form>
        </div>

        <form action="{{ route('admin.guides.update', $guide) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            {{-- Título --}}
            <div class="bg-white/30 p-6 rounded-2xl border border-[#6B8E23]/5">
                <label class="block text-xs font-black uppercase tracking-widest text-[#2F2F2F]/70 mb-3 ml-1">Guide Title</label>
                <input type="text" name="titulo" value="{{ old('titulo', $guide->titulo) }}" 
                       class="w-full bg-white border-2 @error('titulo') border-red-500 @else border-transparent @enderror focus:border-[#6B8E23] focus:ring-0 outline-none px-4 py-4 rounded-xl transition-all font-bold italic text-lg text-[#2F2F2F] shadow-sm">
                @error('titulo') <span class="text-red-600 text-xs mt-2 block font-bold">{{ $message }}</span> @enderror
            </div>

            {{-- Componente de Tags --}}
            <div class="bg-white/30 p-6 rounded-2xl border border-[#6B8E23]/5">
                <x-tag-selector :selectedTags="$guide->tags" :showAll="true" />
            </div>

            {{-- Contenido --}}
            <div class="bg-white/30 p-6 rounded-2xl border border-[#6B8E23]/5">
                <label class="block text-xs font-black uppercase tracking-widest text-[#2F2F2F]/70 mb-3 ml-1">Strategy & Hunting Notes</label>
                <textarea name="contenido" rows="15" 
                          class="w-full bg-white border-2 @error('contenido') border-red-500 @else border-transparent @enderror focus:border-[#6B8E23] focus:ring-0 outline-none px-6 py-5 rounded-2xl transition-all text-[#2F2F2F] leading-relaxed shadow-sm resize-none">{{ old('contenido', $guide->contenido) }}</textarea>
                @error('contenido') <span class="text-red-600 text-xs mt-2 block font-bold">{{ $message }}</span> @enderror
            </div>

            {{-- Botones de acción --}}
            <div class="flex flex-col sm:flex-row items-center gap-6 pt-6">
                <button type="submit" 
                        class="min-w-[240px] bg-[#6B8E23] text-[#F4EBD0] px-16 py-5 rounded-2xl font-black uppercase text-sm tracking-[0.25em] hover:bg-[#2F2F2F] transition-all shadow-xl active:scale-95 border-b-4 border-[#4F6B1A] hover:border-[#1A1A1A]">
                    Save Changes
                </button>
                
                <a href="{{ route('admin.guides.index') }}" 
                class="text-[#2F2F2F]/50 hover:text-black text-xs font-black uppercase tracking-widest transition-colors px-4 py-2">
                    Cancel and Return
                </a>
            </div>
        </form>
    </div>
</div>
@endsection