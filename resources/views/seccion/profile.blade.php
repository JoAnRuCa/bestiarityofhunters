@extends('layouts.master')

@section('title', 'My Profile')

@section('content')
<div class="w-[90%] md:w-[60%] max-w-4xl mx-auto mt-12 mb-12 p-8 bg-[#F4EBD0] rounded-lg shadow-lg border border-[#6B8E23]/10">
    
    <h1 class="text-3xl font-extrabold mb-8 text-[#6B8E23] border-b-2 border-[#6B8E23]/20 pb-4 uppercase">User Profile</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-[#6B8E23]/20 border border-[#6B8E23] text-[#6B8E23] text-xs font-bold rounded uppercase">
            {{ session('success') }}
        </div>
    @endif

    <div class="space-y-6">
        {{-- Nickname --}}
        <div class="flex items-center justify-between p-4 bg-white/40 rounded-md shadow-sm">
            <div>
                <p class="text-[10px] font-bold text-[#C67C48] uppercase tracking-widest">Nickname</p>
                <p class="text-lg font-semibold text-gray-800">{{ $user->name }}</p>
            </div>
            <button onclick="openModal('name')" class="bg-[#C67C48] hover:bg-[#a1633a] text-white text-xs font-bold px-4 py-2 rounded transition-all uppercase">Change</button>
        </div>

        {{-- Email --}}
        <div class="flex items-center justify-between p-4 bg-white/40 rounded-md shadow-sm">
            <div>
                <p class="text-[10px] font-bold text-[#C67C48] uppercase tracking-widest">Email Address</p>
                <p class="text-lg font-semibold text-gray-800">{{ $user->email }}</p>
            </div>
            <button onclick="openModal('email')" class="bg-[#C67C48] hover:bg-[#a1633a] text-white text-xs font-bold px-4 py-2 rounded transition-all uppercase">Change</button>
        </div>

        {{-- Password --}}
        <div class="flex items-center justify-between p-4 bg-white/40 rounded-md shadow-sm">
            <div>
                <p class="text-[10px] font-bold text-[#C67C48] uppercase tracking-widest">Password</p>
                <p class="text-lg font-semibold text-gray-800 tracking-widest">••••••••••••</p>
            </div>
            <button onclick="openModal('password')" class="bg-[#6B8E23] hover:bg-[#556b1c] text-white text-xs font-bold px-4 py-2 rounded transition-all uppercase">Update Password</button>
        </div>
    </div>

    {{-- Info adicional --}}
    <div class="mt-10 pt-6 text-[11px] text-gray-500 flex justify-between uppercase tracking-tighter italic">
        <span>Account created: {{ $user->created_at->format('d M, Y') }}</span>
        <span>Role: {{ $user->role ?? 'Hunter' }}</span>
    </div>
</div>

<div id="modalOverlay" class="fixed inset-0 bg-black/60 z-50 hidden flex items-center justify-center backdrop-blur-sm">
    <div class="bg-[#F4EBD0] p-6 rounded-lg shadow-2xl w-[90%] max-w-md border-2 border-[#C67C48]/30">
        <h2 id="modalTitle" class="text-xl font-bold text-[#C67C48] uppercase mb-4">Update Info</h2>
        
        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="type" id="modalType">
            
            <div id="modalInputContainer">
                {{-- Aquí se inyecta el input vía JS --}}
            </div>

            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="closeModal()" class="text-gray-500 text-xs font-bold uppercase hover:underline">Cancel</button>
                <button type="submit" class="bg-[#6B8E23] text-white px-6 py-2 rounded text-xs font-bold uppercase shadow-md">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal(type) {
    const modal = document.getElementById('modalOverlay');
    const title = document.getElementById('modalTitle');
    const typeInput = document.getElementById('modalType');
    const container = document.getElementById('modalInputContainer');
    
    modal.classList.remove('hidden');
    typeInput.value = type;
    container.innerHTML = '';

    if(type === 'name') {
        title.innerText = 'Update Nickname';
        container.innerHTML = `<input type="text" name="name" value="{{ $user->name }}" class="w-full p-2 border-2 border-[#C67C48]/20 rounded bg-white outline-none focus:border-[#6B8E23]" required>`;
    } else if(type === 'email') {
        title.innerText = 'Update Email';
        container.innerHTML = `<input type="email" name="email" value="{{ $user->email }}" class="w-full p-2 border-2 border-[#C67C48]/20 rounded bg-white outline-none focus:border-[#6B8E23]" required>`;
    } else if(type === 'password') {
        title.innerText = 'Update Password';
        container.innerHTML = `
            <input type="password" name="current_password" placeholder="Current Password" class="w-full p-2 mb-3 border-2 border-[#C67C48]/20 rounded bg-white outline-none focus:border-[#6B8E23]" required>
            <input type="password" name="new_password" placeholder="New Password" class="w-full p-2 border-2 border-[#C67C48]/20 rounded bg-white outline-none focus:border-[#6B8E23]" required>
        `;
    }
}

function closeModal() {
    document.getElementById('modalOverlay').classList.add('hidden');
}
</script>
@endsection