@section('title', 'Login')

<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#F5F5F0]">
        
        {{-- Contenedor Principal con el color solicitado #F4EBD0 --}}
        <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-[#F4EBD0] border border-[#6B8E23]/20 shadow-xl rounded-2xl relative overflow-hidden">
            
            {{-- Decoración de esquina estilo heráldica (Sutil) --}}
            <div class="absolute top-0 right-0 w-24 h-24 bg-[#6B8E23]/5 rounded-bl-full -mr-12 -mt-12 border-b border-l border-[#6B8E23]/10"></div>

            {{-- Cabecera con Título --}}
            <div class="flex flex-col items-center mb-10 relative">
                <h1 class="text-3xl font-black uppercase italic text-[#2F2F2F] tracking-tighter leading-none">
                    Hunter Login
                </h1>
                <div class="w-16 h-0.5 bg-[#C67C48] my-3"></div>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#C67C48] italic">
                    The archives await your command
                </p>
            </div>

            <x-auth-session-status class="mb-4 font-bold text-sm text-green-700" :status="session('status')" />

            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-[11px] font-black uppercase tracking-widest text-[#2F2F2F] mb-1.5 italic ml-1">
                        {{ __('Authorized Email') }}
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
                           class="block w-full px-4 py-3 bg-white/60 border border-[#6B8E23]/20 rounded-xl focus:ring-4 focus:ring-[#6B8E23]/10 focus:border-[#6B8E23] transition-all duration-300 outline-none italic text-gray-700 shadow-sm placeholder:text-gray-400"
                           placeholder="hunter@archives.com" />
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5 ml-1">
                        <label for="password" class="block text-[11px] font-black uppercase tracking-widest text-[#2F2F2F] italic">
                            {{ __('Access Code') }}
                        </label>
                        @if (Route::has('password.request'))
                            <a class="text-[10px] font-bold uppercase text-[#C67C48] hover:text-[#6B8E23] transition-colors tracking-tighter" href="{{ route('password.request') }}">
                                {{ __('Recover Key?') }}
                            </a>
                        @endif
                    </div>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                           class="block w-full px-4 py-3 bg-white/60 border border-[#6B8E23]/20 rounded-xl focus:ring-4 focus:ring-[#6B8E23]/10 focus:border-[#6B8E23] transition-all duration-300 outline-none italic text-gray-700 shadow-sm"
                           placeholder="••••••••" />
                </div>

                <div class="flex items-center justify-between pl-1">
                    <label for="remember_me" class="inline-flex items-center group cursor-pointer">
                        <input id="remember_me" type="checkbox" 
                               class="w-4 h-4 rounded border-[#6B8E23]/30 text-[#6B8E23] shadow-sm focus:ring-[#6B8E23]/50 transition-colors cursor-pointer" 
                               name="remember">
                        <span class="ml-2 text-[11px] font-bold uppercase text-[#2F2F2F]/60 group-hover:text-[#2F2F2F] transition-colors italic">
                            {{ __('Keep session active') }}
                        </span>
                    </label>
                </div>

                {{-- Botón de Acción --}}
                <div class="pt-2">
                    <button type="submit" 
                            class="group relative w-full py-4 bg-[#6B8E23] hover:bg-[#5a7a1d] text-white font-black uppercase italic tracking-widest rounded-xl shadow-lg shadow-[#6B8E23]/20 transform transition-all active:scale-[0.98] duration-200">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            {{ __('Unlock Archives') }}
                        </span>
                    </button>
                </div>

                {{-- Registro --}}
                <div class="text-center pt-4 border-t border-[#6B8E23]/10">
                    <p class="text-[11px] text-gray-500 font-bold uppercase italic">
                        Not yet a registered hunter?
                        <a href="{{ route('register') }}" class="text-[#C67C48] hover:text-[#6B8E23] transition-colors ml-1 underline underline-offset-4 decoration-[#C67C48]/30">
                            Create new License
                        </a>
                    </p>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <p class="mt-8 text-[9px] font-bold uppercase tracking-widest text-[#2F2F2F]/40 italic">
            &copy; {{ date('Y') }} — Bestiarity of hunters
        </p>
    </div>
</x-guest-layout>