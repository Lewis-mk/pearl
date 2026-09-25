@extends('layouts.public')

@section('title', 'Pearl Training Institute — Practical Career & Technical Courses in Kenya')

@section('content')
    <!-- Hero Section -->
    <section class="hero-pattern text-white relative overflow-hidden py-20 lg:py-28">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Hero Text -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-500/10 border border-teal-400/30 text-teal-300 text-xs font-semibold">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                        <span>Accredited Short Courses & Vocational Excellence</span>
                    </div>

                    <h1 class="font-heading font-black text-4xl sm:text-5xl lg:text-6xl tracking-tight leading-none text-white">
                        Gain Job-Ready Skills for the <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-300 via-emerald-300 to-amber-300">Modern Economy</span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-300 font-normal leading-relaxed max-w-2xl">
                        Pearl Training Institute delivers intensive, practical short courses in Software Development, Graphic Design, Computerized Bookkeeping, and Cyber Operations. Master high-demand skills with flexible intake cohorts.
                    </p>

                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a href="{{ route('courses') }}" class="px-8 py-4 rounded-xl font-heading font-bold text-sm bg-gradient-to-r from-pearl-500 to-teal-400 hover:from-pearl-400 hover:to-teal-300 text-slate-950 shadow-lg shadow-teal-900/50 transition transform hover:-translate-y-0.5">
                            Browse All Courses
                        </a>
                        <a href="{{ route('printshop') }}" class="px-7 py-4 rounded-xl font-heading font-bold text-sm bg-slate-800/80 hover:bg-slate-700/80 text-white border border-slate-600 transition">
                            Ideal Print Shop Services
                        </a>
                    </div>

                    <!-- Trust Stats -->
                    <div class="grid grid-cols-3 gap-6 pt-8 border-t border-slate-700/60 max-w-lg">
                        <div>
                            <div class="font-heading font-extrabold text-2xl sm:text-3xl text-amber-400">95%</div>
                            <div class="text-xs text-slate-400 mt-0.5">Practical Hands-On</div>
                        </div>
                        <div>
                            <div class="font-heading font-extrabold text-2xl sm:text-3xl text-teal-300">1,200+</div>
                            <div class="text-xs text-slate-400 mt-0.5">Graduates Certified</div>
                        </div>
                        <div>
                            <div class="font-heading font-extrabold text-2xl sm:text-3xl text-emerald-400">M-Pesa</div>
                            <div class="text-xs text-slate-400 mt-0.5">Instant STK Payment</div>
                        </div>
                    </div>
                </div>

                <!-- Hero Interactive Quick Enrollment / Search Card -->
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-3xl p-6 sm:p-8 shadow-2xl shadow-slate-950/40 text-slate-900 border border-slate-100 relative">
                        <div class="absolute -top-3 right-6 bg-gradient-to-r from-amber-500 to-amber-600 text-white text-[11px] font-bold uppercase tracking-wider px-3 py-1 rounded-full shadow-md">
                            Flexible Intakes
                        </div>

                        <h3 class="font-heading font-black text-2xl text-slate-900">Start Your Application</h3>
                        <p class="text-xs text-slate-400 mt-1 mb-6">Select a course and join our upcoming intake cohort today.</p>

                        <form action="{{ route('register') }}" method="GET" class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Select Course</label>
                                <select name="course_id" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50 font-medium">
                                    @foreach($featuredCourses as $fc)
                                        <option value="{{ $fc->id }}">{{ $fc->title }} ({{ $fc->duration_weeks }} Wks - KES {{ number_format($fc->total_fee) }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="p-4 rounded-xl bg-pearl-50 border border-pearl-100 text-xs text-slate-700 space-y-2">
                                <div class="flex items-center justify-between font-semibold text-pearl-900">
                                    <span>✔ Sequential Permanent Student ID</span>
                                    <span>✔ M-Pesa STK Push</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>✔ Online & In-Person Labs</span>
                                    <span>✔ Verified Certificates</span>
                                </div>
                            </div>

                            <button type="submit" class="w-full py-4 rounded-xl font-heading font-bold text-sm text-white bg-pearl-600 hover:bg-pearl-700 shadow-md shadow-pearl-700/30 transition transform active:scale-95 text-center block">
                                Continue to Enrollment Form &rarr;
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Featured Courses Section -->
    <section class="py-20 bg-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="text-xs font-bold text-pearl-600 uppercase tracking-wider block mb-1">Practical Short Programs</span>
                    <h2 class="font-heading font-black text-3xl sm:text-4xl text-slate-900">Featured Vocational Courses</h2>
                </div>
                <a href="{{ route('courses') }}" class="text-sm font-bold text-pearl-600 hover:text-pearl-700 flex items-center space-x-1">
                    <span>View all courses catalog</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featuredCourses as $course)
                    <div class="bg-white rounded-2xl overflow-hidden border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col group">
                        <div class="h-44 bg-slate-800 relative overflow-hidden">
                            <img src="{{ $course->image_url ?? 'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600' }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @if($course->featured_badge)
                                <div class="absolute top-3 left-3 bg-amber-500 text-white text-[10px] font-bold uppercase tracking-wider px-2.5 py-1 rounded-md shadow-sm">
                                    {{ $course->featured_badge }}
                                </div>
                            @endif
                            <div class="absolute bottom-3 right-3 bg-slate-900/90 text-white text-xs font-semibold px-2.5 py-1 rounded-md">
                                ⏱ {{ $course->duration_weeks }} Weeks
                            </div>
                        </div>

                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div>
                                <span class="text-[11px] font-bold text-pearl-600 uppercase tracking-wider block mb-1">{{ $course->category }}</span>
                                <h3 class="font-heading font-bold text-lg text-slate-900 leading-snug group-hover:text-pearl-600 transition">
                                    <a href="{{ route('courses.detail', $course->slug) }}">{{ $course->title }}</a>
                                </h3>
                                <p class="text-xs text-slate-400 mt-2 line-clamp-2 leading-relaxed">
                                    {{ $course->short_description }}
                                </p>
                            </div>

                            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                                <div>
                                    <div class="text-[10px] text-slate-400 font-semibold uppercase">Total Fee</div>
                                    <div class="font-heading font-black text-lg text-slate-900">KES {{ number_format($course->total_fee) }}</div>
                                </div>
                                <a href="{{ route('courses.detail', $course->slug) }}" class="px-3.5 py-2 text-xs font-bold bg-slate-900 hover:bg-pearl-600 text-white rounded-lg transition">
                                    Details &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Ideal Print Shop & Cyber Highlight -->
    <section class="py-20 bg-white border-t border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <div class="lg:col-span-5 space-y-5">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-bold">
                        <span>🖨 Cyber & Commercial Printshop</span>
                    </div>

                    <h2 class="font-heading font-black text-3xl sm:text-4xl text-slate-900 leading-tight">
                        "Ideal Print Shop" & Digital Cyber Services
                    </h2>

                    <p class="text-sm text-slate-400 leading-relaxed">
                        Need fast, high-quality printing, binding, document typesetting, or government e-Citizen & KRA services? Submit your job online and pick it up ready at our campus counter.
                    </p>

                    <div class="space-y-3 pt-2">
                        <div class="flex items-center space-x-3 text-sm text-slate-700 font-medium">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">✓</span>
                            <span>High-speed color printing, photocopying & spiral binding</span>
                        </div>
                        <div class="flex items-center space-x-3 text-sm text-slate-700 font-medium">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">✓</span>
                            <span>KRA PIN registration, nil returns & iTax tax filings</span>
                        </div>
                        <div class="flex items-center space-x-3 text-sm text-slate-700 font-medium">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs">✓</span>
                            <span>Passport photos, lamination & custom flyer graphic design</span>
                        </div>
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('printshop') }}" class="px-7 py-3.5 rounded-xl font-heading font-bold text-sm bg-slate-900 hover:bg-pearl-600 text-white shadow-md transition inline-block">
                            View Price List & Request Service
                        </a>
                    </div>
                </div>

                <!-- Service Price Highlights Grid -->
                <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($services as $srv)
                        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-pearl-400 transition hover:bg-white shadow-sm flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $srv->category }}</span>
                                    <span class="px-2 py-0.5 text-xs font-extrabold bg-teal-100 text-teal-800 rounded font-mono">KES {{ number_format($srv->unit_price) }}</span>
                                </div>
                                <h4 class="font-heading font-bold text-slate-900 text-base mb-1">{{ $srv->name }}</h4>
                                <p class="text-xs text-slate-400 leading-relaxed">{{ $srv->description }}</p>
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-200/60 text-[11px] text-slate-400">
                                Pricing: {{ $srv->unit_label }}
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </section>

    <!-- Testimonials / Why Choose Us -->
    <section class="py-20 bg-slate-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-xs font-bold text-teal-400 uppercase tracking-widest block mb-1">Student Success Stories</span>
                <h2 class="font-heading font-black text-3xl sm:text-4xl text-white">Why Students Choose Pearl Institute</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-7 rounded-2xl bg-slate-800/80 border border-slate-700 space-y-4">
                    <div class="text-amber-400 text-lg">★★★★★</div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        "The Full Stack Web course gave me practical experience in PHP and Laravel that landed me my first junior developer role. The M-Pesa integration module alone was worth the entire fee!"
                    </p>
                    <div class="pt-4 border-t border-slate-700/60 flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-teal-600 text-white font-bold flex items-center justify-center text-xs">BK</div>
                        <div>
                            <div class="font-bold text-white text-xs">Brian Kiprono</div>
                            <div class="text-[11px] text-slate-400">Full Stack Alumni (2026)</div>
                        </div>
                    </div>
                </div>

                <div class="p-7 rounded-2xl bg-slate-800/80 border border-slate-700 space-y-4">
                    <div class="text-amber-400 text-lg">★★★★★</div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        "I loved the flexibility of the graphic design evening intake. The trainers are patient and the sit-in exam booking let me pick a time that didn't conflict with my day job."
                    </p>
                    <div class="pt-4 border-t border-slate-700/60 flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-purple-600 text-white font-bold flex items-center justify-center text-xs">FM</div>
                        <div>
                            <div class="font-bold text-white text-xs">Faith Mutua</div>
                            <div class="text-[11px] text-slate-400">Graphic Design Graduate</div>
                        </div>
                    </div>
                </div>

                <div class="p-7 rounded-2xl bg-slate-800/80 border border-slate-700 space-y-4">
                    <div class="text-amber-400 text-lg">★★★★★</div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        "The computerized bookkeeping class was direct and to the point. Setting up QuickBooks and filing KRA iTax returns helped me streamline my retail shop accounts immediately."
                    </p>
                    <div class="pt-4 border-t border-slate-700/60 flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-emerald-600 text-white font-bold flex items-center justify-center text-xs">EK</div>
                        <div>
                            <div class="font-bold text-white text-xs">Eric Kariuki</div>
                            <div class="text-[11px] text-slate-400">QuickBooks Alumni</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action Banner -->
    <section class="py-16 bg-gradient-to-r from-pearl-700 via-teal-700 to-emerald-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
            <h2 class="font-heading font-black text-3xl sm:text-4xl">Ready to Upgrade Your Career?</h2>
            <p class="text-sm sm:text-base text-teal-100 max-w-xl mx-auto font-normal">
                Enroll online today, pay your deposit conveniently via M-Pesa STK Push, and receive your official admission number immediately.
            </p>
            <div class="flex flex-wrap justify-center gap-4 pt-2">
                <a href="{{ route('register') }}" class="px-8 py-4 rounded-xl font-heading font-bold text-sm bg-white text-slate-900 hover:bg-slate-100 shadow-xl transition transform active:scale-95">
                    Enroll for Next Intake
                </a>
                <a href="{{ route('contact') }}" class="px-7 py-4 rounded-xl font-heading font-bold text-sm bg-pearl-900/60 hover:bg-pearl-900/90 text-white border border-teal-400/40 transition">
                    Visit Campus / Contact Us
                </a>
            </div>
        </div>
    </section>
@endsection
