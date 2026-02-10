<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title')</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('css/master.css') }}?v={{ time() }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  
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
