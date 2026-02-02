<header>
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="Logo">
    </div>

    <nav>
        <ul>
            <li><a href="{{ route('home') }}">Home</a></li>

            <li class="dropdown">
                <a href="#">Database</a>
                <ul class="dropdown-menu">
                    <li><a href="{{ route('skills.index') }}">Skills</a></li>
                    <li><a href="{{ route('armors.index') }}">Armors</a></li>
                    <li><a href="{{ route('weapons.index') }}">Weapons</a></li>
                    <li><a href="{{ route('decorations.index') }}">Decorations</a></li>
                    <li><a href="{{ route('charms.index') }}">Charms</a></li>
                </ul>
            </li>

            <li><a href="">Builds</a></li>
            <li><a href="{{ route('build.editor') }}">Build editor</a></li>
            <li><a href="">Guides</a></li>
            <li><a href="">Guide editor</a></li>
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
