@extends('layouts.master')

@section('title', config('app.name'))

@section('content')
    <div class="font-sans text-gray-900 antialiased">
        {{ $slot }}
    </div>
@endsection
