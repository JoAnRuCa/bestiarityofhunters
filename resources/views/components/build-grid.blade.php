@if($builds->count() === 0)
    <div id="no-builds-msg" class="py-12 text-center border-2 border-dashed border-[#6B8E23]/10 rounded-lg">
        <p class="text-gray-600 italic font-serif text-lg">No builds found in the archives.</p>
        <a href="{{ route('builds.create') }}" class="mt-4 inline-block text-[#C67C48] font-bold uppercase hover:underline tracking-widest text-xs">
            Forge the first build →
        </a>
    </div>
@else
    <div id="builds-container" class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach($builds as $build)
            <div id="build-card-{{ $build->id }}" 
                 class="group/card p-6 bg-white/40 flex justify-between items-stretch border border-[#6B8E23]/10 rounded-2xl transition-all hover:bg-[#6B8E23]/5 duration-300 shadow-sm hover:shadow-md">
                
                {{-- Columna Izquierda: Info de la Build --}}
                <div class="flex-1">
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
                    
                    {{-- Tags --}}
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($build->tags as $tag)
                            <span class="px-2 py-0.5 bg-[#6B8E23] text-white text-[9px] font-black uppercase rounded shadow-sm">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                    
                    <div class="flex items-center">
                        <p class="text-[11px] text-[#2F2F2F] font-bold tracking-wider uppercase opacity-70">
                            Architect: <span class="text-[#C67C48]">{{ $build->user->name }}</span>
                        </p>
                    </div>
                </div>

                {{-- Columna Derecha: Votos (Usando tu componente exacto) --}}
                <div class="flex flex-col items-end justify-between min-w-[80px] ml-4 border-l border-[#6B8E23]/10 pl-4">
                    <div class="flex justify-end w-full">
                        {{-- Inyectamos tu componente universal de votos --}}
                        <x-vote-block :item="$build" type="build" />
                    </div>

                    {{-- Botón Ver Detalle --}}
                    <a href="{{ route('builds.show', $build->slug) }}" 
                       class="mt-auto p-2 text-[#6B8E23] hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-12 pagination-ajax">
        {{ $builds->links() }}
    </div>
@endif