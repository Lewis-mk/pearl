@extends('layouts.portal')

@section('title', 'Exam & Question Bank Manager — Pearl Training Institute')
@section('page_title', 'Exams & Question Bank')
@section('page_subtitle', 'Create online quizzes, configure physical sit-in capacity dates, and grade student submissions')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Instruction & Cohorts</div>
    <a href="{{ route('trainer.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📊</span> <span>Dashboard</span>
    </a>
    <a href="{{ route('trainer.classes') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>🗓️</span> <span>Schedule & Classes</span>
    </a>
    <a href="{{ route('trainer.materials') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📁</span> <span>Upload Materials</span>
    </a>
    <a href="{{ route('trainer.exams') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-blue-700 text-white font-bold transition">
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
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Left 7 Cols: Existing Exams -->
        <div class="lg:col-span-7 space-y-6">
            <h3 class="font-heading font-bold text-lg text-slate-900">Your Exam Repository</h3>

            <div class="space-y-4">
                @forelse($exams as $ex)
                    <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-4 hover:border-blue-300 transition">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                            <div>
                                <span class="font-heading font-bold text-base text-slate-900">{{ $ex->title }}</span>
                                <span class="text-[10px] uppercase font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded ml-2">{{ str_replace('_', ' ', $ex->type) }}</span>
                            </div>
                            <span class="px-2.5 py-1 text-[10px] font-bold bg-slate-100 text-slate-700 rounded-full">
                                {{ $ex->pass_percentage }}% Pass Mark
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-3 text-center text-xs">
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 text-[10px] uppercase font-semibold">Questions</span>
                                <div class="font-bold text-slate-900 mt-0.5">{{ $ex->questions->count() }} ({{ $ex->total_marks }} Marks)</div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 text-[10px] uppercase font-semibold">Sit-in Slots</span>
                                <div class="font-bold text-slate-900 mt-0.5">{{ $ex->sitinSlots->count() }} Slots</div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 text-[10px] uppercase font-semibold">Submissions</span>
                                <div class="font-bold text-blue-600 mt-0.5">{{ $ex->submissions->count() }} Taken</div>
                            </div>
                        </div>

                        <div class="pt-2 flex flex-wrap items-center justify-between gap-2">
                            <span class="text-[11px] text-slate-400">Course: <strong>{{ $ex->course->title }}</strong></span>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('trainer.exam.questions', $ex->id) }}" class="px-3.5 py-1.5 text-xs font-bold bg-slate-900 hover:bg-blue-600 text-white rounded-lg transition">
                                    ⚙️ Manage Questions & Slots
                                </a>
                                <a href="{{ route('trainer.exam.grade', $ex->id) }}" class="px-3.5 py-1.5 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition">
                                    Grade Submissions &rarr;
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400 bg-white rounded-2xl border border-slate-200">
                        No exams created yet. Create an assessment below.
                    </div>
                @endforelse
            </div>

            <div>
                {{ $exams->links() }}
            </div>
        </div>

        <!-- Right 5 Cols: Create New Exam Form -->
        <div class="lg:col-span-5">
            <div class="bg-white p-7 sm:p-8 rounded-3xl border border-slate-200 shadow-md space-y-5 sticky top-24">
                <div>
                    <span class="text-[10px] uppercase font-bold text-blue-600 tracking-wider">Assessment Setup</span>
                    <h3 class="font-heading font-black text-xl text-slate-900">Create New Exam</h3>
                    <p class="text-xs text-slate-400 mt-1">Configure duration, passing percentage, and assessment type.</p>
                </div>

                <form action="{{ route('trainer.exams.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Select Course *</label>
                        <select name="course_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50 font-medium">
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Select Cohort (Optional)</label>
                        <select name="cohort_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50">
                            <option value="">-- All Cohorts of Course --</option>
                            @foreach($cohorts as $coh)
                                <option value="{{ $coh->id }}">{{ $coh->course->title }} &mdash; {{ $coh->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Exam Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Mid-Term Practical Exam & Quiz" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Assessment Format *</label>
                        <select name="type" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50 font-medium">
                            <option value="online_quiz">Online Timed Quiz (Auto-graded MCQ + Free Response)</option>
                            <option value="physical_sitin">Physical Sit-in Exam Only (Slot Booking)</option>
                            <option value="hybrid">Hybrid (Online Quiz + Physical Sit-in Option)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Duration (Mins) *</label>
                            <input type="number" name="duration_minutes" value="60" min="10" required class="w-full px-2.5 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Total Marks *</label>
                            <input type="number" name="total_marks" value="100" min="10" required class="w-full px-2.5 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Pass % *</label>
                            <input type="number" name="pass_percentage" value="60" min="40" max="100" required class="w-full px-2.5 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Instructions for Students</label>
                        <textarea name="instructions" rows="2" placeholder="Time limit rules, allowed tools..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50"></textarea>
                    </div>

                    <label class="flex items-center space-x-2 text-xs text-slate-700 cursor-pointer pt-1">
                        <input type="checkbox" name="randomize_questions" value="1" checked class="rounded text-blue-600 focus:ring-blue-500">
                        <span>Randomize question sequence per attempt</span>
                    </label>

                    <button type="submit" class="w-full py-3.5 rounded-xl font-heading font-bold text-xs text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-700/20 transition transform active:scale-95 text-center">
                        Create Exam & Build Question Bank &rarr;
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection
