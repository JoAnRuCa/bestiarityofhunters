<header>
    <div class="logo">
        <img src="" alt="Logo">
    </div>

    <nav>
        <ul>
            <li><a href="/">Inicio</a></li>

            <li class="dropdown">
                <a href="#">Categorías</a>
                <ul class="dropdown-menu">
                    <li><a href="#">Opción 1</a></li>
                    <li><a href="#">Opción 2</a></li>
                    <li><a href="#">Opción 3</a></li>
                </ul>
            </li>

            <li><a href="/productos">Productos</a></li>
            <li><a href="/sucursales">Sucursales</a></li>
            <li><a href="/contacto">Contacto</a></li>
        </ul>
    </nav>

    {{-- Zona derecha del header --}}
    <div>
        @guest
            <a href="{{ route('login') }}" class="login">Login</a>
            <a href="{{ route('register') }}" class="register">Register</a>
        @endguest

@auth
  <div class="dropdown dropdown-user">
    <a href="#">{{ Auth::user()->name }}</a>
    <ul class="dropdown-menu">
      <li>
        <a href="#"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          Logout
        </a>
      </li>
    </ul>
  </div>

  <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">
    @csrf
  </form>
@endauth



    </div>
</header>
