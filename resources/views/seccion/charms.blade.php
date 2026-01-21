@extends('layouts.master')
@section('title', 'Charms')
@section('content')

<div> {{-- Centrarlo con css --}}
    <h1>Charms</h1>
    <form method="GET" action="{{ route('charms.index') }}">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search charm...">
        <button type="submit">Search</button>
    </form>

    <ul>
    @foreach ($paginatedCharm as $rank)
        <li>
            <a href="{{ route('charms.show', $rank['slug']) }}">
                {{ $rank['name'] }}
            </a>
        </li>
    @endforeach
    </ul>


    <div> {{-- Centrarlo con css --}}
    {{ $paginatedCharm->links() }}
    </div>
</div>
@endsection