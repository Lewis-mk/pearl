@extends('layouts.portal')

@section('title', 'Admissions Queue — Pearl Training Institute')
@section('page_title', 'Student Admissions Queue')
@section('page_subtitle', 'Approve applications and issue sequential permanent admission numbers (PTI/YYYY/XXXXX)')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Administration</div>
    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📊</span><span>Dashboard</span></a>
    <a href="{{ route('admin.users') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>👥</span><span>Users & RBAC</span></a>
    <a href="{{ route('admin.admissions') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-purple-700 text-white font-bold transition"><span>🎓</span><span>Admissions Queue</span></a>
    <a href="{{ route('admin.financials') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>💳</span><span>Financials & Receipts</span></a>
    <a href="{{ route('admin.courses') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📚</span><span>Courses & Cohorts</span></a>
    <a href="{{ route('admin.misconduct') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>🛡️</span><span>Misconduct Inbox</span></a>
    <a href="{{ route('admin.attendance') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>⏱️</span><span>Attendance Oversight</span></a>
    <a href="{{ route('admin.audit') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📋</span><span>Audit Logs</span></a>
    <a href="{{ route('admin.settings') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>⚙️</span><span>System Settings</span></a>
@endsection

@section('portal_content')
    <div class="space-y-6">

        <!-- Tab Filters -->
        <div class="flex items-center space-x-2 bg-white p-2 rounded-2xl border border-slate-200 shadow-sm w-fit">
            @foreach(['pending' => 'Pending Review', 'active' => 'Active / Approved', 'deactivated' => 'Deactivated'] as $val => $label)
                <a href="{{ route('admin.admissions', ['status' => $val]) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition
                       {{ request('status', 'pending') === $val
                            ? 'bg-purple-700 text-white shadow-sm'
                            : 'text-slate-500 hover:bg-slate-100' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Students Table -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-400 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="p-4">Applicant</th>
                            <th class="p-4">Contact</th>
                            <th class="p-4">Program / Cohort</th>
                            <th class="p-4">Adm Number</th>
                            <th class="p-4">Applied</th>
                            <th class="p-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($students as $student)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-800 flex items-center justify-center font-bold text-[10px]">
                                            {{ strtoupper(substr($student->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-900">{{ $student->name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ $student->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-slate-600">{{ $student->phone }}</td>
                                <td class="p-4">
                                    @php $enr = $student->enrollments->first(); @endphp
                                    <div class="font-semibold text-slate-800 truncate max-w-[160px]">{{ $enr?->course?->title ?? '—' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $enr?->cohort?->name ?? '—' }}</div>
                                </td>
                                <td class="p-4 font-mono text-sm font-bold">
                                    @if($student->admission_number)
                                        <span class="text-emerald-700">{{ $student->admission_number }}</span>
                                    @else
                                        <span class="text-amber-600 text-[11px]">Not yet assigned</span>
                                    @endif
                                </td>
                                <td class="p-4 text-slate-400">{{ $student->created_at->format('d M Y') }}</td>
                                <td class="p-4 text-center">
                                    @if($student->status === 'pending')
                                        <form action="{{ route('admin.admissions.approve', $student->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Approve {{ $student->name }} and issue a permanent sequential admission number?');"
                                                    class="px-3.5 py-1.5 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm transition">
                                                ✓ Approve & Issue Adm No
                                            </button>
                                        </form>
                                    @elseif($student->status === 'active')
                                        <form action="{{ route('admin.admissions.deactivate', $student->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Deactivate {{ $student->name }}\'s portal access?');"
                                                    class="px-3 py-1.5 text-xs font-bold text-rose-600 hover:bg-rose-50 rounded-lg transition border border-rose-200">
                                                Deactivate
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.admissions.approve', $student->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 text-xs font-bold text-teal-700 hover:bg-teal-50 rounded-lg transition border border-teal-200">
                                                Reactivate
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="p-8 text-center text-slate-400">No students in this category.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div>{{ $students->links() }}</div>

    </div>
@endsection
