<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Pearl Training Institute — Practical Short Courses & Career Skills in Kenya')</title>
    <meta name="description" content="@yield('meta_description', 'Pearl Training Institute offers hands-on accredited short courses in Web Dev, Graphic Design, QuickBooks Accounting, and Cyber Services in Kenya.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        heading: ['"Outfit"', 'sans-serif'],
                    },
                    colors: {
                        pearl: {
                            50: '#f0fdf9',
                            100: '#ccfbef',
                            200: '#99f6e0',
                            300: '#5eead4',
                            400: '#2dd4bf',
                            500: '#0d9488',
                            600: '#0f766e',
                            700: '#115e59',
                            800: '#134e4a',
                            900: '#042f2e',
                        },
                        amberGold: {
                            500: '#f59e0b',
                            600: '#d97706',
                        },
                        mpesa: '#00A859',
                    }
                }
            }
        }
    </script>
    <style>
        .font-heading { font-family: 'Outfit', sans-serif; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .hero-pattern {
            background-color: #042f2e;
            background-image: radial-gradient(rgba(13, 148, 136, 0.25) 1px, transparent 1px);
            background-size: 28px 28px;
        }
    </style>
</head>
<body class="font-sans text-slate-800 bg-slate-50 flex flex-col min-h-screen antialiased selection:bg-pearl-500 selection:text-white">

    <!-- Top Announcement Bar -->
    <div class="bg-gradient-to-r from-pearl-900 via-pearl-800 to-teal-950 text-white text-xs py-2 px-4 border-b border-pearl-700/40">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-400 text-slate-950 uppercase tracking-wider">Now Enrolling</span>
                <span>🎓 Next Intake Batches Starting Soon — Flexible Morning & Evening Shifts</span>
            </div>
            <div class="flex items-center space-x-4 text-slate-200 text-xs">
                <span>📍 Pearl Towers, Moi Ave, Nairobi</span>
                <span class="hidden sm:inline">|</span>
                <span class="hidden sm:inline font-semibold">📞 +254 700 123 456</span>
                <a href="{{ route('certificate.verify') }}" class="underline hover:text-amber-300 transition">Verify Certificate</a>
            </div>
        </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="sticky top-0 z-50 glass-nav border-b border-slate-200/80 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center space-x-3 group">
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-pearl-800 to-teal-500 flex items-center justify-center text-white shadow-md shadow-pearl-700/20 group-hover:scale-105 transition transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <div>
                        <span class="font-heading font-black text-xl tracking-tight text-slate-900 block leading-tight">PEARL</span>
                        <span class="text-[10px] font-bold text-pearl-600 tracking-widest uppercase block -mt-1">Training Institute</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold text-slate-700">
                    <a href="{{ route('home') }}" class="hover:text-pearl-600 transition {{ request()->routeIs('home') ? 'text-pearl-600 font-bold' : '' }}">Home</a>
                    <a href="{{ route('courses') }}" class="hover:text-pearl-600 transition {{ request()->routeIs('courses*') ? 'text-pearl-600 font-bold' : '' }}">Courses & Cohorts</a>
                    <a href="{{ route('printshop') }}" class="hover:text-pearl-600 transition flex items-center space-x-1 {{ request()->routeIs('printshop*') ? 'text-pearl-600 font-bold' : '' }}">
                        <span>Ideal Print Shop</span>
                        <span class="px-1.5 py-0.5 text-[10px] bg-teal-100 text-teal-800 rounded font-bold">Cyber</span>
                    </a>
                    <a href="{{ route('about') }}" class="hover:text-pearl-600 transition {{ request()->routeIs('about') ? 'text-pearl-600 font-bold' : '' }}">About Us</a>
                    <a href="{{ route('contact') }}" class="hover:text-pearl-600 transition {{ request()->routeIs('contact') ? 'text-pearl-600 font-bold' : '' }}">Contact</a>
                </nav>

                <!-- Auth & Portal Action Buttons -->
                <div class="flex items-center space-x-3">
                    @auth
                        <div class="flex items-center space-x-2">
                            @if(auth()->user()->hasRole('admin'))
                                <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 text-xs font-bold bg-purple-600 hover:bg-purple-700 text-white rounded-lg shadow-sm transition">Admin Portal</a>
                            @elseif(auth()->user()->hasRole('trainer'))
                                <a href="{{ route('trainer.dashboard') }}" class="px-4 py-2 text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white rounded-lg shadow-sm transition">Trainer Portal</a>
                            @elseif(auth()->user()->hasRole('cyber_attendant'))
                                <a href="{{ route('cyber.dashboard') }}" class="px-4 py-2 text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg shadow-sm transition">Cyber Portal</a>
                            @else
                                <a href="{{ route('student.dashboard') }}" class="px-4 py-2 text-xs font-bold bg-pearl-600 hover:bg-pearl-700 text-white rounded-lg shadow-sm transition">Student Portal</a>
                            @endif
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-2 text-slate-400 hover:text-red-600 transition rounded-lg hover:bg-red-50" title="Logout">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-slate-700 hover:text-pearl-600 hover:bg-slate-100 rounded-lg transition">Login</a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 text-sm font-bold text-white bg-pearl-600 hover:bg-pearl-700 rounded-xl shadow-sm shadow-pearl-700/20 transition transform active:scale-95">Enroll Now</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Global Toast Alerts -->
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start space-x-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <div class="text-sm font-medium leading-relaxed">{{ session('success') }}</div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start space-x-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                <div class="text-sm font-medium leading-relaxed">{{ session('error') }}</div>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="max-w-7xl mx-auto px-4 mt-4 w-full">
            <div class="p-4 rounded-xl bg-sky-50 border border-sky-200 text-sky-900 flex items-start space-x-3 shadow-sm">
                <svg class="w-5 h-5 text-sky-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                <div class="text-sm font-medium leading-relaxed">{{ session('info') }}</div>
            </div>
        </div>
    @endif

    <!-- Main Content Area -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 text-sm mt-20 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <!-- Col 1: Brand & Info -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-3 text-white">
                        <div class="w-9 h-9 rounded-lg bg-pearl-600 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/></svg>
                        </div>
                        <span class="font-heading font-black text-lg text-white">PEARL INSTITUTE</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Leading short-course vocational and technical training center in Kenya. Empowering students with industry-relevant skills in software, design, accounting, and cyber operations.
                    </p>
                    <div class="text-xs text-slate-400">
                        <div class="font-semibold text-slate-300">Daraja M-Pesa Paybill:</div>
                        <span class="font-mono text-emerald-400 text-sm font-bold">174379</span> (Account: Student Admission No)
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div>
                    <h4 class="font-heading font-bold text-white uppercase text-xs tracking-wider mb-4">Quick Navigation</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('courses') }}" class="hover:text-white transition">All Courses & Cohorts</a></li>
                        <li><a href="{{ route('printshop') }}" class="hover:text-white transition">Ideal Print Shop & Cyber</a></li>
                        <li><a href="{{ route('certificate.verify') }}" class="hover:text-white transition">Certificate Verification</a></li>
                        <li><a href="{{ route('about') }}" class="hover:text-white transition">About the Institute</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-white transition">Contact & Campus Map</a></li>
                    </ul>
                </div>

                <!-- Col 3: Popular Programs -->
                <div>
                    <h4 class="font-heading font-bold text-white uppercase text-xs tracking-wider mb-4">Top Programs</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('courses') }}" class="hover:text-white transition">Full Stack Web Dev (Laravel)</a></li>
                        <li><a href="{{ route('courses') }}" class="hover:text-white transition">Graphic Design & UI/UX</a></li>
                        <li><a href="{{ route('courses') }}" class="hover:text-white transition">QuickBooks Accounting & iTax</a></li>
                        <li><a href="{{ route('courses') }}" class="hover:text-white transition">Computer Packages & Typing</a></li>
                    </ul>
                </div>

                <!-- Col 4: Contact & Location -->
                <div>
                    <h4 class="font-heading font-bold text-white uppercase text-xs tracking-wider mb-4">Campus Location</h4>
                    <p class="text-xs text-slate-400 leading-relaxed mb-3">
                        Pearl Towers, 3rd Floor<br>
                        Moi Avenue, Nairobi CBD, Kenya
                    </p>
                    <p class="text-xs text-slate-400 mb-1"><span class="text-slate-300">Helpline:</span> +254 700 123 456</p>
                    <p class="text-xs text-slate-400"><span class="text-slate-300">Email:</span> admissions@pearlinstitute.com</p>
                </div>
            </div>

            <div class="mt-12 pt-6 border-t border-slate-800 text-center text-xs text-slate-400 flex flex-wrap justify-between items-center gap-4">
                <p>&copy; {{ date('Y') }} Pearl Training Institute. All rights reserved. Compliant with Kenya Data Protection Act 2019.</p>
                <div class="flex space-x-4">
                    <a href="{{ route('login') }}" class="hover:text-slate-300">Staff Portal Login</a>
                    <span>•</span>
                    <a href="{{ route('login') }}" class="hover:text-slate-300">Student Portal Login</a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
