<header class="bg-[#F4EBD0] px-6 py-3 flex flex-wrap items-center justify-between" style="font-family: 'Inter', sans-serif;">
    
    {{-- Logo --}}
    <div class="w-32 md:w-40 flex-shrink-0">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-auto object-contain">
    </div>

    {{-- Navegación --}}
    <nav class="mt-6 md:mt-10">
        <ul class="flex flex-wrap gap-5 text-gray-800 font-semibold">

            <li><a href="{{ route('home') }}" class="hover:text-black transition">Home</a></li>

            {{-- Dropdown Database --}}
<li class="relative group">
    <a href="#" class="hover:text-black transition flex items-center pb-3">
        Database
        <span class="ml-1 text-xs">▼</span>
    </a>

    <ul class="absolute left-0 mt-0 hidden group-hover:block bg-[#F4EBD0] shadow-lg rounded-md w-44 py-2 text-sm z-50
               pt-3">
        <li><a href="{{ route('skills.index') }}" class="block px-4 py-2 hover:bg-gray-200">Skills</a></li>
        <li><a href="{{ route('armors.index') }}" class="block px-4 py-2 hover:bg-gray-200">Armors</a></li>
        <li><a href="{{ route('weapons.index') }}" class="block px-4 py-2 hover:bg-gray-200">Weapons</a></li>
        <li><a href="{{ route('decorations.index') }}" class="block px-4 py-2 hover:bg-gray-200">Decorations</a></li>
        <li><a href="{{ route('charms.index') }}" class="block px-4 py-2 hover:bg-gray-200">Charms</a></li>
    </ul>
</li>



            <li><a href="#" class="hover:text-black transition">Builds</a></li>
            <li><a href="{{ route('build.editor') }}" class="hover:text-black transition">Build editor</a></li>
            <li><a href="#" class="hover:text-black transition">Guides</a></li>
            <li><a href="{{ route('guide.editor') }}" class="hover:text-black transition">Guide editor</a></li>

        </ul>
    </nav>

    {{-- Zona derecha: Login/Register o Usuario --}}
    <div class="mt-4 md:mt-0 flex items-center gap-3">

        @guest
            <a href="{{ route('login') }}"
               class="text-[#6B8E23] border-2 border-[#6B8E23] px-4 py-1.5 rounded-md font-semibold hover:bg-[#6B8E23] hover:text-[#F4EBD0] transition">
               Login
            </a>

            <a href="{{ route('register') }}"
               class="bg-[#6B8E23] text-[#F4EBD0] border-2 border-[#6B8E23] px-4 py-1.5 rounded-md font-semibold hover:bg-[#C67C48] hover:border-[#C67C48] transition">
               Register
            </a>
        @endguest

        @auth
            <div class="relative group">
    <a href="#" class="font-semibold text-gray-800 hover:text-black transition flex items-center pb-3">
        {{ Auth::user()->name }}
        <span class="ml-1 text-xs">▼</span>
    </a>

    <ul class="absolute right-0 mt-0 hidden group-hover:block bg-[#F4EBD0] shadow-lg rounded-md w-32 py-2 text-sm z-50 pt-3">
        <li>
            <a href="#"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="block px-4 py-2 hover:bg-gray-200">
               Logout
            </a>
        </li>
    </ul>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>


            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        @endauth

    </div>

</header>
