@if($guides->count() === 0)
    <p class="text-center text-lg text-gray-600 italic py-10">No guides match your search.</p>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach($guides as $guide)
            <div class="p-6 bg-transparent flex justify-between items-start border-b border-[#6B8E23]/10 md:border-none transition-all">
                <div class="flex-1 pr-4">
                    <h2 class="text-2xl font-bold mb-2">
                        <a href="{{ route('guides.show', ['slug' => $guide->slug]) }}" class="text-[#6B8E23] hover:text-[#C67C48] transition-colors">
                            {{ $guide->titulo }}
                        </a>
                    </h2>
                    <p class="text-gray-700 mb-4 leading-snug text-sm">{{ Str::limit($guide->contenido, 120) }}</p>
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($guide->tags as $tag)
                            <span class="px-2 py-0.5 bg-[#C67C48] text-white text-[10px] font-bold uppercase rounded shadow-sm">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                    
                    {{-- Estilo final: 'By' con B mayúscula y el tiempo en minúsculas --}}
                    <p class="text-[11px] text-[#2F2F2F] font-medium tracking-wider">
                        By <span class="text-[#C67C48]">{{ $guide->user->name }}</span> • {{ $guide->created_at->diffForHumans() }}
                    </p>
                </div>
                <div class="pt-1">
                    <x-vote-block :item="$guide" type="guide" />
                </div>
            </div>
        @endforeach
    </div>
    <div class="mt-12 pagination-ajax">
        {{ $guides->links() }}
    </div>
@endif