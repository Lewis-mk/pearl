@extends('layouts.portal')

@section('title', 'System Settings & API Gateways — Pearl Training Institute')
@section('page_title', 'System Configuration & Integration Gateways')
@section('page_subtitle', 'Manage Daraja M-Pesa parameters, Zoho Mail API credentials, Africa\'s Talking SMS, and governance escalation rules')

@section('sidebar_menu')
    <div class="text-[10px] uppercase font-bold text-slate-400 px-3 pt-2 mb-1">Administration</div>
    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📊</span><span>Dashboard</span></a>
    <a href="{{ route('admin.users') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>👥</span><span>Users & RBAC</span></a>
    <a href="{{ route('admin.admissions') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>🎓</span><span>Admissions Queue</span></a>
    <a href="{{ route('admin.financials') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>💳</span><span>Financials & Receipts</span></a>
    <a href="{{ route('admin.courses') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📚</span><span>Courses & Cohorts</span></a>
    <a href="{{ route('admin.misconduct') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>🛡️</span><span>Misconduct Inbox</span></a>
    <a href="{{ route('admin.attendance') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>⏱️</span><span>Attendance Oversight</span></a>
    <a href="{{ route('admin.audit') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition"><span>📋</span><span>Audit Logs</span></a>
    <a href="{{ route('admin.settings') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl bg-purple-700 text-white font-bold transition"><span>⚙️</span><span>System Settings</span></a>
@endsection

@section('portal_content')
    <div class="max-w-4xl mx-auto space-y-8">

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Safaricom Daraja M-Pesa Settings Card -->
            <div class="p-7 sm:p-8 bg-white rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center space-x-3 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-lg">
                        📱
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-slate-900">Safaricom Daraja API Gateway (M-Pesa)</h3>
                        <p class="text-xs text-slate-400">Configure STK Push, C2B Paybill validation & confirmation webhooks</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Environment Mode *</label>
                        <select name="settings[daraja_environment]" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-slate-50 font-medium">
                            <option value="sandbox" {{ ($settings['daraja_environment'] ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox Simulation Mode (Testing)</option>
                            <option value="production" {{ ($settings['daraja_environment'] ?? '') === 'production' ? 'selected' : '' }}>Live Production Gateway</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Shortcode / Business Paybill *</label>
                        <input type="text" name="settings[daraja_paybill]" value="{{ $settings['daraja_paybill'] ?? '174379' }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono font-bold bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Daraja Consumer Key</label>
                        <input type="text" name="settings[daraja_consumer_key]" value="{{ $settings['daraja_consumer_key'] ?? '' }}" placeholder="Paste consumer key..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Daraja Consumer Secret</label>
                        <input type="password" name="settings[daraja_consumer_secret]" value="{{ $settings['daraja_consumer_secret'] ?? '' }}" placeholder="••••••••••••••••" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Online Passkey (Lipa na M-Pesa Online)</label>
                        <input type="text" name="settings[daraja_passkey]" value="{{ $settings['daraja_passkey'] ?? '' }}" placeholder="bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50">
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-emerald-50 text-emerald-950 text-xs flex items-center justify-between font-mono">
                    <span>Registered Webhook Endpoint:</span>
                    <strong class="text-emerald-800">{{ url('/api/v1/mpesa/callback') }}</strong>
                </div>
            </div>

            <!-- Zoho Mail API Settings Card -->
            <div class="p-7 sm:p-8 bg-white rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center space-x-3 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center font-bold text-lg">
                        ✉️
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-slate-900">Zoho Mail API Provisioning (@pearlinstitute.com)</h3>
                        <p class="text-xs text-slate-400">Automated creation of custom domain mailboxes for verified staff and instructors</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Zoho Organization ID (ZOHO_ORG_ID)</label>
                        <input type="text" name="settings[zoho_org_id]" value="{{ $settings['zoho_org_id'] ?? '' }}" placeholder="e.g. 700123456" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Zoho Client ID</label>
                        <input type="text" name="settings[zoho_client_id]" value="{{ $settings['zoho_client_id'] ?? '' }}" placeholder="1000.XXXXXX..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Zoho Client Secret</label>
                        <input type="password" name="settings[zoho_client_secret]" value="{{ $settings['zoho_client_secret'] ?? '' }}" placeholder="••••••••••••••••" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Zoho Refresh Token</label>
                        <input type="password" name="settings[zoho_refresh_token]" value="{{ $settings['zoho_refresh_token'] ?? '' }}" placeholder="1000.refresh_token..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs font-mono bg-slate-50">
                    </div>
                </div>
            </div>

            <!-- Whistleblower Escalation & Governance Card -->
            <div class="p-7 sm:p-8 bg-white rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center space-x-3 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-800 flex items-center justify-center font-bold text-lg">
                        🛡️
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-slate-900">Governance & Whistleblower Secondary Escalation</h3>
                        <p class="text-xs text-slate-400">Independent trustee routing when a misconduct grievance concerns the Administrator</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-purple-950 uppercase tracking-wider mb-1">Secondary Escalation Trustee Email *</label>
                    <input type="email" name="settings[misconduct_escalation_contact]" value="{{ $settings['misconduct_escalation_contact'] ?? 'governance@pearlinstitute.com' }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-purple-300 text-xs font-mono font-bold bg-slate-50">
                    <span class="text-[11px] text-slate-400 mt-1 block">When a report targets an admin, this external address receives full automated escalation dispatch.</span>
                </div>
            </div>

            <!-- Campus & General Settings Card -->
            <div class="p-7 sm:p-8 bg-white rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <div class="flex items-center space-x-3 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-800 flex items-center justify-center font-bold text-lg">
                        🏫
                    </div>
                    <div>
                        <h3 class="font-heading font-bold text-lg text-slate-900">Institution Identity & Contact Info</h3>
                        <p class="text-xs text-slate-400">Displayed on official printable receipts, footer, and public verification records</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Institution Name</label>
                        <input type="text" name="settings[institute_name]" value="{{ $settings['institute_name'] ?? 'Pearl Training Institute' }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Admissions Phone</label>
                        <input type="text" name="settings[support_phone]" value="{{ $settings['support_phone'] ?? '+254 700 123 456' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Support Email</label>
                        <input type="email" name="settings[support_email]" value="{{ $settings['support_email'] ?? 'admissions@pearlinstitute.com' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-slate-50">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Physical Campus Address</label>
                        <input type="text" name="settings[campus_address]" value="{{ $settings['campus_address'] ?? 'Pearl Towers, 3rd Floor, Moi Avenue, Nairobi CBD, Kenya' }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs bg-slate-50">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" class="px-8 py-3.5 rounded-xl font-heading font-bold text-xs text-white bg-purple-600 hover:bg-purple-700 shadow-md shadow-purple-700/20 transition transform active:scale-95">
                    Save System Settings & Gateways &rarr;
                </button>
            </div>
        </form>

    </div>
@endsection
