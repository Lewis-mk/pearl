@extends('layouts.portal')

@section('title', 'Daily Shift Summary — Pearl Training Institute')
@section('page_title', 'Cyber Attendant Daily Shift Summary')
@section('page_subtitle', 'Full accounting of today\'s cash, M-Pesa, and service completions for handover')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Cyber Operations</div>
    <a href="{{ route('cyber.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>🖨</span> <span>Service Queue & POS</span>
    </a>
    <a href="{{ route('cyber.daily_summary') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-teal-700 text-white font-bold transition">
        <span>📊</span> <span>Daily Shift Summary</span>
    </a>
@endsection

@section('portal_content')
    <div class="max-w-4xl mx-auto space-y-8">

        <!-- Summary Header Card -->
        <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-tr from-slate-900 to-teal-950 text-white shadow-xl border border-teal-800 space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <span class="text-[10px] uppercase font-bold text-teal-300 tracking-widest block">Shift Date</span>
                    <h3 class="font-heading font-black text-2xl text-white">{{ \Carbon\Carbon::parse($selectedDate)->format('l, d F Y') }}</h3>
                    <p class="text-xs text-slate-300">Attendant: <strong class="text-teal-200">{{ Auth::user()->name }}</strong></p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-400 block">Total Receipts Issued:</span>
                    <span class="font-heading font-black text-3xl text-amber-300">{{ $payments->count() }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-teal-900">
                <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-sm text-center space-y-1">
                    <span class="text-[10px] font-bold text-slate-300 uppercase block">Cash Collected</span>
                    <span class="font-heading font-black text-2xl text-emerald-300 font-mono">KES {{ number_format($totalCash, 2) }}</span>
                    <span class="text-[11px] text-slate-400">{{ $payments->where('payment_method', 'cash')->count() }} receipts</span>
                </div>
                <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-sm text-center space-y-1">
                    <span class="text-[10px] font-bold text-slate-300 uppercase block">M-Pesa Recorded</span>
                    <span class="font-heading font-black text-2xl text-teal-300 font-mono">KES {{ number_format($totalMpesa, 2) }}</span>
                    <span class="text-[11px] text-slate-400">{{ $payments->whereIn('payment_method', ['mpesa_manual', 'mpesa_stk', 'mpesa_c2b'])->count() }} receipts</span>
                </div>
                <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-sm text-center space-y-1">
                    <span class="text-[10px] font-bold text-slate-300 uppercase block">Grand Total</span>
                    <span class="font-heading font-black text-2xl text-amber-300 font-mono">KES {{ number_format($totalCash + $totalMpesa, 2) }}</span>
                    <span class="text-[11px] text-slate-400">All methods combined</span>
                </div>
            </div>
        </div>

        <!-- Today's Receipt Ledger Table -->
        <div class="space-y-4">
            <h3 class="font-heading font-bold text-lg text-slate-900">Issued Sequential Receipts (Shift Record)</h3>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="p-4">Receipt Number</th>
                                <th class="p-4">Time</th>
                                <th class="p-4">Customer</th>
                                <th class="p-4">Service</th>
                                <th class="p-4">Method</th>
                                <th class="p-4 text-right">Amount (KES)</th>
                                <th class="p-4 text-center">Print</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($payments as $pmt)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-4 font-mono font-bold text-slate-900">{{ $pmt->receipt_number }}</td>
                                    <td class="p-4 text-slate-400">{{ $pmt->created_at->format('h:i A') }}</td>
                                    <td class="p-4 font-semibold text-slate-800">
                                        {{ $pmt->payer_name ?? ($pmt->student->name ?? 'Walk-in') }}
                                    </td>
                                    <td class="p-4 text-slate-600 capitalize">
                                        {{ $pmt->purpose }}
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold 
                                            @if($pmt->payment_method === 'cash') bg-emerald-100 text-emerald-800
                                            @elseif(str_contains($pmt->payment_method, 'mpesa')) bg-teal-100 text-teal-800
                                            @else bg-slate-100 text-slate-600
                                            @endif uppercase">
                                            {{ str_replace('_', ' ', $pmt->payment_method) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-right font-mono font-extrabold text-emerald-600 text-sm">
                                        {{ number_format($pmt->amount, 2) }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <a href="{{ route('receipt.view', $pmt->receipt_number) }}" target="_blank" class="text-xs font-bold text-teal-600 hover:underline">🖨</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-slate-400 text-xs">No transactions recorded in this shift yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-slate-50 border-t border-slate-200">
                            <tr>
                                <td colspan="5" class="p-4 font-heading font-bold text-sm text-slate-900 text-right">Total Collection Today:</td>
                                <td class="p-4 font-heading font-black text-lg font-mono text-emerald-700 text-right">KES {{ number_format($totalCash + $totalMpesa, 2) }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
