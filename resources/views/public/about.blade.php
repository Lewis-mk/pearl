@extends('layouts.public')

@section('title', 'About Us — Pearl Training Institute')

@section('content')
    <section class="bg-slate-900 text-white py-16 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <span class="text-xs font-bold text-teal-400 uppercase tracking-widest block mb-2">Our Mission & Values</span>
            <h1 class="font-heading font-black text-3xl sm:text-5xl text-white">About Pearl Training Institute</h1>
            <p class="text-sm text-slate-300 max-w-2xl mt-3 font-normal">
                Dedicated to bridging the vocational skills gap in East Africa through rigorous practical education and digital empowerment.
            </p>
        </div>
    </section>

    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div class="space-y-5">
                    <span class="text-xs font-bold text-pearl-600 uppercase tracking-wider block">Foundational Vision</span>
                    <h2 class="font-heading font-black text-3xl text-slate-900">Empowering Kenyans with Direct Technical Competencies</h2>
                    <p class="text-sm text-slate-700 leading-relaxed">
                        Founded in Nairobi, Pearl Training Institute offers intensive, outcomes-focused short courses in Software Development, UI/UX Design, Computerized Bookkeeping, and Cyber Operations.
                    </p>
                    <p class="text-sm text-slate-700 leading-relaxed">
                        We reject purely theoretical learning. Every course at Pearl is built around real-world projects, live database systems, simulated corporate payrolls, and M-Pesa payment gateways.
                    </p>
                </div>
                <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-md space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-pearl-100 text-pearl-800 flex items-center justify-center font-bold text-lg shrink-0">🎯</div>
                        <div>
                            <h3 class="font-heading font-bold text-slate-900 text-base">Our Mission</h3>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">To deliver affordable, practical vocational training that produces job-ready and self-employed professionals across Kenya.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center font-bold text-lg shrink-0">💡</div>
                        <div>
                            <h3 class="font-heading font-bold text-slate-900 text-base">Practical Lab First</h3>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">Over 90% of instructional hours are spent building code, formatting layouts, or reconciling ledgers on modern lab computers.</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-4">
                        <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-800 flex items-center justify-center font-bold text-lg shrink-0">📜</div>
                        <div>
                            <h3 class="font-heading font-bold text-slate-900 text-base">Accredited & Verified</h3>
                            <p class="text-xs text-slate-400 mt-1 leading-relaxed">Every graduate receives a tamper-proof digital certificate verifiable publicly via our instant verification registry.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Campus & Facility Highlights -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-pearl-50 text-pearl-700 flex items-center justify-center text-2xl">💻</div>
                    <h3 class="font-heading font-bold text-lg text-slate-900">Modern Computer Labs</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">High-spec machines, high-speed fiber internet, uninterruptible power backups, and air-conditioned workstations.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center text-2xl">👨‍🏫</div>
                    <h3 class="font-heading font-bold text-lg text-slate-900">Industry Instructors</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Learn directly from practicing software engineers, senior graphic designers, and certified public accountants.</p>
                </div>
                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-2xl">🚀</div>
                    <h3 class="font-heading font-bold text-lg text-slate-900">Career & Placement Support</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">Resume reviews, freelance portfolio coaching on Upwork/Fiverr, and direct recruitment linkages with local SMEs.</p>
                </div>
            </div>

        </div>
    </section>
@endsection
