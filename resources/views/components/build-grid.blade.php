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

                {{-- Columna Derecha --}}
                <div class="flex flex-col items-end justify-between min-w-[80px] ml-4">
                    <div class="flex flex-col items-center gap-4">
                        {{-- Botón de Guardar (Favoritos) --}}
                        <div class="save-container">
                            <button type="button" 
                                    class="save-btn flex items-center justify-center w-10 h-10 rounded-full bg-[#6B8E23] text-[#2F2F2F] shadow-sm transition-all hover:scale-110"
                                    data-url="{{ route('saved.toggle', ['type' => 'build', 'id' => $build->id]) }}" 
                                    data-type="build">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z" />
                                </svg>
                            </button>
                        </div>

                        {{-- Bloque de Votos --}}
                        <div class="transform scale-90">
                            <x-vote-block :item="$build" type="build" />
                        </div>
                    </div>

                    {{-- Acciones de Propietario (Editar/Borrar) --}}
                    {{-- Solo aparece si se pasa editable="true" Y el usuario es el autor --}}
                    @if(isset($editable) && $editable && auth()->check() && auth()->id() === $build->user_id)
                        <div class="flex flex-row items-center justify-end gap-2 w-full mt-auto">
                            <x-edit-button :url="route('builds.edit', $build->id)" :editable="true" />
                            <x-delete-button :action="route('builds.destroy', $build->id)" :id="$build->id" />
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-12 pagination-ajax flex justify-end">
        {{ $builds->links() }}
    </div>
@endif