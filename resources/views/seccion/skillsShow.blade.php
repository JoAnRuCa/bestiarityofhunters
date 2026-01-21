@extends('layouts.master')
@section('title', $skill['slug'])
@section('content')
<div class="weapon-show">

    {{-- Nombre y descripción --}}
    <h1>{{ $weapon['name'] }}</h1>
    <p>{{ $weapon['description'] }}</p>

    {{-- Datos básicos --}}
    <p><strong>Rarity:</strong> {{ $weapon['rarity'] ?? '—' }}</p>
    <p><strong>Type:</strong> {{ $weapon['kind'] }}</p>
    <p><strong>Affinity:</strong> {{ $weapon['affinity'] }}%</p>
    <p><strong>Defense Bonus:</strong> {{ $weapon['defenseBonus'] }}</p>

    {{-- Daño --}}
    @if(isset($weapon['damage']))
        <h3>Damage</h3>
        <p><strong>Raw:</strong> {{ $weapon['damage']['raw'] }}</p>
        <p><strong>Display:</strong> {{ $weapon['damage']['display'] }}</p>
    @endif

    {{-- Sharpness --}}
    @if(isset($weapon['sharpness']))
        <h3>Sharpness</h3>
        @foreach ($weapon['sharpness'] as $color => $value)
            <p><strong>{{ ucfirst($color) }}:</strong> {{ $value }}</p>
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
                <strong>{{ $skill['skill']['name'] }}</strong> (Lv {{ $skill['level'] }})  
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

    {{-- Specials (elementos, estados, etc.) --}}
    @if(isset($weapon['specials']) && count($weapon['specials']) > 0)
        <h3>Special Effects</h3>
        @foreach ($weapon['specials'] as $special)
            <p>
                <strong>{{ ucfirst($special['element']) }}</strong>  
                ({{ $special['kind'] }})  
                — Raw: {{ $special['damage']['raw'] }}  
                — Display: {{ $special['damage']['display'] }}  
                @if($special['hidden'])
                    <span>(Hidden)</span>
                @endif
            </p>
        @endforeach
    @endif

</div>

@endsection