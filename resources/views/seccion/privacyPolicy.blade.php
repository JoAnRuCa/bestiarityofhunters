@extends('layouts.master')
@section('title', 'Privacy Policy')

@section('content')

<div class="max-w-3xl mx-auto mt-12 mb-12 p-8 rounded-lg shadow-sm"
     style="background-color: #F4EBD0;">

  {{-- Título principal (único más grande) --}}
  <h1 class="text-4xl md:text-5xl font-extrabold mb-6 border-b pb-4 text-[#6B8E23]">
      Privacy Policy
  </h1>

  {{-- Contenido general con tamaño uniforme --}}
  <div class="text-lg leading-relaxed text-slate-700">

    <p class="mb-6">
      At <strong class="text-[#C67C48]">Bestiarity of Hunters</strong>, our mission is to provide high-quality tools for hunters to plan and share their builds. In line with our values of <span class="font-semibold text-slate-900 underline decoration-[#6B8E23]/40">Transparency</span> and <span class="font-semibold text-slate-900 underline decoration-[#6B8E23]/40">Respect</span>, we are committed to protecting your data and being clear about how we use it.
    </p>

    <hr class="my-8 border-slate-100">

    {{-- Section 1 --}}
    <section class="mb-8">
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        1. Information We Collect
      </h3>

      <ul class="space-y-3 ml-5">
        <li class="pl-3 border-l-2 border-slate-300"><strong class="text-slate-900">Account Data:</strong> If you create an account, we collect your username and email address to manage your profile.</li>
        <li class="pl-3 border-l-2 border-slate-300"><strong class="text-slate-900">User Content:</strong> We store the builds you create, your votes, and any comments you share to help the community grow.</li>
        <li class="pl-3 border-l-2 border-slate-300"><strong class="text-slate-900">Technical Data:</strong> We use essential cookies and log your IP address to ensure site stability and security.</li>
      </ul>
    </section>
        <br>
    {{-- Section 2 --}}
    <section class="mb-8">
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        2. How We Use Your Data
      </h3>

      <p class="mb-4 italic text-slate-600">Everything we do is designed to fuel your creativity and passion for the game:</p>

      <ul class="grid grid-cols-1 md:grid-cols-2 gap-3 ml-4">
        <li class="flex items-start"><span class="text-[#C67C48] mr-2">✔</span> Save and share custom builds.</li>
        <li class="flex items-start"><span class="text-[#C67C48] mr-2">✔</span> Foster community feedback.</li>
        <li class="flex items-start"><span class="text-[#C67C48] mr-2">✔</span> Improve tool accessibility.</li>
        <li class="flex items-start"><span class="text-[#C67C48] mr-2">✔</span> Anonymous usage analysis.</li>
      </ul>
    </section>
        <br>
    {{-- Section 3 --}}
    <section class="mb-8">
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        3. Data Sharing & Transparency
      </h3>

      <div class="bg-[#6B8E23]/5 p-5 rounded-xl border border-[#6B8E23]/10">
        <ul class="space-y-3">
          <li><strong class="text-[#C67C48]">No Sale of Data:</strong> We do not sell your personal information to third parties.</li>
          <li><strong class="text-[#C67C48]">Community Visibility:</strong> Shared builds are public by default to promote learning. Privacy can be managed in settings.</li>
        </ul>
      </div>
    </section>
        <br>
    {{-- Section 4 & 5 --}}
    <div class="space-y-8">
      <section>
        <h3 class="text-xl font-bold mb-3 text-[#6B8E23]">4. Security</h3>
        <p>
          To maintain a <span class="font-bold">toxicity-free environment</span>, we monitor interactions and use industry-standard security to protect your hunting data.
        </p>
      </section>
        <br>
      <section>
        <h3 class="text-xl font-bold mb-3 text-[#6B8E23]">5. Your Rights</h3>
        <p>
          Access, update, or delete your account and builds at any time through your profile settings or by contacting us.
        </p>
      </section>
    </div>
        <br>
    <hr class="my-8 border-slate-100">

    <section class="text-center">
      <p class="text-xs uppercase tracking-widest text-slate-500">
        Last updated: February 2026 • Bestiarity of Hunters
      </p>
    </section>

  </div> {{-- cierre del text-lg wrapper --}}

</div>

@endsection
