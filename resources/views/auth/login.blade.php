@extends('layouts.public')

@section('title', 'Login to Portal — Pearl Training Institute')

@section('content')
    <section class="py-16 bg-slate-100 min-h-[75vh] flex items-center justify-center">
        <div class="max-w-md w-full mx-auto px-4">
            
            <div class="bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/50 space-y-6">
                
                <div class="text-center space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-pearl-600 text-white font-black flex items-center justify-center mx-auto shadow-md shadow-pearl-700/20 text-xl">
                        P
                    </div>
                    <h1 class="font-heading font-black text-2xl text-slate-900">Portal Login</h1>
                    <p class="text-xs text-slate-400">Sign in with your registered email or phone number</p>
                </div>

                <!-- Quick Test Credentials Pill -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-[11px] text-slate-600 space-y-1.5">
                    <div class="font-bold text-slate-800 uppercase text-[10px] tracking-wider">Demo / Test Accounts (Password: <code>password</code>):</div>
                    <div class="flex flex-wrap gap-1">
                        <button type="button" onclick="fillCreds('admin@pearlinstitute.com')" class="px-2 py-0.5 bg-purple-100 text-purple-900 rounded font-semibold text-[10px] hover:bg-purple-200">Admin</button>
                        <button type="button" onclick="fillCreds('peter.trainer@gmail.com')" class="px-2 py-0.5 bg-blue-100 text-blue-900 rounded font-semibold text-[10px] hover:bg-blue-200">Trainer (Peter)</button>
                        <button type="button" onclick="fillCreds('john.otieno@gmail.com')" class="px-2 py-0.5 bg-amber-100 text-amber-900 rounded font-semibold text-[10px] hover:bg-amber-200">Trainer+Cyber (John)</button>
                        <button type="button" onclick="fillCreds('mary.cyber@gmail.com')" class="px-2 py-0.5 bg-emerald-100 text-emerald-900 rounded font-semibold text-[10px] hover:bg-emerald-200">Cyber Attendant (Mary)</button>
                        <button type="button" onclick="fillCreds('brian.kip@gmail.com')" class="px-2 py-0.5 bg-teal-100 text-teal-900 rounded font-semibold text-[10px] hover:bg-teal-200">Student (Brian)</button>
                    </div>
                </div>

                <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Email Address or Phone Number</label>
                        <input type="text" id="login_identifier" name="login_identifier" required value="{{ old('login_identifier') }}" placeholder="e.g. name@gmail.com or 07XXXXXXXX" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50 @error('login_identifier') border-rose-500 @enderror">
                        @error('login_identifier')
                            <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password</label>
                            <a href="#" onclick="alert('Please contact the campus administrator or trainer desk to reset your password.')" class="text-[11px] text-pearl-600 hover:underline">Forgot password?</a>
                        </div>
                        <input type="password" id="password" name="password" required value="password" placeholder="••••••••" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-sm focus:ring-2 focus:ring-pearl-500 focus:outline-none bg-slate-50">
                    </div>

                    <div class="flex items-center justify-between text-xs">
                        <label class="flex items-center space-x-2 text-slate-600 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-pearl-600 focus:ring-pearl-500">
                            <span>Remember me</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3.5 rounded-xl font-heading font-bold text-sm text-white bg-pearl-600 hover:bg-pearl-700 shadow-md shadow-pearl-700/20 transition transform active:scale-95 text-center">
                        Sign In to Portal &rarr;
                    </button>
                </form>

                <div class="pt-4 border-t border-slate-100 text-center text-xs text-slate-400">
                    New student? <a href="{{ route('register') }}" class="font-bold text-pearl-600 hover:underline">Apply / Enroll for a Course</a>
                </div>

            </div>

        </div>
    </section>

    <script>
        function fillCreds(ident) {
            document.getElementById('login_identifier').value = ident;
            document.getElementById('password').value = 'password';
        }
    </script>
@endsection
