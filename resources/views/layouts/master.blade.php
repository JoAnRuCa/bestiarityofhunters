<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title')</title>

  <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ time() }}">
  <link rel="stylesheet" href="{{ asset('css/master.css') }}?v={{ time() }}">
</head>

<body class="flex flex-col min-h-screen" style="font-family: 'Inter', sans-serif;">
  
  @include('layouts.partials.header')

  <main class="flex-1">
    @yield('content')
  </main>

  @include('layouts.partials.footer')

</body>

</html>
