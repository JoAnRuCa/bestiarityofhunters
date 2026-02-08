@extends('layouts.master')
@section('title', 'Guide Editor')

@section('content')

<div class="w-[80%] mx-auto mt-12 mb-12 p-8 rounded-lg shadow-lg bg-[#F4EBD0]">

    <h1 class="text-4xl md:text-5xl font-extrabold mb-6 border-b pb-4 text-[#6B8E23]">
        Create Guide
    </h1>

    @if(session('success'))
        <p class="text-green-600 font-semibold mb-4">
            {{ session('success') }}
        </p>
    @endif

    <form action="{{ route('guide.editor.store') }}" method="POST" class="space-y-8">
        @csrf

        {{-- Título --}}
        <div>
            <label class="block font-semibold mb-1">Title</label>
            <input type="text"
                   name="titulo"
                   value="{{ old('titulo') }}"
                   class="w-full p-3 rounded border border-slate-300 focus:ring-2 focus:ring-[#6B8E23]"
                   required>
        </div>

        {{-- COMPONENTE DE TAGS EXTERNALIZADO --}}
        {{-- Pasamos old('tags') para que si hay un error, no se borren los seleccionados --}}
        <x-tag-selector :selectedTags="old('tags', [])" />

        {{-- Contenido --}}
        <div>
            <label class="block font-semibold mb-1">Content</label>
            <textarea name="contenido"
                      rows="10"
                      class="w-full p-3 rounded border border-slate-300 focus:ring-2 focus:ring-[#6B8E23]"
                      required>{{ old('contenido') }}</textarea>
        </div>

        <button type="submit"
                class="px-6 py-3 bg-[#6B8E23] text-white font-bold rounded-lg hover:bg-[#58751C] transition">
            Create Guide
        </button>

    </form>
</div>
@endsection