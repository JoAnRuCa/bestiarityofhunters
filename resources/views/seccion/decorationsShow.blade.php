@extends('layouts.master')
@section('title', $decoration['name'])
@section('content')

<div>

    {{-- Nombre --}}
    <h1>{{ $decoration['name'] }}</h1>

    {{-- Descripción general --}}
    @if(!empty($decoration['description']))
        <p>{{ $decoration['description'] }}</p>
    @endif

    {{-- Tipo --}}
    @if(isset($decoration['kind']))
        <p>Type: {{ ucfirst($decoration['kind']) }}</p>
    @endif

    {{-- Rareza --}}
    <p>Rarity: {{ $decoration['rarity'] }}</p>

    {{-- Slot --}}
    <p>Slot: {{ $decoration['slot'] }}</p>

    {{-- Skills --}}
    @if(isset($decoration['skills']) && count($decoration['skills']) > 0)
        <h3>Skills</h3>

        @foreach ($decoration['skills'] as $skill)
            <p>
                {{ $skill['skill']['name'] }}
                (Lv {{ $skill['level'] }})  
                <br>
                {{ $skill['description'] }}
            </p>
        @endforeach
    @endif

</div>

@endsection

