@extends('layouts.portal')

@section('title', 'Grade Submissions: ' . $exam->title . ' — Pearl Training Institute')
@section('page_title', 'Grade Student Submissions')
@section('page_subtitle', $exam->title . ' (Total Marks: ' . $exam->total_marks . ' &bull; Pass: ' . $exam->pass_percentage . '%)')

@section('sidebar_menu')
    <a href="{{ route('trainer.exams') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>&larr;</span> <span>Back to Exams</span>
    </a>
@endsection

@section('portal_content')
    <div class="max-w-5xl mx-auto space-y-6">

        <div class="space-y-6">
            @forelse($submissions as $sub)
                <div class="p-6 sm:p-8 bg-white rounded-3xl border border-slate-200 shadow-sm space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center font-bold text-xs uppercase">
                                {{ substr($sub->student->name, 0, 2) }}
                            </div>
                            <div>
                                <h3 class="font-heading font-bold text-lg text-slate-900">{{ $sub->student->name }}</h3>
                                <div class="text-xs text-slate-400">Adm No: <strong class="font-mono text-slate-700">{{ $sub->student->admission_number ?? 'N/A' }}</strong> &bull; Submitted: {{ $sub->submitted_at ? $sub->submitted_at->format('d M Y, h:i A') : 'N/A' }}</div>
                            </div>
                        </div>

                        <div>
                            <span class="px-3 py-1 text-xs font-bold rounded-full 
                                @if($sub->grade_status === 'graded' || $sub->grade_status === 'auto_graded')
                                    {{ $sub->passed ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}
                                @else
                                    bg-amber-100 text-amber-900
                                @endif uppercase">
                                Status: {{ $sub->grade_status }} 
                                @if($sub->score !== null)
                                    ({{ $sub->score }}/{{ $exam->total_marks }} &mdash; {{ $sub->percentage }}%)
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Answers Inspection -->
                    @if(!empty($sub->student_answers))
                        <div class="space-y-3 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <h4 class="font-heading font-bold text-xs text-slate-700 uppercase tracking-wider">Submitted Answers by Student:</h4>
                            <div class="space-y-3">
                                @foreach($exam->questions as $index => $q)
                                    <div class="p-3 bg-white rounded-xl border border-slate-200/80 text-xs space-y-1">
                                        <div class="font-bold text-slate-900">Q{{ $index + 1 }}: {{ $q->question_text }} ({{ $q->marks }} Marks)</div>
                                        <div class="text-slate-700 font-mono bg-slate-50 p-2 rounded border border-slate-100">
                                            Student Answer: <strong>{{ $sub->student_answers[$q->id] ?? 'No answer provided' }}</strong>
                                        </div>
                                        @if($q->type === 'multiple_choice')
                                            <div class="text-[11px] text-emerald-700">Correct Answer Key: {{ $q->correct_answer }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Trainer Grading Form -->
                    <form action="{{ route('trainer.exam.grade.save', $sub->id) }}" method="POST" class="p-5 rounded-2xl bg-blue-50/60 border border-blue-100 space-y-4">
                        @csrf

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-blue-950 uppercase tracking-wider mb-1">Total Score Out of {{ $exam->total_marks }} *</label>
                                <input type="number" step="0.5" name="score" value="{{ old('score', $sub->score) }}" min="0" max="{{ $exam->total_marks }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-blue-300 text-sm font-mono font-bold focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-blue-950 uppercase tracking-wider mb-1">Feedback / Remarks to Student</label>
                                <input type="text" name="trainer_feedback" value="{{ old('trainer_feedback', $sub->trainer_feedback) }}" placeholder="e.g. Excellent solution structure in coding question 3." class="w-full px-3.5 py-2.5 rounded-xl border border-blue-300 text-xs focus:ring-2 focus:ring-blue-500 bg-white">
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-[11px] text-blue-800">Saving a passing score will automatically issue an official verified completion certificate.</span>
                            <button type="submit" class="px-6 py-2.5 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-sm transition">
                                Save Grade & Release Result &rarr;
                            </button>
                        </div>
                    </form>
                </div>
            @empty
                <div class="p-12 text-center text-xs text-slate-400 bg-white rounded-2xl border border-slate-200">
                    No submissions found for this exam yet.
                </div>
            @endforelse
        </div>

    </div>
@endsection
