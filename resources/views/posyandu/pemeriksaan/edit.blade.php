@extends('layouts.app')
@section('title', 'Edit Pemeriksaan')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('posyandu.pemeriksaan.index') }}" style="color: #64748b; text-decoration: none; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.25rem;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Kembali ke Data Pemeriksaan
    </a>
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0.5rem 0 0;">Edit Pemeriksaan</h1>
</div>

<div style="background: #fff; border-radius: 1rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #f1f5f9; max-width: 640px;">
    <form method="POST" action="{{ route('posyandu.pemeriksaan.update', $pemeriksaan) }}">
        @csrf @method('PUT')
        @include('posyandu.pemeriksaan._form', ['pemeriksaan' => $pemeriksaan])
        <div style="display: flex; gap: 0.75rem; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-primary">Perbarui Pemeriksaan</button>
            <a href="{{ route('posyandu.pemeriksaan.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
