@extends('layouts.master')
@section('title', 'Armors')

@section('content')

<div>
    <h1>Armors</h1>

    <form method="GET" action="{{ route('armors.index') }}">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search armor...">
        <button type="submit">Search</button>
    </form>

    <ul>
        @foreach ($paginatedArmor as $armor)
            <li>
                <a href="{{ route('armors.show', $armor['slug']) }}">
                    {{ $armor['name'] }}
                </a>
            </li>
        @endforeach
    </ul>

    <div>
        {{ $paginatedArmor->links() }}
    </div>
</div>

@endsection
