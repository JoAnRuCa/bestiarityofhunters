@section('title', 'Recovery')

<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#F5F5F0]">
        
        {{-- Contenedor Principal #F4EBD0 --}}
        <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-[#F4EBD0] border border-[#6B8E23]/20 shadow-xl rounded-2xl relative overflow-hidden">
            
            {{-- Decoración de esquina --}}
            <div class="absolute top-0 right-0 w-24 h-24 bg-[#6B8E23]/5 rounded-bl-full -mr-12 -mt-12 border-b border-l border-[#6B8E23]/10"></div>

            {{-- Cabecera --}}
            <div class="flex flex-col items-center mb-6 relative">
                <h1 class="text-3xl font-black uppercase italic text-[#2F2F2F] tracking-tighter leading-none">
                    Key Recovery
                </h1>
                <div class="w-16 h-0.5 bg-[#C67C48] my-3"></div>
            </div>

            <div class="mb-6 text-[11px] font-bold uppercase italic text-gray-600 leading-relaxed text-center px-2">
                {{ __('Lost your access key? Provide your authorized email and we will dispatch a recovery link to your scoutflies.') }}
            </div>

            <x-auth-session-status class="mb-4 font-bold text-sm text-green-700 text-center" :status="session('status')" />

            <x-auth-validation-errors class="mb-4" :errors="$errors" />

            <form id="recovery-form" method="POST" action="{{ route('password.email') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="email" class="block text-[11px] font-black uppercase tracking-widest text-[#2F2F2F] mb-1.5 italic ml-1">
                        {{ __('Authorized Email') }}
                    </label>
                    <input id="email-field" type="email" name="email" value="{{ old('email') }}" required autofocus 
                           class="block w-full px-4 py-3 bg-white/60 border border-[#6B8E23]/20 rounded-xl focus:ring-4 focus:ring-[#6B8E23]/10 focus:border-[#6B8E23] transition-all duration-300 outline-none italic text-gray-700 shadow-sm"
                           placeholder="hunter@archives.com" />
                </div>

                <div id="status-msg" class="hidden animate-fade-in">
                    <p class="text-[10px] font-black uppercase tracking-tighter text-[#6B8E23] italic text-center bg-[#6B8E23]/5 py-2 rounded-lg border border-[#6B8E23]/10">
                        Check your inbox. If it exists, it has been sent.
                    </p>
                </div>

                <div class="flex flex-col gap-4">
                    <button type="submit" id="submit-btn"
                            class="group relative w-full py-4 bg-[#6B8E23] hover:bg-[#5a7a1d] text-white font-black uppercase italic tracking-widest rounded-xl shadow-lg shadow-[#6B8E23]/20 transform transition-all active:scale-[0.98] duration-200">
                        {{ __('Send Recovery Link') }}
                    </button>

                    <a href="{{ route('login') }}" class="text-center text-[10px] font-black uppercase text-[#C67C48] hover:text-[#6B8E23] transition-colors tracking-widest italic">
                        {{ __('Return to Gate') }}
                    </a>
                </div>
            </form>
        </div>

        <p class="mt-8 text-[9px] font-bold uppercase tracking-widest text-[#2F2F2F]/40 italic">
            &copy; {{ date('Y') }} — Monster Hunter Build Architect
        </p>
    </div>

<script src="{{ asset('js/falseReset.js') }}"></script>
</x-guest-layout>