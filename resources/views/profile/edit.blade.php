@extends('layouts.app')
@section('title', 'Edit Profil')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0;">Edit Profil</h1>
    <p style="color: #64748b; font-size: 0.875rem; margin: 0.25rem 0 0;">Perbarui nama lengkap, alamat email, dan kata sandi akun Anda secara mandiri.</p>
</div>

<div style="background: #ffffff; border-radius: 1rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #f1f5f9; max-width: 650px;">
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        {{-- Row 1: Name --}}
        <div style="margin-bottom: 1.5rem;">
            <label for="name" class="form-label">Nama Lengkap</label>
            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                class="form-input @error('name') is-invalid @enderror" placeholder="Masukkan nama lengkap">
            @error('name')
                <span style="color: #dc2626; font-size: 0.75rem; margin-top: 0.375rem; display: block; font-weight: 500;">
                    {{ $message }}
                </span>
            @enderror
        </div>

        {{-- Row 2: Email --}}
        <div style="margin-bottom: 1.5rem;">
            <label for="email" class="form-label">Alamat Email (Gmail / Test)</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                class="form-input @error('email') is-invalid @enderror" placeholder="nama@email.com">
            @error('email')
                <span style="color: #dc2626; font-size: 0.75rem; margin-top: 0.375rem; display: block; font-weight: 500;">
                    {{ $message }}
                </span>
            @enderror
        </div>

        {{-- Divider --}}
        <hr style="border: 0; border-top: 1px solid #e2e8f0; margin: 2rem 0;">

        {{-- Info Box --}}
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1.5rem; display: flex; gap: 0.75rem; align-items: flex-start;">
            <svg width="18" height="18" fill="none" stroke="#64748b" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink: 0; mt-0.5;"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p style="margin: 0; font-size: 0.8125rem; color: #475569; line-height: 1.4;">
                <strong>Catatan keamanan:</strong> Kosongkan kolom kata sandi di bawah jika Anda tidak ingin mengubah kata sandi Anda saat ini.
            </p>
        </div>

        {{-- Row 3: New Password --}}
        <div style="margin-bottom: 1.5rem;">
            <label for="password" class="form-label">Kata Sandi Baru</label>
            <input type="password" name="password" id="password"
                class="form-input @error('password') is-invalid @enderror" placeholder="Min. 8 karakter (opsional)">
            @error('password')
                <span style="color: #dc2626; font-size: 0.75rem; margin-top: 0.375rem; display: block; font-weight: 500;">
                    {{ $message }}
                </span>
            @enderror
        </div>

        {{-- Row 4: Confirm New Password --}}
        <div style="margin-bottom: 2rem;">
            <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi Baru</label>
            <input type="password" name="password_confirmation" id="password_confirmation"
                class="form-input" placeholder="Ketik ulang kata sandi baru">
        </div>

        {{-- Form Actions --}}
        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <button type="submit" class="btn btn-primary" style="padding: 0.625rem 1.5rem;">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                Simpan Perubahan
            </button>
            <a href="{{ auth()->user()->isKelurahan() ? route('kelurahan.dashboard') : route('posyandu.dashboard') }}" class="btn btn-secondary" style="padding: 0.625rem 1.5rem;">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
