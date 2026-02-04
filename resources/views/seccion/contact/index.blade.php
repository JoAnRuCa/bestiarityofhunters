@extends('layouts.master')
@section('title', 'Contact Us')

@section('content')

<div class="max-w-3xl mx-auto mt-12 mb-12 p-8 rounded-lg shadow-sm"
     style="background-color: #F4EBD0;">

    <h1 class="text-4xl md:text-5xl font-extrabold mb-6 border-b pb-4 text-[#6B8E23]">
        Contact Us
    </h1>

    <div class="text-lg leading-relaxed text-slate-700">

        @if(session('success'))
            <p class="text-green-600 font-semibold mb-4">
                {{ session('success') }}
            </p>
        @endif

        <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Subject --}}
            <div>
                <label class="block font-semibold mb-1">Subject</label>
                <input type="text" name="subject"
                       class="w-full p-3 rounded border border-slate-300"
                       required>
            </div>

            {{-- Message --}}
            <div>
                <label class="block font-semibold mb-1">Message</label>
                <textarea name="message" rows="5"
                          class="w-full p-3 rounded border border-slate-300"
                          required></textarea>
            </div>

            <button type="submit"
                    class="px-6 py-3 bg-[#6B8E23] text-white font-bold rounded-lg hover:bg-[#58751C]">
                Send Message
            </button>
        </form>

    </div>

</div>

@endsection
