@extends('layouts.master')
@section('title', 'Weapons')
@section('content')

<div> {{-- Centrarlo con css --}}
    <h1>Weapons</h1>
    <form method="GET" action="{{ route('weapons.index') }}">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search weapon...">
        <button type="submit">Search</button>
    </form>

    <ul>
        @foreach ($paginatedWeapons as $weapon)
            <li>
                <a href="{{ route('weapons.show', $weapon['slug']) }}">
                    {{ $weapon['name'] }}
                </a>
            </li>
        @endforeach
    </ul>
    <div> {{-- Centrarlo con css --}}
    {{ $paginatedWeapons->links() }}
    </div>
</div>
@endsection