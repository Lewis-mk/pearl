@extends('layouts.public')

@section('title', 'Application Submitted — Pearl Training Institute')

@section('content')
    <section class="py-16 bg-slate-100 min-h-[75vh] flex items-center justify-center">
        <div class="max-w-xl w-full mx-auto px-4">
            
            <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/50 space-y-6 text-center">
                
                <div class="w-16 h-16 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-3xl shadow-sm">
                    ✓
                </div>

                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-pearl-600">Application Received</span>
                    <h1 class="font-heading font-black text-2xl sm:text-3xl text-slate-900">Welcome, {{ $user->name }}!</h1>
                    <p class="text-xs text-slate-400">Your application has been registered in our admissions system.</p>
                </div>

                <!-- Registration Summary Card -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 text-left space-y-3 text-xs">
                    <div class="flex justify-between border-b border-slate-200 pb-2">
                        <span class="text-slate-400">Program Enrolled:</span>
                        <strong class="text-slate-900">{{ $enrollment->course->title }}</strong>
                    </div>
                    <div class="flex justify-between border-b border-slate-200 pb-2">
                        <span class="text-slate-400">Intake Cohort:</span>
                        <strong class="text-slate-900">{{ $enrollment->cohort->name }}</strong>
                    </div>
                    <div class="flex justify-between border-b border-slate-200 pb-2">
                        <span class="text-slate-400">Total Program Fee:</span>
                        <strong class="text-slate-900 font-mono">KES {{ number_format($enrollment->fee_total, 2) }}</strong>
                    </div>
                    <div class="flex justify-between items-center pt-1">
                        <span class="text-slate-400">Status:</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-900">Pending Staff Approval</span>
                    </div>
                </div>

                <!-- Next Steps Info Box -->
                <div class="p-4 rounded-xl bg-teal-50 border border-teal-100 text-left text-xs text-teal-950 space-y-2">
                    <strong class="font-bold flex items-center space-x-1.5 text-teal-900">
                        <span>ℹ️</span>
                        <span>What Happens Next:</span>
                    </strong>
                    <ol class="list-decimal pl-4 space-y-1 text-teal-800">
                        <li>Our admissions desk reviews and approves your application.</li>
                        <li>You will be assigned a permanent, sequential <strong>Admission Number (PTI/{{ date('Y') }}/XXXXX)</strong>.</li>
                        <li>You will receive a welcome SMS and email to activate your student portal login.</li>
                    </ol>
                </div>

                <!-- Action Button -->
                <div class="pt-2 flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('login') }}" class="flex-1 py-3.5 rounded-xl font-heading font-bold text-sm text-white bg-pearl-600 hover:bg-pearl-700 shadow-md shadow-pearl-700/20 transition text-center">
                        Proceed to Login &rarr;
                    </a>
                    <a href="{{ route('home') }}" class="py-3.5 px-5 rounded-xl font-heading font-bold text-sm text-slate-700 bg-slate-100 hover:bg-slate-200 transition text-center">
                        Return Home
                    </a>
                </div>

            </div>

        </div>
    </section>
@endsection
