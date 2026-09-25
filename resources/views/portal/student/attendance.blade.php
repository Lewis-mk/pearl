@extends('layouts.portal')

@section('title', 'Attendance & Absence Requests — Pearl Training Institute')
@section('page_title', 'My Attendance & Absence Requests')
@section('page_subtitle', 'Review your classroom attendance history and submit formal absence requests with documentation')

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
    <a href="{{ route('student.attendance') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-pearl-700 text-white font-bold transition">
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
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Left 7 Cols: Attendance Log & History -->
        <div class="lg:col-span-7 space-y-6">
            <h3 class="font-heading font-bold text-lg text-slate-900">Attendance Record</h3>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 font-bold uppercase text-[10px]">
                            <tr>
                                <th class="p-4">Class Session</th>
                                <th class="p-4">Date</th>
                                <th class="p-4">Cohort</th>
                                <th class="p-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($attendances as $att)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="p-4 font-semibold text-slate-900">
                                        {{ $att->classSession->title }}
                                    </td>
                                    <td class="p-4 text-slate-400">
                                        {{ $att->classSession->scheduled_start->format('d M Y') }}
                                    </td>
                                    <td class="p-4 text-slate-600">
                                        {{ $att->classSession->cohort->name }}
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold 
                                            @if($att->status === 'present') bg-emerald-100 text-emerald-800
                                            @elseif($att->status === 'absent') bg-rose-100 text-rose-800
                                            @elseif($att->status === 'excused') bg-sky-100 text-sky-800
                                            @else bg-amber-100 text-amber-800
                                            @endif uppercase">
                                            {{ $att->status }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-slate-400">No attendance sessions logged yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>
                {{ $attendances->links() }}
            </div>

            <!-- Past Absence Requests List -->
            <div class="space-y-3 pt-4">
                <h4 class="font-heading font-bold text-base text-slate-900">Your Submitted Absence Requests</h4>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
                    @forelse($absenceRequests as $req)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-slate-900">Requested Date: {{ $req->requested_date->format('d M Y') }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold 
                                    @if($req->status === 'approved') bg-emerald-100 text-emerald-800
                                    @elseif($req->status === 'rejected') bg-rose-100 text-rose-800
                                    @else bg-amber-100 text-amber-800
                                    @endif uppercase">
                                    {{ $req->status }}
                                </span>
                            </div>
                            <p class="text-slate-600 italic">"{{ $req->reason }}"</p>
                            @if($req->reviewer_notes)
                                <div class="text-[11px] text-teal-800 font-medium">Trainer Review: {{ $req->reviewer_notes }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-3 text-xs text-slate-400">No absence requests filed.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right 5 Cols: Submit Absence Request Form -->
        <div class="lg:col-span-5">
            <div class="bg-white p-7 rounded-3xl border border-slate-200 shadow-md space-y-5 sticky top-24">
                <div>
                    <span class="text-[10px] uppercase font-bold text-pearl-600">Formal Leave Request</span>
                    <h3 class="font-heading font-black text-xl text-slate-900">Request Class Absence</h3>
                    <p class="text-xs text-slate-400 mt-1">Submit ahead of time with medical or official documentation for trainer review.</p>
                </div>

                <form action="{{ route('student.attendance.absence') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Select Cohort *</label>
                        <select name="cohort_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                            @foreach($enrolledCohorts as $enr)
                                <option value="{{ $enr->cohort->id }}">{{ $enr->course->title }} &mdash; {{ $enr->cohort->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Requested Date of Absence *</label>
                        <input type="date" name="requested_date" required min="{{ today()->toDateString() }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Reason for Absence *</label>
                        <textarea name="reason" rows="3" required placeholder="Provide clear reason (e.g. medical appointment, family emergency)..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Optional Evidence File (Doctor note, travel doc)</label>
                        <input type="file" name="evidence_file" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-pearl-50 file:text-pearl-700 hover:file:bg-pearl-100 border border-slate-300 rounded-xl p-1 bg-slate-50">
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-xl font-heading font-bold text-xs text-white bg-pearl-600 hover:bg-pearl-700 shadow-md shadow-pearl-700/20 transition transform active:scale-95 text-center">
                        Submit Absence Request &rarr;
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection
