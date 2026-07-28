<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Stunting') — Kelurahan Sukahaji</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Custom styles */
        :root {
            --sidebar-width: 260px;
        }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; }
        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            transition: transform 0.3s ease;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.625rem 1rem;
            border-radius: 0.5rem;
            color: #94a3b8;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 0.875rem;
        }
        .nav-link:hover, .nav-link.active {
            background: rgba(255,255,255,0.08);
            color: #f1f5f9;
        }
        .nav-link.active {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #fff;
            font-weight: 600;
        }
        .stat-card {
            background: #fff;
            border-radius: 1rem;
            padding: 1.25rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            border: 1px solid #f1f5f9;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .table-container {
            overflow-x: auto;
            border-radius: 0.75rem;
            border: 1px solid #e2e8f0;
        }
        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-container th {
            background: #f8fafc;
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }
        .table-container td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.875rem;
            color: #334155;
        }
        .table-container tr:hover td {
            background: #f8fafc;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary { background: #10b981; color: #fff; }
        .btn-primary:hover { background: #059669; }
        .btn-secondary { background: #f1f5f9; color: #475569; }
        .btn-secondary:hover { background: #e2e8f0; }
        .btn-danger { background: #fee2e2; color: #dc2626; }
        .btn-danger:hover { background: #fecaca; }
        .btn-warning { background: #fef3c7; color: #d97706; }
        .btn-warning:hover { background: #fde68a; }
        .btn-info { background: #dbeafe; color: #2563eb; }
        .btn-info:hover { background: #bfdbfe; }
        .btn-sm { padding: 0.375rem 0.75rem; font-size: 0.8125rem; }
        .form-input {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .form-input:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }
        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.375rem;
        }
        .alert {
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); position: fixed; z-index: 50; }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body style="background: #f8fafc; margin: 0;">
    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar" style="background: linear-gradient(135deg, #0f172a 0%, #020617 50%, #022c22 100%); position: fixed; top: 0; bottom: 0; left: 0; overflow-y: auto; padding: 1.5rem 1rem; z-index: 40; display: flex; flex-direction: column; justify-content: space-between; box-sizing: border-box;">
        <div style="display: flex; flex-direction: column;">
            {{-- Logo --}}
            <div style="padding: 0 0.5rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 1.5rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <img src="{{ asset('logo.png') }}" style="width: 40px; height: 40px; object-fit: contain;" alt="Logo eSStunting">
                    <div>
                        <div style="color: #f1f5f9; font-weight: 700; font-size: 0.9375rem;">eSstunting</div>
                        <div style="color: #64748b; font-size: 0.6875rem;">Kelurahan Sukahaji, Bandung</div>
                    </div>
                </div>
            </div>

            {{-- Navigation --}}
            <nav style="display: flex; flex-direction: column; gap: 0.25rem;">
                @if(auth()->user()->isKelurahan())
                    <div style="color: #475569; font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; padding: 0.5rem 1rem;">Menu Kelurahan</div>
                    <a href="{{ route('kelurahan.dashboard') }}" class="nav-link {{ request()->routeIs('kelurahan.dashboard') ? 'active' : '' }}">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('kelurahan.posyandu.index') }}" class="nav-link {{ request()->routeIs('kelurahan.posyandu.*') ? 'active' : '' }}">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
                        Data Posyandu
                    </a>
                    <a href="{{ route('kelurahan.klasifikasi.index') }}" class="nav-link {{ request()->routeIs('kelurahan.klasifikasi.*') ? 'active' : '' }}">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Klasifikasi
                    </a>
                    <a href="{{ route('kelurahan.laporan.index') }}" class="nav-link {{ request()->routeIs('kelurahan.laporan.*') ? 'active' : '' }}">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M16 13H8M16 17H8M10 9H8"/></svg>
                        Laporan
                    </a>
                @endif

                @if(auth()->user()->isPosyandu())
                    <div style="color: #475569; font-size: 0.6875rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; padding: 0.5rem 1rem;">Menu Posyandu</div>
                    <a href="{{ route('posyandu.dashboard') }}" class="nav-link {{ request()->routeIs('posyandu.dashboard') ? 'active' : '' }}">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('posyandu.balita.index') }}" class="nav-link {{ request()->routeIs('posyandu.balita.*') ? 'active' : '' }}">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        Data Balita
                    </a>
                    <a href="{{ route('posyandu.pemeriksaan.index') }}" class="nav-link {{ request()->routeIs('posyandu.pemeriksaan.*') ? 'active' : '' }}">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        Pemeriksaan
                    </a>
                @endif
            </nav>
        </div>

        {{-- Sidebar Footer Watermark --}}
        <div style="padding: 1.25rem 0.5rem 0.25rem; border-top: 1px solid rgba(255,255,255,0.06); margin-top: 2rem; text-align: center;">
            <p style="color: rgba(255,255,255,0.22); font-size: 0.6875rem; margin: 0; font-family: monospace; letter-spacing: 0.05em; line-height: 1.4;">
                &copy; 2026 eSStunting<br>
                <span style="color: rgba(255,255,255,0.12); font-size: 0.625rem; font-family: sans-serif; letter-spacing: 0;">Kelurahan Sukahaji</span>
            </p>
        </div>
    </aside>e>

    {{-- Main Content --}}
    <main class="main-content" style="padding: 0; background: #f8fafc; min-height: 100vh; display: flex; flex-direction: column;">
        {{-- Top Navbar --}}
        <header style="background: #ffffff; border-bottom: 1px solid #e2e8f0; padding: 0.75rem 2rem; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 30; box-shadow: 0 1px 2px rgba(0,0,0,0.03); min-height: 60px;">
            {{-- Left Side: Mobile toggle and title info --}}
            <div style="display: flex; align-items: center; gap: 1rem;">
                <button onclick="document.getElementById('sidebar').classList.toggle('open')" style="display: none; background: #1e293b; color: #fff; border: none; padding: 0.5rem; border-radius: 0.5rem; cursor: pointer;" id="sidebarToggle">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                </button>
                <div style="font-size: 0.8125rem; color: #64748b; font-weight: 500; display: flex; align-items: center; gap: 0.5rem;">
                    <span>Sistem eSStunting Kelurahan Sukahaji</span>
                    <span style="color: #cbd5e1;">&bull;</span>
                    <span style="color: #1e293b; font-weight: 600;">{{ auth()->user()->isKelurahan() ? 'Kelurahan (Admin)' : ('Posyandu: ' . (auth()->user()->posyandu->nama ?? '-')) }}</span>
                </div>
            </div>

            {{-- Right Side: Profile Dropdown --}}
            <div style="position: relative;">
                <button id="profileDropdownBtn" onclick="toggleProfileDropdown()" style="background: none; border: none; padding: 0; margin: 0; cursor: pointer; display: flex; align-items: center; gap: 0.75rem; text-align: left; outline: none; user-select: none;">
                    <div style="width: 34px; height: 34px; background: linear-gradient(135deg, #10b981, #059669); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #ffffff; font-weight: 600; font-size: 0.875rem; box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div style="display: flex; flex-direction: column;">
                        <span style="color: #1e293b; font-size: 0.8125rem; font-weight: 600; max-width: 140px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->name }}</span>
                        <span style="color: #64748b; font-size: 0.6875rem; font-weight: 500;">{{ ucfirst(auth()->user()->role) }}</span>
                    </div>
                    <svg width="12" height="12" fill="none" stroke="#64748b" stroke-width="2.5" viewBox="0 0 24 24" style="transition: transform 0.2s;" id="profileDropdownArrow"><path d="M19 9l-7 7-7-7"/></svg>
                </button>

                {{-- Dropdown Card --}}
                <div id="profileDropdownMenu" style="display: none; position: absolute; right: 0; top: 100%; margin-top: 0.5rem; width: 220px; background: #ffffff; border-radius: 0.75rem; border: 1px solid #e2e8f0; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05); flex-direction: column; overflow: hidden; z-index: 50;">
                    {{-- User Details Header --}}
                    <div style="padding: 1rem; border-bottom: 1px solid #f1f5f9; background: #f8fafc;">
                        <div style="font-weight: 700; color: #0f172a; font-size: 0.8125rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->name }}</div>
                        <div style="font-size: 0.6875rem; color: #64748b; margin-top: 0.125rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ auth()->user()->email }}</div>
                    </div>
                    
                    {{-- Menu Actions --}}
                    <a href="{{ route('profile.edit') }}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; color: #334155; font-size: 0.8125rem; text-decoration: none; transition: background 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='none'">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span>Edit Profil</span>
                    </a>
                    <button type="button" onclick="confirmLogout(); toggleProfileDropdown();" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; width: 100%; background: none; border: none; color: #dc2626; font-size: 0.8125rem; text-align: left; cursor: pointer; border-top: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.background='#fff5f5'" onmouseout="this.style.background='none'">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Logout</span>
                    </button>
                </div>
            </div>
        </header>

        {{-- Page Content Wrapper --}}
        <div style="padding: 1.5rem 2rem; flex: 1;">

        {{-- Flash messages --}}
        @if(session('success'))
            <div class="alert alert-success">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-error">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
        </div>
    </main>

    <!-- Logout Confirmation Modal -->
    <div id="logoutModal" onclick="if(event.target === this) closeLogoutModal();" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1rem; transition: all 0.2s ease-in-out;">
        <div style="background: #ffffff; width: 100%; max-width: 400px; border-radius: 1rem; padding: 1.75rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); transform: scale(0.95); transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);">
            <div style="display: flex; align-items: flex-start; gap: 1rem; margin-bottom: 1.5rem;">
                <div style="width: 44px; height: 44px; background: #fee2e2; color: #dc2626; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.1);">
                    <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div style="flex: 1;">
                    <h3 style="font-weight: 700; color: #0f172a; margin: 0 0 0.5rem 0; font-size: 1.125rem;">Konfirmasi Keluar</h3>
                    <p style="color: #64748b; font-size: 0.875rem; margin: 0; line-height: 1.5;">Apakah Anda yakin ingin keluar dari akun Anda? Sesi Anda saat ini akan diakhiri.</p>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                <button onclick="closeLogoutModal()" style="background: #f8fafc; border: 1px solid #e2e8f0; color: #475569; padding: 0.625rem 1.25rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: background 0.2s, border-color 0.2s;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                    Batal
                </button>
                <button onclick="submitLogoutForm()" style="background: #dc2626; border: none; color: #ffffff; padding: 0.625rem 1.25rem; border-radius: 0.75rem; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: background 0.2s, transform 0.1s;" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'" onmousedown="this.style.transform='scale(0.98)'" onmouseup="this.style.transform='scale(1)'">
                    Ya, Keluar
                </button>
            </div>
        </div>
    </div>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <script>
        // Mobile sidebar toggle
        if (document.getElementById('sidebarToggle')) {
            if (window.innerWidth <= 768) {
                document.getElementById('sidebarToggle').style.display = 'block';
            }
            window.addEventListener('resize', function() {
                document.getElementById('sidebarToggle').style.display = window.innerWidth <= 768 ? 'block' : 'none';
            });
        }

        // Logout Confirmation Modal Actions
        function confirmLogout() {
            const modal = document.getElementById('logoutModal');
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.firstElementChild.style.transform = 'scale(1)';
            }, 50);
        }

        function closeLogoutModal() {
            const modal = document.getElementById('logoutModal');
            modal.firstElementChild.style.transform = 'scale(0.95)';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 150);
        }

        function submitLogoutForm() {
            document.getElementById('logout-form').submit();
        }

        // Profile Dropdown Actions
        function toggleProfileDropdown() {
            const menu = document.getElementById('profileDropdownMenu');
            const arrow = document.getElementById('profileDropdownArrow');
            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'flex';
                arrow.style.transform = 'rotate(180deg)';
            } else {
                menu.style.display = 'none';
                arrow.style.transform = 'rotate(0deg)';
            }
        }

        // Close dropdown when clicking outside
        window.addEventListener('click', function(e) {
            const dropdown = document.getElementById('profileDropdownMenu');
            const btn = document.getElementById('profileDropdownBtn');
            const arrow = document.getElementById('profileDropdownArrow');
            if (dropdown && btn && !btn.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.style.display = 'none';
                if (arrow) arrow.style.transform = 'rotate(0deg)';
            }
        });
    </script>
    @stack('scripts')
</body>
</html>
