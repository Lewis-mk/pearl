@extends('layouts.portal')

@section('title', 'Class Scheduling & Postponements — Pearl Training Institute')
@section('page_title', 'Cohort Class Management')
@section('page_subtitle', 'Schedule classroom & virtual sessions, post meeting links, and trigger postponement broadcasts')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Instruction & Cohorts</div>
    <a href="{{ route('trainer.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📊</span> <span>Dashboard</span>
    </a>
    <a href="{{ route('trainer.classes') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-blue-700 text-white font-bold transition">
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
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left 7 Cols: Class Schedule List & Postpone Actions -->
        <div class="lg:col-span-7 space-y-6">
            <h3 class="font-heading font-bold text-lg text-slate-900">Scheduled Sessions</h3>

            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden divide-y divide-slate-100">
                @forelse($classes as $cls)
                    <div class="p-6 hover:bg-slate-50 transition space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <span class="font-heading font-bold text-base text-slate-900">{{ $cls->title }}</span>
                                <span class="text-[10px] font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded ml-2">{{ $cls->cohort->name }}</span>
                            </div>
                            <div>
                                @if($cls->is_postponed)
                                    <span class="px-2.5 py-1 text-[10px] font-bold bg-amber-100 text-amber-900 rounded-full border border-amber-300">⚠️ Postponed</span>
                                @else
                                    <span class="px-2.5 py-1 text-[10px] font-bold bg-teal-100 text-teal-800 rounded-full uppercase">{{ $cls->status }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="text-xs text-slate-400 flex flex-wrap gap-4">
                            <span>🗓️ {{ $cls->scheduled_start->format('d M Y, h:i A') }} &mdash; {{ $cls->scheduled_end->format('h:i A') }}</span>
                            @if($cls->physical_location)
                                <span>📍 {{ $cls->physical_location }}</span>
                            @endif
                            @if($cls->meeting_url)
                                <span>📹 <a href="{{ $cls->meeting_url }}" target="_blank" class="text-blue-600 underline">Join Link</a></span>
                            @endif
                        </div>

                        @if($cls->is_postponed)
                            <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-950">
                                <strong>Postponement Broadcast Reason:</strong> {{ $cls->postponement_reason }}
                            </div>
                        @endif

                        <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                            <a href="{{ route('trainer.classes.attendance', $cls->id) }}" class="px-3.5 py-1.5 text-xs font-bold bg-slate-900 hover:bg-blue-600 text-white rounded-lg transition">
                                📝 Attendance Register
                            </a>

                            @if(!$cls->is_postponed)
                                <!-- Postpone Trigger Button -->
                                <button type="button" onclick="document.getElementById('postponeModal_{{ $cls->id }}').classList.toggle('hidden')" class="px-3.5 py-1.5 text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-lg transition border border-amber-200">
                                    ⚠️ Postpone Class
                                </button>
                            @endif
                        </div>

                        <!-- Postpone Modal/Drawer -->
                        <div id="postponeModal_{{ $cls->id }}" class="hidden p-5 rounded-2xl bg-amber-50/90 border border-amber-300 space-y-3 mt-3">
                            <h4 class="font-heading font-bold text-sm text-amber-950">Postpone Session & Auto-Broadcast Notice</h4>
                            <p class="text-[11px] text-amber-800">All {{ $cls->cohort->enrollments->count() }} enrolled students in this cohort will receive an automated SMS & Email notification with your reason.</p>

                            <form action="{{ route('trainer.classes.postpone', $cls->id) }}" method="POST" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-xs font-bold text-amber-900 mb-1">Reason for Postponement *</label>
                                    <textarea name="postponement_reason" required rows="2" placeholder="e.g. Power maintenance at campus lab. Rescheduled to Friday morning." class="w-full p-2.5 rounded-xl border border-amber-300 text-xs focus:ring-2 focus:ring-amber-500 bg-white"></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-bold text-amber-900 mb-1">Rescheduled Start (Optional)</label>
                                        <input type="datetime-local" name="rescheduled_start" class="w-full p-2 rounded-xl border border-amber-300 text-xs bg-white">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold text-amber-900 mb-1">Rescheduled End (Optional)</label>
                                        <input type="datetime-local" name="rescheduled_end" class="w-full p-2 rounded-xl border border-amber-300 text-xs bg-white">
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-2">
                                    <button type="button" onclick="document.getElementById('postponeModal_{{ $cls->id }}').classList.add('hidden')" class="px-3 py-1.5 text-xs font-bold text-slate-600">Cancel</button>
                                    <button type="submit" onclick="return confirm('Broadcast postponement alerts to all students in this cohort?');" class="px-4 py-1.5 text-xs font-bold bg-amber-600 hover:bg-amber-700 text-white rounded-lg shadow-sm">Confirm & Notify Students</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-xs text-slate-400">No classes found. Schedule a session below.</div>
                @endforelse
            </div>

            <div>
                {{ $classes->links() }}
            </div>
        </div>

        <!-- Right 5 Cols: Schedule New Class Session Form -->
        <div class="lg:col-span-5">
            <div class="bg-white p-7 sm:p-8 rounded-3xl border border-slate-200 shadow-md space-y-5 sticky top-24">
                <div>
                    <span class="text-[10px] uppercase font-bold text-blue-600 tracking-wider">Timetable Management</span>
                    <h3 class="font-heading font-black text-xl text-slate-900">Schedule New Session</h3>
                    <p class="text-xs text-slate-400 mt-1">Configure meeting links, venue, and date for your cohort.</p>
                </div>

                <form action="{{ route('trainer.classes.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Select Cohort *</label>
                        <select name="cohort_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50 font-medium">
                            @foreach($cohorts as $coh)
                                <option value="{{ $coh->id }}">{{ $coh->course->title }} &mdash; {{ $coh->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Session Topic / Title *</label>
                        <input type="text" name="title" required placeholder="e.g. Lab 4: Database Relationships & Eloquent" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Description / Preparation Notes</label>
                        <textarea name="description" rows="2" placeholder="Topics covered, software needed..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Start Date & Time *</label>
                            <input type="datetime-local" name="scheduled_start" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">End Date & Time *</label>
                            <input type="datetime-local" name="scheduled_end" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Delivery Mode *</label>
                        <select name="delivery_mode" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50">
                            <option value="physical">Physical Classroom Lab</option>
                            <option value="online">Virtual Video Class (Online)</option>
                            <option value="hybrid">Hybrid (Lab + Online Stream)</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Physical Location</label>
                            <input type="text" name="physical_location" placeholder="e.g. Lab 1, Pearl Campus" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Meeting URL (Zoom/Meet)</label>
                            <input type="url" name="meeting_url" placeholder="https://meet.google.com/..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-xl font-heading font-bold text-xs text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-700/20 transition transform active:scale-95 text-center">
                        Schedule Session &rarr;
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection
