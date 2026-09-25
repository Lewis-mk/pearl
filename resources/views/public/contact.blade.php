@extends('layouts.public')

@section('title', 'Contact Us & Campus Location — Pearl Training Institute')

@section('content')
    <section class="bg-slate-900 text-white py-16 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <span class="text-xs font-bold text-teal-400 uppercase tracking-widest block mb-2">Get in Touch</span>
            <h1 class="font-heading font-black text-3xl sm:text-5xl text-white">Contact & Campus Location</h1>
            <p class="text-sm text-slate-300 max-w-2xl mt-3 font-normal">
                Visit our campus in Nairobi CBD or reach our admissions hotline via phone, email, or M-Pesa desk.
            </p>
        </div>
    </section>

    <section class="py-16 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                
                <!-- Left: Contact Details & Map -->
                <div class="lg:col-span-6 space-y-8">
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4 p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-pearl-50 text-pearl-700 flex items-center justify-center text-xl shrink-0">📍</div>
                            <div>
                                <h3 class="font-heading font-bold text-slate-900 text-base">Campus Physical Address</h3>
                                <p class="text-xs text-slate-400 mt-1 leading-relaxed">{{ $settings['campus_address'] ?? 'Pearl Towers, 3rd Floor, Moi Avenue, Nairobi CBD, Kenya' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4 p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xl shrink-0">📞</div>
                            <div>
                                <h3 class="font-heading font-bold text-slate-900 text-base">Helpline & Admissions Hotline</h3>
                                <p class="text-xs text-slate-400 mt-1">{{ $settings['support_phone'] ?? '+254 700 123 456' }} (Mon - Sat: 7:30 AM - 6:30 PM)</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4 p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center text-xl shrink-0">✉️</div>
                            <div>
                                <h3 class="font-heading font-bold text-slate-900 text-base">Email Enquiries</h3>
                                <p class="text-xs text-slate-400 mt-1">{{ $settings['support_email'] ?? 'admissions@pearlinstitute.com' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-4 p-5 rounded-2xl bg-emerald-900/90 text-white border border-emerald-800 shadow-sm">
                            <div class="w-10 h-10 rounded-xl bg-emerald-800 text-emerald-200 flex items-center justify-center text-xl shrink-0">💳</div>
                            <div>
                                <h3 class="font-heading font-bold text-white text-base">Official M-Pesa Paybill</h3>
                                <p class="text-xs text-emerald-100 mt-1">Paybill Business Number: <strong class="font-mono text-amber-300 font-bold">{{ $settings['daraja_paybill'] ?? '174379' }}</strong><br>Account Number: Student Admission No or Customer Name</p>
                            </div>
                        </div>
                    </div>

                    <!-- Map Preview / Location Box -->
                    <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-sm h-64 bg-slate-200 relative flex items-center justify-center text-center p-6">
                        <div class="space-y-2">
                            <div class="text-4xl">🗺️</div>
                            <div class="font-heading font-bold text-slate-900">Pearl Towers — Nairobi CBD</div>
                            <div class="text-xs text-slate-400">Conveniently situated opposite Kencom House along Moi Avenue. Accessible by all central matatu routes.</div>
                        </div>
                    </div>
                </div>

                <!-- Right: Interactive Inquiry Message Form -->
                <div class="lg:col-span-6">
                    <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-md space-y-6">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-pearl-600 tracking-wider">Quick Inquiry</span>
                            <h3 class="font-heading font-black text-2xl text-slate-900">Send Us a Message</h3>
                            <p class="text-xs text-slate-400 mt-1">Have a question regarding fees, intakes, or custom corporate training? Leave your inquiry below.</p>
                        </div>

                        <form onsubmit="event.preventDefault(); alert('Thank you for reaching out! Our admissions desk has received your message and will respond via phone/email shortly.');" class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Your Full Name *</label>
                                <input type="text" required placeholder="e.g. Kelvin Omondi" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone Number *</label>
                                    <input type="tel" required placeholder="07XXXXXXXX" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address *</label>
                                    <input type="email" required placeholder="email@domain.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Program of Interest</label>
                                <select class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                                    <option>Full Stack Web Development & Laravel</option>
                                    <option>Graphic Design & UI/UX</option>
                                    <option>Computerized Accounting & QuickBooks</option>
                                    <option>Computer Packages & Secretarial</option>
                                    <option>Ideal Print Shop & Commercial Services</option>
                                    <option>Corporate Customized Training</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Your Message / Questions</label>
                                <textarea rows="4" placeholder="How can we assist you?" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50"></textarea>
                            </div>

                            <button type="submit" class="w-full py-4 rounded-xl font-heading font-bold text-sm text-white bg-pearl-600 hover:bg-pearl-700 shadow-md shadow-pearl-700/20 transition transform active:scale-95 text-center">
                                Send Inquiry Message &rarr;
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
