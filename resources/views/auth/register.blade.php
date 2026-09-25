@extends('layouts.public')

@section('title', 'Student Course Enrollment Application — Pearl Training Institute')

@section('content')
    <section class="py-14 bg-slate-100 min-h-[80vh]">
        <div class="max-w-2xl mx-auto px-4">
            
            <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/50 space-y-6">
                
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-50 text-teal-700 text-xs font-bold mb-2">
                        <span>🎓 Student Portal Registration</span>
                    </div>
                    <h1 class="font-heading font-black text-2xl sm:text-3xl text-slate-900">Enrollment Application</h1>
                    <p class="text-xs text-slate-400 mt-1">Fill out your details below to create your student portal account and enroll in your chosen cohort.</p>
                </div>

                @if($errors->any())
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs space-y-1">
                        <strong class="font-bold">Please correct the following errors:</strong>
                        <ul class="list-disc pl-4 space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register.submit') }}" method="POST" class="space-y-5">
                    @csrf

                    <!-- Course & Cohort Selector -->
                    <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Select Program / Course *</label>
                            <select id="courseSelect" name="course_id" required onchange="updateCohortsAndGuardianNotice()" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-white font-medium">
                                <option value="">-- Choose a Course --</option>
                                @foreach($courses as $c)
                                    <option value="{{ $c->id }}" 
                                            data-guardian="{{ $c->requires_guardian_info ? '1' : '0' }}"
                                            data-fee="{{ $c->total_fee }}"
                                            data-deposit="{{ $c->deposit_required }}"
                                            {{ (old('course_id', $selectedCourseId) == $c->id) ? 'selected' : '' }}>
                                        {{ $c->title }} (KES {{ number_format($c->total_fee) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Select Intake Cohort Batch *</label>
                            <select id="cohortSelect" name="cohort_id" required class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-white font-medium">
                                <option value="">-- Select Intake Batch --</option>
                                @foreach($courses as $c)
                                    @foreach($c->activeCohorts as $co)
                                        <option value="{{ $co->id }}" 
                                                data-course="{{ $c->id }}"
                                                class="cohort-option"
                                                {{ (old('cohort_id', $selectedCohortId) == $co->id) ? 'selected' : '' }}>
                                            {{ $c->title }} &mdash; {{ $co->name }} (Starts {{ $co->start_date->format('d M Y') }})
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Personal Information -->
                    <div class="space-y-4">
                        <h3 class="font-heading font-bold text-sm text-slate-900 uppercase tracking-wider">Student Profile Details</h3>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Legal Name *</label>
                            <input type="text" name="name" required value="{{ old('name') }}" placeholder="e.g. Kelvin Omondi" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Personal Email (Login ID) *</label>
                                <input type="email" name="email" required value="{{ old('email') }}" placeholder="student@gmail.com" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                                <span class="text-[10px] text-slate-400 mt-1 block">Your personal email will be your portal login username.</span>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone Number (M-Pesa) *</label>
                                <input type="tel" name="phone" required value="{{ old('phone') }}" placeholder="07XXXXXXXX" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                                <span class="text-[10px] text-slate-400 mt-1 block">Used for Daraja STK Push & SMS notifications.</span>
                            </div>
                        </div>
                    </div>

                    <!-- Conditional Guardian Info Box (visible if course is flagged for minors) -->
                    <div id="guardianSection" class="p-5 rounded-2xl bg-amber-50/80 border border-amber-200 space-y-4 hidden">
                        <div class="flex items-center space-x-2">
                            <span class="text-base">👨‍👧</span>
                            <h3 class="font-heading font-bold text-xs text-amber-950 uppercase tracking-wider">Parent / Guardian Information (Required for Minors/Teens Programs)</h3>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-amber-900 uppercase tracking-wider mb-1">Guardian Full Name *</label>
                            <input type="text" id="guardian_name" name="guardian_name" value="{{ old('guardian_name') }}" placeholder="e.g. Samuel Omondi" class="w-full px-4 py-2.5 rounded-xl border border-amber-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none bg-white">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-amber-900 uppercase tracking-wider mb-1">Guardian Phone *</label>
                                <input type="tel" id="guardian_phone" name="guardian_phone" value="{{ old('guardian_phone') }}" placeholder="07XXXXXXXX" class="w-full px-4 py-2.5 rounded-xl border border-amber-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-amber-900 uppercase tracking-wider mb-1">Relationship *</label>
                                <input type="text" id="guardian_relationship" name="guardian_relationship" value="{{ old('guardian_relationship', 'Parent') }}" placeholder="e.g. Mother, Father, Guardian" class="w-full px-4 py-2.5 rounded-xl border border-amber-300 text-sm focus:ring-2 focus:ring-amber-500 focus:outline-none bg-white">
                            </div>
                        </div>
                    </div>

                    <!-- Password Setup -->
                    <div class="space-y-4 pt-2">
                        <h3 class="font-heading font-bold text-sm text-slate-900 uppercase tracking-wider">Set Portal Password</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Password *</label>
                                <input type="password" name="password" required placeholder="Minimum 8 characters" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Confirm Password *</label>
                                <input type="password" name="password_confirmation" required placeholder="Re-type password" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                            </div>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-teal-50 border border-teal-100 text-xs text-teal-900 leading-relaxed">
                        📌 Upon registration, an authorized admissions staff member will review your application and issue your <strong>permanent sequential Admission Number (PTI/{{ date('Y') }}/XXXXX)</strong>.
                    </div>

                    <button type="submit" class="w-full py-4 rounded-xl font-heading font-bold text-sm text-white bg-pearl-600 hover:bg-pearl-700 shadow-md shadow-pearl-700/20 transition transform active:scale-95 text-center">
                        Submit Enrollment Application &rarr;
                    </button>
                </form>

            </div>

        </div>
    </section>

    <script>
        function updateCohortsAndGuardianNotice() {
            const courseSelect = document.getElementById('courseSelect');
            const selectedOpt = courseSelect.options[courseSelect.selectedIndex];
            const courseId = courseSelect.value;
            const requiresGuardian = selectedOpt ? selectedOpt.getAttribute('data-guardian') === '1' : false;

            const guardianSection = document.getElementById('guardianSection');
            if (requiresGuardian) {
                guardianSection.classList.remove('hidden');
            } else {
                guardianSection.classList.add('hidden');
            }

            // Filter cohorts dropdown
            const cohortOpts = document.querySelectorAll('.cohort-option');
            cohortOpts.forEach(opt => {
                if (!courseId || opt.getAttribute('data-course') === courseId) {
                    opt.style.display = 'block';
                } else {
                    opt.style.display = 'none';
                }
            });
        }

        // Run on load
        document.addEventListener('DOMContentLoaded', updateCohortsAndGuardianNotice);
    </script>
@endsection
