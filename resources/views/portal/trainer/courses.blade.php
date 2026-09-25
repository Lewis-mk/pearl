@extends('layouts.portal')

@section('title', 'Propose New Course — Pearl Training Institute')
@section('page_title', 'Course Curriculum Proposal')
@section('page_subtitle', 'Draft and submit new technical short course outlines for administrative review and publication')

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
    <a href="{{ route('trainer.exams') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📝</span> <span>Exams & Question Bank</span>
    </a>

    @canPermission('course.create')
        <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-4 mb-1">Curriculum Permissions</div>
        <a href="{{ route('trainer.courses') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-blue-700 text-white font-bold transition">
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
        
        <!-- Left 7 Cols: Submitted Course Proposals -->
        <div class="lg:col-span-7 space-y-6">
            <h3 class="font-heading font-bold text-lg text-slate-900">Your Proposed Courses</h3>

            <div class="space-y-4">
                @forelse($myCourses as $c)
                    <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-2">
                            <div>
                                <span class="font-heading font-bold text-base text-slate-900">{{ $c->title }}</span>
                                <span class="text-[10px] uppercase font-bold text-slate-400 ml-2">({{ $c->category }})</span>
                            </div>
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full 
                                @if($c->status === 'published') bg-emerald-100 text-emerald-800
                                @elseif($c->status === 'pending_review') bg-amber-100 text-amber-900
                                @else bg-slate-100 text-slate-600
                                @endif uppercase">
                                {{ $c->status }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600">{{ $c->short_description }}</p>
                        <div class="flex items-center justify-between text-xs text-slate-400 pt-2">
                            <span>Duration: <strong>{{ $c->duration_weeks }} Weeks</strong></span>
                            <span>Fee: <strong class="font-mono text-slate-900 font-bold">KES {{ number_format($c->total_fee) }}</strong></span>
                            <span>Cohorts: <strong>{{ $c->cohorts->count() }}</strong></span>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400 bg-white rounded-2xl border border-slate-200">
                        You have not proposed any courses yet.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Right 5 Cols: Submit Course Proposal Form -->
        <div class="lg:col-span-5">
            <div class="bg-white p-7 sm:p-8 rounded-3xl border border-slate-200 shadow-md space-y-5 sticky top-24">
                <div>
                    <span class="text-[10px] uppercase font-bold text-blue-600 tracking-wider">Curriculum Proposal</span>
                    <h3 class="font-heading font-black text-xl text-slate-900">Draft New Course</h3>
                    <p class="text-xs text-slate-400 mt-1">Submitted proposals route to the Administrator for approval before publishing.</p>
                </div>

                <form action="{{ route('trainer.courses.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Course Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Python for Data Analysis & AI" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Category *</label>
                        <select name="category" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50 font-medium">
                            <option value="Technology">Technology & Programming</option>
                            <option value="Creative Design">Creative Design & Media</option>
                            <option value="Business">Business & Accounting</option>
                            <option value="Basic Computing">Basic Computing & Cyber</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Short Tagline Summary *</label>
                        <input type="text" name="short_description" required placeholder="Brief 1-sentence summary..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Course Description *</label>
                        <textarea name="description" rows="3" required placeholder="Target audience, objectives..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-slate-50"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Curriculum Outline (One module per line) *</label>
                        <textarea name="curriculum_outline" rows="4" required placeholder="Module 1: Setup & Python Syntax&#10;Module 2: Pandas & NumPy&#10;Module 3: Visualizations & Seaborn" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50"></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Duration (Wks) *</label>
                            <input type="number" name="duration_weeks" value="8" min="1" required class="w-full px-2 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Total Fee *</label>
                            <input type="number" name="total_fee" value="25000" min="0" required class="w-full px-2 py-2 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Min Deposit *</label>
                            <input type="number" name="deposit_required" value="8000" min="0" required class="w-full px-2 py-2 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50">
                        </div>
                    </div>

                    <label class="flex items-center space-x-2 text-xs text-slate-700 cursor-pointer pt-1">
                        <input type="checkbox" name="requires_guardian_info" value="1" class="rounded text-blue-600 focus:ring-blue-500">
                        <span>Requires guardian info during registration (Minors/Teens)</span>
                    </label>

                    <button type="submit" class="w-full py-3.5 rounded-xl font-heading font-bold text-xs text-white bg-blue-600 hover:bg-blue-700 shadow-md transition text-center">
                        Submit Proposal to Admin &rarr;
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection
