@extends('layouts.portal')

@section('title', 'System Audit Trail — Pearl Training Institute')
@section('page_title', 'System Audit & Activity Logs')
@section('page_subtitle', 'Immutable chronological record of cash collections, admission decisions, permission modifications, and escalations')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Administration</div>
    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📊</span><span>Dashboard</span></a>
    <a href="{{ route('admin.users') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>👥</span><span>Users & RBAC</span></a>
    <a href="{{ route('admin.admissions') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>🎓</span><span>Admissions Queue</span></a>
    <a href="{{ route('admin.financials') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>💳</span><span>Financials & Receipts</span></a>
    <a href="{{ route('admin.courses') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📚</span><span>Courses & Cohorts</span></a>
    <a href="{{ route('admin.misconduct') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>🛡️</span><span>Misconduct Inbox</span></a>
    <a href="{{ route('admin.attendance') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>⏱️</span><span>Attendance Oversight</span></a>
    <a href="{{ route('admin.audit') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-purple-700 text-white font-bold transition"><span>📋</span><span>Audit Logs</span></a>
    <a href="{{ route('admin.settings') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>⚙️</span><span>System Settings</span></a>
@endsection

@section('portal_content')
    <div class="space-y-6">

        <!-- Search & Filter Card -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-3">
            <form action="{{ route('admin.audit') }}" method="GET" class="flex flex-1 flex-wrap gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search action, description, IP, or user..." class="flex-1 min-w-[200px] px-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-purple-500 bg-slate-50">
                <select name="action" class="px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50 font-medium">
                    <option value="">All Action Types</option>
                    <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Login / Auth</option>
                    <option value="payment_recorded" {{ request('action') === 'payment_recorded' ? 'selected' : '' }}>Payment Recorded</option>
                    <option value="admission_approved" {{ request('action') === 'admission_approved' ? 'selected' : '' }}>Admission Approved</option>
                    <option value="role_assigned" {{ request('action') === 'role_assigned' ? 'selected' : '' }}>Role / Permission Change</option>
                    <option value="misconduct_filed" {{ request('action') === 'misconduct_filed' ? 'selected' : '' }}>Misconduct Incident</option>
                    <option value="setting_updated" {{ request('action') === 'setting_updated' ? 'selected' : '' }}>Settings Update</option>
                </select>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold bg-purple-600 text-white hover:bg-purple-700 transition">Filter Logs</button>
            </form>
        </div>

        <!-- Audit Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="p-4">Timestamp</th>
                            <th class="p-4">Actor / User</th>
                            <th class="p-4">Action Event</th>
                            <th class="p-4">Subject</th>
                            <th class="p-4">IP & User Agent</th>
                            <th class="p-4">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono text-[11px]">
                        @forelse($logs as $log)
                            <tr class="hover:bg-slate-50 transition font-sans">
                                <td class="p-4 text-slate-400 font-mono text-[11px] whitespace-nowrap">
                                    {{ $log->created_at->format('d M Y, H:i:s') }}
                                </td>
                                <td class="p-4">
                                    @if($log->user)
                                        <div class="font-bold text-slate-900">{{ $log->user->name }}</div>
                                        <div class="text-[10px] text-slate-400">{{ $log->user->email }}</div>
                                    @else
                                        <span class="text-slate-400 italic">System / Anonymous</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold font-mono 
                                        @if(str_contains($log->action, 'payment')) bg-emerald-100 text-emerald-800
                                        @elseif(str_contains($log->action, 'role') || str_contains($log->action, 'permission')) bg-purple-100 text-purple-800
                                        @elseif(str_contains($log->action, 'misconduct')) bg-rose-100 text-rose-800
                                        @elseif(str_contains($log->action, 'admit')) bg-teal-100 text-teal-800
                                        @else bg-slate-100 text-slate-700
                                        @endif uppercase">
                                        {{ $log->action }}
                                    </span>
                                </td>
                                <td class="p-4 text-slate-700 font-semibold max-w-[200px] truncate">
                                    {{ $log->subject }}
                                </td>
                                <td class="p-4 text-slate-400 font-mono text-[10px]">
                                    {{ $log->ip_address ?? '127.0.0.1' }}
                                </td>
                                <td class="p-4 text-slate-600 text-xs max-w-[250px] truncate font-sans">
                                    {{ $log->description }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400 font-sans text-xs">No audit logs matching query.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            {{ $logs->links() }}
        </div>

    </div>
@endsection
