@extends('layouts.portal')

@section('title', 'Courses & Cohorts Management — Pearl Training Institute')
@section('page_title', 'Course Catalogue & Cohort Management')
@section('page_subtitle', 'Review trainer proposals, publish courses, create intake cohorts, and manage capacity')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Administration</div>
    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📊</span><span>Dashboard</span></a>
    <a href="{{ route('admin.users') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>👥</span><span>Users & RBAC</span></a>
    <a href="{{ route('admin.admissions') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>🎓</span><span>Admissions Queue</span></a>
    <a href="{{ route('admin.financials') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>💳</span><span>Financials & Receipts</span></a>
    <a href="{{ route('admin.courses') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-purple-700 text-white font-bold transition"><span>📚</span><span>Courses & Cohorts</span></a>
    <a href="{{ route('admin.misconduct') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>🛡️</span><span>Misconduct Inbox</span></a>
    <a href="{{ route('admin.attendance') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>⏱️</span><span>Attendance Oversight</span></a>
    <a href="{{ route('admin.audit') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📋</span><span>Audit Logs</span></a>
    <a href="{{ route('admin.settings') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>⚙️</span><span>System Settings</span></a>
@endsection

@section('portal_content')
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

        <!-- Left: Course List with Approve / Archive Controls -->
        <div class="lg:col-span-7 space-y-6">
            <h3 class="font-heading font-bold text-lg text-slate-900">All Courses ({{ $courses->total() }})</h3>

            <div class="space-y-5">
                @forelse($courses as $course)
                    <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
                            <div>
                                <span class="font-heading font-bold text-base text-slate-900">{{ $course->title }}</span>
                                <span class="text-[10px] text-slate-400 ml-2 uppercase">{{ $course->category }}</span>
                            </div>
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-full {{ $course->status === 'published' ? 'bg-emerald-100 text-emerald-800' : ($course->status === 'pending_review' ? 'bg-amber-100 text-amber-900' : 'bg-slate-100 text-slate-600') }} uppercase">
                                {{ $course->status }}
                            </span>
                        </div>

                        <div class="grid grid-cols-3 gap-3 text-center text-xs">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 text-[10px] uppercase">Duration</span>
                                <div class="font-bold text-slate-900 mt-0.5">{{ $course->duration_weeks }} Weeks</div>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 text-[10px] uppercase">Total Fee</span>
                                <div class="font-mono font-bold text-emerald-600 mt-0.5">KES {{ number_format($course->total_fee) }}</div>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-slate-400 text-[10px] uppercase">Active Cohorts</span>
                                <div class="font-bold text-slate-900 mt-0.5">{{ $course->cohorts->where('status', 'active')->count() }}</div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <div>
                                @if($course->created_by_user)
                                    <span class="text-[10px] text-slate-400">Proposed by: <strong>{{ $course->created_by_user->name }}</strong></span>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($course->status === 'pending_review')
                                    <form action="{{ route('admin.courses.approve', $course->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3.5 py-1.5 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm transition">
                                            ✓ Publish Course
                                        </button>
                                    </form>
                                @endif
                                <button type="button" onclick="document.getElementById('cohort-form-{{ $course->id }}').classList.toggle('hidden')" class="px-3.5 py-1.5 text-xs font-bold bg-slate-900 hover:bg-purple-700 text-white rounded-lg transition">
                                    + Add Cohort
                                </button>
                            </div>
                        </div>

                        <!-- Cohort Add Form (inline collapsible) -->
                        <div id="cohort-form-{{ $course->id }}" class="hidden p-4 rounded-2xl bg-purple-50/60 border border-purple-100 space-y-3">
                            <h4 class="font-heading font-bold text-xs text-purple-950 uppercase">Add New Intake Cohort for {{ $course->title }}</h4>
                            <form action="{{ route('admin.cohorts.store', $course->id) }}" method="POST" class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                @csrf
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-bold text-purple-900 uppercase mb-1">Cohort Name *</label>
                                    <input type="text" name="name" required placeholder="e.g. Sep 2026 Cohort" class="w-full px-3 py-2 rounded-xl border border-purple-200 text-xs bg-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-purple-900 uppercase mb-1">Start Date *</label>
                                    <input type="date" name="start_date" required class="w-full px-2 py-2 rounded-xl border border-purple-200 text-xs bg-white">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-purple-900 uppercase mb-1">Max Capacity</label>
                                    <input type="number" name="max_capacity" value="30" min="1" class="w-full px-2 py-2 rounded-xl border border-purple-200 text-xs bg-white">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-[10px] font-bold text-purple-900 uppercase mb-1">Lead Trainer</label>
                                    <select name="lead_trainer_id" class="w-full px-3 py-2 rounded-xl border border-purple-200 text-xs bg-white">
                                        <option value="">-- Unassigned --</option>
                                        @foreach($trainers as $t)
                                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-span-2 flex justify-end pt-1">
                                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-purple-600 hover:bg-purple-700 text-white transition">Create Cohort</button>
                                </div>
                            </form>
                        </div>

                        <!-- Existing Cohorts List -->
                        @if($course->cohorts->isNotEmpty())
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                @foreach($course->cohorts as $coh)
                                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs flex items-center justify-between">
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $coh->name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $coh->enrollments->count() }}/{{ $coh->max_capacity }} enrolled &bull; {{ $coh->status }}</div>
                                        </div>
                                        <span class="px-2 py-0.5 text-[10px] font-bold rounded {{ $coh->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' }} uppercase">
                                            {{ $coh->status }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400 bg-white rounded-2xl border border-slate-200">No courses found.</div>
                @endforelse
            </div>

            <div>{{ $courses->links() }}</div>
        </div>

        <!-- Right: Quick Create Course Form -->
        <div class="lg:col-span-5">
            <div class="bg-white p-7 sm:p-8 rounded-3xl border border-slate-200 shadow-md space-y-5 sticky top-24">
                <div>
                    <span class="text-[10px] uppercase font-bold text-purple-600 tracking-wider">Direct Publish</span>
                    <h3 class="font-heading font-black text-xl text-slate-900">Create & Publish Course</h3>
                    <p class="text-xs text-slate-400 mt-1">As admin you can create and publish courses directly.</p>
                </div>

                <form action="{{ route('admin.courses.store') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Computer Packages & Office Suite" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Category *</label>
                        <select name="category" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-slate-50 font-medium">
                            <option value="Technology">Technology & Programming</option>
                            <option value="Creative Design">Creative Design & Media</option>
                            <option value="Business">Business & Accounting</option>
                            <option value="Basic Computing">Basic Computing & Cyber</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Short Tagline *</label>
                        <input type="text" name="short_description" required placeholder="One sentence description..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-slate-50">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Description *</label>
                        <textarea name="description" rows="3" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-slate-50"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Curriculum (one module per line) *</label>
                        <textarea name="curriculum_outline" rows="3" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50" placeholder="Module 1: ...&#10;Module 2: ..."></textarea>
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Weeks *</label>
                            <input type="number" name="duration_weeks" value="8" min="1" required class="w-full px-2 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Total Fee *</label>
                            <input type="number" name="total_fee" value="25000" required class="w-full px-2 py-2 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-700 uppercase mb-1">Deposit *</label>
                            <input type="number" name="deposit_required" value="8000" required class="w-full px-2 py-2 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50">
                        </div>
                    </div>
                    <input type="hidden" name="status" value="published">
                    <button type="submit" class="w-full py-3.5 rounded-xl font-heading font-bold text-xs text-white bg-purple-600 hover:bg-purple-700 shadow-md transition text-center">
                        Create & Publish Course &rarr;
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection
