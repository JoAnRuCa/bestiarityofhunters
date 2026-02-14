@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-white py-12">
    <div class="max-w-3xl mx-auto">
        <div class="p-8 rounded-3xl bg-[#F4EBD0] border border-[#6B8E23]/20 shadow-xl">
            <h2 class="text-2xl font-black italic uppercase text-[#2F2F2F] mb-6">Modify <span class="text-[#6B8E23]">Echo</span></h2>
            
            <form action="{{ route('admin.buildComments.update', $comment->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-[#6B8E23] text-xs font-black uppercase mb-2">Commentary Content</label>
                    <textarea name="comentario" rows="5" 
                        class="w-full bg-white border-2 border-[#6B8E23]/20 focus:border-[#6B8E23] outline-none p-4 rounded-xl italic font-medium shadow-inner">{{ $comment->comentario }}</textarea>
                </div>

                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.buildComments.index') }}" class="px-6 py-2 text-gray-500 font-bold italic hover:text-gray-700 transition-colors">Cancel</a>
                    <button type="submit" class="px-8 py-2 bg-[#6B8E23] text-white font-black uppercase italic rounded-xl hover:bg-[#556B2F] transition-all shadow-lg">
                        Update Echo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection