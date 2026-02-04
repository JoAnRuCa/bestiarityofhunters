@extends('layouts.master')
@section('title', 'About Us')

@section('content')

<div class="max-w-3xl mx-auto mt-12 mb-12 p-8 rounded-lg shadow-sm"
     style="background-color: #F4EBD0;">

  {{-- Main Title --}}
  <h1 class="text-4xl md:text-5xl font-extrabold mb-6 border-b pb-4 text-[#6B8E23]">
      About Us
  </h1>

  {{-- Unified text size --}}
  <div class="text-lg leading-relaxed text-slate-700">

    <p class="mb-6">
      <strong class="text-[#C67C48]">Bestiarity of Hunters</strong> was created with a clear purpose: to offer hunters a modern, accessible, and powerful tool to build, share, and refine their loadouts. We are a small team of developers and passionate players who believe in the value of shared knowledge and community-driven improvement.
    </p>

    <p class="mb-6">
      Our goal is to build a space where every hunter—new or experienced—can find inspiration, learn new strategies, and contribute their expertise. We believe in an open, collaborative, and toxicity‑free environment where creativity thrives.
    </p>

    <hr class="my-8 border-slate-100">

    {{-- Mission --}}
    <section class="mb-8">
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        Our Mission
      </h3>

      <p class="mb-4">
        We want Bestiarity of Hunters to become the ultimate tool for planning builds, analyzing skills, and discovering new combinations. Our mission is built on three core pillars:
      </p>

      <ul class="space-y-3 ml-5">
        <li class="pl-3 border-l-2 border-slate-300"><strong class="text-slate-900">Accessibility:</strong> A clean, fast, and intuitive interface for all players.</li>
        <li class="pl-3 border-l-2 border-slate-300"><strong class="text-slate-900">Collaboration:</strong> A community where hunters can share builds and learn from each other.</li>
        <li class="pl-3 border-l-2 border-slate-300"><strong class="text-slate-900">Transparency:</strong> Clear information, no hidden mechanics or barriers.</li>
      </ul>
    </section>
        <br>
    {{-- What We Do --}}
    <section class="mb-8">
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        What We Do
      </h3>

      <ul class="grid grid-cols-1 md:grid-cols-2 gap-3 ml-4">
        <li class="flex items-start"><span class="text-[#C67C48] mr-2">✔</span> Complete database of skills, armors, weapons, and decorations.</li>
        <li class="flex items-start"><span class="text-[#C67C48] mr-2">✔</span> Powerful and intuitive build editor.</li>
        <li class="flex items-start"><span class="text-[#C67C48] mr-2">✔</span> Tools to compare and optimize equipment sets.</li>
        <li class="flex items-start"><span class="text-[#C67C48] mr-2">✔</span> Community‑driven guides and resources.</li>
      </ul>
    </section>
        <br>
    {{-- Commitment --}}
    <section class="mb-8">
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        Our Commitment
      </h3>

      <div class="bg-[#6B8E23]/5 p-5 rounded-xl border border-[#6B8E23]/10">
        <ul class="space-y-3">
          <li><strong class="text-[#C67C48]">Community First:</strong> We design every feature with the hunter community in mind.</li>
          <li><strong class="text-[#C67C48]">No Toxicity:</strong> We promote a respectful and welcoming environment for all players.</li>
          <li><strong class="text-[#C67C48]">Continuous Improvement:</strong> We constantly refine our tools based on user feedback.</li>
        </ul>
      </div>
    </section>
        <br>
    <hr class="my-8 border-slate-100">

    <section class="text-center">
      <p class="text-xs uppercase tracking-widest text-slate-500">
        Bestiarity of Hunters • Built by hunters, for hunters
      </p>
    </section>

  </div> {{-- end text-lg wrapper --}}

</div>

@endsection
