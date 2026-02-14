@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-white py-12">
    <div class="w-[95%] max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8">
        
        {{-- Formulario de Creación (Lado Izquierdo) --}}
        <div class="md:col-span-1">
            <div class="p-6 rounded-3xl bg-[#F4EBD0] border border-[#6B8E23]/20 shadow-xl">
                <h2 class="text-xl font-black italic text-[#2F2F2F] mb-4">New Category</h2>
                <form action="{{ route('admin.tags.store') }}" method="POST">
                    @csrf
                    <input type="text" name="name" placeholder="E.g. Elder Dragon" required
                           {{-- Se quitó 'uppercase' de aquí --}}
                           class="w-full bg-white border-2 border-transparent focus:border-[#6B8E23] outline-none px-4 py-3 rounded-xl font-bold text-xs mb-4">
                    
                    <button type="submit" class="w-full bg-[#6B8E23] text-[#F4EBD0] py-3 rounded-xl font-black uppercase text-xs tracking-widest hover:bg-[#2F2F2F] transition-all">
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
                        @foreach($tags as $tag)
                        <tr class="bg-white group">
                            {{-- Se quitó 'uppercase' de aquí para que respete tu DB --}}
                            <td class="px-4 py-3 rounded-l-xl font-bold text-sm">{{ $tag->name }}</td>
                            
                            <td class="px-4 py-3 text-center text-xs text-gray-400">
                                {{ $tag->guides_count }} Guides
                            </td>
                            <td class="px-4 py-3 rounded-r-xl text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.tags.edit', $tag) }}" class="text-gray-400 hover:text-[#6B8E23] transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </a>
                                        <form action="{{ route('admin.tags.destroy', $tag) }}" 
                                            method="POST" 
                                            class="inline-block"
                                            {{-- Esta línea lanza la alerta de confirmación --}}
                                            onsubmit="return confirm('¿Estás seguro de que quieres eliminar la categoría «{{ $tag->name }}»? Esta acción no se puede deshacer.');">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection