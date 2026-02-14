@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-white py-12">
    <div class="w-[95%] max-w-7xl mx-auto">
        
        {{-- Barra de Búsqueda y Botón Superior --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <form action="{{ route('admin.builds.index') }}" method="GET" class="w-full md:w-96 flex">
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                        placeholder="Search by title, playstyle or author..." 
                        class="w-full bg-[#F4EBD0] border-2 border-[#6B8E23]/20 focus:border-[#6B8E23] outline-none px-4 py-3 rounded-xl text-sm italic font-medium shadow-inner">
                    <button type="submit" class="absolute right-3 top-3 text-[#6B8E23]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
                @if($search)
                    <a href="{{ route('admin.builds.index') }}" class="ml-2 px-4 py-3 bg-red-100 text-red-600 rounded-xl flex items-center hover:bg-red-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </form>

            <a href="{{ route('admin.builds.create') }}" class="flex items-center gap-2 bg-[#C67C48] text-[#F4EBD0] px-6 py-3 rounded-xl font-black uppercase text-xs tracking-widest hover:bg-[#2F2F2F] transition-all shadow-lg w-full md:w-auto justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                Forge New Build
            </a>
        </div>

        {{-- Contenedor de la Tabla --}}
        <div class="p-8 md:p-12 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20 text-[#2F2F2F]">
            
            <div class="mb-10 pb-6 border-b-2 border-[#6B8E23]/10">
                <h1 class="text-3xl font-black uppercase italic tracking-tighter text-[#2F2F2F]">Armory Archives</h1>
                <p class="text-[#6B8E23] font-bold text-xs uppercase tracking-[0.3em] mt-1">Master Rank Equipment & Blueprint Records</p>
            </div>

            @if(session('success'))
                <div class="mb-6 p-4 bg-[#6B8E23]/20 border-l-4 border-[#6B8E23] text-[#2F2F2F] font-bold italic rounded-r-xl">
                    {{ session('success') }}
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="w-full text-left border-separate border-spacing-y-3">
                    <thead>
                        <tr class="text-[#6B8E23] uppercase text-xs font-black tracking-widest">
                            <th class="px-6 pb-4">Build Title</th>
                            <th class="px-6 pb-4">Author</th>
                            <th class="px-6 pb-4">Equipment Tags</th>
                            <th class="px-6 pb-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($builds as $build)
                        <tr class="bg-white hover:shadow-md transition-all duration-200 group">
                            {{-- Título y Playstyle --}}
                            <td class="px-6 py-4 rounded-l-2xl font-bold italic text-sm border-y-2 border-l-2 border-transparent group-hover:border-[#6B8E23]/20">
                                {{ $build->titulo }}
                                <div class="text-[10px] text-gray-400 font-normal normal-case italic line-clamp-1 max-w-xs">{{ $build->playstyle }}</div>
                            </td>

                            {{-- Autor --}}
                            <td class="px-6 py-4 text-sm border-y-2 border-transparent group-hover:border-[#6B8E23]/20">
                                <span class="font-bold text-[#2F2F2F]">{{ $build->user->name ?? 'Unknown Hunter' }}</span>
                            </td>

                            {{-- Etiquetas --}}
                            <td class="px-6 py-4 border-y-2 border-transparent group-hover:border-[#6B8E23]/20">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($build->tags as $tag)
                                        <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded bg-[#6B8E23]/10 text-[#6B8E23] border border-[#6B8E23]/20">
                                            {{ $tag->name }}
                                        </span>
                                    @empty
                                        <span class="text-[9px] text-gray-400 italic">No equipment tags</span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- Acciones --}}
                            <td class="px-6 py-4 text-right rounded-r-2xl border-y-2 border-r-2 border-transparent group-hover:border-[#6B8E23]/20">
                                <div class="flex justify-end gap-3">
                                    {{-- Editar: Usamos explicitamente el ID --}}
                                    <a href="{{ route('admin.builds.edit', $build->id) }}" class="text-[#2F2F2F] hover:text-[#6B8E23] transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </a>

                                    {{-- Borrar: Apuntando al ID exacto --}}
                                    <form action="{{ route('admin.builds.destroy', $build->id) }}" method="POST" onsubmit="return confirm('¿Desmantelar este set de equipo permanentemente?')">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="text-red-300 hover:text-red-600 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-10 text-center text-gray-400 italic">
                                No armor sets found in the armory for your search.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Paginación (si decides usarla) --}}
            @if(method_exists($builds, 'links'))
                <div class="mt-6">
                    {{ $builds->appends(['search' => $search])->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection