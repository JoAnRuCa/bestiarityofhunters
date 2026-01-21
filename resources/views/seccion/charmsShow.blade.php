@extends('layouts.master')
@section('title', $selectedRank['name'])
@section('content')

<div>

    <h1>{{ $selectedRank['name'] }}</h1>

    @if(!empty($selectedRank['description']))
        <p>{{ $selectedRank['description'] }}</p>
    @endif

    <p>Rank: {{ $selectedRank['level'] }}</p>
    <p>Rarity: {{ $selectedRank['rarity'] }}</p>

    @if(isset($selectedRank['skills']))
        <h3>Skills</h3>
        @foreach ($selectedRank['skills'] as $skill)
            <p>{{ $skill['skill']['name'] }} (Lv {{ $skill['level'] }})</p>
            <p>Level {{ $skill['level'] }}: {{ $skill['description'] }}</p>
        @endforeach
    @endif

</div>

@endsection

