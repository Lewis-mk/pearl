@extends('layouts.portal')

@section('title', 'Attendance Oversight — Pearl Training Institute')
@section('page_title', 'Institution-Wide Attendance Oversight')
@section('page_subtitle', 'Monitor student attendance across all cohorts, review absence requests, and generate reports')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Administration</div>
    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📊</span><span>Dashboard</span></a>
    <a href="{{ route('admin.users') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>👥</span><span>Users & RBAC</span></a>
    <a href="{{ route('admin.admissions') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>🎓</span><span>Admissions Queue</span></a>
    <a href="{{ route('admin.financials') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>💳</span><span>Financials & Receipts</span></a>
    <a href="{{ route('admin.courses') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📚</span><span>Courses & Cohorts</span></a>
    <a href="{{ route('admin.misconduct') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>🛡️</span><span>Misconduct Inbox</span></a>
    <a href="{{ route('admin.attendance') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-purple-700 text-white font-bold transition"><span>⏱️</span><span>Attendance Oversight</span></a>
    <a href="{{ route('admin.audit') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📋</span><span>Audit Logs</span></a>
    <a href="{{ route('admin.settings') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>⚙️</span><span>System Settings</span></a>
@endsection

@section('portal_content')
    <div class="space-y-8">

        <!-- Cohort Attendance Summary Cards -->
        <div class="space-y-4">
            <h3 class="font-heading font-bold text-lg text-slate-900">Attendance Rate by Cohort</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($cohortStats as $stat)
                    <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-3">
                        <div>
                            <span class="text-[10px] font-bold text-purple-600 uppercase">{{ $stat['course'] }}</span>
                            <h4 class="font-heading font-bold text-base text-slate-900">{{ $stat['name'] }}</h4>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div class="h-2.5 rounded-full transition-all duration-500 {{ $stat['rate'] >= 75 ? 'bg-emerald-500' : ($stat['rate'] >= 50 ? 'bg-amber-400' : 'bg-rose-500') }}"
                                 style="width: {{ $stat['rate'] }}%"></div>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-slate-400">{{ $stat['present'] }} / {{ $stat['total'] }} sessions attended</span>
                            <span class="font-bold {{ $stat['rate'] >= 75 ? 'text-emerald-600' : ($stat['rate'] >= 50 ? 'text-amber-600' : 'text-rose-600') }}">{{ $stat['rate'] }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Absence Requests Admin Override Panel -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="font-heading font-bold text-lg text-slate-900">All Pending Absence Requests</h3>
                <span class="text-xs font-mono font-bold text-amber-700 bg-amber-50 px-3 py-1 rounded-full border border-amber-200">
                    {{ $pendingAbsences->count() }} Pending
                </span>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="p-4">Student</th>
                                <th class="p-4">Cohort</th>
                                <th class="p-4">Requested Date</th>
                                <th class="p-4">Reason</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 text-center">Admin Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($pendingAbsences as $abs)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-4">
                                        <div class="font-bold text-slate-900">{{ $abs->student->name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ $abs->student->admission_number }}</div>
                                    </td>
                                    <td class="p-4 text-slate-600">{{ $abs->cohort->name }}</td>
                                    <td class="p-4 font-semibold text-slate-800">{{ $abs->requested_date->format('d M Y') }}</td>
                                    <td class="p-4 text-slate-600 max-w-[200px] truncate">{{ $abs->reason }}</td>
                                    <td class="p-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                            @if($abs->status === 'approved') bg-emerald-100 text-emerald-800
                                            @elseif($abs->status === 'rejected') bg-rose-100 text-rose-800
                                            @else bg-amber-100 text-amber-900
                                            @endif uppercase">
                                            {{ $abs->status }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-center">
                                        @if($abs->status === 'pending')
                                            <div class="flex items-center justify-center space-x-2">
                                                <form action="{{ route('admin.attendance.absence', $abs->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="approved">
                                                    <button type="submit" class="px-3 py-1.5 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg transition">Approve</button>
                                                </form>
                                                <form action="{{ route('admin.attendance.absence', $abs->id) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="rejected">
                                                    <button type="submit" class="px-3 py-1.5 text-xs font-bold text-rose-600 border border-rose-200 hover:bg-rose-50 rounded-lg transition">Reject</button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-[10px]">Reviewed</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400">No pending absence requests.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div>{{ $pendingAbsences->links() }}</div>
        </div>

    </div>
@endsection
