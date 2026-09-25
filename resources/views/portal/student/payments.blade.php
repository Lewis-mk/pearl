@extends('layouts.portal')

@section('title', 'Fee Payments & M-Pesa Ledger — Pearl Training Institute')
@section('page_title', 'Fee Payments & Running Ledger')
@section('page_subtitle', 'Pay course fees via Safaricom Daraja M-Pesa STK Push and access official sequential receipts')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Learning & Academics</div>
    <a href="{{ route('student.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📊</span> <span>My Dashboard</span>
    </a>
    <a href="{{ route('student.classes') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>🗓️</span> <span>Timetable & Classes</span>
    </a>
    <a href="{{ route('student.materials') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📁</span> <span>Course Materials</span>
    </a>
    <a href="{{ route('student.exams') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📝</span> <span>Exams & Quizzes</span>
    </a>
    <a href="{{ route('student.attendance') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>⏱️</span> <span>Attendance & Leaves</span>
    </a>

    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-4 mb-1">Financials & Governance</div>
    <a href="{{ route('student.payments') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-pearl-700 text-white font-bold transition">
        <span>💳</span> <span>Payments & M-Pesa</span>
    </a>
    <a href="{{ route('student.misconduct') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>🛡️</span> <span>Misconduct Report</span>
    </a>
    <a href="{{ route('student.profile') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>⚙️</span> <span>Profile & Settings</span>
    </a>
@endsection

@section('portal_content')
    <div class="space-y-8">

        <!-- Running Fee Balance Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($enrollments as $enr)
                <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div>
                            <span class="text-[10px] font-bold text-pearl-600 uppercase">{{ $enr->cohort->name }}</span>
                            <h3 class="font-heading font-bold text-lg text-slate-900 leading-snug">{{ $enr->course->title }}</h3>
                        </div>
                        <span class="px-2.5 py-1 text-xs font-bold rounded-full {{ $enr->fee_balance == 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-900' }}">
                            {{ $enr->fee_balance == 0 ? 'Fully Cleared' : 'Balance Due' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-3 gap-3 text-center text-xs">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 text-[10px] uppercase font-semibold">Total Fee</span>
                            <div class="font-heading font-black text-sm text-slate-900 font-mono mt-0.5">KES {{ number_format($enr->fee_total, 2) }}</div>
                        </div>
                        <div class="p-3 rounded-xl bg-emerald-50/60 border border-emerald-100">
                            <span class="text-emerald-700 text-[10px] uppercase font-semibold">Total Paid</span>
                            <div class="font-heading font-black text-sm text-emerald-700 font-mono mt-0.5">KES {{ number_format($enr->fee_paid, 2) }}</div>
                        </div>
                        <div class="p-3 rounded-xl bg-amber-50/60 border border-amber-100">
                            <span class="text-amber-800 text-[10px] uppercase font-semibold">Running Balance</span>
                            <div class="font-heading font-black text-sm text-amber-800 font-mono mt-0.5">KES {{ number_format($enr->fee_balance, 2) }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- M-Pesa STK Push Trigger Card -->
        <div class="bg-white p-7 sm:p-8 rounded-3xl border border-slate-200 shadow-md">
            <div class="max-w-2xl space-y-5">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-black text-sm">
                        M
                    </div>
                    <div>
                        <h3 class="font-heading font-black text-xl text-slate-900">Make Payment via Safaricom M-Pesa</h3>
                        <p class="text-xs text-slate-400">Trigger an STK Push PIN prompt directly to your phone.</p>
                    </div>
                </div>

                <form action="{{ route('student.payments.stk') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Select Program / Cohort *</label>
                            <select name="enrollment_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50 font-medium">
                                @foreach($enrollments as $enr)
                                    <option value="{{ $enr->id }}">{{ $enr->course->title }} (Bal: KES {{ number_format($enr->fee_balance) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Payment Purpose *</label>
                            <select name="purpose" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50 font-medium">
                                <option value="installment">Installment / Balance Top-up</option>
                                <option value="deposit">Registration Deposit</option>
                                <option value="full">Full Course Fee</option>
                                <option value="exam_fee">Sit-in / Exam Fee</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Amount to Pay (KES) *</label>
                            <input type="number" name="amount" min="10" required placeholder="e.g. 5000" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm font-mono font-bold focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">M-Pesa Phone Number *</label>
                            <input type="tel" name="phone_number" value="{{ $user->phone }}" required placeholder="07XXXXXXXX" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                        </div>
                    </div>

                    <button type="submit" class="px-7 py-3.5 rounded-xl font-heading font-bold text-sm text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-700/20 transition transform active:scale-95 flex items-center space-x-2">
                        <span>📲 Send M-Pesa STK Push Prompt</span>
                    </button>
                </form>

                <!-- Offline / Paybill Option Note -->
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1">
                    <span class="font-bold text-slate-800">Alternative Direct Paybill Payment:</span>
                    <p>You can also pay directly on your phone: Paybill <strong class="font-mono text-slate-900">174379</strong> &bull; Account: <strong class="font-mono text-slate-900">{{ $user->admission_number ?? $user->phone }}</strong>. Payments reconcile into your ledger automatically upon callback.</p>
                </div>
            </div>
        </div>

        <!-- Sequential Immutable Receipt Ledger Table -->
        <div class="space-y-4">
            <h3 class="font-heading font-bold text-lg text-slate-900">Complete Payment History & Official Receipts</h3>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="p-4">Receipt Number</th>
                                <th class="p-4">Date & Time</th>
                                <th class="p-4">Program</th>
                                <th class="p-4">Method / TransID</th>
                                <th class="p-4">Purpose</th>
                                <th class="p-4 text-right">Amount (KES)</th>
                                <th class="p-4 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($payments as $pmt)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-4 font-mono font-bold text-slate-900">
                                        {{ $pmt->receipt_number }}
                                    </td>
                                    <td class="p-4 text-slate-400">
                                        {{ $pmt->created_at->format('d M Y, h:i A') }}
                                    </td>
                                    <td class="p-4 font-semibold text-slate-800">
                                        {{ $pmt->enrollment->course->title ?? 'General / Printshop' }}
                                    </td>
                                    <td class="p-4">
                                        <span class="capitalize font-semibold text-slate-700">{{ str_replace('_', ' ', $pmt->payment_method) }}</span>
                                        @if($pmt->mpesa_receipt_number)
                                            <span class="block font-mono text-[10px] text-emerald-700">{{ $pmt->mpesa_receipt_number }}</span>
                                        @endif
                                    </td>
                                    <td class="p-4 capitalize text-slate-600">
                                        {{ $pmt->purpose }}
                                    </td>
                                    <td class="p-4 text-right font-mono font-extrabold text-emerald-600 text-sm">
                                        {{ number_format($pmt->amount, 2) }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="{{ route('receipt.view', $pmt->receipt_number) }}" target="_blank" class="px-3 py-1.5 text-[11px] font-bold bg-slate-900 hover:bg-pearl-600 text-white rounded-lg transition inline-flex items-center space-x-1">
                                            <span>🖨 Print</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-slate-400">No payment records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $payments->links() }}
            </div>
        </div>

    </div>
@endsection
