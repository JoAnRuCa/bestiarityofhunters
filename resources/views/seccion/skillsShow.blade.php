@extends('layouts.master')
@section('title', $skill['slug'])
@section('content')
<div> {{-- Centrarlo con css --}}
    <h1>{{ $skill['name'] }}</h1>
    <p>{{ $skill['description'] }}</p>
    <p>Type: {{ $skill['kind'] }}</p>
@foreach ($skill['ranks'] as $rank)
    <p>
        Level {{ $rank['level'] }} : {{ $rank['description'] }}
    </p>
@endforeach

</div>
@endsection