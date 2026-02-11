{{-- Extendemos de TU archivo master --}}
@extends('layouts.master') 

@section('content')
<div class="container mx-auto p-6">
    {{-- Contenido de la build --}}
    <h1 class="text-2xl font-bold">{{ $build->titulo }}</h1>
    
    <div class="mt-4">
        <h3 class="font-bold border-b mb-2">Equipamiento:</h3>
        @foreach($equipments as $eq)
    <div class="p-4 mb-2 border rounded-lg {{ $eq->equipment_id ? 'bg-white' : 'bg-gray-50 border-dashed opacity-60' }}">
        <div class="flex justify-between items-center">
            <div>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                    {{ $eq->tipo == 1 ? 'Weapon' : ($eq->tipo == 2 ? 'Armor' : 'Charm') }}
                </span>
                <p class="font-bold text-gray-800">
                    {{ $eq->real_name }}
                </p>
            </div>
            
            @if(!$eq->equipment_id)
                <span class="text-xs italic text-gray-400">Sin equipar</span>
            @endif
        </div>
    </div>
@endforeach
    </div>
</div>
@endsection