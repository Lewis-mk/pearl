@extends('layouts.portal')

@section('title', 'In-Person Student Admission — Pearl Training Institute')
@section('page_title', 'In-Person Student Admission Desk')
@section('page_subtitle', 'Authorized staff registration for walk-in students with permanent sequential admission number generation and cash receipting')

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
        <a href="{{ route('trainer.courses') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
            <span>📚</span> <span>Propose Courses</span>
        </a>
    @endcanPermission

    @canPermission('student.admit')
        <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-4 mb-1">Admissions Permissions</div>
        <a href="{{ route('trainer.admission') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-blue-700 text-white font-bold transition">
            <span>➕</span> <span>In-Person Admission</span>
        </a>
    @endcanPermission
@endsection

@section('portal_content')
    <div class="max-w-3xl mx-auto space-y-6">

        <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-md space-y-6">
            
            <div class="flex items-center space-x-3 border-b border-slate-100 pb-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-lg">
                    ✍️
                </div>
                <div>
                    <h3 class="font-heading font-black text-xl text-slate-900">Admit Student at Campus Desk</h3>
                    <p class="text-xs text-slate-400">Generates instant permanent sequential Admission Number (PTI/{{ date('Y') }}/XXXXX)</p>
                </div>
            </div>

            <form action="{{ route('trainer.admission.submit') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Program Selection -->
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Select Program *</label>
                        <select id="admCourseSelect" name="course_id" required onchange="filterAdmCohorts()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-white font-medium">
                            <option value="">-- Choose Course --</option>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->title }} (Total: KES {{ number_format($c->total_fee) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Select Intake Cohort *</label>
                        <select id="admCohortSelect" name="cohort_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-white font-medium">
                            <option value="">-- Select Cohort --</option>
                            @foreach($courses as $c)
                                @foreach($c->activeCohorts as $co)
                                    <option value="{{ $co->id }}" data-course="{{ $c->id }}" class="adm-cohort-opt">
                                        {{ $c->title }} &mdash; {{ $co->name }} (Starts {{ $co->start_date->format('d M Y') }})
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Student Bio Details -->
                <div class="space-y-4">
                    <h4 class="font-heading font-bold text-xs text-slate-700 uppercase tracking-wider">Student Bio Data</h4>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Student Full Name *</label>
                        <input type="text" name="name" required placeholder="e.g. Kelvin Omondi" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Personal Email (Login ID) *</label>
                            <input type="email" name="email" required placeholder="student@gmail.com" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone Number (M-Pesa) *</label>
                            <input type="tel" name="phone" required placeholder="07XXXXXXXX" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-blue-500 bg-slate-50">
                        </div>
                    </div>
                </div>

                <!-- Payment Details at Counter -->
                <div class="p-5 rounded-2xl bg-emerald-50/60 border border-emerald-200 space-y-4">
                    <h4 class="font-heading font-bold text-xs text-emerald-950 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>💰</span>
                        <span>Front-Desk Initial Payment (Optional)</span>
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-emerald-900 uppercase mb-1">Amount (KES)</label>
                            <input type="number" name="initial_payment_amount" min="0" value="0" class="w-full px-3 py-2 rounded-xl border border-emerald-300 text-xs font-mono font-bold bg-white">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-emerald-900 uppercase mb-1">Payment Method</label>
                            <select name="payment_method" class="w-full px-3 py-2 rounded-xl border border-emerald-300 text-xs bg-white font-medium">
                                <option value="cash">Cash (Counter Receipt)</option>
                                <option value="mpesa_manual">Manual M-Pesa (Outage fallback)</option>
                                <option value="none">No payment now (Pay later)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-emerald-900 uppercase mb-1">M-Pesa Code (If manual)</label>
                            <input type="text" name="mpesa_receipt_number" placeholder="e.g. QK89123456" class="w-full px-3 py-2 rounded-xl border border-emerald-300 text-xs font-mono uppercase bg-white">
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 text-xs text-slate-600 leading-relaxed">
                    * Admitting this student will generate a unique sequential admission number, activate their portal login with default password <code>Pearl@{{ date('Y') }}</code>, and trigger an official sequential receipt.
                </div>

                <button type="submit" onclick="return confirm('Complete in-person admission and issue official admission number?');" class="w-full py-4 rounded-xl font-heading font-bold text-xs text-white bg-emerald-600 hover:bg-emerald-700 shadow-md transition transform active:scale-95 text-center">
                    Complete Admission & Issue Admission Number &rarr;
                </button>
            </form>

        </div>

    </div>

    <script>
        function filterAdmCohorts() {
            const courseSelect = document.getElementById('admCourseSelect');
            const courseId = courseSelect.value;
            const cohortOpts = document.querySelectorAll('.adm-cohort-opt');
            cohortOpts.forEach(opt => {
                if (!courseId || opt.getAttribute('data-course') === courseId) {
                    opt.style.display = 'block';
                } else {
                    opt.style.display = 'none';
                }
            });
        }
        document.addEventListener('DOMContentLoaded', filterAdmCohorts);
    </script>
@endsection
