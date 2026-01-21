@extends('layouts.master')
@section('title', $weapon['slug'])
@section('content')
<div>

    {{-- Nombre y descripción --}}
    <h1>{{ $weapon['name'] }}</h1>
    <p>{{ $weapon['description'] }}</p>

    {{-- Datos básicos --}}
    <p>Rarity: {{ $weapon['rarity'] ?? '—' }}</p>   
    <p>Type: {{ $weapon['kind'] }}</p>
    <p>Affinity: {{ $weapon['affinity'] }}%</p>
    <p>Defense Bonus: {{ $weapon['defenseBonus'] }}</p>

    {{-- Daño --}}
    @if(isset($weapon['damage']))
        <h3>Damage</h3>
        <p>Raw: {{ $weapon['damage']['raw'] }}</p>
        <p>Display: {{ $weapon['damage']['display'] }}</p>
    @endif

        {{-- Specials (elementos, estados, etc.) --}}
    @if(isset($weapon['specials']) && count($weapon['specials']) > 0)
        <h3>Special Effects</h3>
        @foreach ($weapon['specials'] as $special)
            <div>
                <p>
                    {{ ucfirst($special['element'] ?? $special['status'] ?? 'Unknown') }}
                    ({{ $special['kind'] }})
                </p>

                <p>Raw: {{ $special['damage']['raw'] }}</p>
                <p>Display: {{ $special['damage']['display'] }}</p>

                @if($special['hidden'])
                    <p>(Hidden)</p>
                @endif
            </div>
        @endforeach
    @endif


    {{-- Sharpness --}}
    @if(isset($weapon['sharpness']))
        <h3>Sharpness</h3>
        @foreach ($weapon['sharpness'] as $color => $value)
            <p>{{ ucfirst($color) }}: {{ $value }}</p>
        @endforeach
    @endif

    {{-- Slots --}}
    @if(isset($weapon['slots']) && count($weapon['slots']) > 0)
        <h3>Slots</h3>
        @foreach ($weapon['slots'] as $slot)
            <p>Slot Level: {{ $slot }}</p>
        @endforeach
    @endif

    {{-- Skills --}}
    @if(isset($weapon['skills']) && count($weapon['skills']) > 0)
        <h3>Skills</h3>
        @foreach ($weapon['skills'] as $skill)
            <p>
                {{ $skill['skill']['name'] }} (Lv {{ $skill['level'] }})  
                <br>
                {{ $skill['skill']['description'] }}  
                <br>
                <em>{{ $skill['description'] }}</em>
            </p>
        @endforeach
    @endif

    {{-- Series --}}
    @if(isset($weapon['series']))
        <h3>Series</h3>
        <p>{{ $weapon['series']['name'] }}</p>
    @endif



</div>

@endsection