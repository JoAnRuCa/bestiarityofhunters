@extends('layouts.master')
@section('title', 'Terms of Use')

@section('content')

<div class="max-w-3xl mx-auto mt-12 mb-12 p-8 rounded-lg shadow-sm"
     style="background-color: #F4EBD0;">

  {{-- Main Title --}}
  <h1 class="text-4xl md:text-5xl font-extrabold mb-6 border-b pb-4 text-[#6B8E23]">
      Terms of Use
  </h1>

  {{-- Unified text size --}}
  <div class="text-lg leading-relaxed text-slate-700">

    {{-- Section 1 --}}
    <section>
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        Acceptance of Terms
      </h3>

      <p>
        By accessing or using <strong>Bestiarity of Hunters</strong>, you agree to comply with and be bound by these Terms of Use. If you do not agree with any part of these terms, you must discontinue use of the platform.
      </p>
    </section>

    <br>

    {{-- Section 2 --}}
    <section>
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        Use of the Platform
      </h3>

      <p>
        You may use this platform for personal, non‑commercial purposes related to gameplay, build creation, and community interaction. Any misuse, exploitation, or unauthorized access to the platform is strictly prohibited.
      </p>
    </section>

    <br>

    {{-- Section 3 --}}
    <section>
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        User Responsibilities
      </h3>

      <p>
        Users are responsible for the accuracy and legality of the content they publish, including builds, comments, and guides. You agree not to upload harmful, offensive, or misleading content, and to respect other members of the community.
      </p>
    </section>

    <br>

    {{-- Section 4 --}}
    <section>
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        Intellectual Property
      </h3>

      <p>
        All tools, features, and original content on this platform—including databases, UI components, and design elements—are the property of Bestiarity of Hunters. Users retain ownership of their own builds and contributions but grant us permission to display them publicly.
      </p>
    </section>

    <br>

    {{-- Section 5 --}}
    <section>
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        Termination of Access
      </h3>

      <p>
        We reserve the right to suspend or terminate access to the platform for users who violate these Terms of Use, engage in abusive behavior, or compromise the safety and integrity of the community.
      </p>
    </section>

    <br>

    {{-- Section 6 --}}
    <section>
      <h3 class="text-xl font-bold mb-4 flex items-center text-[#6B8E23]">
        <span class="w-1.5 h-6 bg-[#C67C48] rounded-full mr-3"></span>
        Changes to These Terms
      </h3>

      <p>
        We may update or modify these Terms of Use at any time. Continued use of the platform after changes are posted constitutes acceptance of the updated terms.
      </p>
    </section>

    <br>

    {{-- Final Section --}}
    <section class="text-center mt-10">
      <p class="text-xs uppercase tracking-widest text-slate-500">
        Terms of Use • Bestiarity of Hunters
      </p>
    </section>

  </div> {{-- end text-lg wrapper --}}

</div>

@endsection
