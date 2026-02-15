@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-white py-12">
    <div class="w-[95%] max-w-5xl mx-auto">
        
        {{-- Bloque de Alertas (Flash Messages) --}}
        @if(session('success'))
            <div class="mb-8 p-4 bg-[#6B8E23]/20 border-l-4 border-[#6B8E23] text-[#2F2F2F] font-bold italic rounded-r-xl shadow-sm flex items-center animate-fade-in-down">
                <svg class="w-5 h-5 mr-3 text-[#6B8E23]" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-8 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 font-bold italic rounded-r-xl shadow-sm flex items-center">
                <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Formulario de Creación (Lado Izquierdo) --}}
            <div class="md:col-span-1">
                <div class="p-6 rounded-3xl bg-[#F4EBD0] border border-[#6B8E23]/20 shadow-xl">
                    <h2 class="text-xl font-black italic text-[#2F2F2F] mb-4">New Category</h2>
                    <form action="{{ route('admin.tags.store') }}" method="POST">
                        @csrf
                        <input type="text" name="name" placeholder="E.g. Elder Dragon" required
                               class="w-full bg-white border-2 border-transparent focus:border-[#6B8E23] outline-none px-4 py-3 rounded-xl font-bold text-xs mb-4 shadow-inner">
                        
                        <button type="submit" class="w-full bg-[#6B8E23] text-[#F4EBD0] py-3 rounded-xl font-black uppercase text-xs tracking-widest hover:bg-[#2F2F2F] transition-all transform active:scale-95">
                            Add Tag
                        </button>
                    </form>
                </div>
            </div>

            {{-- Listado de Tags (Lado Derecho) --}}
            <div class="md:col-span-2">
                <div class="p-8 rounded-3xl bg-[#F4EBD0] border border-[#6B8E23]/20 shadow-xl">
                    <table class="w-full text-left border-separate border-spacing-y-2">
                        <thead>
                            <tr class="text-[#6B8E23] uppercase text-[10px] font-black tracking-[0.2em]">
                                <th class="px-4 pb-2">Category Name</th>
                                <th class="px-4 pb-2 text-center">Usage</th>
                                <th class="px-4 pb-2 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tags as $tag)
                            <tr class="bg-white group hover:shadow-md transition-shadow">
                                <td class="px-4 py-3 rounded-l-xl font-bold text-sm text-[#2F2F2F]">{{ $tag->name }}</td>
                                
                                <td class="px-4 py-3 text-center text-xs text-gray-400 font-medium">
                                    <span class="bg-gray-100 px-2 py-1 rounded-md">
                                        {{ $tag->guides_count + $tag->builds_count }} Mentions
                                    </span>
                                </td>
                                <td class="px-4 py-3 rounded-r-xl text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.tags.edit', $tag) }}" class="text-gray-400 hover:text-[#6B8E23] transition-colors p-1" title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>
                                        <form action="{{ route('admin.tags.destroy', $tag) }}" 
                                              method="POST" 
                                              class="inline-block"
                                              onsubmit="return confirm('¿Estás seguro de que quieres eliminar la categoría «{{ $tag->name }}»?');">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-1" title="Delete">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-10 text-gray-400 italic">No tags found in the database.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection