@if($guides->count() === 0)
    <div id="no-guides-msg" class="py-12 text-center border-2 border-dashed border-[#6B8E23]/10 rounded-lg">
        <p class="text-gray-600 italic font-serif text-lg">No guides match your search.</p>
        
        @if(request()->routeIs('my.guides'))
            <a href="{{ url('/guide-editor') }}" class="mt-4 inline-block text-[#C67C48] font-bold uppercase hover:underline tracking-widest text-xs">
                Write your own scroll →
            </a>
        @endif
    </div>
@else
    <div id="guides-container" class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach($guides as $guide)
            <div id="guide-card-{{ $guide->slug }}" 
                 class="group/card p-6 bg-white/40 flex justify-between items-stretch border border-[#6B8E23]/10 rounded-2xl transition-all hover:bg-[#6B8E23]/5 duration-300 shadow-sm hover:shadow-md min-h-[160px]">
                
                {{-- Columna Izquierda: Información --}}
                <div class="flex-1 flex flex-col pr-4">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-black text-[#6B8E23] uppercase tracking-tighter italic">Guide</span>
                    </div>
                    <h2 class="text-2xl font-black uppercase italic leading-none mb-3">
                        <a href="{{ route('guides.show', ['slug' => $guide->slug]) }}" class="text-[#2F2F2F] hover:text-[#6B8E23] transition-colors">
                            {{ $guide->titulo }}
                        </a>
                    </h2>
                    
                    <p class="text-gray-700 mb-4 leading-snug text-sm italic line-clamp-2">
                        {{ Str::limit($guide->contenido, 120) }}
                    </p>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($guide->tags as $tag)
                            <span class="px-2 py-0.5 bg-[#C67C48] text-white text-[9px] font-black uppercase rounded shadow-sm">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                    
                    <div class="mt-auto">
                        <p class="text-[11px] text-[#2F2F2F] font-bold tracking-wider uppercase opacity-70">
                            By <span class="text-[#C67C48]">{{ $guide->user->name }}</span> <span class="mx-1 text-[8px]">•</span> {{ $guide->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                {{-- Columna Derecha: Interacción (Sin Borde) --}}
                <div class="flex flex-col items-end justify-between min-w-[60px] ml-4">
                    
                    {{-- Bloque de Votos --}}
                    <div class="flex justify-end w-full">
                        <x-vote-block :item="$guide" type="guide" />
                    </div>

                    {{-- Acciones del Propietario --}}
                    @if(isset($editable) && $editable && auth()->id() === $guide->user_id)
                        <div class="flex flex-row items-center justify-end gap-2 w-full mt-auto">
                            <x-edit-button :url="route('guides.edit', $guide->slug)" :editable="$editable" />
                            <x-delete-button :action="route('guides.destroy', $guide->slug)" :id="$guide->slug" />
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Paginación AJAX --}}
    <div class="mt-12 pagination-ajax flex justify-end">
        {{ $guides->links() }}
    </div>
@endif