@extends('layouts.portal')

@section('title', 'Mark Class Attendance — Pearl Training Institute')
@section('page_title', 'Mark Attendance: ' . $classSession->title)
@section('page_subtitle', $cohort->name . ' — ' . $classSession->scheduled_start->format('d M Y, h:i A'))

@section('sidebar_menu')
    <a href="{{ route('trainer.classes') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>&larr;</span> <span>Back to Class Schedule</span>
    </a>
@endsection

@section('portal_content')
    <div class="max-w-4xl mx-auto space-y-6">

        <div class="p-6 bg-white rounded-3xl border border-slate-200 shadow-md space-y-6">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
                <div>
                    <h3 class="font-heading font-bold text-xl text-slate-900">{{ $classSession->title }}</h3>
                    <div class="text-xs text-slate-400">{{ $cohort->course->title }} &bull; {{ $cohort->name }}</div>
                </div>
                <div class="text-xs text-slate-600 bg-slate-100 px-3 py-1.5 rounded-lg">
                    Total Enrolled: <strong>{{ $students->count() }} Students</strong>
                </div>
            </div>

            <form action="{{ route('trainer.classes.attendance.save', $classSession->id) }}" method="POST" class="space-y-6">
                @csrf

                <div class="divide-y divide-slate-100">
                    @forelse($students as $enr)
                        @php
                            $st = $enr->student;
                            $currentStatus = $attendances[$st->id]->status ?? 'present';
                        @endphp
                        <div class="py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-800 flex items-center justify-center font-bold text-xs uppercase">
                                    {{ substr($st->name, 0, 2) }}
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-slate-900">{{ $st->name }}</div>
                                    <div class="text-xs font-mono text-slate-400">Adm: {{ $st->admission_number ?? 'PTI-TEMP' }} &bull; {{ $st->phone }}</div>
                                </div>
                            </div>

                            <!-- Radio Button Group for Attendance Status -->
                            <div class="flex items-center space-x-2">
                                <label class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer flex items-center space-x-1.5 text-xs font-semibold">
                                    <input type="radio" name="attendance[{{ $st->id }}]" value="present" {{ $currentStatus === 'present' ? 'checked' : '' }} class="text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-emerald-700">Present</span>
                                </label>

                                <label class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer flex items-center space-x-1.5 text-xs font-semibold">
                                    <input type="radio" name="attendance[{{ $st->id }}]" value="absent" {{ $currentStatus === 'absent' ? 'checked' : '' }} class="text-rose-600 focus:ring-rose-500">
                                    <span class="text-rose-700">Absent</span>
                                </label>

                                <label class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer flex items-center space-x-1.5 text-xs font-semibold">
                                    <input type="radio" name="attendance[{{ $st->id }}]" value="excused" {{ $currentStatus === 'excused' ? 'checked' : '' }} class="text-sky-600 focus:ring-sky-500">
                                    <span class="text-sky-700">Excused</span>
                                </label>

                                <label class="px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 cursor-pointer flex items-center space-x-1.5 text-xs font-semibold">
                                    <input type="radio" name="attendance[{{ $st->id }}]" value="late" {{ $currentStatus === 'late' ? 'checked' : '' }} class="text-amber-600 focus:ring-amber-500">
                                    <span class="text-amber-700">Late</span>
                                </label>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-xs text-slate-400">No active students enrolled in this cohort.</div>
                    @endforelse
                </div>

                <div class="pt-4 border-t border-slate-100 flex justify-end space-x-3">
                    <a href="{{ route('trainer.classes') }}" class="px-4 py-2.5 text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-xl shadow-md transition">
                        Save Attendance Register &rarr;
                    </button>
                </div>
            </form>

        </div>

    </div>
@endsection
