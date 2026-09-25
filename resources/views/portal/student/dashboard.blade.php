@extends('layouts.portal')

@section('title', 'Student Dashboard — Pearl Training Institute')
@section('page_title', 'Student Learning Dashboard')
@section('page_subtitle', 'Welcome back, ' . $user->name . ' (Adm: ' . ($user->admission_number ?? 'Pending Approval') . ')')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Learning & Academics</div>
    <a href="{{ route('student.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-pearl-700 text-white font-bold transition">
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
    <a href="{{ route('student.payments') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
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

        <!-- Top Overview Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Enrolled Program</span>
                    <h4 class="font-heading font-bold text-slate-900 text-sm mt-0.5 truncate max-w-[170px]">{{ $activeEnrollment->course->title ?? 'None' }}</h4>
                    <span class="text-[11px] text-teal-600 font-semibold">{{ $activeEnrollment->cohort->name ?? 'Awaiting batch' }}</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center text-xl">🎓</div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Curriculum Progress</span>
                    <h4 class="font-heading font-black text-2xl text-slate-900 mt-0.5">{{ $activeEnrollment->completion_percentage ?? 0 }}%</h4>
                    <span class="text-[11px] text-slate-400">Syllabus coverage</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center text-xl">📈</div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Total Fees Paid</span>
                    <h4 class="font-heading font-black text-xl text-emerald-600 font-mono mt-0.5">KES {{ number_format($totalPaid, 2) }}</h4>
                    <span class="text-[11px] text-slate-400">Sequential receipted</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xl">💰</div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Outstanding Balance</span>
                    <h4 class="font-heading font-black text-xl text-amber-600 font-mono mt-0.5">KES {{ number_format($totalBalance, 2) }}</h4>
                    <a href="{{ route('student.payments') }}" class="text-[11px] text-pearl-600 font-bold hover:underline">Pay via M-Pesa &rarr;</a>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-xl">💳</div>
            </div>
        </div>

        <!-- Visual Progress Bar -->
        @if($activeEnrollment)
            <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-3">
                <div class="flex justify-between items-center text-xs">
                    <span class="font-bold text-slate-700">Course Progress: {{ $activeEnrollment->course->title }}</span>
                    <span class="font-bold font-mono text-pearl-600">{{ $activeEnrollment->completion_percentage }}% Complete</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-pearl-500 to-teal-400 h-3 rounded-full transition-all duration-500" style="width: {{ $activeEnrollment->completion_percentage }}%"></div>
                </div>
                <div class="flex justify-between items-center text-[11px] text-slate-400 pt-1">
                    <span>Lead Trainer: <strong>{{ $activeEnrollment->cohort->leadTrainer->name ?? 'Peter Kamau' }}</strong></span>
                    <span>Intake Ends: <strong>{{ $activeEnrollment->cohort->end_date ? $activeEnrollment->cohort->end_date->format('d M Y') : 'Ongoing' }}</strong></span>
                </div>
            </div>
        @endif

        <!-- Upcoming Classes & Materials Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left 7 Cols: Upcoming Classes -->
            <div class="lg:col-span-7 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-bold text-lg text-slate-900">Upcoming Class Sessions</h3>
                    <a href="{{ route('student.classes') }}" class="text-xs font-bold text-pearl-600 hover:underline">Full Timetable &rarr;</a>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm divide-y divide-slate-100">
                    @forelse($upcomingClasses as $cls)
                        <div class="p-5 hover:bg-slate-50 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-heading font-bold text-sm text-slate-900">{{ $cls->title }}</span>
                                    @if($cls->is_postponed)
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-900">⚠️ Postponed</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-teal-100 text-teal-800 uppercase">{{ $cls->delivery_mode }}</span>
                                    @endif
                                </div>
                                
                                <div class="text-xs text-slate-400 flex items-center space-x-3">
                                    <span>🗓️ {{ $cls->scheduled_start->format('D, d M — h:i A') }}</span>
                                    @if($cls->physical_location)
                                        <span>📍 {{ $cls->physical_location }}</span>
                                    @endif
                                </div>

                                @if($cls->is_postponed)
                                    <div class="p-2.5 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-900 mt-2">
                                        <strong>Notice:</strong> {{ $cls->postponement_reason }}
                                        @if($cls->rescheduled_start)
                                            <div class="mt-0.5 font-semibold">Rescheduled to: {{ $cls->rescheduled_start->format('d M Y, h:i A') }}</div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="shrink-0">
                                @if($cls->meeting_url && !$cls->is_postponed)
                                    <a href="{{ $cls->meeting_url }}" target="_blank" class="px-4 py-2 text-xs font-bold bg-pearl-600 hover:bg-pearl-700 text-white rounded-xl shadow-sm transition inline-flex items-center space-x-1.5">
                                        <span>📹 Join Class</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs text-slate-400">
                            No upcoming class sessions scheduled at the moment.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right 5 Cols: Recent Course Materials & Exams -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Materials Box -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-heading font-bold text-lg text-slate-900">Learning Materials</h3>
                        <a href="{{ route('student.materials') }}" class="text-xs font-bold text-pearl-600 hover:underline">View all &rarr;</a>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 divide-y divide-slate-100">
                        @forelse($materials as $mat)
                            <div class="py-3 first:pt-0 last:pb-0 flex items-center justify-between">
                                <div class="flex items-center space-x-3 overflow-hidden">
                                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-700 flex items-center justify-center text-sm shrink-0">
                                        📄
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="font-bold text-xs text-slate-800 truncate">{{ $mat->title }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $mat->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                                <a href="{{ route('student.materials') }}" class="text-xs font-bold text-pearl-600 hover:underline shrink-0 ml-2">Download</a>
                            </div>
                        @empty
                            <div class="py-4 text-center text-xs text-slate-400">No learning materials uploaded yet.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Payments Box -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-heading font-bold text-lg text-slate-900">Recent Receipts</h3>
                        <a href="{{ route('student.payments') }}" class="text-xs font-bold text-pearl-600 hover:underline">Ledger &rarr;</a>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
                        @forelse($payments as $pmt)
                            <div class="flex items-center justify-between text-xs pb-2 border-b border-slate-100 last:border-0 last:pb-0">
                                <div>
                                    <span class="font-mono font-bold text-slate-900 block">{{ $pmt->receipt_number }}</span>
                                    <span class="text-[10px] text-slate-400 capitalize">{{ str_replace('_', ' ', $pmt->payment_method) }} &bull; {{ $pmt->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono font-bold text-emerald-600 block">KES {{ number_format($pmt->amount, 2) }}</span>
                                    <a href="{{ route('receipt.view', $pmt->receipt_number) }}" target="_blank" class="text-[10px] text-pearl-600 font-bold hover:underline">Print Receipt</a>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-xs text-slate-400 py-3">No payments recorded yet.</div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
