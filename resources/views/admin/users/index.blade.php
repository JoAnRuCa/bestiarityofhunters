@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-white py-12">
    <div class="w-[95%] max-w-7xl mx-auto">
        
        {{-- Barra de Búsqueda y Botón --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            {{-- Barra de Búsqueda --}}
            <form action="{{ route('admin.users.index') }}" method="GET" class="w-full md:w-96 flex">
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                        placeholder="Search hunter by name or email..." 
                        class="w-full bg-[#F4EBD0] border-2 border-[#6B8E23]/20 focus:border-[#6B8E23] outline-none px-4 py-3 rounded-xl text-sm italic font-medium shadow-inner">
                    <button type="submit" class="absolute right-3 top-3 text-[#6B8E23]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
                @if($search)
                    <a href="{{ route('admin.users.index') }}" class="ml-2 px-4 py-3 bg-red-100 text-red-600 rounded-xl flex items-center hover:bg-red-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </form>

            {{-- Botón para Añadir Usuario --}}
            <a href="{{ route('admin.users.create') }}" class="flex items-center gap-2 bg-[#6B8E23] text-[#F4EBD0] px-6 py-3 rounded-xl font-black uppercase text-xs tracking-widest hover:bg-[#2F2F2F] transition-all shadow-lg active:scale-95 w-full md:w-auto justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                </svg>
                New Hunter Registry
            </a>
        </div>

        {{-- Contenedor Principal --}}
        <div class="p-8 md:p-12 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20 text-[#2F2F2F]">
            
            <div class="flex flex-col md:flex-row justify-between items-center mb-10 pb-6 border-b-2 border-[#6B8E23]/10">
                <div>
                    <h1 class="text-3xl font-black uppercase italic tracking-tighter text-[#2F2F2F]">Hunter Registry</h1>
                    <p class="text-[#6B8E23] font-bold text-xs uppercase tracking-[0.3em] mt-1">Authorized Research Division Only</p>
                </div>
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
                            <th class="px-6 pb-4">Hunter Name</th>
                            <th class="px-6 pb-4">Email Address</th>
                            <th class="px-6 pb-4">Role</th>
                            <th class="px-6 pb-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr class="bg-white hover:shadow-md transition-all duration-200 group">
                            {{-- Nombre con Link y sin uppercase forzado --}}
                            <td class="px-6 py-4 rounded-l-2xl font-bold italic text-sm border-y-2 border-l-2 border-transparent group-hover:border-[#6B8E23]/20">
                                <a href="{{ route('admin.users.edit', $user) }}" class="text-[#2F2F2F] hover:text-[#6B8E23] transition-all block">
                                    {{ $user->name }}
                                </a>
                            </td>
                            
                            <td class="px-6 py-4 text-gray-500 text-sm border-y-2 border-transparent group-hover:border-[#6B8E23]/20">
                                {{ $user->email }}
                            </td>

                            <td class="px-6 py-4 border-y-2 border-transparent group-hover:border-[#6B8E23]/20">
                                <span class="text-[10px] font-black uppercase px-3 py-1 rounded-lg {{ $user->role === 'admin' ? 'bg-[#C67C48]/10 text-[#C67C48]' : 'bg-[#6B8E23]/10 text-[#6B8E23]' }}">
                                    {{ $user->role }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right rounded-r-2xl border-y-2 border-r-2 border-transparent group-hover:border-[#6B8E23]/20">
                                <div class="flex justify-end gap-3 items-center">
                                    {{-- Botón Editar --}}
                                    <a href="{{ route('admin.users.edit', $user) }}" class="text-[#2F2F2F] hover:text-[#6B8E23] transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                        </svg>
                                    </a>

                                    {{-- Botón Eliminar (Rojo Claro) --}}
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('¿Confirmar expulsión del gremio?')">
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
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Paginación si existe --}}
            @if(method_exists($users, 'links'))
                <div class="mt-6">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection