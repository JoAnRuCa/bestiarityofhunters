@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-white py-12">
    <div class="max-w-5xl mx-auto p-8 md:p-12 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20 text-[#2F2F2F]">
        
        <div class="mb-10 border-b-2 border-[#6B8E23]/10 pb-6">
            <h1 class="text-3xl font-black uppercase italic tracking-tighter text-[#2F2F2F]">New Hunting Archive</h1>
            <p class="text-[#6B8E23] font-bold text-xs uppercase tracking-[0.3em] mt-2">Create a new entry for the Gremio Records</p>
        </div>

        <form action="{{ route('admin.guides.store') }}" method="POST" class="space-y-8">
            @csrf

            {{-- Título --}}
            <div class="bg-white/30 p-6 rounded-2xl border border-[#6B8E23]/5">
                <label class="block text-xs font-black uppercase tracking-widest text-[#2F2F2F]/70 mb-3 ml-1">Guide Title</label>
                <input type="text" name="titulo" value="{{ old('titulo') }}" placeholder="E.g. Rathalos Hunting Guide" required
                       class="w-full bg-white border-2 border-transparent focus:border-[#6B8E23] focus:ring-0 outline-none px-4 py-4 rounded-xl transition-all font-bold italic text-lg text-[#2F2F2F] shadow-sm">
                @error('titulo') <span class="text-red-600 text-xs mt-2 block font-bold">{{ $message }}</span> @enderror
            </div>

            {{-- Componente de Tags --}}
            <div class="bg-white/30 p-6 rounded-2xl border border-[#6B8E23]/5">
                {{-- Enviamos un array vacío ya que es una guía nueva --}}
                <x-tag-selector :selectedTags="[]" :showAll="true" />
            </div>

            {{-- Contenido --}}
            <div class="bg-white/30 p-6 rounded-2xl border border-[#6B8E23]/5">
                <label class="block text-xs font-black uppercase tracking-widest text-[#2F2F2F]/70 mb-3 ml-1">Strategy & Hunting Notes</label>
                <textarea name="contenido" rows="15" placeholder="Write the weak points, elemental effectiveness, and combat tips..." required
                          class="w-full bg-white border-2 border-transparent focus:border-[#6B8E23] focus:ring-0 outline-none px-6 py-5 rounded-2xl transition-all text-[#2F2F2F] leading-relaxed shadow-sm resize-none">{{ old('contenido') }}</textarea>
                @error('contenido') <span class="text-red-600 text-xs mt-2 block font-bold">{{ $message }}</span> @enderror
            </div>

            {{-- Botones de acción --}}
            <div class="flex flex-col sm:flex-row items-center gap-6 pt-6">
                <button type="submit" 
                        class="min-w-[240px] bg-[#6B8E23] text-[#F4EBD0] px-16 py-5 rounded-2xl font-black uppercase text-sm tracking-[0.25em] hover:bg-[#2F2F2F] transition-all shadow-xl active:scale-95 border-b-4 border-[#4F6B1A] hover:border-[#1A1A1A]">
                    Publish Guide
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