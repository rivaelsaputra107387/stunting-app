@extends('layouts.guest')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 min-h-screen w-full overflow-x-hidden">
    {{-- Left Side: Branding and Features Showcase --}}
    <div class="hidden lg:flex lg:col-span-7 relative bg-gradient-to-br from-slate-900 via-slate-950 to-emerald-950 flex-col justify-between p-12 lg:p-16 overflow-hidden text-white">
        {{-- Background Grid & Orbs --}}
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b_1px,transparent_1px),linear-gradient(to_bottom,#1e293b_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-35"></div>
        <div class="absolute -top-40 -right-40 w-96 h-96 bg-emerald-500 rounded-full mix-blend-screen filter blur-3xl opacity-15"></div>
        <div class="absolute -bottom-20 -left-20 w-80 h-80 bg-teal-500 rounded-full mix-blend-screen filter blur-3xl opacity-10"></div>

        {{-- Logo Header --}}
        <div class="relative z-10 flex items-center gap-3">
            <img src="{{ asset('logo.png') }}" class="w-10 h-10 object-contain" alt="Logo eSStunting">
            <div>
                <span class="font-bold text-lg tracking-wide bg-gradient-to-r from-white to-slate-200 bg-clip-text text-transparent">eSStunting</span>
                <span class="text-[10px] block text-emerald-400 font-semibold tracking-wider uppercase -mt-1">Kelurahan Sukahaji</span>
            </div>
        </div>

        {{-- Middle Feature Showcase --}}
        <div class="relative z-10 my-auto py-12 max-w-xl">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-semibold tracking-wide uppercase mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Sistem Klasifikasi Status Stunting</span>
            </div>
            
            <h1 class="text-3xl lg:text-4xl font-extrabold tracking-tight leading-tight text-white mb-4">
                Klasifikasi Status Stunting Balita &amp; Pada Kelurahan Sukahaji
            </h1>
            <p class="text-slate-400 text-sm lg:text-base leading-relaxed mb-8">
                Membantu kader Posyandu dan perangkat Kelurahan Sukahaji dalam memantau, mendeteksi secara dini, serta mengekspor data klasifikasi stunting sesuai standar antropometri WHO.
            </p>
        </div>

        {{-- Left Footer --}}
        <div class="relative z-10 flex items-center justify-between text-xs text-slate-500 border-t border-slate-800/60 pt-6">
            <span>Kelurahan Sukahaji (Kec. Babakan Ciparay)</span>
            <span>Standar WHO &amp; Kemenkes RI</span>
        </div>
    </div>

    {{-- Right Side: Login Form --}}
    <div class="flex lg:col-span-5 flex-col justify-between px-6 py-10 sm:px-12 lg:px-16 bg-white relative z-10 min-h-screen">
        {{-- Mobile Logo --}}
        <div class="flex lg:hidden items-center gap-2 mb-8">
            <img src="{{ asset('logo.png') }}" class="w-9 h-9 object-contain" alt="Logo eSStunting">
            <div>
                <span class="font-bold text-sm tracking-wide text-slate-800 block">eSStunting</span>
                <span class="text-[9px] block text-slate-500 -mt-1">Kelurahan Sukahaji</span>
            </div>
        </div>

        <div class="hidden lg:block"></div> {{-- Spacer --}}

        {{-- Form Container --}}
        <div class="w-full max-w-md mx-auto py-8">
            <div class="mb-8">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900">Selamat Datang 👋</h2>
                <p class="text-sm text-slate-500 mt-1.5">
                    Silakan masukkan akun Anda untuk mengakses sistem eSStunting.
                </p>
            </div>

            {{-- Error Alerts --}}
            @if($errors->any())
                <div class="flex items-start gap-3 p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-800 text-xs mb-6">
                    <svg class="w-5 h-5 text-rose-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01"/>
                    </svg>
                    <div>
                        <span class="font-bold block">Gagal masuk sistem</span>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ url('/login') }}" class="space-y-5">
                @csrf

                {{-- Email Input --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"/>
                            </svg>
                        </div>
                        <input type="email" name="email" value="{{ old('email') }}" required autofocus
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:bg-white rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-sm"
                            placeholder="contoh@email.com">
                    </div>
                </div>

                {{-- Password Input --}}
                <div class="space-y-1.5">
                    <label class="block text-xs font-semibold text-slate-700 tracking-wide">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" name="password" required
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:border-emerald-500 focus:bg-white rounded-xl text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all duration-200 text-sm"
                            placeholder="••••••••">
                    </div>
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="remember" id="remember" 
                            class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4 cursor-pointer accent-emerald-600">
                        <span class="text-xs text-slate-500 group-hover:text-slate-700 transition-colors select-none">Ingat saya di perangkat ini</span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <button type="submit" 
                    class="w-full mt-2 py-3 px-4 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl font-semibold text-sm shadow-md shadow-emerald-900/10 hover:shadow-lg hover:shadow-emerald-900/25 active:scale-[0.98] transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                    <span>Masuk ke Dashboard</span>
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </button>
            </form>
        </div>

        {{-- Footer --}}
        <div class="w-full max-w-md mx-auto text-center border-t border-slate-100 pt-6">
            <p class="text-xs text-slate-400 leading-relaxed">
                &copy; {{ date('Y') }} eSStunting Kelurahan Sukahaji.
            </p>
        </div>
    </div>
</div>
@endsection
