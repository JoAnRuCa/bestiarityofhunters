<header class="bg-[#F4EBD0] px-6 py-3 flex flex-wrap items-center justify-between" style="font-family: 'Inter', sans-serif;">
    
    {{-- Logo --}}
    <div class="w-32 md:w-40 flex-shrink-0">
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-auto object-contain">
        </a>
    </div>

    {{-- Navegación de Administración --}}
    <nav class="flex items-center">
        <ul class="flex flex-wrap gap-6 text-gray-800 font-bold items-center uppercase text-xs tracking-widest">
            <li class="text-[#6B8E23] border-r border-gray-300 pr-4">Research Division</li>
            
            <li><a href="{{ route('admin.users.index') }}" class="hover:text-[#C67C48] transition">Hunters</a></li>
            
            {{-- Dropdown de Contenido --}}
            <li class="relative group">
                <a href="#" class="hover:text-[#C67C48] transition flex items-center">
                    Content Management
                    <span class="ml-1 text-[8px]">▼</span>
                </a>
                <div class="absolute left-0 top-full pt-2 hidden group-hover:block z-50">
                    <ul class="bg-[#F4EBD0] shadow-lg rounded-md w-48 py-2 text-sm border border-[#6B8E23]/20">
                        <li><a href="{{ route('admin.guides.index') }}" class="block px-4 py-2 hover:bg-[#6B8E23]/10">Guides</a></li>
                        <li><a href="{{ route('admin.guideComments.index') }}" class="block px-4 py-2 hover:bg-[#6B8E23]/10">Guide Comments</a></li>
                        <li><a href="{{ route('admin.builds.index') }}" class="block px-4 py-2 hover:bg-[#6B8E23]/10">Builds</a></li>
                        <li><a href="{{ route('admin.buildComments.index') }}" class="block px-4 py-2 hover:bg-[#6B8E23]/10">Build Comments</a></li>
                        <li><a href="{{ route('admin.tags.index') }}" class="block px-4 py-2 hover:bg-[#6B8E23]/10">Tags</a></li>
                    </ul>
                </div>
            </li>
        </ul>
    </nav>

    {{-- Zona derecha --}}
    <div class="flex items-center gap-4">
        {{-- Botón para volver a la web pública --}}
        <a href="{{ route('home') }}" 
           class="text-xs font-black uppercase bg-[#6B8E23] text-[#F4EBD0] px-4 py-2 rounded-md hover:bg-[#2F2F2F] transition shadow-sm">
            Back to the Forge
        </a>

        @auth
            <div class="relative group">
                <button class="font-bold text-[#2F2F2F] flex items-center text-sm">
                    {{ Auth::user()->name }}
                    <span class="ml-1 text-[8px]">▼</span>
                </button>
                <div class="absolute right-0 top-full pt-2 hidden group-hover:block z-50">
                    <ul class="bg-[#F4EBD0] shadow-lg rounded-md w-40 py-2 text-sm border border-[#6B8E23]/20">
                        <li>
                            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                               class="block px-4 py-2 text-red-600 hover:bg-red-50">
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