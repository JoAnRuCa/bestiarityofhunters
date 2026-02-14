<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  {{-- Favicon --}}
  <link rel="icon" type="image/png" href="{{ asset('images/favicon.png') }}">
  <link rel="apple-touch-icon" href="{{ asset('images/favicon.png') }}"> {{-- Para móviles --}}

  <title>@yield('title') | Bestiarity of Hunters</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  
  {{-- Estilos con control de caché --}}
  <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('css/master.css') }}?v={{ time() }}">
</head>

<body class="flex flex-col min-h-screen" style="font-family: 'Inter', sans-serif;">
  
  @include('layouts.partials.header')

  <main class="flex-1">
    @yield('content')
  </main>

  @include('layouts.partials.footer')


  @yield('scripts')
</body>

</html>
