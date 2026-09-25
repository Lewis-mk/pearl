@extends('layouts.portal')

@section('title', 'Confidential Misconduct & Whistleblower Reporting — Pearl Training Institute')
@section('page_title', 'Confidential Misconduct Reporting')
@section('page_subtitle', 'Secure whistleblower channel with strict confidentiality protection and administrative escalation')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Learning & Academics</div>
    <a href="{{ route('student.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📊</span> <span>My Dashboard</span>
    </a>
    <a href="{{ route('student.classes') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
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
    <a href="{{ route('student.misconduct') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-pearl-700 text-white font-bold transition">
        <span>🛡️</span> <span>Misconduct Report</span>
    </a>
    <a href="{{ route('student.profile') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>⚙️</span> <span>Profile & Settings</span>
    </a>
@endsection

@section('portal_content')
    <div class="max-w-4xl mx-auto space-y-8">

        <!-- Protection Banner -->
        <div class="p-6 rounded-3xl bg-slate-900 text-white shadow-lg border border-slate-800 space-y-3">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-teal-500/20 text-teal-300 flex items-center justify-center font-bold text-lg">🛡️</div>
                <div>
                    <h3 class="font-heading font-black text-lg text-white">Whistleblower Protection Guarantee</h3>
                    <p class="text-xs text-slate-300">Your safety and integrity are fully guaranteed under Kenyan law and Pearl Institute policies.</p>
                </div>
            </div>
            <div class="p-3 rounded-xl bg-slate-800 text-xs text-slate-300 space-y-1">
                <p>• <strong>Reporter Identity Isolation:</strong> Your name and email are strictly masked from the reported trainer or staff member.</p>
                <p>• <strong>Independent Escalation:</strong> If a report concerns the Director/Administrator, it is automatically routed directly to our external Governance Escalation Trustee (<span class="font-mono text-amber-300">governance@pearlinstitute.com</span>).</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <!-- Left: Filing Form -->
            <div class="lg:col-span-7">
                <div class="bg-white p-7 sm:p-8 rounded-3xl border border-slate-200 shadow-md space-y-5">
                    <h3 class="font-heading font-bold text-xl text-slate-900">Submit Confidential Report</h3>

                    <form action="{{ route('student.misconduct.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Select Reported Staff Member / Trainer *</label>
                            <select name="reported_user_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50 font-medium">
                                <option value="">-- Choose Person to Report --</option>
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->roles->pluck('display_name')->implode(', ') }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Category of Incident *</label>
                            <select name="category" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50 font-medium">
                                <option value="harassment">Harassment or Discrimination</option>
                                <option value="bribery">Extortion, Bribery or Solicitation</option>
                                <option value="unprofessional_conduct">Unprofessional Conduct or Lateness</option>
                                <option value="academic_dishonesty">Academic Irregularity or Grading Bias</option>
                                <option value="abuse_of_office">Abuse of Office / Retaliation</option>
                                <option value="other">Other Grievance</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Brief Subject Summary *</label>
                            <input type="text" name="subject_summary" required placeholder="e.g. Unfair grading and absenteeism during Lab 3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Detailed Description of Incident *</label>
                            <textarea name="incident_details" rows="5" required placeholder="Describe dates, times, what happened, and any witnesses present..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50"></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Date of Incident</label>
                                <input type="date" name="incident_date" max="{{ today()->toDateString() }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Location / Classroom</label>
                                <input type="text" name="location" placeholder="e.g. Lab 2 / Online Class" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Attach Supporting Evidence (Screenshots, PDFs, Recordings)</label>
                            <input type="file" name="evidence_file" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-pearl-50 file:text-pearl-700 hover:file:bg-pearl-100 border border-slate-300 rounded-xl p-1 bg-slate-50">
                        </div>

                        <button type="submit" onclick="return confirm('Submit this report under official whistleblower protection?');" class="w-full py-3.5 rounded-xl font-heading font-bold text-xs text-white bg-slate-900 hover:bg-pearl-600 shadow-md transition transform active:scale-95 text-center">
                            Submit Confidential Report &rarr;
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Past Reports Tracking -->
            <div class="lg:col-span-5 space-y-4">
                <h3 class="font-heading font-bold text-lg text-slate-900">Your Filed Reports</h3>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3">
                    @forelse($reports as $rep)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 space-y-2 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="font-mono font-bold text-slate-900">{{ $rep->reference_code }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold 
                                    @if($rep->status === 'resolved') bg-emerald-100 text-emerald-800
                                    @elseif($rep->status === 'investigating') bg-blue-100 text-blue-800
                                    @elseif($rep->status === 'escalated') bg-purple-100 text-purple-800
                                    @else bg-amber-100 text-amber-800
                                    @endif uppercase">
                                    {{ $rep->status }}
                                </span>
                            </div>
                            <div class="font-bold text-slate-800">{{ $rep->subject_summary }}</div>
                            <div class="text-[11px] text-slate-400">Filed: {{ $rep->created_at->format('d M Y, h:i A') }}</div>

                            @if($rep->resolution_summary)
                                <div class="mt-2 p-3 rounded-lg bg-emerald-50 text-emerald-900 text-xs">
                                    <strong>Resolution Outcome:</strong> {{ $rep->resolution_summary }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-slate-400">
                            You have not submitted any misconduct reports.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
@endsection
