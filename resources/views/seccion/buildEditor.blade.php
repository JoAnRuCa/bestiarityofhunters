@extends('layouts.master')
@section('title', 'Build Editor')

@section('content')

<div>
    <h1>Build Editor</h1>

    <hr>

    <h3>Current Build:</h3>
    <div id="selected">
@foreach (['weapon1','weapon2','head','chest','arms','waist','legs','charm'] as $slot)
    <div>
        <strong
            onclick="openSelector('{{ $slot }}')"
            style="cursor:pointer; color:#007bff; text-decoration:underline;"
            onmouseover="this.style.textDecoration='none'"
            onmouseout="this.style.textDecoration='underline'">
            {{ ucfirst($slot) }}
        </strong>

        <span id="{{ $slot }}">—</span>

        <button onclick="clearSlot('{{ $slot }}')">Clear</button>
    </div>
@endforeach



    </div>

    <hr>

    <h3>Total Skills:</h3>
    <div id="skillTotals">—</div>
</div>

{{-- MODAL --}}
<div id="modal" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.6);
    justify-content:center;
    align-items:center;
    z-index:9999;
">
    <div style="
        background:white;
        padding:20px;
        width:400px;
        max-height:80%;
        overflow-y:auto;
        border-radius:8px;
        box-shadow:0 0 20px rgba(0,0,0,0.4);
    ">
        <h3 id="modalTitle">Select Item</h3>

        <input id="searchInput" type="text" placeholder="Search..." style="width:100%; padding:6px; margin-bottom:10px;">

        <div id="modalList"></div>

        <button onclick="closeModal()" style="margin-top:10px;">Close</button>
    </div>
</div>

<script>
    window.BUILD_DATA = {
        weapons: @json($weapons),
        armors: @json($armors),
        charms: @json($charms),
        decorations: @json($decorations),
        skills: @json($skills)
    };
</script>

<script src="{{ asset('js/build-editor.js') }}"></script>
<script>console.log("JS cargado correctamente");</script>


@endsection
