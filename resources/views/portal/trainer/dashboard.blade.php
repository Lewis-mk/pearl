@extends('layouts.portal')

@section('title', 'Trainer Portal Dashboard — Pearl Training Institute')
@section('page_title', 'Instructor Dashboard')
@section('page_subtitle', 'Welcome back, ' . $user->name . ' (' . ($user->staff_official_email ?? 'Trainer') . ')')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Instruction & Cohorts</div>
    <a href="{{ route('trainer.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-blue-700 text-white font-bold transition">
        <span>📊</span> <span>Dashboard</span>
    </a>
    <a href="{{ route('trainer.classes') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>🗓️</span> <span>Schedule & Classes</span>
    </a>
    <a href="{{ route('trainer.materials') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📁</span> <span>Upload Materials</span>
    </a>
    <a href="{{ route('trainer.exams') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📝</span> <span>Exams & Question Bank</span>
    </a>

    @canPermission('course.create')
        <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-4 mb-1">Curriculum Permissions</div>
        <a href="{{ route('trainer.courses') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
            <span>📚</span> <span>Propose Courses</span>
        </a>
    @endcanPermission

    @canPermission('student.admit')
        <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-4 mb-1">Admissions Permissions</div>
        <a href="{{ route('trainer.admission') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
            <span>➕</span> <span>In-Person Admission</span>
        </a>
    @endcanPermission
@endsection

@section('portal_content')
    <div class="space-y-8">

        <!-- Metrics Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Assigned Cohorts</span>
                    <h4 class="font-heading font-black text-2xl text-slate-900 mt-0.5">{{ $cohorts->count() }}</h4>
                    <span class="text-[11px] text-blue-600 font-semibold">{{ $totalStudents }} Enrolled Students</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center text-xl">👥</div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Today's Classes</span>
                    <h4 class="font-heading font-black text-2xl text-slate-900 mt-0.5">{{ $todayClasses->count() }}</h4>
                    <span class="text-[11px] text-slate-400">Scheduled sessions</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-teal-50 text-teal-700 flex items-center justify-center text-xl">🗓️</div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Pending Grading</span>
                    <h4 class="font-heading font-black text-2xl text-amber-600 mt-0.5">{{ $pendingGrading->count() }}</h4>
                    <span class="text-[11px] text-slate-400">Free response exams</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-xl">✍️</div>
            </div>

            <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400">Absence Requests</span>
                    <h4 class="font-heading font-black text-2xl text-purple-600 mt-0.5">{{ $pendingAbsences->count() }}</h4>
                    <span class="text-[11px] text-slate-400">Awaiting your review</span>
                </div>
                <div class="w-11 h-11 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center text-xl">⏱️</div>
            </div>
        </div>

        <!-- Today's Schedule & Attendance Section -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left 7 Cols: Today's Classes & Attendance Quick Launch -->
            <div class="lg:col-span-7 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="font-heading font-bold text-lg text-slate-900">Today's Class Schedule</h3>
                    <a href="{{ route('trainer.classes') }}" class="text-xs font-bold text-blue-600 hover:underline">Schedule New Class &rarr;</a>
                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden divide-y divide-slate-100">
                    @forelse($todayClasses as $cls)
                        <div class="p-5 hover:bg-slate-50 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center space-x-2">
                                    <span class="font-heading font-bold text-sm text-slate-900">{{ $cls->title }}</span>
                                    <span class="text-[10px] font-bold bg-slate-100 text-slate-700 px-2 py-0.5 rounded">{{ $cls->cohort->name }}</span>
                                </div>
                                <div class="text-xs text-slate-400">
                                    🕒 {{ $cls->scheduled_start->format('h:i A') }} &mdash; {{ $cls->scheduled_end->format('h:i A') }} &bull; {{ $cls->physical_location ?? 'Online Meeting' }}
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 shrink-0">
                                <a href="{{ route('trainer.classes.attendance', $cls->id) }}" class="px-3.5 py-2 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-sm transition">
                                    Take Attendance
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-xs text-slate-400">
                            No classes scheduled for today.
                        </div>
                    @endforelse
                </div>

                <!-- Assigned Cohorts Summary -->
                <div class="space-y-3 pt-4">
                    <h3 class="font-heading font-bold text-lg text-slate-900">My Intake Cohorts</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($cohorts as $coh)
                            <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-3">
                                <div>
                                    <span class="text-[10px] font-bold uppercase text-blue-600 tracking-wider">{{ $coh->course->category }}</span>
                                    <h4 class="font-heading font-bold text-base text-slate-900 leading-snug">{{ $coh->name }}</h4>
                                    <div class="text-xs text-slate-400">{{ $coh->course->title }}</div>
                                </div>
                                <div class="flex justify-between items-center text-xs border-t border-slate-100 pt-3">
                                    <span class="text-slate-600">Students: <strong>{{ $coh->enrollments->count() }} / {{ $coh->max_capacity }}</strong></span>
                                    <span class="px-2 py-0.5 text-[10px] font-bold bg-emerald-50 text-emerald-700 rounded">{{ $coh->status }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right 5 Cols: Pending Absence Approvals & Exam Submissions -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- Pending Absences Box -->
                <div class="space-y-3">
                    <h3 class="font-heading font-bold text-lg text-slate-900">Pending Absence Requests</h3>
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
                        @forelse($pendingAbsences as $abs)
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2 text-xs">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <strong class="font-bold text-slate-900">{{ $abs->student->name }}</strong>
                                        <div class="text-[10px] text-slate-400">{{ $abs->cohort->name }} &bull; Date: {{ $abs->requested_date->format('d M Y') }}</div>
                                    </div>
                                    <span class="px-2 py-0.5 text-[10px] font-bold bg-amber-100 text-amber-900 rounded">Pending</span>
                                </div>
                                <p class="text-slate-600 italic">"{{ $abs->reason }}"</p>
                                <div class="pt-2 border-t border-slate-200 flex items-center justify-end space-x-2">
                                    <form action="{{ route('trainer.absences.review', $abs->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="px-3 py-1 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-lg">Reject</button>
                                    </form>
                                    <form action="{{ route('trainer.absences.review', $abs->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" class="px-3 py-1 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm">Approve Leave</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-xs text-slate-400">No pending absence requests.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Pending Exam Grading Box -->
                <div class="space-y-3">
                    <h3 class="font-heading font-bold text-lg text-slate-900">Exams Awaiting Grading</h3>
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
                        @forelse($pendingGrading as $sub)
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between text-xs">
                                <div>
                                    <strong class="text-slate-900 block">{{ $sub->exam->title }}</strong>
                                    <span class="text-[10px] text-slate-400">Student: {{ $sub->student->name }}</span>
                                </div>
                                <a href="{{ route('trainer.exam.grade', $sub->exam->id) }}" class="px-3 py-1.5 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm">
                                    Grade &rarr;
                                </a>
                            </div>
                        @empty
                            <div class="text-center py-4 text-xs text-slate-400">All student submissions are graded!</div>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

    </div>
@endsection
