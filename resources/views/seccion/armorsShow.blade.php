@extends('layouts.master')
@section('title', $armor['name'])
@section('content')

<div>

    {{-- Nombre y descripción --}}
    <h1>{{ $armor['name'] }}</h1>
    <p>{{ $armor['description'] }}</p>

    {{-- Datos básicos --}}
    <p>Type: {{ ucfirst($armor['kind']) }}</p>
    <p>Rank: {{ ucfirst($armor['rank']) }}</p>
    <p>Rarity: {{ $armor['rarity'] }}</p>

    {{-- Defensa --}}
    @if(isset($armor['defense']))
        <h3>Defense</h3>
        <p>Base: {{ $armor['defense']['base'] }}</p>
        <p>Max: {{ $armor['defense']['max'] }}</p>
    @endif

    {{-- Resistencias --}}
    @if(isset($armor['resistances']))
        <h3>Resistances</h3>
        @foreach ($armor['resistances'] as $element => $value)
            <p>{{ ucfirst($element) }}: {{ $value }}</p>
        @endforeach
    @endif

    {{-- Slots --}}
    @if(isset($armor['slots']) && count($armor['slots']) > 0)
        <h3>Slots</h3>
        @foreach ($armor['slots'] as $slot)
            <p>Slot Level: {{ $slot }}</p>
        @endforeach
    @endif

    {{-- Skills --}}
    @if(isset($armor['skills']) && count($armor['skills']) > 0)
        <h3>Skills</h3>
        @foreach ($armor['skills'] as $skill)
            <div>
                <p>{{ $skill['skill']['name'] }} (Lv {{ $skill['level'] }})</p>
                <p>{{ $skill['description'] }}</p>
            </div>
        @endforeach
    @endif

    {{-- Armor Set --}}
    @if(isset($armor['armorSet']))
        <h3>Armor Set</h3>
        <p>{{ $armor['armorSet']['name'] }}</p>
    @endif

</div>

@endsection
