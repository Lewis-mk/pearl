<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Portal — Pearl Training Institute')</title>
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
                        mpesa: '#00A859',
                    }
                }
            }
        }
    </script>
    <style>
        .font-heading { font-family: 'Outfit', sans-serif; }
    </style>
</head>
<body class="font-sans text-slate-800 bg-slate-100 min-h-screen flex flex-col md:flex-row antialiased">

    <!-- Mobile Header -->
    <div class="md:hidden bg-slate-900 text-white p-4 flex items-center justify-between sticky top-0 z-50">
        <a href="{{ route('home') }}" class="flex items-center space-x-2">
            <div class="w-8 h-8 rounded-lg bg-pearl-600 flex items-center justify-center font-bold text-white text-sm">P</div>
            <span class="font-heading font-black text-lg">PEARL PORTAL</span>
        </a>
        <button onclick="document.getElementById('mobileSidebar').classList.toggle('hidden')" class="p-2 rounded-lg bg-slate-800 text-slate-300">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
        </button>
    </div>

    <!-- Sidebar Navigation -->
    <aside id="mobileSidebar" class="hidden md:flex md:w-64 lg:w-72 bg-slate-900 text-slate-300 flex-col shrink-0 border-r border-slate-800 min-h-screen sticky top-0">
        <!-- Logo & Header -->
        <div class="p-6 border-b border-slate-800 flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-pearl-600 to-teal-400 flex items-center justify-center text-white font-black shadow-md shadow-pearl-900/50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5z"/></svg>
            </div>
            <div>
                <a href="{{ route('home') }}" class="font-heading font-bold text-white text-base tracking-tight block">PEARL INSTITUTE</a>
                <span class="text-[10px] text-teal-400 font-bold uppercase tracking-wider block">Integrated LMS & Ops</span>
            </div>
        </div>

        <!-- User Profile Card in Sidebar -->
        <div class="p-4 mx-4 my-4 rounded-xl bg-slate-800/80 border border-slate-700/60">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full bg-pearl-700/60 text-teal-200 flex items-center justify-center font-bold text-sm uppercase">
                    {{ substr(auth()->user()->name, 0, 2) }}
                </div>
                <div class="overflow-hidden">
                    <div class="font-bold text-white text-sm truncate">{{ auth()->user()->name }}</div>
                    @if(auth()->user()->admission_number)
                        <div class="text-[11px] font-mono text-amber-400 font-semibold truncate">Adm: {{ auth()->user()->admission_number }}</div>
                    @elseif(auth()->user()->staff_official_email)
                        <div class="text-[11px] text-teal-300 truncate">{{ auth()->user()->staff_official_email }}</div>
                    @else
                        <div class="text-[11px] text-slate-400 truncate">{{ auth()->user()->email }}</div>
                    @endif
                </div>
            </div>

            <!-- Role Badges & Multi-Role Support -->
            <div class="mt-3 pt-2 border-t border-slate-700/50 flex flex-wrap gap-1">
                @foreach(auth()->user()->roles as $r)
                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-md 
                        @if($r->name === 'admin') bg-purple-900/60 text-purple-200 border border-purple-700/50
                        @elseif($r->name === 'trainer') bg-blue-900/60 text-blue-200 border border-blue-700/50
                        @elseif($r->name === 'cyber_attendant') bg-emerald-900/60 text-emerald-200 border border-emerald-700/50
                        @else bg-teal-900/60 text-teal-200 border border-teal-700/50
                        @endif">
                        {{ $r->display_name }}
                    </span>
                @endforeach
            </div>

            <!-- Role Switcher Quick Links if holding multiple roles -->
            @if(auth()->user()->roles->count() > 1 || auth()->user()->hasRole('admin'))
                <div class="mt-3 text-[11px] text-slate-400">
                    <span class="block text-[10px] uppercase font-bold text-slate-400 mb-1">Switch Portal View:</span>
                    <div class="grid grid-cols-2 gap-1">
                        @if(auth()->user()->hasRole('admin'))
                            <a href="{{ route('admin.dashboard') }}" class="px-2 py-1 bg-purple-950/70 hover:bg-purple-900 text-purple-200 rounded text-center text-[10px] font-bold transition">Admin</a>
                        @endif
                        @if(auth()->user()->hasRole(['trainer', 'admin']))
                            <a href="{{ route('trainer.dashboard') }}" class="px-2 py-1 bg-blue-950/70 hover:bg-blue-900 text-blue-200 rounded text-center text-[10px] font-bold transition">Trainer</a>
                        @endif
                        @if(auth()->user()->hasRole(['cyber_attendant', 'admin']))
                            <a href="{{ route('cyber.dashboard') }}" class="px-2 py-1 bg-emerald-950/70 hover:bg-emerald-900 text-emerald-200 rounded text-center text-[10px] font-bold transition">Cyber</a>
                        @endif
                        @if(auth()->user()->hasRole(['student', 'admin']))
                            <a href="{{ route('student.dashboard') }}" class="px-2 py-1 bg-teal-950/70 hover:bg-teal-900 text-teal-200 rounded text-center text-[10px] font-bold transition">Student</a>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Navigation Menu Scoped by Active Section -->
        <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto text-xs font-semibold">
            @yield('sidebar_menu')
        </nav>

        <!-- Sidebar Footer & Logout -->
        <div class="p-4 border-t border-slate-800 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-xs text-slate-400 hover:text-white transition flex items-center space-x-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Public Website</span>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="px-3 py-1.5 text-xs font-bold text-red-400 hover:text-red-300 hover:bg-red-950/50 rounded-lg transition flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Workspace Content -->
    <div class="flex-1 flex flex-col min-w-0">
        <!-- Top Navbar -->
        <header class="bg-white border-b border-slate-200 px-6 py-4 flex flex-wrap items-center justify-between gap-4 shadow-sm">
            <div>
                <h1 class="font-heading font-black text-xl text-slate-900">@yield('page_title', 'Dashboard')</h1>
                <p class="text-xs text-slate-400">@yield('page_subtitle', 'Pearl Training Institute Platform')</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-xs text-slate-400 hidden sm:inline">🕒 Nairobi (EAT): {{ now()->setTimezone('Africa/Nairobi')->format('d M Y, h:i A') }}</span>
                <div class="h-4 w-px bg-slate-200 hidden sm:inline"></div>
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">System Live</span>
                </div>
            </div>
        </header>

        <!-- Alerts -->
        <div class="px-6 pt-4">
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 flex items-start space-x-3 shadow-sm mb-4">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <div class="text-sm font-medium leading-relaxed">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start space-x-3 shadow-sm mb-4">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                    <div class="text-sm font-medium leading-relaxed">{{ session('error') }}</div>
                </div>
            @endif

            @if(session('info'))
                <div class="p-4 rounded-xl bg-sky-50 border border-sky-200 text-sky-900 flex items-start space-x-3 shadow-sm mb-4">
                    <svg class="w-5 h-5 text-sky-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                    <div class="text-sm font-medium leading-relaxed">{{ session('info') }}</div>
                </div>
            @endif
        </div>

        <!-- Page Body Content -->
        <main class="flex-1 p-6">
            @yield('portal_content')
        </main>
    </div>

</body>
</html>
