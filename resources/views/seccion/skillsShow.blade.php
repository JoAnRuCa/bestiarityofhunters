@extends('layouts.master')
@section('title', $skill['slug'])
@section('content')
<div> {{-- Centrarlo con css --}}
    <h1>{{ $skill['name'] }}</h1>
    <p>{{ $skill['description'] }}</p>
    <p>{{ $skill['kind'] }}</p>
    @foreach ($skill['ranks'] as $rank)
        <p>{{ $rank['name'] }}</p>
        <p>{{ $rank['description'] }}</p>
    @endforeach
</div>
@endsection