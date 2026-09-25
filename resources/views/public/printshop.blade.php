@extends('layouts.public')

@section('title', 'Ideal Print Shop & Cyber Services — Pearl Training Institute')
@section('meta_description', 'High speed color printing, photocopying, document binding, KRA PIN registration, and digital cyber services in Nairobi CBD.')

@section('content')
    <!-- Header Banner -->
    <section class="bg-slate-900 text-white py-14 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 text-xs font-bold mb-3">
                <span>🖨 Commercial & Campus Print Services</span>
            </div>
            <h1 class="font-heading font-black text-3xl sm:text-5xl text-white">Ideal Print Shop & Cyber Cafe</h1>
            <p class="text-sm text-slate-300 max-w-2xl mt-3 font-normal">
                Professional printing, scanning, binding, typesetting, and Kenyan e-Citizen & KRA cyber services. Submit your order online for express pickup!
            </p>
        </div>
    </section>

    <!-- Services & Request Form -->
    <section class="py-14 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('service_success'))
                @php $ss = session('service_success'); @endphp
                <div class="mb-10 p-6 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-950 shadow-md flex items-start space-x-4">
                    <div class="text-3xl">🎉</div>
                    <div class="space-y-1">
                        <h3 class="font-heading font-bold text-lg text-emerald-900">Service Request Received!</h3>
                        <p class="text-xs text-emerald-800">Thank you <strong>{{ $ss['name'] }}</strong>. Your request has been queued in our Cyber Attendant portal.</p>
                        <div class="pt-2 text-xs font-mono font-bold text-emerald-900">
                            Request Tracking Code: <span class="bg-emerald-200 px-2 py-1 rounded text-slate-900 font-extrabold">{{ $ss['code'] }}</span>
                            @if($ss['amount'] > 0)
                                | Estimated Quote: <span>KES {{ number_format($ss['amount'], 2) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                
                <!-- Left: Full Price List -->
                <div class="lg:col-span-7 space-y-6">
                    <div>
                        <span class="text-xs font-bold text-pearl-600 uppercase tracking-wider block mb-1">Transparent Pricing</span>
                        <h2 class="font-heading font-black text-2xl sm:text-3xl text-slate-900">Services & Rate Card</h2>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm">
                        <div class="divide-y divide-slate-100">
                            @foreach($services as $service)
                                <div class="p-5 hover:bg-slate-50/80 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-heading font-bold text-base text-slate-900">{{ $service->name }}</span>
                                            <span class="text-[10px] uppercase font-bold text-slate-400 px-2 py-0.5 bg-slate-100 rounded">{{ $service->category }}</span>
                                        </div>
                                        <p class="text-xs text-slate-400 leading-relaxed">{{ $service->description }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <div class="font-heading font-black text-lg text-emerald-600 font-mono">KES {{ number_format($service->unit_price) }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $service->unit_label }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Cyber Assistance Card -->
                    <div class="p-6 rounded-2xl bg-gradient-to-r from-slate-900 to-teal-950 text-white space-y-3">
                        <h3 class="font-heading font-bold text-lg text-white">Government & e-Citizen Assistance</h3>
                        <p class="text-xs text-slate-300 leading-relaxed">
                            Our qualified cyber attendants assist with KRA PIN registration, annual tax returns, HELB compliance certificates, NSSF/SHIF registration, passport/e-Citizen forms, and typesetting of professional CVs and job applications.
                        </p>
                    </div>
                </div>

                <!-- Right: Online Request Form -->
                <div class="lg:col-span-5">
                    <div class="bg-white p-7 sm:p-8 rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/60 sticky top-24 space-y-6">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-pearl-600 tracking-wider">Fast Turnaround</span>
                            <h3 class="font-heading font-black text-2xl text-slate-900">Request a Service</h3>
                            <p class="text-xs text-slate-400 mt-1">Submit your order details and upload your document. Our cyber attendant will prepare it for collection.</p>
                        </div>

                        <form action="{{ route('printshop.request') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Your Full Name *</label>
                                <input type="text" name="customer_name" required value="{{ old('customer_name') }}" placeholder="e.g. Grace Njeri" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone (M-Pesa) *</label>
                                    <input type="tel" name="customer_phone" required value="{{ old('customer_phone') }}" placeholder="07XXXXXXXX" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email (Optional)</label>
                                    <input type="email" name="customer_email" value="{{ old('customer_email') }}" placeholder="email@domain.com" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Service Type *</label>
                                    <select name="service_type" required class="w-full px-3 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50 font-medium">
                                        @foreach($services as $srv)
                                            <option value="{{ $srv->name }}">{{ $srv->name }} (KES {{ number_format($srv->unit_price) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Quantity / Copies *</label>
                                    <input type="number" name="quantity" min="1" value="{{ old('quantity', 1) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Upload File / Document (PDF, Word, Image)</label>
                                <input type="file" name="document" class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-pearl-50 file:text-pearl-700 hover:file:bg-pearl-100 border border-slate-300 rounded-xl p-1 bg-slate-50">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Instructions / Specifics</label>
                                <textarea name="instructions" rows="3" placeholder="e.g. 2 copies stapled, color cover, double-sided..." class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">{{ old('instructions') }}</textarea>
                            </div>

                            <button type="submit" class="w-full py-3.5 rounded-xl font-heading font-bold text-sm text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-700/20 transition transform active:scale-95 text-center">
                                Submit Print Request &rarr;
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection
