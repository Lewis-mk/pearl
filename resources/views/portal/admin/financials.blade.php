@extends('layouts.portal')

@section('title', 'Financials & Receipts — Pearl Training Institute')
@section('page_title', 'Institution Financials & Receipt Ledger')
@section('page_subtitle', 'Unified cash and M-Pesa ledger with sequential immutable receipts (PRL-RCP-YYYYMM-XXXXX)')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Administration</div>
    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📊</span><span>Dashboard</span></a>
    <a href="{{ route('admin.users') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>👥</span><span>Users & RBAC</span></a>
    <a href="{{ route('admin.admissions') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>🎓</span><span>Admissions Queue</span></a>
    <a href="{{ route('admin.financials') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-purple-700 text-white font-bold transition"><span>💳</span><span>Financials & Receipts</span></a>
    <a href="{{ route('admin.courses') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📚</span><span>Courses & Cohorts</span></a>
    <a href="{{ route('admin.misconduct') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>🛡️</span><span>Misconduct Inbox</span></a>
    <a href="{{ route('admin.attendance') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>⏱️</span><span>Attendance Oversight</span></a>
    <a href="{{ route('admin.audit') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📋</span><span>Audit Logs</span></a>
    <a href="{{ route('admin.settings') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>⚙️</span><span>System Settings</span></a>
@endsection

@section('portal_content')
    <div class="space-y-8">

        <!-- Revenue Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-[10px] font-bold uppercase text-slate-400">Today's Revenue</span>
                <h4 class="font-heading font-black text-xl text-slate-900 font-mono mt-1">KES {{ number_format($todayTotal, 2) }}</h4>
                <span class="text-[11px] text-slate-400">{{ $todayCount }} receipts issued</span>
            </div>
            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-[10px] font-bold uppercase text-slate-400">This Month</span>
                <h4 class="font-heading font-black text-xl text-emerald-600 font-mono mt-1">KES {{ number_format($monthTotal, 2) }}</h4>
                <span class="text-[11px] text-slate-400">{{ now()->format('F Y') }}</span>
            </div>
            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-[10px] font-bold uppercase text-slate-400">Total Cash</span>
                <h4 class="font-heading font-black text-xl text-slate-900 font-mono mt-1">KES {{ number_format($cashTotal, 2) }}</h4>
                <span class="text-[11px] text-slate-400">Counter collections</span>
            </div>
            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm">
                <span class="text-[10px] font-bold uppercase text-slate-400">Total M-Pesa</span>
                <h4 class="font-heading font-black text-xl text-teal-600 font-mono mt-1">KES {{ number_format($mpesaTotal, 2) }}</h4>
                <span class="text-[11px] text-slate-400">STK Push + Manual</span>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap gap-3">
            <form action="{{ route('admin.financials') }}" method="GET" class="flex flex-wrap gap-3 flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search receipt number, student name, M-Pesa code..." class="flex-1 min-w-[200px] px-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-purple-500 bg-slate-50">
                <select name="method" class="px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50 font-medium">
                    <option value="">All Methods</option>
                    <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="mpesa_stk" {{ request('method') === 'mpesa_stk' ? 'selected' : '' }}>M-Pesa STK</option>
                    <option value="mpesa_c2b" {{ request('method') === 'mpesa_c2b' ? 'selected' : '' }}>M-Pesa C2B</option>
                    <option value="mpesa_manual" {{ request('method') === 'mpesa_manual' ? 'selected' : '' }}>M-Pesa Manual</option>
                </select>
                <input type="date" name="from" value="{{ request('from') }}" class="px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
                <input type="date" name="to" value="{{ request('to') }}" class="px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-purple-600 text-white hover:bg-purple-700 transition">Filter</button>
            </form>
        </div>

        <!-- Full Ledger Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="p-4">Receipt Number</th>
                            <th class="p-4">Date & Time</th>
                            <th class="p-4">Payer / Student</th>
                            <th class="p-4">Purpose</th>
                            <th class="p-4">Method / TransID</th>
                            <th class="p-4 text-right">Amount (KES)</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($payments as $pmt)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-4 font-mono font-bold text-slate-900">{{ $pmt->receipt_number }}</td>
                                <td class="p-4 text-slate-400">{{ $pmt->created_at->format('d M Y, h:i A') }}</td>
                                <td class="p-4">
                                    <div class="font-semibold text-slate-800">{{ $pmt->user?->name ?? $pmt->payer_name ?? 'Walk-in' }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $pmt->user?->admission_number ?? '' }}</div>
                                </td>
                                <td class="p-4 capitalize text-slate-600">{{ $pmt->purpose }}</td>
                                <td class="p-4">
                                    <span class="capitalize font-semibold text-slate-700">{{ str_replace('_', ' ', $pmt->payment_method) }}</span>
                                    @if($pmt->mpesa_receipt_number)
                                        <span class="block font-mono text-[10px] text-emerald-700">{{ $pmt->mpesa_receipt_number }}</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right font-mono font-extrabold text-emerald-600 text-sm">{{ number_format($pmt->amount, 2) }}</td>
                                <td class="p-4 text-center">
                                    <a href="{{ route('receipt.view', $pmt->receipt_number) }}" target="_blank" class="px-3 py-1.5 text-xs font-bold bg-slate-900 hover:bg-purple-600 text-white rounded-lg transition">🖨 Print</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="p-8 text-center text-slate-400">No payment records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-50 border-t border-slate-200">
                        <tr>
                            <td colspan="5" class="p-4 font-heading font-bold text-sm text-right text-slate-900">Page Total:</td>
                            <td class="p-4 text-right font-heading font-black text-base font-mono text-emerald-700">
                                KES {{ number_format($payments->sum('amount'), 2) }}
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <div>{{ $payments->links() }}</div>

    </div>
@endsection
