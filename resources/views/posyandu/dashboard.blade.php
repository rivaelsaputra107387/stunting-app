@extends('layouts.app')
@section('title', 'Dashboard Posyandu')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0;">Dashboard</h1>
    <p style="color: #64748b; font-size: 0.875rem; margin: 0.25rem 0 0;">Selamat datang, {{ $user->name }} — {{ $user->posyandu->nama ?? '' }}</p>
</div>

{{-- Stats Cards --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="stat-card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="color: #64748b; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Total Balita</div>
                <div style="font-size: 1.75rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">{{ $totalBalita }}</div>
            </div>
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #dbeafe, #bfdbfe); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" fill="none" stroke="#2563eb" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="color: #64748b; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Pemeriksaan Bulan Ini</div>
                <div style="font-size: 1.75rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">{{ $pemeriksaanBulanIni }}</div>
            </div>
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="color: #64748b; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Normal</div>
                <div style="font-size: 1.75rem; font-weight: 700; color: #059669; margin-top: 0.25rem;">{{ $statusCounts['normal'] }}</div>
            </div>
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #d1fae5, #a7f3d0); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" fill="none" stroke="#059669" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
            </div>
        </div>
    </div>

    <div class="stat-card">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div style="color: #64748b; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Stunting & Risk</div>
                <div style="font-size: 1.75rem; font-weight: 700; color: #dc2626; margin-top: 0.25rem;">{{ $statusCounts['risk'] + $statusCounts['stunting'] }}</div>
            </div>
            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #fee2e2, #fecaca); border-radius: 0.75rem; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" fill="none" stroke="#dc2626" stroke-width="2" viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><path d="M12 9v4M12 17h.01"/></svg>
            </div>
        </div>
    </div>
</div>

{{-- Recent Pemeriksaan --}}
<div style="background: #fff; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #f1f5f9;">
    <h2 style="font-size: 1rem; font-weight: 600; color: #0f172a; margin: 0 0 1rem;">Pemeriksaan Terbaru</h2>

    @if($recentPemeriksaans->isEmpty())
        <p style="color: #94a3b8; font-size: 0.875rem; text-align: center; padding: 2rem 0;">Belum ada data pemeriksaan.</p>
    @else
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Nama Balita</th>
                        <th>Tanggal</th>
                        <th>TB (cm)</th>
                        <th>BB (kg)</th>
                        <th>Z-Score</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentPemeriksaans as $p)
                    <tr>
                        <td style="font-weight: 500;">{{ $p->balita->nama ?? '-' }}</td>
                        <td>{{ $p->tanggal_pemeriksaan->format('d/m/Y') }}</td>
                        <td>{{ $p->tinggi_badan }}</td>
                        <td>{{ $p->berat_badan }}</td>
                        <td>{{ $p->zscore_tb_u ?? '-' }}</td>
                        <td><span class="badge {{ $p->status_color }}">{{ $p->status_label }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
