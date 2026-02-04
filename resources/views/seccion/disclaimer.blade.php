@extends('layouts.master')
@section('title', 'Disclaimer')

@section('content')

<div class="max-w-3xl mx-auto mt-12 mb-12 p-8 rounded-lg shadow-sm"
     style="background-color: #F4EBD0;">

  {{-- Main Title --}}
  <h1 class="text-4xl md:text-5xl font-extrabold mb-6 border-b pb-4 text-[#6B8E23]">
      Disclaimer
  </h1>

  {{-- Unified text size --}}
  <div class="text-lg leading-relaxed text-slate-700">

    {{-- Section 1 --}}
    <section>
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        General Information
      </h3>

      <p>
        The information provided on <strong>Bestiarity of Hunters</strong> is for general informational and community‑driven purposes only. While we strive to maintain accurate and up‑to‑date data, we cannot guarantee that all content is free of errors, omissions, or outdated information.
      </p>
    </section>

    <br>

    {{-- Section 2 --}}
    <section>
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        No Professional Advice
      </h3>

      <p>
        All builds, guides, and recommendations shared on this platform—whether created by us or by the community—are based on personal experience and gameplay preferences. They should not be considered professional advice or definitive solutions.
      </p>
    </section>

    <br>

    {{-- Section 3 --}}
    <section>
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        User‑Generated Content
      </h3>

      <p>
        Bestiarity of Hunters allows users to create and share builds, comments, and guides. We are not responsible for the accuracy, reliability, or opinions expressed in user‑generated content. Each user is solely responsible for the material they publish.
      </p>
    </section>

    <br>

    {{-- Section 4 --}}
    <section>
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        External Links
      </h3>

      <p>
        This website may contain links to third‑party websites or resources. We do not endorse, control, or take responsibility for the content, policies, or practices of any external sites.
      </p>
    </section>

    <br>

    {{-- Section 5 --}}
    <section>
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        Limitation of Liability
      </h3>

      <p>
        By using this website, you agree that <strong>Bestiarity of Hunters</strong> is not liable for any damages, losses, or issues arising from the use of our tools, data, or community content. All use is at your own discretion and risk.
      </p>
    </section>
    <br>      
    {{-- Final Section --}}
    <section class="text-center mt-10">
      <p class="text-xs uppercase tracking-widest text-slate-500">
        Disclaimer • Bestiarity of Hunters
      </p>
    </section>

  </div> {{-- end text-lg wrapper --}}

</div>

@endsection
