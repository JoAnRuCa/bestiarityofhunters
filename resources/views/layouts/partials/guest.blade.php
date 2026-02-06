@extends('layouts.master')
@section('title', 'Guest')

@section('content')

<div class="w-[40%] mx-auto mt-12 p-8 bg-[#F4EBD0] rounded-lg shadow-lg text-center">
    <h2 class="text-3xl font-bold text-[#6B8E23] mb-4">You must be logged in</h2>
    <p class="text-lg mb-6">Please log in to access this section.</p>

    <a href="{{ route('login') }}"
       class="px-6 py-3 bg-[#6B8E23] text-white font-bold rounded-lg hover:bg-[#58751C] transition">
        Log In
    </a>
</div>


@endsection
