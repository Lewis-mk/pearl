@extends('layouts.portal')

@section('title', 'Cyber & Print Desk — Pearl Training Institute')
@section('page_title', 'Ideal Print Shop & Cyber Attendant Desk')
@section('page_subtitle', 'Manage walk-in customers, fulfill online print orders, record cash collections, and issue sequential receipts')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Cyber Operations</div>
    <a href="{{ route('cyber.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-teal-700 text-white font-bold transition">
        <span>🖨</span> <span>Service Queue & POS</span>
    </a>
    <a href="{{ route('cyber.daily_summary') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📊</span> <span>Daily Shift Summary</span>
    </a>
@endsection

@section('portal_content')
    <div class="space-y-8">

        <!-- Metrics Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Queue Items</span>
                    <h4 class="font-heading font-black text-2xl text-slate-900 mt-0.5">{{ $pendingRequests->count() + $inProgressRequests->count() }}</h4>
                    <span class="text-[11px] text-teal-600 font-semibold">{{ $pendingRequests->count() }} Pending &bull; {{ $inProgressRequests->count() }} In Progress</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center text-xl">⏳</div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Completed Today</span>
                    <h4 class="font-heading font-black text-2xl text-emerald-600 mt-0.5">{{ $todayJobsCount }}</h4>
                    <span class="text-[11px] text-slate-400">Print & cyber jobs</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xl">✓</div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Shift Collections</span>
                    <h4 class="font-heading font-black text-xl text-teal-700 font-mono mt-0.5">KES {{ number_format($todayRevenue, 2) }}</h4>
                    <span class="text-[11px] text-slate-400">Total collected today</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center text-xl">💵</div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Shift Report</span>
                    <h4 class="font-heading font-black text-base text-slate-900 mt-1">Daily Log</h4>
                    <a href="{{ route('cyber.daily_summary') }}" class="text-[11px] text-teal-600 font-bold hover:underline">View Breakdown &rarr;</a>
                </div>
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-xl">📊</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left 7 Cols: Service Order Queue & Completed List -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Pending Queue -->
                <div class="space-y-4">
                    <h3 class="font-heading font-bold text-lg text-slate-900">Active Service Queue (Online Requests & Orders)</h3>

                    <div class="space-y-4">
                        @php
                            $allQueue = $pendingRequests->merge($inProgressRequests);
                        @endphp

                        @forelse($allQueue as $req)
                            <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-4">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <div>
                                        <span class="font-mono font-bold text-xs text-teal-700">{{ $req->request_code }}</span>
                                        <h4 class="font-heading font-bold text-base text-slate-900 mt-0.5">{{ $req->customer_name }}</h4>
                                        <span class="text-xs text-slate-400">📞 {{ $req->customer_phone }}</span>
                                    </div>
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-full 
                                        @if($req->status === 'pending') bg-amber-100 text-amber-900
                                        @elseif($req->status === 'in_progress') bg-blue-100 text-blue-900
                                        @else bg-purple-100 text-purple-900
                                        @endif uppercase">
                                        {{ $req->status }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-3 text-xs">
                                    <div class="p-3 bg-slate-50 rounded-xl">
                                        <span class="text-slate-400 uppercase text-[10px]">Service / Qty</span>
                                        <div class="font-bold text-slate-900 mt-0.5">{{ $req->service_type }} (x{{ $req->quantity }})</div>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-xl">
                                        <span class="text-slate-400 uppercase text-[10px]">Quoted Amount</span>
                                        <div class="font-mono font-bold text-emerald-600 mt-0.5">KES {{ number_format($req->quoted_amount, 2) }}</div>
                                    </div>
                                </div>

                                @if($req->instructions)
                                    <div class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                        <strong>Customer Instructions:</strong> {{ $req->instructions }}
                                    </div>
                                @endif

                                @if($req->file_path)
                                    <div class="text-xs">
                                        <a href="{{ asset('storage/' . $req->file_path) }}" download class="text-teal-700 font-bold hover:underline inline-flex items-center space-x-1">
                                            <span>📎 Download Customer Attached File</span>
                                        </a>
                                    </div>
                                @endif

                                <!-- Complete / Receipt Action Form -->
                                <form action="{{ route('cyber.requests.status', $req->id) }}" method="POST" class="p-4 rounded-xl bg-teal-50/60 border border-teal-100 space-y-3">
                                    @csrf

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                        <div>
                                            <label class="block text-[10px] font-bold text-teal-900 uppercase mb-1">Status</label>
                                            <select name="status" class="w-full px-2.5 py-1.5 rounded-lg border border-teal-300 text-xs bg-white font-medium">
                                                <option value="in_progress" {{ $req->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                <option value="completed">Completed & Paid</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-teal-900 uppercase mb-1">Amount Collected (KES)</label>
                                            <input type="number" name="amount_collected" value="{{ $req->quoted_amount }}" min="0" class="w-full px-2.5 py-1.5 rounded-lg border border-teal-300 text-xs font-mono font-bold bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-teal-900 uppercase mb-1">Payment Method</label>
                                            <select name="payment_method" class="w-full px-2.5 py-1.5 rounded-lg border border-teal-300 text-xs bg-white font-medium">
                                                <option value="cash">Cash (Counter)</option>
                                                <option value="mpesa_manual">M-Pesa (Till/Paybill)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="flex justify-end">
                                        <button type="submit" class="px-4 py-2 text-xs font-bold bg-teal-700 hover:bg-teal-800 text-white rounded-xl shadow-sm transition">
                                            Update Status & Issue Receipt &rarr;
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @empty
                            <div class="p-8 text-center text-xs text-slate-400 bg-white rounded-2xl border border-slate-200">
                                No pending service requests in queue!
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- Right 5 Cols: Quick Walk-in POS POS Logging Form -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white p-7 sm:p-8 rounded-3xl border border-slate-200 shadow-md space-y-5 sticky top-24">
                    <div>
                        <span class="text-[10px] uppercase font-bold text-teal-600 tracking-wider">Counter POS</span>
                        <h3 class="font-heading font-black text-xl text-slate-900">Record Walk-in Sale</h3>
                        <p class="text-xs text-slate-400 mt-1">Direct cashier entry for walk-in photocopies, printing, or e-Citizen services.</p>
                    </div>

                    <form action="{{ route('cyber.walkin.store') }}" method="POST" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Customer Full Name *</label>
                            <input type="text" name="customer_name" required placeholder="Walk-in Customer Name" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-slate-50">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone Number *</label>
                            <input type="tel" name="customer_phone" required placeholder="07XXXXXXXX" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-slate-50">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Service Type *</label>
                            <select id="posService" name="service_type" required onchange="calculatePosTotal()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-slate-50 font-medium">
                                @foreach($services as $srv)
                                    <option value="{{ $srv->name }}" data-price="{{ $srv->unit_price }}">{{ $srv->name }} (KES {{ number_format($srv->unit_price) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Quantity / Pages *</label>
                                <input type="number" id="posQuantity" name="quantity" value="1" min="1" required oninput="calculatePosTotal()" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50 font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Amount Paid (KES) *</label>
                                <input type="number" id="posAmount" name="amount_paid" value="50" min="0" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono font-bold bg-white text-emerald-600">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Payment Method *</label>
                                <select name="payment_method" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
                                    <option value="cash">Cash (Counter)</option>
                                    <option value="mpesa_manual">M-Pesa (Till/Paybill)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">M-Pesa Code (If manual)</label>
                                <input type="text" name="mpesa_receipt_number" placeholder="e.g. QK89123456" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs font-mono uppercase bg-slate-50">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3.5 rounded-xl font-heading font-bold text-xs text-white bg-teal-700 hover:bg-teal-800 shadow-md shadow-teal-700/20 transition transform active:scale-95 text-center">
                            Process Sale & Issue Sequential Receipt &rarr;
                        </button>
                    </form>
                </div>
            </div>

        </div>

    </div>

    <script>
        function calculatePosTotal() {
            const select = document.getElementById('posService');
            if (!select) return;
            const opt = select.options[select.selectedIndex];
            const price = parseFloat(opt ? opt.getAttribute('data-price') || 0 : 0);
            const qty = parseInt(document.getElementById('posQuantity').value || 1);
            document.getElementById('posAmount').value = price * qty;
        }
        document.addEventListener('DOMContentLoaded', calculatePosTotal);
    </script>
@endsection
