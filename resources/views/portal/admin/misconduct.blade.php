@extends('layouts.portal')

@section('title', 'Misconduct Reports Inbox — Pearl Training Institute')
@section('page_title', 'Misconduct Reports Inbox')
@section('page_subtitle', 'Confidential whistleblower reports with reporter identity isolation and escalation trail')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Administration</div>
    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📊</span><span>Dashboard</span></a>
    <a href="{{ route('admin.users') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>👥</span><span>Users & RBAC</span></a>
    <a href="{{ route('admin.admissions') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>🎓</span><span>Admissions Queue</span></a>
    <a href="{{ route('admin.financials') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>💳</span><span>Financials & Receipts</span></a>
    <a href="{{ route('admin.courses') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📚</span><span>Courses & Cohorts</span></a>
    <a href="{{ route('admin.misconduct') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-purple-700 text-white font-bold transition"><span>🛡️</span><span>Misconduct Inbox</span></a>
    <a href="{{ route('admin.attendance') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>⏱️</span><span>Attendance Oversight</span></a>
    <a href="{{ route('admin.audit') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📋</span><span>Audit Logs</span></a>
    <a href="{{ route('admin.settings') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>⚙️</span><span>System Settings</span></a>
@endsection

@section('portal_content')
    <div class="space-y-6">

        <!-- Escalation Notice Banner -->
        @php $escalated = $reports->filter(fn($r) => $r->is_escalated_to_secondary_contact); @endphp
        @if($escalated->isNotEmpty())
            <div class="p-5 rounded-2xl bg-purple-900 text-white border border-purple-700 space-y-2">
                <div class="flex items-center space-x-2">
                    <span class="text-xl">⚠️</span>
                    <h4 class="font-heading font-bold text-base">{{ $escalated->count() }} Report(s) Escalated to External Governance Trustee</h4>
                </div>
                <p class="text-xs text-purple-200">These reports concern an Administrator and have been automatically forwarded to the Secondary Escalation Contact per institutional policy. They are visible here for audit trail only.</p>
            </div>
        @endif

        <!-- Reports -->
        <div class="space-y-6">
            @forelse($reports as $rep)
                <div class="p-6 sm:p-7 bg-white rounded-2xl border {{ $rep->is_escalated_to_secondary_contact ? 'border-purple-300' : 'border-slate-200' }} shadow-sm space-y-5">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 border-b border-slate-100 pb-4">
                        <div class="space-y-1">
                            <div class="flex items-center space-x-2">
                                <span class="font-mono font-bold text-slate-600 text-xs">{{ $rep->reference_code }}</span>
                                @if($rep->is_escalated_to_secondary_contact)
                                    <span class="px-2 py-0.5 text-[10px] font-bold bg-purple-100 text-purple-800 rounded-full">🔺 Escalated to Trustee</span>
                                @endif
                            </div>
                            <h3 class="font-heading font-bold text-lg text-slate-900">{{ $rep->subject_summary }}</h3>
                            <div class="text-xs text-slate-400">
                                Category: <strong class="capitalize text-slate-600">{{ str_replace('_', ' ', $rep->category) }}</strong>
                                &bull; Reported: {{ $rep->created_at->format('d M Y, h:i A') }}
                                @if($rep->incident_date)
                                    &bull; Incident date: {{ \Carbon\Carbon::parse($rep->incident_date)->format('d M Y') }}
                                @endif
                            </div>
                        </div>
                        <span class="shrink-0 px-3 py-1.5 text-[10px] font-bold rounded-full
                            @if($rep->status === 'resolved') bg-emerald-100 text-emerald-800
                            @elseif($rep->status === 'investigating') bg-blue-100 text-blue-800
                            @else bg-amber-100 text-amber-900
                            @endif uppercase">
                            {{ $rep->status }}
                        </span>
                    </div>

                    <!-- Reported Party & Reporter Identity Isolation -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-100 space-y-1">
                            <span class="text-[10px] font-bold text-rose-800 uppercase block">Reported Party (Visible to Admin)</span>
                            <div class="font-bold text-slate-900 text-sm">{{ $rep->reportedUser?->name ?? 'Unknown' }}</div>
                            <div class="text-slate-500">{{ $rep->reportedUser?->roles->pluck('display_name')->implode(', ') }}</div>
                        </div>
                        <div class="p-4 rounded-xl bg-teal-50 border border-teal-100 space-y-1">
                            <span class="text-[10px] font-bold text-teal-800 uppercase block">
                                Reporter Identity
                                @if($rep->hide_reporter_from_subject)
                                    (🔒 Masked from Reported Party)
                                @endif
                            </span>
                            @if($rep->reporter)
                                <div class="font-bold text-slate-900 text-sm">{{ $rep->reporter->name }}</div>
                                <div class="text-slate-500">{{ $rep->reporter->email }}</div>
                            @else
                                <div class="text-slate-500 italic">Anonymous / Unlinked</div>
                            @endif
                        </div>
                    </div>

                    <!-- Incident Details -->
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-2">
                        <span class="font-bold text-slate-700 uppercase text-[10px] tracking-wider block">Incident Description:</span>
                        <p class="text-slate-700 leading-relaxed">{{ $rep->incident_details }}</p>
                        @if($rep->location)
                            <div class="text-slate-400">📍 Location: <strong class="text-slate-600">{{ $rep->location }}</strong></div>
                        @endif
                    </div>

                    <!-- Admin Resolution Actions -->
                    @if($rep->status !== 'resolved')
                        <form action="{{ route('admin.misconduct.resolve', $rep->id) }}" method="POST" class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Update Status</label>
                                <select name="status" required class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white font-medium">
                                    <option value="investigating" {{ $rep->status === 'investigating' ? 'selected' : '' }}>Under Investigation</option>
                                    <option value="resolved">Mark as Resolved</option>
                                    <option value="dismissed">Dismiss Report</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Resolution Summary / Action Taken</label>
                                <textarea name="resolution_summary" rows="2" placeholder="Describe the outcome or disciplinary action taken..." class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs bg-white"></textarea>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="px-5 py-2 text-xs font-bold bg-purple-600 hover:bg-purple-700 text-white rounded-xl shadow-sm transition">
                                    Save Resolution &rarr;
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-xs">
                            <strong class="text-emerald-900">Resolution:</strong>
                            <p class="text-emerald-800 mt-1">{{ $rep->resolution_summary }}</p>
                        </div>
                    @endif

                </div>
            @empty
                <div class="p-12 text-center text-xs text-slate-400 bg-white rounded-2xl border border-slate-200">
                    No misconduct reports in the inbox at this time.
                </div>
            @endforelse
        </div>

        <div>{{ $reports->links() }}</div>

    </div>
@endsection
