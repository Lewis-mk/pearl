@extends('layouts.public')

@section('title', 'Official Certificate Verification Registry — Pearl Training Institute')

@section('content')
    <section class="bg-slate-900 text-white py-16 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center max-w-3xl">
            <span class="text-xs font-bold text-teal-400 uppercase tracking-widest block mb-2">Public Registry</span>
            <h1 class="font-heading font-black text-3xl sm:text-5xl text-white">Certificate Verification System</h1>
            <p class="text-sm text-slate-300 mt-3 font-normal">
                Verify the authenticity of any completion certificate issued by Pearl Training Institute Kenya.
            </p>
        </div>
    </section>

    <section class="py-16 bg-slate-50 min-h-[60vh]">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Search Bar Form -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-md mb-10">
                <form action="{{ route('certificate.verify') }}" method="GET" class="space-y-4">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Enter Certificate Number or Verification Hash Code</label>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="code" value="{{ $code }}" required placeholder="e.g. PTI-CERT-2026-00001 or VER-PTI-XXXXX" class="flex-1 px-4 py-3.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50 font-mono">
                        <button type="submit" class="px-7 py-3.5 rounded-xl font-heading font-bold text-sm text-white bg-pearl-600 hover:bg-pearl-700 shadow-md transition shrink-0">
                            Verify Authenticity &rarr;
                        </button>
                    </div>
                    <div class="text-[11px] text-slate-400">
                        * Verification codes are printed on the bottom footer of all official digital and physical certificates.
                    </div>
                </form>
            </div>

            <!-- Verification Result Card -->
            @if(!empty($code))
                @if($certificate)
                    <div class="bg-white p-8 rounded-3xl border-2 border-emerald-500 shadow-xl space-y-6 relative overflow-hidden">
                        <div class="absolute top-0 right-0 bg-emerald-600 text-white text-xs font-bold uppercase px-4 py-1 rounded-bl-xl tracking-wider">
                            ✓ Verified Genuine
                        </div>

                        <div class="flex items-center space-x-4 border-b border-slate-100 pb-6">
                            <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-3xl">🎓</div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-emerald-700">Official Graduation Record</span>
                                <h3 class="font-heading font-black text-2xl text-slate-900">{{ $certificate->student_full_name }}</h3>
                                <span class="text-xs font-mono text-slate-500 font-semibold">Adm No: {{ $certificate->student->admission_number ?? 'N/A' }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-slate-400 font-semibold block uppercase text-[10px]">Program Completed</span>
                                <strong class="text-sm font-bold text-slate-900 block mt-0.5">{{ $certificate->course_title }}</strong>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-slate-400 font-semibold block uppercase text-[10px]">Grade Awarded</span>
                                <strong class="text-sm font-bold text-emerald-700 block mt-0.5">{{ $certificate->grade }}</strong>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-slate-400 font-semibold block uppercase text-[10px]">Certificate Number</span>
                                <strong class="text-sm font-mono text-slate-900 block mt-0.5">{{ $certificate->certificate_number }}</strong>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                                <span class="text-slate-400 font-semibold block uppercase text-[10px]">Date of Completion</span>
                                <strong class="text-sm text-slate-900 block mt-0.5">{{ $certificate->completion_date->format('d F Y') }}</strong>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-teal-50 border border-teal-100 text-[11px] text-teal-900 flex items-center justify-between">
                            <span>Issued By: <strong>Pearl Training Institute Academic Board</strong></span>
                            <span>Verification Code: <strong class="font-mono">{{ $certificate->verification_code }}</strong></span>
                        </div>
                    </div>
                @else
                    <div class="bg-rose-50 border border-rose-200 p-8 rounded-3xl text-center space-y-3">
                        <div class="text-4xl">⚠️</div>
                        <h3 class="font-heading font-bold text-xl text-rose-900">Certificate Not Found</h3>
                        <p class="text-xs text-rose-800 max-w-md mx-auto leading-relaxed">
                            No active certificate matches the code "<span class="font-mono font-bold">{{ $code }}</span>". Please double-check the spelling or contact academic administration at <strong class="underline">admissions@pearlinstitute.com</strong>.
                        </p>
                    </div>
                @endif
            @endif

        </div>
    </section>
@endsection
