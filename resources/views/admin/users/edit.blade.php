@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-white py-12">
    {{-- Contenedor Crema (Pergamino) --}}
    <div class="max-w-3xl mx-auto p-8 md:p-12 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20 text-[#2F2F2F]">
        
        <div class="mb-10 border-b-2 border-[#6B8E23]/10 pb-6">
            <h1 class="text-3xl font-black uppercase italic tracking-tighter text-[#2F2F2F]">Edit Hunter</h1>
            <p class="text-[#6B8E23] font-bold text-xs tracking-[0.3em] mt-2">Modifying Registry: {{ $user->name }}</p>
        </div>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Nombre --}}
            <div>
                <label class="block text-xs font-black tracking-widest text-[#2F2F2F]/70 mb-2 ml-1">Hunter Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                       class="w-full bg-white border-2 @error('name') border-red-500 @else border-transparent @enderror focus:border-[#6B8E23] focus:ring-0 outline-none px-4 py-3 rounded-xl transition-all font-bold italic text-[#2F2F2F] shadow-sm">
                @error('name') <span class="text-red-600 text-xs mt-1 font-bold ml-1">{{ $message }}</span> @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-[#2F2F2F]/70 mb-2 ml-1">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                       class="w-full bg-white border-2 @error('email') border-red-500 @else border-transparent @enderror focus:border-[#6B8E23] focus:ring-0 outline-none px-4 py-3 rounded-xl transition-all text-[#2F2F2F] shadow-sm">
                @error('email') <span class="text-red-600 text-xs mt-1 font-bold ml-1">{{ $message }}</span> @enderror
            </div>

            <hr class="border-[#6B8E23]/10 my-8">

            {{-- Password --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-[#2F2F2F]/70 mb-2 ml-1">New Password (Optional)</label>
                    <input type="password" name="password" 
                           class="w-full bg-white border-2 @error('password') border-red-500 @else border-transparent @enderror focus:border-[#C67C48] focus:ring-0 outline-none px-4 py-3 rounded-xl transition-all text-[#2F2F2F] shadow-sm">
                    @error('password') <span class="text-red-600 text-xs mt-1 font-bold ml-1">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-[#2F2F2F]/70 mb-2 ml-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" 
                           class="w-full bg-white border-2 border-transparent focus:border-[#C67C48] focus:ring-0 outline-none px-4 py-3 rounded-xl transition-all text-[#2F2F2F] shadow-sm">
                </div>
            </div>
            <p class="text-[10px] text-[#6B8E23] font-bold italic mt-2 ml-1">Leave blank to keep current password.</p>

            {{-- Botones --}}
            <div class="flex items-center gap-6 pt-10">
                <button type="submit" 
                        class="bg-[#6B8E23] text-[#F4EBD0] px-10 py-4 rounded-xl font-black uppercase text-xs tracking-[0.2em] hover:bg-[#2F2F2F] transition-all shadow-lg active:transform active:scale-95">
                    Update Registry
                </button>
                <a href="{{ route('admin.users.index') }}" class="text-[#2F2F2F]/50 hover:text-black text-xs font-black uppercase tracking-widest transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection