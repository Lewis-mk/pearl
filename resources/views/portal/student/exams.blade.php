@extends('layouts.portal')

@section('title', 'Exams, Quizzes & Certification — Pearl Training Institute')
@section('page_title', 'Exams, Quizzes & Certifications')
@section('page_subtitle', 'Take online timed quizzes, book physical sit-in slots, and view verified course certificates')

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
    <a href="{{ route('student.exams') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-pearl-700 text-white font-bold transition">
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
    <div class="space-y-10">

        <!-- Issued Certificates Highlight -->
        @if($certificates->isNotEmpty())
            <div class="space-y-4">
                <h3 class="font-heading font-bold text-lg text-slate-900 flex items-center space-x-2">
                    <span>🎓</span>
                    <span>Your Official Completion Certificates</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($certificates as $cert)
                        <div class="p-6 rounded-3xl bg-gradient-to-tr from-slate-900 to-teal-950 text-white border border-teal-800 shadow-xl space-y-4 relative overflow-hidden">
                            <div class="flex items-center justify-between">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-400 text-slate-950 uppercase tracking-wider">Verified Certificate</span>
                                <span class="font-mono text-xs text-amber-300">{{ $cert->certificate_number }}</span>
                            </div>

                            <div>
                                <span class="text-xs text-slate-400 block uppercase tracking-wider">Program Completed:</span>
                                <h4 class="font-heading font-bold text-xl text-white mt-0.5">{{ $cert->course_title }}</h4>
                                <div class="text-xs text-emerald-300 font-semibold mt-1">Honors Grade: {{ $cert->grade }}</div>
                            </div>

                            <div class="pt-4 border-t border-teal-900/60 flex items-center justify-between text-xs">
                                <span class="text-slate-400">Issue Date: {{ $cert->issue_date->format('d M Y') }}</span>
                                <a href="{{ route('certificate.verify', ['code' => $cert->verification_code]) }}" target="_blank" class="px-4 py-2 rounded-xl bg-pearl-600 hover:bg-pearl-500 text-white font-bold transition">
                                    Verify Online &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Active Exams & Quizzes -->
        <div class="space-y-4">
            <h3 class="font-heading font-bold text-lg text-slate-900">Assigned Exams & Assessments</h3>

            <div class="space-y-6">
                @forelse($exams as $exam)
                    @php
                        $mySubmission = $exam->submissions->first();
                    @endphp
                    <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="font-heading font-bold text-lg text-slate-900">{{ $exam->title }}</span>
                                    <span class="text-[10px] uppercase font-bold text-teal-800 bg-teal-50 px-2.5 py-0.5 rounded-full border border-teal-200">{{ str_replace('_', ' ', $exam->type) }}</span>
                                </div>
                                <span class="text-xs text-slate-400">{{ $exam->course->title }}</span>
                            </div>

                            <div class="flex items-center space-x-4 text-xs">
                                <div>⏱ Duration: <strong class="text-slate-700">{{ $exam->duration_minutes }} Mins</strong></div>
                                <div>🎯 Total Marks: <strong class="text-slate-700">{{ $exam->total_marks }}</strong></div>
                                <div>Pass: <strong class="text-emerald-700">{{ $exam->pass_percentage }}%</strong></div>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <p class="text-xs text-slate-700 leading-relaxed">{{ $exam->instructions }}</p>

                        <!-- Exam Submission Status & Actions -->
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                @if($mySubmission)
                                    <div class="space-y-1 text-xs">
                                        <div class="font-bold text-slate-900">
                                            Status: 
                                            @if($mySubmission->grade_status === 'auto_graded' || $mySubmission->grade_status === 'graded')
                                                <span class="{{ $mySubmission->passed ? 'text-emerald-600 font-extrabold' : 'text-rose-600 font-extrabold' }}">
                                                    {{ $mySubmission->passed ? 'Passed' : 'Failed' }} (Score: {{ $mySubmission->score }}/{{ $exam->total_marks }} &mdash; {{ $mySubmission->percentage }}%)
                                                </span>
                                            @elseif($mySubmission->sitin_slot_id)
                                                <span class="text-purple-700 font-bold">
                                                    Sit-in Exam Booked ({{ $mySubmission->sitinSlot->slot_datetime->format('d M Y, h:i A') }} @ {{ $mySubmission->sitinSlot->venue }})
                                                </span>
                                            @else
                                                <span class="text-amber-700 font-bold">Pending Manual Free-Response Grading by Trainer</span>
                                            @endif
                                        </div>
                                        @if($mySubmission->trainer_feedback)
                                            <div class="text-slate-600 italic">"{{ $mySubmission->trainer_feedback }}"</div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs font-semibold text-slate-700">You have not attempted this exam yet.</span>
                                @endif
                            </div>

                            <div class="shrink-0 flex items-center space-x-3">
                                @if(!$mySubmission || $mySubmission->retake_granted)
                                    @if($exam->type === 'online_quiz' || $exam->type === 'hybrid')
                                        <a href="{{ route('student.exams.take', $exam->id) }}" class="px-5 py-2.5 text-xs font-bold bg-pearl-600 hover:bg-pearl-700 text-white rounded-xl shadow-sm transition">
                                            Take Online Quiz &rarr;
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <!-- Physical Sit-in Slots Booking Section -->
                        @if($exam->sitinSlots->isNotEmpty() && (!$mySubmission || empty($mySubmission->sitin_slot_id)))
                            <div class="space-y-3 pt-2">
                                <h4 class="font-heading font-bold text-sm text-slate-900">Available Physical Sit-in Exam Slots:</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    @foreach($exam->sitinSlots as $slot)
                                        <div class="p-4 rounded-xl border border-slate-200 bg-white flex items-center justify-between">
                                            <div class="text-xs space-y-0.5">
                                                <div class="font-bold text-slate-900">{{ $slot->slot_datetime->format('d M Y, h:i A') }}</div>
                                                <div class="text-slate-400">📍 {{ $slot->venue }}</div>
                                                <div class="text-[10px] text-slate-400">
                                                    Capacity: <span class="font-bold text-slate-700">{{ $slot->booked_count }}/{{ $slot->capacity }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                @if($slot->hasAvailableSeats())
                                                    <form action="{{ route('student.exams.book_slot', $slot->id) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="px-3.5 py-1.5 text-xs font-bold bg-slate-900 hover:bg-pearl-600 text-white rounded-lg transition">
                                                            Book Slot
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="px-2.5 py-1 text-[10px] font-bold bg-slate-100 text-slate-400 rounded">Full</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>
                @empty
                    <div class="p-12 text-center text-xs text-slate-400 bg-white rounded-2xl border border-slate-200">
                        No active exams scheduled for your courses at this time.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
@endsection
