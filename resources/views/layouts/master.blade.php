<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title')</title>
  <link rel="stylesheet" href="{{ asset('css/master.css') }}?v={{ time() }}">
  <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
  <header>
    <div class="logo">
      <img src="" alt="Logo">
    </div>
    <nav>
      <ul>
        <li><a href="MU_Inicio.html">Inicio</a></li>
        <li class="dropdown">
          <a href="#">Categorías</a>
          <ul class="dropdown-menu">
            <li><a href="#">Opción 1</a></li>
            <li><a href="#">Opción 2</a></li>
            <li><a href="#">Opción 3</a></li>
          </ul>
        </li>
        <li><a href="MU_Productos.html">Productos</a></li>
        <li><a href="MU_Sucursales.html">Sucursales</a></li>
        <li><a href="MU_Contacto.html">Contacto</a></li>
      </ul>
    </nav>
    <div>
      <input type="button" class="login" value="Login">
      <input type="button" class="register" value="Register">
    </div>
  </header>
  <main>
    @yield('content')
  </main>
  <footer>
    <div>
      <ul>
        <li><a href="MU_Inicio.html">Inicio</a></li>
        <li><a href="MU_Productos.html">Productos</a></li>
        <li><a href="MU_Sucursales.html">Sucursales</a></li>
        <li><a href="MU_Contacto.html">Contacto</a></li>
      </ul>
    </div>
    <div class="copyright">
      <p>© 2024 Mi Empresa. Todos los derechos reservados.</p>
    </div>
  </footer>
</body>
</html>