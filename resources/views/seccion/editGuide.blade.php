@extends('layouts.master')
@section('title', 'Edit Guide: ' . $guide->titulo)

@section('content')

<div class="w-[80%] mx-auto mt-12 mb-12 p-8 rounded-lg shadow-lg bg-[#F4EBD0]">

    <h1 class="text-4xl md:text-5xl font-extrabold mb-6 border-b pb-4 text-[#6B8E23]">
        Edit Guide
    </h1>

    @if(session('success'))
        <p class="text-green-600 font-semibold mb-4">
            {{ session('success') }}
        </p>
    @endif

    {{-- 1. CAMBIO: Action dinámica según el rol del usuario --}}
    <form action="{{ Auth::user()->role === 'admin' ? route('admin.guides.update', $guide->slug) : route('guides.update', $guide->slug) }}" 
          method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- 2. AÑADIDO: Campo oculto para capturar la URL de origen --}}
        <input type="hidden" name="previous_url" value="{{ $previous_url ?? (Auth::user()->role === 'admin' ? url('/admin/guides') : route('my.guides')) }}">

        {{-- Título --}}
        <div>
            <label class="block font-semibold mb-1 text-[#2F2F2F]">Title</label>
            <input type="text"
                   name="titulo"
                   value="{{ old('titulo', $guide->titulo) }}"
                   class="w-full p-3 rounded border @error('titulo') border-red-500 @else border-slate-300 @enderror focus:ring-2 focus:ring-[#6B8E23]">
            @error('titulo')
                <p class="text-red-500 text-xs mt-1 font-bold italic">{{ $message }}</p>
            @enderror
        </div>

        {{-- COMPONENTE DE TAGS --}}
        <x-tag-selector :selectedTags="old('tags', $guide->tags->pluck('id')->toArray())" :showAll="true" />

        {{-- Contenido --}}
        <div>
            <label class="block font-semibold mb-1 text-[#2F2F2F]">Content</label>
            <textarea name="contenido"
                      rows="10"
                      class="w-full p-3 rounded border @error('contenido') border-red-500 @else border-slate-300 @enderror focus:ring-2 focus:ring-[#6B8E23]">{{ old('contenido', $guide->contenido) }}</textarea>
            @error('contenido')
                <p class="text-red-500 text-xs mt-1 font-bold italic">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="px-6 py-3 bg-[#6B8E23] text-white font-bold rounded-lg hover:bg-[#58751C] transition">
                Update Guide
            </button>
            
            {{-- 3. CAMBIO: El enlace de Cancelar ahora es inteligente y vuelve de donde viniste --}}
            <a href="{{ $previous_url ?? (Auth::user()->role === 'admin' ? url('/admin/guides') : route('my.guides')) }}" 
               class="text-gray-600 hover:underline font-medium">
                Cancel
            </a>
        </div>

    </form>
</div>
@endsection