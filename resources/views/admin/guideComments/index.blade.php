@extends('layouts.admin')

@section('content')
<div class="min-h-screen bg-white py-12">
    <div class="w-[95%] max-w-7xl mx-auto">
        
        {{-- Buscador --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <form action="{{ route('admin.guideComments.index') }}" method="GET" class="w-full md:w-96 flex">
                <div class="relative w-full">
                    <input type="text" name="search" value="{{ $search ?? '' }}" 
                        placeholder="Search by hunter, content or guide..." 
                        class="w-full bg-[#F4EBD0] border-2 border-[#6B8E23]/20 focus:border-[#6B8E23] outline-none px-4 py-3 rounded-xl text-sm italic font-medium shadow-inner">
                    <button type="submit" class="absolute right-3 top-3 text-[#6B8E23]">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </button>
                </div>
            </form>
        </div>

        <div class="p-8 md:p-12 rounded-3xl shadow-2xl bg-[#F4EBD0] border border-[#6B8E23]/20 text-[#2F2F2F]">
            <div class="mb-10 pb-6 border-b-2 border-[#6B8E23]/10">
                <h1 class="text-3xl font-black uppercase italic tracking-tighter text-[#2F2F2F]">Hunter <span class="text-[#6B8E23]">Echoes</span></h1>
                <p class="text-[#6B8E23] font-bold text-xs uppercase tracking-[0.3em] mt-1">Moderation of Guide Commentary</p>
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
                            <th class="px-6 pb-4">ID</th>
                            <th class="px-6 pb-4">Hunter</th>
                            <th class="px-6 pb-4">Commentary</th>
                            <th class="px-6 pb-4 text-center">Parent ID</th>
                            <th class="px-6 pb-4">Origin Guide</th>
                            <th class="px-6 pb-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($comments as $comment)
                        @php $isDeleted = ($comment->comentario === 'This text has been deleted'); @endphp
                        
                        <tr class="bg-white hover:shadow-md transition-all duration-200 group {{ $isDeleted ? 'opacity-60' : '' }}">
                            {{-- ID propio --}}
                            <td class="px-6 py-4 rounded-l-2xl border-y-2 border-l-2 border-transparent group-hover:border-[#6B8E23]/20">
                                <span class="text-xs font-mono font-bold text-gray-400">#{{ $comment->id }}</span>
                            </td>

                            <td class="px-6 py-4 border-y-2 border-transparent group-hover:border-[#6B8E23]/20">
                                <span class="font-bold text-[#2F2F2F] block">{{ $comment->user->name }}</span>
                                <span class="text-[10px] text-gray-400 font-medium italic">{{ $comment->created_at->diffForHumans() }}</span>
                            </td>

                            <td class="px-6 py-4 border-y-2 border-transparent group-hover:border-[#6B8E23]/20">
                                <div class="text-sm text-[#2F2F2F] max-w-md line-clamp-2 italic font-medium {{ $isDeleted ? 'text-red-400' : '' }}">
                                    "{{ $comment->comentario }}"
                                </div>
                            </td>

                            {{-- CAMPO PADRE --}}
                            <td class="px-6 py-4 border-y-2 border-transparent group-hover:border-[#6B8E23]/20 text-center">
                                @if($comment->padre)
                                    <span class="text-xs font-mono bg-gray-100 px-2 py-1 rounded text-gray-500 border border-gray-200">
                                        #{{ $comment->padre }}
                                    </span>
                                @else
                                    <span class="text-[9px] font-black uppercase text-gray-300 tracking-widest">Original</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 border-y-2 border-transparent group-hover:border-[#6B8E23]/20">
                                <span class="text-[10px] font-black uppercase px-2 py-0.5 rounded bg-[#6B8E23]/10 text-[#6B8E23] border border-[#6B8E23]/20">
                                    {{ $comment->guide->titulo ?? 'Unknown' }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right rounded-r-2xl border-y-2 border-r-2 border-transparent group-hover:border-[#6B8E23]/20">
                                <div class="flex justify-end gap-3 items-center">
                                    @if(!$isDeleted)
                                        <a href="{{ route('admin.guideComments.edit', $comment->id) }}" class="text-[#2F2F2F] hover:text-[#6B8E23] transition-colors" title="Edit Content">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </a>

                                        <form action="{{ route('admin.guideComments.destroy', $comment->id) }}" method="POST" onsubmit="return confirm('Do you want to redact this comment content?')">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="text-red-300 hover:text-red-600 transition-colors" title="Redact Content">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[9px] font-black uppercase px-2 py-1 rounded bg-gray-100 text-gray-400 border border-gray-200 tracking-tighter">Redacted</span>
                                    @endif

                                    <form action="{{ route('admin.guideComments.hardDelete', $comment->id) }}" method="POST" onsubmit="return confirm('CRITICAL WARNING: This will permanently delete this comment AND ALL its replies. Proceed?')">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 transition-all duration-300 hover:scale-125 active:translate-y-1" title="True Delete">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">The archives are silent. No echoes found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-6">
                    {{ $comments->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection