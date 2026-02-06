@extends('layouts.master')
@section('title', 'Guide Editor')

@section('content')

<div class="max-w-4xl mx-auto mt-12 mb-12 p-8 rounded-lg shadow-sm"
     style="background-color: #F4EBD0;">

    <h1 class="text-4xl md:text-5xl font-extrabold mb-6 border-b pb-4 text-[#6B8E23]">
        Create Guide
    </h1>

    {{-- Mensaje de éxito --}}
    @if(session('success'))
        <p class="text-green-600 font-semibold mb-4">
            {{ session('success') }}
        </p>
    @endif

    {{-- Formulario --}}
    <form action="{{ route('guide.editor.store') }}" method="POST" class="space-y-8">
        @csrf

        {{-- Título --}}
        <div>
            <label class="block font-semibold mb-1">Title</label>
            <input type="text"
                   name="titulo"
                   value="{{ old('titulo') }}"
                   class="w-full p-3 rounded border border-slate-300"
                   required>
        </div>

                {{-- Tags --}}
        <div>
            <label class="block font-semibold mb-2">Tags</label>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                @foreach($tags as $tag)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox"
                               name="tags[]"
                               value="{{ $tag->id }}"
                               class="h-4 w-4 text-[#6B8E23] border-gray-300 rounded">
                        <span>{{ $tag->name }}</span>
                    </label>
                @endforeach
            </div>
        </div>
        

        {{-- Contenido --}}
        <div>
            <label class="block font-semibold mb-1">Content</label>
            <textarea name="contenido"
                      rows="10"
                      class="w-full p-3 rounded border border-slate-300"
                      required>{{ old('contenido') }}</textarea>
        </div>



        {{-- Botón --}}
        <button type="submit"
                class="px-6 py-3 bg-[#6B8E23] text-white font-bold rounded-lg hover:bg-[#58751C]">
            Create Guide
        </button>

    </form>

</div>

@endsection
