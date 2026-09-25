@extends('layouts.public')

@section('title', $course->title . ' — Pearl Training Institute')
@section('meta_description', $course->short_description)

@section('content')
    <!-- Course Header Banner -->
    <section class="bg-slate-900 text-white py-16 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl space-y-4">
                <div class="flex items-center space-x-3">
                    <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider bg-teal-500/20 text-teal-300 border border-teal-400/30 rounded-full">
                        {{ $course->category }}
                    </span>
                    @if($course->featured_badge)
                        <span class="px-3 py-1 text-xs font-bold uppercase tracking-wider bg-amber-500 text-white rounded-full">
                            {{ $course->featured_badge }}
                        </span>
                    @endif
                </div>

                <h1 class="font-heading font-black text-3xl sm:text-4xl lg:text-5xl text-white leading-tight">
                    {{ $course->title }}
                </h1>

                <p class="text-base text-slate-300 font-normal leading-relaxed">
                    {{ $course->short_description }}
                </p>

                <div class="flex flex-wrap items-center gap-6 pt-4 text-xs sm:text-sm text-slate-300">
                    <div>⏱ Duration: <span class="text-white font-bold">{{ $course->duration_weeks }} Weeks</span></div>
                    <div>💰 Full Fee: <span class="text-amber-400 font-bold font-mono">KES {{ number_format($course->total_fee) }}</span></div>
                    <div>💳 Min Deposit: <span class="text-teal-300 font-bold font-mono">KES {{ number_format($course->deposit_required) }}</span></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Course Details Content -->
    <section class="py-14 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                
                <!-- Left: Syllabus & Details -->
                <div class="lg:col-span-8 space-y-10">
                    
                    <!-- Course Overview -->
                    <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                        <h2 class="font-heading font-bold text-2xl text-slate-900">About This Course</h2>
                        <div class="text-sm text-slate-700 leading-relaxed space-y-3 whitespace-pre-line">
                            {{ $course->description }}
                        </div>

                        @if($course->requires_guardian_info)
                            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start space-x-3">
                                <span class="text-lg">ℹ️</span>
                                <div>
                                    <strong class="font-bold">Guardian Information Notice:</strong>
                                    <p class="mt-0.5 text-amber-800">This course frequently serves minor students and teens. Guardian contact information will be required during registration.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Curriculum Outline -->
                    <div class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6">
                        <h2 class="font-heading font-bold text-2xl text-slate-900">Curriculum & Learning Modules</h2>
                        <div class="space-y-3">
                            @php
                                $modules = explode("\n", $course->curriculum_outline ?? "Module 1: Foundations\nModule 2: Practical Labs\nModule 3: Project Deployment");
                            @endphp
                            @foreach($modules as $index => $mod)
                                @if(!empty(trim($mod)))
                                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-start space-x-3">
                                        <div class="w-7 h-7 rounded-lg bg-pearl-600 text-white font-bold flex items-center justify-center text-xs shrink-0 mt-0.5">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="text-sm font-semibold text-slate-800 leading-snug">
                                            {{ trim($mod) }}
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                </div>

                <!-- Right: Intake Batches & Quick Enrollment Card -->
                <div class="lg:col-span-4 space-y-6">
                    
                    <!-- Cohorts & Enroll Card -->
                    <div class="bg-white p-6 sm:p-7 rounded-2xl border border-slate-200 shadow-lg shadow-slate-200/50 sticky top-24 space-y-6">
                        <div class="border-b border-slate-100 pb-5">
                            <span class="text-[10px] uppercase font-bold text-slate-400">Total Program Investment</span>
                            <div class="font-heading font-black text-3xl text-slate-900 font-mono">KES {{ number_format($course->total_fee) }}</div>
                            <div class="text-xs text-slate-400 mt-1">Pay full fee or deposit of <strong class="text-slate-700">KES {{ number_format($course->deposit_required) }}</strong> via M-Pesa.</div>
                        </div>

                        <!-- Available Intake Cohorts -->
                        <div>
                            <h3 class="font-heading font-bold text-base text-slate-900 mb-3">Available Intake Cohorts</h3>
                            @if($course->cohorts->isNotEmpty())
                                <div class="space-y-2.5">
                                    @foreach($course->cohorts as $cohort)
                                        <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-between">
                                            <div>
                                                <div class="text-xs font-bold text-slate-900">{{ $cohort->name }}</div>
                                                <div class="text-[11px] text-slate-400">Starts: {{ $cohort->start_date->format('d M Y') }}</div>
                                                @if($cohort->leadTrainer)
                                                    <div class="text-[10px] text-pearl-600 font-medium">Trainer: {{ $cohort->leadTrainer->name }}</div>
                                                @endif
                                            </div>
                                            <a href="{{ route('register', ['course_id' => $course->id, 'cohort_id' => $cohort->id]) }}" class="px-3 py-1.5 text-xs font-bold bg-pearl-600 hover:bg-pearl-700 text-white rounded-lg transition shadow-sm">
                                                Enroll
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="p-4 rounded-xl bg-slate-50 text-xs text-slate-400 text-center">
                                    New cohorts opening soon. Register your interest.
                                </div>
                            @endif
                        </div>

                        <!-- Overall Enroll Button -->
                        <a href="{{ route('register', ['course_id' => $course->id]) }}" class="w-full py-4 rounded-xl font-heading font-bold text-sm text-center bg-gradient-to-r from-pearl-600 to-teal-600 hover:from-pearl-500 hover:to-teal-500 text-white shadow-md shadow-pearl-700/20 transition block">
                            Enroll in This Course &rarr;
                        </a>

                        <div class="text-[11px] text-slate-400 space-y-1.5 pt-2">
                            <div class="flex items-center space-x-2">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span>Official Verified Certificate on Completion</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span>Sequential Permanent Admission No (PTI/YYYY/XXXXX)</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span>Installment Payments Allowed</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>
@endsection
