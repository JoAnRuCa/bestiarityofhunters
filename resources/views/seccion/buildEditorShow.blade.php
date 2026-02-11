@extends('layouts.master')

@section('content')
<div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <div class="mb-8 border-b pb-4">
        <h1 class="text-4xl font-black text-gray-900 uppercase tracking-tighter">{{ $build->titulo }}</h1>
        <p class="text-gray-500 italic mt-1">{{ $build->playstyle ?? 'Sin descripción' }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        
        <div class="lg:col-span-2 space-y-3">
            @foreach($equipments as $eq)
            <div class="bg-white border rounded-xl p-4 shadow-sm flex items-center gap-4 {{ !$eq->real_name ? 'border-dashed opacity-50 bg-gray-50' : 'border-gray-200' }}">
                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-2xl shadow-inner">
                    {!! $eq->tipo == 1 ? '⚔️' : ($eq->tipo == 2 ? '🛡️' : '📿') !!}
                </div>

                <div class="flex-grow">
                    <span class="text-[10px] font-black text-blue-500 uppercase tracking-widest">
                        {{ $eq->tipo == 1 ? 'Weapon' : ($eq->tipo == 2 ? 'Armor' : 'Charm') }}
                    </span>
                    <h3 class="font-bold text-gray-800 text-lg leading-tight">
                        {{ $eq->real_name ?? 'Empty Slot' }}
                    </h3>

                    @if(!empty($eq->attached_decos))
                    <div class="mt-2 flex flex-wrap gap-1">
                        @foreach($eq->attached_decos as $deco)
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-600 text-[10px] font-bold rounded border border-blue-100 flex items-center">
                                <span class="mr-1 text-[8px]">💎</span> {{ $deco }}
                            </span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="bg-[#1a1a1a] rounded-2xl p-6 shadow-2xl border border-gray-800 sticky top-6">
            <div class="flex items-center justify-between mb-6 border-b border-gray-700 pb-3">
                <h2 class="text-xs font-black text-yellow-500 uppercase tracking-[0.2em]">Habilidades Activas</h2>
                <span class="bg-yellow-500 text-black text-[10px] font-bold px-2 py-0.5 rounded">TOTAL</span>
            </div>
            
            @if(empty($totalSkills))
                <div class="text-center py-10">
                    <p class="text-gray-500 text-sm italic">No hay habilidades detectadas.</p>
                </div>
            @else
                <div class="space-y-5">
                    @foreach($totalSkills as $name => $level)
                    <div class="group">
                        <div class="flex justify-between items-end mb-1">
                            <span class="text-gray-200 font-bold text-sm group-hover:text-yellow-400 transition-colors">{{ $name }}</span>
                            <span class="text-yellow-500 font-black text-xs">LV. {{ $level }}</span>
                        </div>
                        <div class="w-full bg-gray-800 rounded-full h-2 p-0.5 border border-gray-700">
                            <div class="bg-gradient-to-r from-yellow-600 to-yellow-400 h-full rounded-full shadow-[0_0_10px_rgba(234,179,8,0.3)]" 
                                 style="width: {{ min(($level / 7) * 100, 100) }}%">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-8 pt-4 border-t border-gray-800 text-center">
                <p class="text-[9px] text-gray-600 uppercase tracking-widest font-bold">Bestiarity of Hunters Forge</p>
            </div>
        </div>

    </div>
</div>
@endsection