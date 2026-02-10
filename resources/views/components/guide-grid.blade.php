{{-- resources/views/components/guide-grid.blade.php --}}
@if($guides->count() === 0)
    <div id="no-guides-msg" class="py-12 text-center border-2 border-dashed border-[#6B8E23]/10 rounded-lg">
        <p class="text-gray-600 italic font-serif text-lg">No guides match your search.</p>
        @if(Route::currentRouteName() == 'my.guides')
            <a href="{{ route('guides.create') }}" class="mt-4 inline-block text-[#C67C48] font-bold uppercase hover:underline tracking-widest text-xs">
                Write your first scroll →
            </a>
        @endif
    </div>
@else
    <div id="guides-container" class="grid grid-cols-1 md:grid-cols-2 gap-8">
        @foreach($guides as $guide)
            {{-- EL ID ES CLAVE: Permite al JS encontrar esta tarjeta específica --}}
            <div id="guide-card-{{ $guide->id }}" 
                 class="group/card p-6 bg-transparent flex justify-between items-stretch border-b border-[#6B8E23]/10 md:border md:border-[#6B8E23]/5 md:rounded-lg transition-all hover:bg-[#6B8E23]/5 duration-300">
                
                {{-- Columna Izquierda: Información --}}
                <div class="flex-1">
                    <h2 class="text-2xl font-bold mb-2">
                        <a href="{{ route('guides.show', ['slug' => $guide->slug]) }}" class="text-[#6B8E23] hover:text-[#C67C48] transition-colors">
                            {{ $guide->titulo }}
                        </a>
                    </h2>
                    
                    <p class="text-gray-700 mb-4 leading-snug text-sm">
                        {{ Str::limit($guide->contenido, 120) }}
                    </p>
                    
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($guide->tags as $tag)
                            <span class="px-2 py-0.5 bg-[#C67C48] text-white text-[10px] font-bold uppercase rounded shadow-sm">
                                {{ $tag->name }}
                            </span>
                        @endforeach
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <p class="text-[11px] text-[#2F2F2F] font-medium tracking-wider">
                            By <span class="text-[#C67C48]">{{ $guide->user->name }}</span> • {{ $guide->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                {{-- Columna Derecha: Interacción --}}
                <div class="flex flex-col items-end justify-between min-w-[80px] ml-4">
                    
                    {{-- Bloque de Votos --}}
                    <div class="flex justify-end w-full">
                        <x-vote-block :item="$guide" type="guide" />
                    </div>

                    {{-- Acciones del Propietario --}}
                    @if(isset($editable) && $editable && auth()->id() === $guide->user_id)
                        <div class="flex flex-row items-center justify-end gap-2 w-full mt-auto">
                            {{-- Componente de Editar --}}
                            <x-edit-button :url="route('guides.edit', $guide->id)" :editable="$editable" />
                            
                            {{-- Componente de Borrar con ID --}}
                            <x-delete-button :action="route('guides.destroy', $guide->id)" :id="$guide->id" />
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Paginación AJAX --}}
    <div class="mt-12 pagination-ajax">
        {{ $guides->links() }}
    </div>
@endif