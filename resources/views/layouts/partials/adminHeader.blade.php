<header class="bg-[#F4EBD0] px-6 py-3 flex flex-wrap items-center justify-between" style="font-family: 'Inter', sans-serif;">
    
    {{-- Logo --}}
    <div class="w-32 md:w-40 flex-shrink-0">
        <a href="{{ route('admin.users.index') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-auto object-contain">
        </a>
    </div>

    {{-- Navegación de Administración --}}
    <nav class="flex items-center">
        <ul class="flex flex-wrap gap-5 text-gray-800 font-semibold items-center">
            
            {{-- Indicador de Sección --}}
            <li class="text-[#6B8E23] border-r border-gray-300 pr-4 font-bold tracking-widest text-[10px] uppercase italic">
                Research Division
            </li>

            <li><a href="{{ route('admin.users.index') }}" class="hover:text-black transition">Hunters</a></li>
            <li><a href="{{ route('admin.guides.index') }}" class="hover:text-black transition">Guides</a></li>
            <li><a href="{{ route('admin.builds.index') }}" class="hover:text-black transition">Builds</a></li>

            {{-- Dropdown de Comentarios (Estilo Database) --}}
            <li class="relative group">
                <a href="#" class="hover:text-black transition flex items-center">
                    Comments
                    <span class="ml-1 text-xs">▼</span>
                </a>
                {{-- Contenedor hover estable --}}
                <div class="absolute left-0 top-full pt-2 hidden group-hover:block z-50">
                    <ul class="bg-[#F4EBD0] shadow-lg rounded-md w-48 py-2 text-sm transition-all duration-150 ease-out border border-[#6B8E23]/10">
                        <li><a href="{{ route('admin.guideComments.index') }}" class="block px-4 py-2 hover:bg-[#6B8E23]/10 hover:text-[#6B8E23]">Guide comments</a></li>
                        <li><a href="{{ route('admin.buildComments.index') }}" class="block px-4 py-2 hover:bg-[#6B8E23]/10 hover:text-[#6B8E23]">Build comments</a></li>
                    </ul>
                </div>
            </li>

            <li><a href="{{ route('admin.tags.index') }}" class="hover:text-black transition">Tags</a></li>
        </ul>
    </nav>

    {{-- Zona derecha --}}
    <div class="flex items-center gap-6">
        
        {{-- Botón Volver (Estilo Botón Hunter) --}}
        <a href="{{ route('home') }}" 
           class="bg-[#6B8E23] text-[#F4EBD0] border-2 border-[#6B8E23] px-4 py-1.5 rounded-md font-semibold text-xs uppercase tracking-wider hover:bg-[#C67C48] hover:border-[#C67C48] transition">
            Back to the Forge
        </a>

        @auth
            {{-- Dropdown del Usuario --}}
            <div class="relative group">
                <a href="#" class="font-semibold text-[#2F2F2F] hover:text-black transition flex items-center">
                    {{ Auth::user()->name }}
                    <span class="ml-1 text-xs">▼</span>
                </a>

                {{-- Contenedor hover estable --}}
                <div class="absolute right-0 top-full pt-2 hidden group-hover:block z-50">
                    <ul class="bg-[#F4EBD0] shadow-lg rounded-md w-40 py-2 text-sm transition-all duration-150 ease-out border border-[#6B8E23]/10">
                        {{-- Opciones de usuario --}}
                        <li><a href="{{ route('profile') }}" class="block px-4 py-2 hover:bg-[#6B8E23]/10 hover:text-[#6B8E23]">Profile</a></li>
                        <hr class="my-1 border-[#6B8E23]/10">
                        <li>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                               class="block px-4 py-2 hover:bg-red-50 hover:text-red-600">
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
        @endauth
    </div>
</header>