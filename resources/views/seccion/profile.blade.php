@extends('layouts.master')

@section('title', 'My Profile')

@section('content')
{{-- ELEMENTO PUENTE PARA EL JS (Invisible) --}}
<div id="userData" 
     class="hidden" 
     data-name="{{ $user->name }}" 
     data-email="{{ $user->email }}" 
     data-profile-url="{{ route('profile') }}">
</div>

<div class="w-[90%] md:w-[60%] max-w-4xl mx-auto mt-12 mb-12 p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
    {{-- Título y mensajes (Mantén tu código actual aquí) --}}
    <h1 class="text-3xl font-extrabold mb-8 text-[#6B8E23] border-b-2 border-[#6B8E23]/20 pb-4 uppercase tracking-tight">User Profile</h1>

    @if(session('success'))
        <div class="mb-6 p-4 bg-[#6B8E23]/15 text-[#6B8E23] text-xs font-bold rounded uppercase tracking-wider">
             ✓ {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 text-red-700 text-xs font-bold rounded uppercase tracking-wider">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Filas de datos (Nickname, Email, Password) --}}
    <div class="space-y-6">
        <div class="flex items-center justify-between p-4 bg-white/40 rounded-md shadow-sm">
            <div>
                <p class="text-[10px] font-bold text-[#C67C48] uppercase tracking-widest">Nickname</p>
                <p class="text-lg font-semibold text-gray-800">{{ $user->name }}</p>
            </div>
            <button onclick="openModal('name')" class="bg-[#C67C48] hover:bg-[#a1633a] text-white text-xs font-bold px-4 py-2 rounded transition-all uppercase shadow-sm">Change</button>
        </div>

        <div class="flex items-center justify-between p-4 bg-white/40 rounded-md shadow-sm">
            <div>
                <p class="text-[10px] font-bold text-[#C67C48] uppercase tracking-widest">Email Address</p>
                <p class="text-lg font-semibold text-gray-800">{{ $user->email }}</p>
            </div>
            <button onclick="openModal('email')" class="bg-[#C67C48] hover:bg-[#a1633a] text-white text-xs font-bold px-4 py-2 rounded transition-all uppercase shadow-sm">Change</button>
        </div>

        <div class="flex items-center justify-between p-4 bg-white/40 rounded-md shadow-sm">
            <div>
                <p class="text-[10px] font-bold text-[#C67C48] uppercase tracking-widest">Password</p>
                <p class="text-lg font-semibold text-gray-800 tracking-widest">••••••••••••</p>
            </div>
            <button onclick="openModal('password')" class="bg-[#6B8E23] hover:bg-[#556b1c] text-white text-xs font-bold px-4 py-2 rounded transition-all uppercase shadow-sm">Update Password</button>
        </div>
    </div>
</div>

{{-- MODAL OVERLAY --}}
<div id="modalOverlay" 
     data-has-errors="{{ $errors->any() ? 'true' : 'false' }}"
     class="fixed inset-0 bg-black/60 z-50 {{ $errors->any() ? '' : 'hidden' }} flex items-center justify-center backdrop-blur-sm">
    <div class="bg-[#F4EBD0] p-6 rounded-lg shadow-2xl w-[90%] max-w-md border-2 border-[#C67C48]/30">
        
        <h2 id="modalTitle" class="text-xl font-bold text-[#C67C48] uppercase mb-4">
            {{ old('type') === 'password' ? 'Update Password' : (old('type') === 'email' ? 'Update Email' : 'Update Nickname') }}
        </h2>
        
        @if($errors->any())
            <div class="mb-4 p-4 bg-red-100/50 text-red-700 text-[11px] font-bold rounded uppercase tracking-tight">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" id="modalType" value="{{ old('type') }}">
            <div id="modalInputContainer" class="space-y-4">
                @if(old('type') === 'name')
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full p-2 border-2 border-[#C67C48]/20 rounded bg-white">
                @elseif(old('type') === 'email')
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full p-2 border-2 border-[#C67C48]/20 rounded bg-white">
                @elseif(old('type') === 'password')
                    <input type="password" name="current_password" placeholder="Current Password" class="w-full p-2 border-2 border-[#C67C48]/20 rounded bg-white">
                    <input type="password" name="new_password" placeholder="New Password" class="w-full p-2 mt-3 border-2 border-[#C67C48]/20 rounded bg-white">
                @endif
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <button type="button" onclick="closeModal()" class="text-gray-500 text-xs font-bold uppercase hover:underline cursor-pointer">Cancel</button>
                <button type="submit" class="bg-[#6B8E23] hover:bg-[#556b1c] text-white px-6 py-2 rounded text-xs font-bold uppercase shadow-md transition-all">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/updateProfile.js') }}"></script>
@endsection