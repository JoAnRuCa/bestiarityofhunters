@extends('layouts.master')
@section('title', $weapon['slug'])
@section('content')
<div> {{-- Centrarlo con css --}}
    <h1>{{ $weapon['name'] }}</h1>
    <p>{{ $weapon['description'] }}</p>
    <p>Type: {{ $weapon['kind'] }}</p>
@foreach ($weapon['ranks'] as $rank)
    <p>
        Level {{ $rank['level'] }} : {{ $rank['description'] }}
    </p>
@endforeach

</div>
@endsection