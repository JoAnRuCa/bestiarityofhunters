@extends('layouts.master')
@section('title', 'Skills')
@section('content')

<div> {{-- Centrarlo con css --}}
    <h1>Skills</h1>
    <form method="GET" action="{{ route('skills.index') }}">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search skill...">
        <button type="submit">Search</button>
    </form>

    <ul>
        @foreach ($paginatedSkills as $skill)
            <li>
                <a href="{{ route('skills.show', $skill['slug']) }}">
                    {{ $skill['name'] }}
                </a>
            </li>
        @endforeach
    </ul>
    <div> {{-- Centrarlo con css --}}
    {{ $paginatedSkills->links() }}
    </div>
</div>
@endsection