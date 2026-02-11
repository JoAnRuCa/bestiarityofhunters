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

    <form action="{{ route('guides.update', $guide->id) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        {{-- Título --}}
        <div>
            <label class="block font-semibold mb-1 text-[#2F2F2F]">Title</label>
            <input type="text"
                   name="titulo"
                   value="{{ old('titulo', $guide->titulo) }}"
                   class="w-full p-3 rounded border border-slate-300 focus:ring-2 focus:ring-[#6B8E23]"
                   required>
        </div>

        {{-- COMPONENTE DE TAGS --}}
        {{-- 1. Pasamos old('tags') o los tags actuales de la guía --}}
        {{-- 2. Añadimos :showAll="true" para ver el listado completo (Armas + Categorías) --}}
        <x-tag-selector :selectedTags="old('tags', $guide->tags->pluck('id')->toArray())" :showAll="true" />

        {{-- Contenido --}}
        <div>
            <label class="block font-semibold mb-1 text-[#2F2F2F]">Content</label>
            <textarea name="contenido"
                      rows="10"
                      class="w-full p-3 rounded border border-slate-300 focus:ring-2 focus:ring-[#6B8E23]"
                      required>{{ old('contenido', $guide->contenido) }}</textarea>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                    class="px-6 py-3 bg-[#6B8E23] text-white font-bold rounded-lg hover:bg-[#58751C] transition">
                Update Guide
            </button>
            
            <a href="{{ route('my.guides') }}" class="text-gray-600 hover:underline font-medium">
                Cancel
            </a>
        </div>

    </form>
</div>
@endsection