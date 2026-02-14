@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-white py-12">
    <div class="max-w-xl mx-auto p-8 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20 text-[#2F2F2F]">
        
        <div class="mb-10 border-b-2 border-[#6B8E23]/10 pb-6 text-center">
            <h1 class="text-3xl font-black uppercase italic tracking-tighter">Edit Category</h1>
            <p class="text-[#6B8E23] font-bold text-xs uppercase tracking-[0.3em] mt-2">Update Tag ID: {{ $tag->id }}</p>
        </div>

        <form action="{{ route('admin.tags.update', $tag) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-black uppercase tracking-widest text-[#2F2F2F]/70 mb-2 ml-1">
                    Tag Name
                </label>
                {{-- Sin 'uppercase' para que veas lo que editas --}}
                <input type="text" name="name" value="{{ old('name', $tag->name) }}" required
                       class="w-full bg-white border-2 border-transparent focus:border-[#6B8E23] outline-none px-4 py-3 rounded-xl font-bold text-sm shadow-sm transition-all">
                @error('name')
                    <span class="text-red-600 text-[10px] font-black uppercase mt-1">{{ $message }}</span>
                @enderror
            </div>

            <div class="flex items-center justify-between pt-6">
                <a href="{{ route('admin.tags.index') }}" class="text-[#2F2F2F]/50 hover:text-black text-xs font-black uppercase tracking-widest transition-colors">
                    Cancel
                </a>
                
                <button type="submit" 
                        class="bg-[#6B8E23] text-[#F4EBD0] px-8 py-3 rounded-xl font-black uppercase text-xs tracking-[0.2em] hover:bg-[#2F2F2F] transition-all shadow-lg active:scale-95">
                    Update Tag
                </button>
            </div>
        </form>
    </div>
</div>
@endsection