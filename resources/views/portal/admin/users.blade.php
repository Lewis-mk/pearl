@extends('layouts.portal')

@section('title', 'Users, Roles & Permissions — Pearl Training Institute')
@section('page_title', 'User Management & RBAC Matrix')
@section('page_subtitle', 'Assign multiple roles, grant or revoke granular per-user permission overrides, manage staff email provisioning')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Administration</div>
    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📊</span> <span>Dashboard</span>
    </a>
    <a href="{{ route('admin.users') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-purple-700 text-white font-bold transition">
        <span>👥</span> <span>Users & RBAC</span>
    </a>
    <a href="{{ route('admin.admissions') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>🎓</span> <span>Admissions Queue</span>
    </a>
    <a href="{{ route('admin.financials') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>💳</span> <span>Financials & Receipts</span>
    </a>
    <a href="{{ route('admin.courses') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📚</span> <span>Courses & Cohorts</span>
    </a>
    <a href="{{ route('admin.misconduct') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>🛡️</span> <span>Misconduct Inbox</span>
    </a>
    <a href="{{ route('admin.attendance') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>⏱️</span> <span>Attendance Oversight</span>
    </a>
    <a href="{{ route('admin.audit') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>📋</span> <span>Audit Logs</span>
    </a>
    <a href="{{ route('admin.settings') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>⚙️</span> <span>System Settings</span>
    </a>
@endsection

@section('portal_content')
    <div class="space-y-6">

        <!-- Search & Filter Bar -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-col sm:flex-row gap-3">
            <form action="{{ route('admin.users') }}" method="GET" class="flex flex-1 gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, phone, or admission number..." class="flex-1 px-4 py-2 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-purple-500 focus:outline-none bg-slate-50">
                <select name="role" class="px-3 py-2 rounded-xl border border-slate-300 text-xs bg-slate-50 font-medium">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>{{ $role->display_name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold bg-purple-600 text-white hover:bg-purple-700 transition">Search</button>
            </form>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="p-4">User</th>
                            <th class="p-4">Adm No / Phone</th>
                            <th class="p-4">Assigned Roles</th>
                            <th class="p-4">Staff Email</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $u)
                            <tr class="hover:bg-slate-50 transition" id="user-row-{{ $u->id }}">
                                <td class="p-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-800 flex items-center justify-center font-bold text-[10px]">
                                            {{ strtoupper(substr($u->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <div class="font-mono text-slate-700 text-[11px]">{{ $u->admission_number ?? '—' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $u->phone }}</div>
                                </td>
                                <td class="p-4">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($u->roles as $role)
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 text-purple-800">{{ $role->display_name }}</span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="p-4">
                                    @if($u->staff_official_email)
                                        <span class="font-mono text-xs text-teal-700">{{ $u->staff_official_email }}</span>
                                        <span class="block text-[10px] {{ $u->staff_email_status === 'active' ? 'text-emerald-600' : 'text-amber-600' }} font-bold">{{ $u->staff_email_status }}</span>
                                    @else
                                        <span class="text-slate-400 text-[10px]">Not provisioned</span>
                                    @endif
                                </td>
                                <td class="p-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $u->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }} uppercase">
                                        {{ $u->status }}
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <button type="button" onclick="document.getElementById('rbac-modal-{{ $u->id }}').classList.toggle('hidden')" class="px-3 py-1.5 text-xs font-bold bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition">
                                        Manage Roles &rarr;
                                    </button>
                                </td>
                            </tr>

                            <!-- Inline RBAC Row Drawer -->
                            <tr id="rbac-modal-{{ $u->id }}" class="hidden bg-purple-50/40">
                                <td colspan="6" class="p-5">
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                                        <!-- Role Assignment -->
                                        <div class="p-5 rounded-2xl bg-white border border-purple-200 space-y-3">
                                            <h4 class="font-heading font-bold text-sm text-purple-900">Role Assignments (Multi-role)</h4>
                                            <form action="{{ route('admin.users.roles', $u->id) }}" method="POST">
                                                @csrf
                                                <div class="flex flex-wrap gap-2 mb-3">
                                                    @foreach($roles as $role)
                                                        <label class="flex items-center space-x-1.5 text-xs cursor-pointer">
                                                            <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                                                {{ $u->roles->pluck('name')->contains($role->name) ? 'checked' : '' }}
                                                                class="rounded text-purple-600 focus:ring-purple-500">
                                                            <span class="font-semibold text-slate-700">{{ $role->display_name }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                @if($u->is_staff)
                                                    <div class="space-y-2 pt-2 border-t border-purple-100">
                                                        <label class="block text-[10px] font-bold text-purple-900 uppercase">Staff Email Provisioning</label>
                                                        @if(!$u->staff_official_email)
                                                            <input type="text" name="staff_email_username" placeholder="Desired username (e.g. peter.kamau)" class="w-full px-3 py-2 rounded-lg border border-purple-200 text-xs bg-white">
                                                            <span class="text-[10px] text-slate-400 block">Will provision @pearlinstitute.com via Zoho Mail API</span>
                                                        @else
                                                            <div class="text-xs text-emerald-700 font-semibold">✓ {{ $u->staff_official_email }} ({{ $u->staff_email_status }})</div>
                                                        @endif
                                                    </div>
                                                @endif
                                                <button type="submit" class="mt-3 w-full py-2.5 rounded-xl text-xs font-bold bg-purple-600 hover:bg-purple-700 text-white transition">Save Role Changes</button>
                                            </form>
                                        </div>

                                        <!-- Permission Overrides Matrix -->
                                        <div class="p-5 rounded-2xl bg-white border border-purple-200 space-y-3">
                                            <h4 class="font-heading font-bold text-sm text-purple-900">Per-User Permission Overrides</h4>
                                            <p class="text-[11px] text-slate-400">These override role-level permissions. Red = explicit deny, Green = explicit grant.</p>

                                            <form action="{{ route('admin.users.permissions', $u->id) }}" method="POST" class="space-y-2">
                                                @csrf
                                                <div class="divide-y divide-slate-100">
                                                    @foreach($permissions->take(10) as $perm)
                                                        @php
                                                            $override = $u->permissionOverrides->where('id', $perm->id)->first();
                                                            $currentVal = $override ? ($override->pivot->is_granted ? 'grant' : 'deny') : 'inherit';
                                                        @endphp
                                                        <div class="py-2 flex items-center justify-between text-xs gap-2">
                                                            <span class="text-slate-700 font-semibold truncate max-w-[130px]" title="{{ $perm->name }}">{{ $perm->display_name }}</span>
                                                            <select name="overrides[{{ $perm->id }}]" class="px-2 py-1 rounded-lg border border-slate-200 text-[10px] bg-slate-50 font-medium">
                                                                <option value="inherit" {{ $currentVal === 'inherit' ? 'selected' : '' }}>Inherit from Role</option>
                                                                <option value="grant" {{ $currentVal === 'grant' ? 'selected' : '' }}>✓ Explicitly Grant</option>
                                                                <option value="deny" {{ $currentVal === 'deny' ? 'selected' : '' }}>✕ Explicitly Deny</option>
                                                            </select>
                                                        </div>
                                                    @endforeach
                                                </div>
                                                <button type="submit" class="w-full py-2.5 rounded-xl text-xs font-bold bg-slate-900 hover:bg-purple-700 text-white transition">Save Permission Overrides</button>
                                            </form>
                                        </div>

                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400 text-xs">No users found matching your search.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>{{ $users->links() }}</div>

    </div>
@endsection
