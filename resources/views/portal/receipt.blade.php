<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt — {{ $payment->receipt_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@700;900&family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Cinzel', 'serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                    colors: {
                        pearl: {
                            50: '#f0fdf9',
                            100: '#ccfbef',
                            600: '#0d9488',
                            700: '#0f766e',
                            800: '#115e59',
                            900: '#134e4a',
                            950: '#042f2e',
                        },
                    }
                }
            }
        }
    </script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
            }
            .receipt-card {
                border: none !important;
                box-shadow: none !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen py-10 px-4 font-sans text-slate-800 antialiased">

    <div class="max-w-2xl mx-auto space-y-6">

        <!-- Top Action Bar (hidden on print) -->
        <div class="no-print flex items-center justify-between bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center space-x-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                <span class="text-xs font-bold text-slate-700">Official Payment Receipt &bull; Immutable Record</span>
            </div>
            <div class="flex items-center space-x-3">
                <button onclick="window.print()" class="px-5 py-2 rounded-xl text-xs font-bold bg-pearl-700 hover:bg-pearl-800 text-white shadow-md transition flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Print Receipt</span>
                </button>
                <button onclick="window.close()" class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition">
                    Close
                </button>
            </div>
        </div>

        <!-- Receipt Main Card -->
        <div class="receipt-card bg-white p-8 sm:p-12 rounded-3xl border border-slate-200 shadow-xl space-y-8 relative overflow-hidden">
            
            <!-- Watermark / Stamp -->
            <div class="absolute right-8 top-32 rotate-[-15deg] border-4 border-emerald-500/20 text-emerald-600/20 text-3xl font-black uppercase px-6 py-2 rounded-2xl pointer-events-none select-none font-mono">
                PAID &bull; VERIFIED
            </div>

            <!-- Receipt Header -->
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6 border-b-2 border-slate-900 pb-6">
                <div>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-pearl-800 text-white font-heading font-black text-xl flex items-center justify-center">
                            P
                        </div>
                        <div>
                            <h1 class="font-heading font-black text-xl text-slate-900 leading-tight">Pearl Training Institute</h1>
                            <span class="text-[10px] uppercase font-bold text-pearl-700 tracking-widest block">Excellence in Vocational & Digital Education</span>
                        </div>
                    </div>
                    <div class="text-xs text-slate-500 mt-3 space-y-0.5">
                        <p>Pearl Towers, 3rd Floor, Moi Avenue, Nairobi CBD, Kenya</p>
                        <p>Tel: +254 700 123 456 &bull; Email: finance@pearlinstitute.com</p>
                    </div>
                </div>

                <div class="text-left sm:text-right space-y-1">
                    <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Official Receipt</span>
                    <div class="font-mono font-bold text-lg text-slate-900">{{ $payment->receipt_number }}</div>
                    <div class="text-xs text-slate-500">Date: {{ $payment->created_at->format('d F Y, h:i A') }}</div>
                </div>
            </div>

            <!-- Payer & Transaction Meta -->
            <div class="grid grid-cols-2 gap-6 text-xs">
                <div class="space-y-1">
                    <span class="text-slate-400 uppercase text-[10px] font-bold block">Received From / Payer:</span>
                    <strong class="text-sm font-bold text-slate-900 block">
                        {{ $payment->user->name ?? $payment->payer_name ?? 'Walk-in Customer' }}
                    </strong>
                    <div class="text-slate-600">Phone: {{ $payment->phone_number ?? ($payment->user->phone ?? 'N/A') }}</div>
                    @if($payment->user && $payment->user->admission_number)
                        <div class="font-mono font-bold text-teal-800">Adm No: {{ $payment->user->admission_number }}</div>
                    @endif
                </div>

                <div class="space-y-1 sm:text-right">
                    <span class="text-slate-400 uppercase text-[10px] font-bold block">Payment Details:</span>
                    <div class="font-bold text-slate-900 uppercase">
                        Method: {{ str_replace('_', ' ', $payment->payment_method) }}
                    </div>
                    @if($payment->mpesa_receipt_number)
                        <div class="font-mono text-emerald-700 font-bold">M-Pesa Ref: {{ $payment->mpesa_receipt_number }}</div>
                    @endif
                    <div class="text-slate-500 capitalize">Purpose: {{ $payment->purpose }}</div>
                </div>
            </div>

            <!-- Line Items Table -->
            <div class="border border-slate-200 rounded-2xl overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 border-b border-slate-200 font-bold uppercase text-[10px] text-slate-400">
                        <tr>
                            <th class="p-3.5">Item Description</th>
                            <th class="p-3.5 text-right">Amount (KES)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="p-3.5">
                                <strong class="text-slate-900 block font-semibold">
                                    @if($payment->enrollment)
                                        {{ $payment->enrollment->course->title }} &mdash; {{ $payment->enrollment->cohort->name }}
                                    @elseif($payment->serviceRequest)
                                        Ideal Print Shop: {{ $payment->serviceRequest->service_type }} (x{{ $payment->serviceRequest->quantity }})
                                    @else
                                        Course Fee Payment / Print Service
                                    @endif
                                </strong>
                                <span class="text-[11px] text-slate-400 capitalize">Purpose: {{ $payment->purpose }}</span>
                            </td>
                            <td class="p-3.5 text-right font-mono font-bold text-sm text-slate-900">
                                {{ number_format($payment->amount, 2) }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot class="bg-slate-50/80 border-t border-slate-200">
                        <tr>
                            <td class="p-3.5 font-heading font-black text-sm text-slate-900 text-right">TOTAL RECEIVED:</td>
                            <td class="p-3.5 font-heading font-black text-lg text-emerald-700 text-right font-mono">
                                KES {{ number_format($payment->amount, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Student Running Balance Summary if Course Enrollment -->
            @if($payment->enrollment)
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs flex justify-between items-center">
                    <div>
                        <span class="text-slate-400 uppercase text-[10px] font-bold block">Course Fee Status:</span>
                        <span class="text-slate-700 font-semibold">{{ $payment->enrollment->course->title }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-400 text-[10px] block">Updated Running Balance:</span>
                        <strong class="font-mono text-sm font-bold text-slate-900">KES {{ number_format($payment->enrollment->fee_balance, 2) }}</strong>
                    </div>
                </div>
            @endif

            <!-- Receipt Footer & Verification Sign-off -->
            <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row sm:items-end justify-between gap-6 text-[11px] text-slate-500">
                <div class="space-y-1">
                    <p>Issued by: <strong>{{ $payment->recorder->name ?? 'Accounts Desk / System Gateway' }}</strong></p>
                    <p class="text-[10px] text-slate-400">This is a computer-generated official receipt and requires no physical seal.</p>
                </div>
                <div class="text-left sm:text-right font-mono text-[10px] text-slate-400">
                    <div>Ref: {{ $payment->receipt_number }}</div>
                    <div>PTI Finance Management System &bull; Kenya</div>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
