@extends('layouts.master')

@section('title', 'My Profile')

@section('content')
<div class="w-[90%] md:w-[60%] max-w-4xl mx-auto mt-12 mb-12 p-8 bg-[#F4EBD0] rounded-lg shadow-lg">

    <h1 class="text-3xl font-extrabold mb-8 text-[#6B8E23] pb-4 uppercase tracking-tight">
        User Profile
    </h1>

    <div class="space-y-6">
        {{-- Fila: Nickname --}}
        <div class="flex items-center justify-between p-4 bg-white/50 rounded-md">
            <div>
                <p class="text-[10px] font-bold text-[#C67C48] uppercase tracking-widest">Nickname</p>
                <p class="text-lg font-semibold text-gray-800">{{ $user->name }}</p>
            </div>
            <button class="bg-[#C67C48] hover:bg-[#a1633a] text-white text-xs font-bold px-4 py-2 rounded transition-all shadow-sm uppercase">
                Change
            </button>
        </div>

        {{-- Fila: Email --}}
        <div class="flex items-center justify-between p-4 bg-white/50 rounded-md">
            <div>
                <p class="text-[10px] font-bold text-[#C67C48] uppercase tracking-widest">Email Address</p>
                <p class="text-lg font-semibold text-gray-800">{{ $user->email }}</p>
            </div>
            <button class="bg-[#C67C48] hover:bg-[#a1633a] text-white text-xs font-bold px-4 py-2 rounded transition-all shadow-sm uppercase">
                Change
            </button>
        </div>

        {{-- Fila: Password --}}
        <div class="flex items-center justify-between p-4 bg-white/50 rounded-md">
            <div>
                <p class="text-[10px] font-bold text-[#C67C48] uppercase tracking-widest">Password</p>
                <p class="text-lg font-semibold text-gray-800 tracking-tighter">••••••••••••</p>
            </div>
            <button class="bg-[#6B8E23] hover:bg-[#556b1c] text-white text-xs font-bold px-4 py-2 rounded transition-all shadow-sm uppercase">
                Update Password
            </button>
        </div>
    </div>

    {{-- Información adicional --}}
    <div class="mt-10 pt-6 text-[11px] text-gray-500 flex justify-between tracking-tighter">
        <span>Account created: {{ $user->created_at->format('d M, Y') }}</span>
        <span>Role: {{ $user->role ?? 'Hunter' }}</span>
    </div>
</div>
@endsection
