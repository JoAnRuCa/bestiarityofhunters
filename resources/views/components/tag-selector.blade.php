@php
    $weaponNames = ['Great Sword', 'Long Sword', 'Bow', 'Hammer', 'Lance', 'Gunlance', 'Switch Axe', 'Charge Blade', 'Insect Glaive', 'Light Bowgun', 'Heavy Bowgun', 'Sword and Shield', 'Dual Blades', 'Hunting Horn'];
@endphp

<div>
    <label class="block font-semibold mb-2 text-[#2F2F2F] text-base">Tags</label>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
        @foreach($allTags as $tag)
            @php
                // Ocultar si no es showAll y es un arma
                $isHiddenWeapon = !$showAll && in_array($tag->name, $weaponNames);
            @endphp
            
            <label class="items-center space-x-2 cursor-pointer group {{ $isHiddenWeapon ? 'hidden' : 'flex' }}">
                <input type="checkbox"
                       name="tags[]"
                       value="{{ $tag->id }}"
                       data-name="{{ $tag->name }}"
                       {{ in_array($tag->id, $selectedTags) ? 'checked' : '' }}
                       class="h-4 w-4 text-[#6B8E23] border-gray-300 rounded focus:ring-[#6B8E23] cursor-pointer">
                
                <span class="text-base text-[#2F2F2F] group-hover:text-[#6B8E23] transition-colors">
                    {{ $tag->name }}
                </span>
            </label>
        @endforeach
    </div>
</div>