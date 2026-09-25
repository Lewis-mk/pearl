@extends('layouts.portal')

@section('title', 'Admin Portal Dashboard — Pearl Training Institute')
@section('page_title', 'Administrator Control Center')
@section('page_subtitle', 'Institution-wide oversight, financials, staff management, and governance escalation')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Administration</div>
    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-purple-700 text-white font-bold transition">
        <span>📊</span> <span>Dashboard</span>
    </a>
    <a href="{{ route('admin.users') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>👥</span> <span>Users & RBAC</span>
    </a>
    <a href="{{ route('admin.admissions') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>🎓</span> <span>Admissions Queue</span>
    </a>
    <a href="{{ route('admin.financials') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>💳</span> <span>Financials & Receipts</span>
    </a>
    <a href="{{ route('admin.courses') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📚</span> <span>Courses & Cohorts</span>
    </a>
    <a href="{{ route('admin.misconduct') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>🛡️</span> <span>Misconduct Inbox</span>
    </a>
    <a href="{{ route('admin.attendance') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>⏱️</span> <span>Attendance Oversight</span>
    </a>
    <a href="{{ route('admin.audit') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📋</span> <span>Audit Logs</span>
    </a>
    <a href="{{ route('admin.settings') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>⚙️</span> <span>System Settings</span>
    </a>
@endsection

@section('portal_content')
    <div class="space-y-8">

        <!-- KPI Metrics Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Total Students</span>
                    <h4 class="font-heading font-black text-2xl text-slate-900 mt-0.5">{{ $totalStudents }}</h4>
                    <span class="text-[11px] text-purple-600 font-semibold">{{ $pendingAdmissions }} pending approval</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center text-xl">🎓</div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Monthly Revenue</span>
                    <h4 class="font-heading font-black text-xl text-emerald-600 font-mono mt-0.5">KES {{ number_format($monthRevenue, 2) }}</h4>
                    <span class="text-[11px] text-slate-400">{{ now()->format('M Y') }}</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xl">💰</div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Misconduct Pending</span>
                    <h4 class="font-heading font-black text-2xl {{ $pendingMisconduct > 0 ? 'text-rose-600' : 'text-slate-900' }} mt-0.5">{{ $pendingMisconduct }}</h4>
                    <a href="{{ route('admin.misconduct') }}" class="text-[11px] text-rose-600 font-bold hover:underline">View reports &rarr;</a>
                </div>
                <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-700 flex items-center justify-center text-xl">🛡️</div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Active Courses</span>
                    <h4 class="font-heading font-black text-2xl text-slate-900 mt-0.5">{{ $activeCourses }}</h4>
                    <span class="text-[11px] text-slate-400">{{ $pendingCourses }} pending review</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-xl">📚</div>
            </div>
        </div>

        <!-- Charts / Income Charts Placeholder + Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left 7 Cols: Income Chart + Pending Admissions Quick View -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Revenue Summary Card with monthly breakdown -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-heading font-bold text-base text-slate-900">Monthly Income Breakdown ({{ now()->year }})</h3>
                        <a href="{{ route('admin.financials') }}" class="text-xs font-bold text-purple-600 hover:underline">Full Ledger &rarr;</a>
                    </div>

                    <div class="grid grid-cols-3 gap-3 text-center text-xs">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block uppercase text-[10px] font-semibold">Course Fees</span>
                            <strong class="font-mono text-emerald-600 text-sm font-bold block mt-0.5">KES {{ number_format($monthFeesIncome, 2) }}</strong>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block uppercase text-[10px] font-semibold">Print Shop</span>
                            <strong class="font-mono text-teal-600 text-sm font-bold block mt-0.5">KES {{ number_format($monthPrintshopIncome, 2) }}</strong>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 block uppercase text-[10px] font-semibold">Total Revenue</span>
                            <strong class="font-mono text-slate-900 text-sm font-bold block mt-0.5">KES {{ number_format($monthRevenue, 2) }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Pending Admissions Quick Approve Panel -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-heading font-bold text-base text-slate-900">Pending Admission Approvals ({{ $pendingAdmissions }})</h3>
                        <a href="{{ route('admin.admissions') }}" class="text-xs font-bold text-purple-600 hover:underline">Full Queue &rarr;</a>
                    </div>

                    @if($pendingAdmissions === 0)
                        <div class="p-6 bg-white rounded-2xl border border-slate-200 text-center text-xs text-slate-400">
                            No pending admission applications.
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
                            @foreach($latestPendingStudents as $student)
                                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-4 text-xs">
                                    <div>
                                        <div class="font-bold text-slate-900">{{ $student->name }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $student->email }} &bull; {{ $student->phone }}</div>
                                        <div class="text-[11px] text-purple-700 font-semibold mt-0.5">
                                            {{ $student->enrollments->first()?->course?->title ?? 'N/A' }}
                                        </div>
                                    </div>

                                    <div class="shrink-0 flex items-center space-x-2">
                                        <form action="{{ route('admin.admissions.approve', $student->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-1.5 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm transition">
                                                Approve &rarr;
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            <!-- Right 5 Cols: Recent Misconduct & Audit Trail -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Recent Misconduct Reports -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-heading font-bold text-base text-slate-900">Recent Misconduct Reports</h3>
                        <a href="{{ route('admin.misconduct') }}" class="text-xs font-bold text-rose-600 hover:underline">View All &rarr;</a>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
                        @forelse($recentMisconduct as $rep)
                            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-xs space-y-1.5">
                                <div class="flex items-center justify-between">
                                    <span class="font-mono text-slate-600">{{ $rep->reference_code }}</span>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded
                                        @if($rep->is_escalated_to_secondary_contact) bg-purple-100 text-purple-800
                                        @else bg-amber-100 text-amber-800
                                        @endif uppercase">
                                        {{ $rep->is_escalated_to_secondary_contact ? 'Escalated to Trustee' : $rep->status }}
                                    </span>
                                </div>
                                <div class="font-bold text-slate-900">{{ $rep->subject_summary }}</div>
                                <div class="text-[11px] text-slate-400">
                                    Reported Party: <strong class="text-slate-600">{{ $rep->reportedUser->name ?? 'N/A' }}</strong>
                                    @if($rep->hide_reporter_from_subject)
                                        &bull; <span class="text-teal-700 font-semibold">Reporter identity masked</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-xs text-slate-400">No misconduct reports filed.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Audit Logs -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h3 class="font-heading font-bold text-base text-slate-900">Recent Audit Activity</h3>
                        <a href="{{ route('admin.audit') }}" class="text-xs font-bold text-purple-600 hover:underline">Full Audit Log &rarr;</a>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-2">
                        @forelse($recentAuditLogs as $log)
                            <div class="py-2 border-b border-slate-100 last:border-0 text-xs flex items-start space-x-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-purple-500 mt-1.5 shrink-0"></div>
                                <div>
                                    <span class="font-semibold text-slate-800">{{ $log->subject }}</span>
                                    <span class="text-[11px] text-slate-400 block">{{ $log->description }} &bull; {{ $log->created_at->diffForHumans() }}</span>
                                    @if($log->user)
                                        <span class="text-[10px] text-slate-400">By: {{ $log->user->name }}</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="py-4 text-center text-xs text-slate-400">No audit events yet.</div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
