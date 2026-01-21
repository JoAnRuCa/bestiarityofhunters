@extends('layouts.master')
@section('title', 'Decorations')
@section('content')

<div> {{-- Centrarlo con css --}}
    <h1>Decorations</h1>
    <form method="GET" action="{{ route('decorations.index') }}">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search decoration...">
        <button type="submit">Search</button>
    </form>

    <ul>
        @foreach ($paginatedDecorations as $decoration)
            <li>
                <a href="{{ route('decorations.show', $decoration['slug']) }}">
                    {{ $decoration['name'] }}
                </a>
            </li>
        @endforeach
    </ul>
    <div> {{-- Centrarlo con css --}}
    {{ $paginatedDecorations->links() }}
    </div>
</div>
@endsection