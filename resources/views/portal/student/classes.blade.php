@extends('layouts.portal')

@section('title', 'Timetable & Classes — Pearl Training Institute')
@section('page_title', 'Class Timetable & Sessions')
@section('page_subtitle', 'View scheduled classes, online video meeting links, and postponement notices')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Learning & Academics</div>
    <a href="{{ route('student.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📊</span> <span>My Dashboard</span>
    </a>
    <a href="{{ route('student.classes') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-pearl-700 text-white font-bold transition">
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
    <div class="space-y-6">
        
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden divide-y divide-slate-100">
            @forelse($classes as $cls)
                <div class="p-6 hover:bg-slate-50 transition flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-heading font-bold text-base text-slate-900">{{ $cls->title }}</span>
                            <span class="text-[10px] uppercase font-bold text-pearl-700 bg-pearl-50 px-2.5 py-0.5 rounded-full border border-pearl-200">{{ $cls->cohort->name }}</span>
                            
                            @if($cls->is_postponed)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900 border border-amber-300">⚠️ Postponed Notice</span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-teal-100 text-teal-800 uppercase">{{ $cls->delivery_mode }}</span>
                            @endif
                        </div>

                        <p class="text-xs text-slate-700 leading-relaxed">{{ $cls->description }}</p>

                        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-400 pt-1">
                            <div>🗓️ Scheduled: <strong class="text-slate-700">{{ $cls->scheduled_start->format('d M Y, h:i A') }} &mdash; {{ $cls->scheduled_end->format('h:i A') }}</strong></div>
                            @if($cls->physical_location)
                                <div>📍 Venue: <strong class="text-slate-700">{{ $cls->physical_location }}</strong></div>
                            @endif
                            <div>👨‍🏫 Trainer: <strong class="text-slate-700">{{ $cls->trainer->name }}</strong></div>
                        </div>

                        @if($cls->is_postponed)
                            <div class="mt-3 p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-950 space-y-1">
                                <div class="font-bold flex items-center space-x-1.5 text-amber-900">
                                    <span>⚠️</span>
                                    <span>Postponement Reason:</span>
                                </div>
                                <p class="text-amber-900">{{ $cls->postponement_reason }}</p>
                                @if($cls->rescheduled_start)
                                    <div class="font-semibold text-emerald-800 pt-1">
                                        ✓ Rescheduled New Date: {{ $cls->rescheduled_start->format('d M Y, h:i A') }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="shrink-0 flex items-center space-x-3">
                        @if($cls->meeting_url && !$cls->is_postponed)
                            <a href="{{ $cls->meeting_url }}" target="_blank" class="px-5 py-3 text-xs font-bold bg-pearl-600 hover:bg-pearl-700 text-white rounded-xl shadow-md shadow-pearl-700/20 transition flex items-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span>Join Virtual Class</span>
                            </a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-xs text-slate-400">
                    No classes found for your enrolled cohorts.
                </div>
            @endforelse
        </div>

        <div>
            {{ $classes->links() }}
        </div>

    </div>
@endsection
