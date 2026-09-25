@extends('layouts.portal')

@section('title', 'Learning Materials — Pearl Training Institute')
@section('page_title', 'Course Learning Materials')
@section('page_subtitle', 'Lecture slides, practical guides, cheat sheets, and source code repositories')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Learning & Academics</div>
    <a href="{{ route('student.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📊</span> <span>My Dashboard</span>
    </a>
    <a href="{{ route('student.classes') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>🗓️</span> <span>Timetable & Classes</span>
    </a>
    <a href="{{ route('student.materials') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-pearl-700 text-white font-bold transition">
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
    <div class="space-y-6">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($materials as $mat)
                <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col justify-between space-y-4 hover:border-pearl-400 transition">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] uppercase font-bold text-pearl-700 bg-pearl-50 px-2 py-0.5 rounded border border-pearl-200">{{ $mat->file_type }}</span>
                            <span class="text-[10px] text-slate-400">{{ $mat->created_at->format('d M Y') }}</span>
                        </div>
                        <h4 class="font-heading font-bold text-base text-slate-900 leading-snug">{{ $mat->title }}</h4>
                        <p class="text-xs text-slate-400 leading-relaxed">{{ $mat->description }}</p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-[10px] text-slate-400">By: <strong>{{ $mat->uploader->name }}</strong></span>
                        @if($mat->external_url)
                            <a href="{{ $mat->external_url }}" target="_blank" class="px-3.5 py-2 text-xs font-bold bg-slate-900 hover:bg-pearl-600 text-white rounded-xl transition inline-flex items-center space-x-1">
                                <span>External Link &rarr;</span>
                            </a>
                        @else
                            <a href="{{ asset('storage/' . $mat->file_path) }}" download class="px-3.5 py-2 text-xs font-bold bg-pearl-600 hover:bg-pearl-700 text-white rounded-xl shadow-sm transition inline-flex items-center space-x-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Download</span>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-16 bg-white rounded-2xl border border-slate-200 text-xs text-slate-400">
                    No course materials uploaded for your enrolled cohorts yet.
                </div>
            @endforelse
        </div>

        <div>
            {{ $materials->links() }}
        </div>

    </div>
@endsection
