@section('title', 'Register')

<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#F5F5F0]">
        
        {{-- Contenedor Principal #F4EBD0 --}}
        <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-[#F4EBD0] border border-[#6B8E23]/20 shadow-xl rounded-2xl relative overflow-hidden">
            
            {{-- Decoración de esquina --}}
            <div class="absolute top-0 right-0 w-24 h-24 bg-[#6B8E23]/5 rounded-bl-full -mr-12 -mt-12 border-b border-l border-[#6B8E23]/10"></div>

            {{-- Cabecera --}}
            <div class="flex flex-col items-center mb-8 relative">
                <h1 class="text-3xl font-black uppercase italic text-[#2F2F2F] tracking-tighter leading-none">
                    New License
                </h1>
                <div class="w-16 h-0.5 bg-[#C67C48] my-3"></div>
                <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#C67C48] italic text-center">
                    Join the ranks of the elite hunters
                </p>
            </div>

            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <form method="POST" action="{{ route('register') }}" class="space-y-5" novalidate>
                @csrf

                <div>
                    <label for="name" class="block text-[11px] font-black uppercase tracking-widest text-[#2F2F2F] mb-1.5 italic ml-1">
                        {{ __('Hunter Nickname') }}
                    </label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus 
                           class="block w-full px-4 py-3 bg-white/60 border border-[#6B8E23]/20 rounded-xl focus:ring-4 focus:ring-[#6B8E23]/10 focus:border-[#6B8E23] transition-all duration-300 outline-none italic text-gray-700 shadow-sm"
                           placeholder="e.g. Rathalos_Slayer" />
                </div>

                <div>
                    <label for="email" class="block text-[11px] font-black uppercase tracking-widest text-[#2F2F2F] mb-1.5 italic ml-1">
                        {{ __('Contact Method (Email)') }}
                    </label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                           class="block w-full px-4 py-3 bg-white/60 border border-[#6B8E23]/20 rounded-xl focus:ring-4 focus:ring-[#6B8E23]/10 focus:border-[#6B8E23] transition-all duration-300 outline-none italic text-gray-700 shadow-sm"
                           placeholder="hunter@archives.com" />
                </div>

                <div>
                    <label for="password" class="block text-[11px] font-black uppercase tracking-widest text-[#2F2F2F] mb-1.5 italic ml-1">
                        {{ __('Security Key') }}
                    </label>
                    <input id="password" type="password" name="password" required autocomplete="new-password"
                           class="block w-full px-4 py-3 bg-white/60 border border-[#6B8E23]/20 rounded-xl focus:ring-4 focus:ring-[#6B8E23]/10 focus:border-[#6B8E23] transition-all duration-300 outline-none italic text-gray-700 shadow-sm"
                           placeholder="••••••••" />
                </div>

                <div>
                    <label for="password_confirmation" class="block text-[11px] font-black uppercase tracking-widest text-[#2F2F2F] mb-1.5 italic ml-1">
                        {{ __('Verify Key') }}
                    </label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required
                           class="block w-full px-4 py-3 bg-white/60 border border-[#6B8E23]/20 rounded-xl focus:ring-4 focus:ring-[#6B8E23]/10 focus:border-[#6B8E23] transition-all duration-300 outline-none italic text-gray-700 shadow-sm"
                           placeholder="••••••••" />
                </div>

                {{-- Botón de Registro --}}
                <div class="pt-4">
                    <button type="submit" 
                            class="group relative w-full py-4 bg-[#6B8E23] hover:bg-[#5a7a1d] text-white font-black uppercase italic tracking-widest rounded-xl shadow-lg shadow-[#6B8E23]/20 transform transition-all active:scale-[0.98] duration-200">
                        <span class="relative z-10">
                            {{ __('Issue License') }}
                        </span>
                    </button>
                </div>

                {{-- Login Link --}}
                <div class="text-center pt-4 border-t border-[#6B8E23]/10">
                    <p class="text-[11px] text-gray-500 font-bold uppercase italic">
                        Already have an account? 
                        <a href="{{ route('login') }}" class="text-[#C67C48] hover:text-[#6B8E23] transition-colors ml-1 underline underline-offset-4 decoration-[#C67C48]/30">
                            Log in here
                        </a>
                    </p>
                </div>
            </form>
        </div>

        {{-- Footer --}}
        <p class="mt-8 text-[9px] font-bold uppercase tracking-widest text-[#2F2F2F]/40 italic">
            &copy; {{ date('Y') }} — Monster Hunter Build Architect
        </p>
    </div>
</x-guest-layout>