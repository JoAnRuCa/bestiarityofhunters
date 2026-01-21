@extends('layouts.master')
@section('title', $skill['name'])
@section('content')

<div>

    {{-- Nombre --}}
    <h1>{{ $skill['name'] }}</h1>

    {{-- Descripción general --}}
    @if(!empty($skill['description']))
        <p>{{ $skill['description'] }}</p>
    @endif

    {{-- Tipo --}}
    <p>Type: {{ ucfirst($skill['kind']) }}</p>

    {{-- Ranks --}}
   @if(isset($skill['ranks']) && count($skill['ranks']) > 0)
    <h3>Ranks</h3>
    @foreach ($skill['ranks'] as $rank)
        <p>
            Lv {{ $rank['level'] }}: {{ $rank['description'] }}
        </p>
    @endforeach
@endif


</div>

@endsection
