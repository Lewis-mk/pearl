@extends('layouts.portal')

@section('title', 'Student Profile & Settings — Pearl Training Institute')
@section('page_title', 'Student Profile & Account Settings')
@section('page_subtitle', 'Manage your contact details and portal credentials')

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
    <a href="{{ route('student.misconduct') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition">
        <span>🛡️</span> <span>Misconduct Report</span>
    </a>
    <a href="{{ route('student.profile') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-pearl-700 text-white font-bold transition">
        <span>⚙️</span> <span>Profile & Settings</span>
    </a>
@endsection

@section('portal_content')
    <div class="max-w-2xl mx-auto space-y-6">

        <div class="bg-white p-8 rounded-3xl border border-slate-200 shadow-md space-y-6">
            
            <div class="flex items-center space-x-4 border-b border-slate-100 pb-6">
                <div class="w-16 h-16 rounded-2xl bg-pearl-700 text-white font-black text-2xl flex items-center justify-center shadow-md">
                    {{ substr($user->name, 0, 2) }}
                </div>
                <div>
                    <h3 class="font-heading font-black text-xl text-slate-900">{{ $user->name }}</h3>
                    <div class="text-xs font-mono font-bold text-amber-600">Admission No: {{ $user->admission_number ?? 'Pending Approval' }}</div>
                    <div class="text-xs text-slate-400 mt-0.5">Enrolled Student &bull; Compliant with Kenya Data Protection Act</div>
                </div>
            </div>

            <form action="{{ route('student.profile.update') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Full Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Personal Login Email (Read Only)</label>
                    <input type="email" disabled value="{{ $user->email }}" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm bg-slate-100 text-slate-500 cursor-not-allowed">
                    <span class="text-[10px] text-slate-400 mt-1 block">Your personal email is linked to your permanent academic record.</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Phone Number (M-Pesa & SMS Alerts) *</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                </div>

                <div class="pt-4 border-t border-slate-100 space-y-4">
                    <h4 class="font-heading font-bold text-sm text-slate-900 uppercase tracking-wider">Change Password (Leave blank to keep current)</h4>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Current Password</label>
                        <input type="password" name="current_password" placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">New Password</label>
                            <input type="password" name="new_password" placeholder="Min 8 characters" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Confirm New Password</label>
                            <input type="password" name="new_password_confirmation" placeholder="Re-type password" class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-3.5 rounded-xl font-heading font-bold text-xs text-white bg-pearl-600 hover:bg-pearl-700 shadow-md shadow-pearl-700/20 transition transform active:scale-95 text-center">
                    Save Profile Changes &rarr;
                </button>
            </form>

        </div>

    </div>
@endsection
