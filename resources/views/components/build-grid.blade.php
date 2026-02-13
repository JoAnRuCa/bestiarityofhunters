@if($builds->count() === 0)
    <div id="no-builds-msg" class="py-12 text-center border-2 border-dashed border-[#6B8E23]/10 rounded-lg">
        <p class="text-gray-600 italic font-serif text-lg">No builds found in the archives.</p>
    </div>
@else
    <div id="builds-container" class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach($builds as $build)
            <div id="build-card-{{ $build->id }}" 
                 class="group/card p-6 bg-white/40 flex justify-between items-stretch border border-[#6B8E23]/10 rounded-2xl transition-all hover:bg-[#6B8E23]/5 duration-300 shadow-sm hover:shadow-md min-h-[160px]">
                
                {{-- Columna Izquierda --}}
                <div class="flex-1 flex flex-col pr-4">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-[10px] font-black text-[#6B8E23] uppercase tracking-tighter italic">Loadout</span>
                    </div>
                    <h2 class="text-2xl font-black uppercase italic leading-none mb-3">
                        <a href="{{ route('builds.show', $build->slug) }}" class="text-[#2F2F2F] hover:text-[#6B8E23] transition-colors">
                            {{ $build->titulo }}
                        </a>
                    </h2>
                    
                    <p class="text-gray-700 mb-4 leading-snug text-sm italic line-clamp-2">
                        {{ $build->playstyle ?? 'No strategy described for this build.' }}
                    </p>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($build->tags as $tag)
                            <span class="px-2 py-0.5 bg-[#C67C48] text-white text-[9px] font-black uppercase rounded shadow-sm">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                    
                    <div class="mt-auto">
                        <p class="text-[11px] text-[#2F2F2F] font-bold tracking-wider uppercase opacity-70">
                            Architect: <span class="text-[#C67C48]">{{ $build->user->name }}</span>
                        </p>
                    </div>
                </div>

                {{-- Columna Derecha (Sin Borde) --}}
                <div class="flex flex-col items-end justify-between min-w-[60px] ml-4">
                    <div class="flex justify-end w-full">
                        <x-vote-block :item="$build" type="build" />
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-12 pagination-ajax flex justify-end">
        {{ $builds->links() }}
    </div>
@endif