@extends('layouts.app')
@section('title', 'Detail Posyandu — ' . $posyandu->nama)

@section('content')
<div style="margin-bottom: 1.5rem;">
    <a href="{{ route('kelurahan.posyandu.index') }}" style="color: #64748b; text-decoration: none; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.25rem;">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Kembali ke Data Posyandu
    </a>
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0.5rem 0 0;">{{ $posyandu->nama }}</h1>
    <p style="color: #64748b; font-size: 0.875rem; margin: 0.25rem 0 0;">{{ $posyandu->rw }} — {{ $posyandu->alamat ?? 'Kelurahan Sukahaji, Bandung' }}</p>
</div>

{{-- Stats --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="stat-card" style="text-align: center;">
        <div style="font-size: 1.5rem; font-weight: 700; color: #2563eb;">{{ $posyandu->balitas_count }}</div>
        <div style="color: #64748b; font-size: 0.8125rem;">Total Balita</div>
    </div>
    <div class="stat-card" style="text-align: center;">
        <div style="font-size: 1.5rem; font-weight: 700; color: #10b981;">{{ $statusCounts['normal'] }}</div>
        <div style="color: #64748b; font-size: 0.8125rem;">Normal</div>
    </div>
    <div class="stat-card" style="text-align: center;">
        <div style="font-size: 1.5rem; font-weight: 700; color: #f59e0b;">{{ $statusCounts['risk'] }}</div>
        <div style="color: #64748b; font-size: 0.8125rem;">Risk of Stunting</div>
    </div>
    <div class="stat-card" style="text-align: center;">
        <div style="font-size: 1.5rem; font-weight: 700; color: #ef4444;">{{ $statusCounts['stunting'] }}</div>
        <div style="color: #64748b; font-size: 0.8125rem;">Stunting</div>
    </div>
</div>

{{-- Balita List --}}
<div style="background: #fff; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #f1f5f9; margin-bottom: 2rem;">
    <h3 style="font-size: 1rem; font-weight: 600; color: #0f172a; margin: 0 0 1rem;">Daftar Balita</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIK</th>
                    <th>JK</th>
                    <th>Umur</th>
                    <th>Status Stunting</th>
                    <th>Status Gizi BB</th>
                </tr>
            </thead>
            <tbody>
                @forelse($balitas as $i => $balita)
                <tr>
                    <td>{{ $balitas->firstItem() + $i }}</td>
                    <td style="font-weight: 500;">{{ $balita->nama }}</td>
                    <td style="font-family: monospace; font-size: 0.8125rem;">{{ $balita->nik }}</td>
                    <td>{{ $balita->jenis_kelamin }}</td>
                    <td>{{ $balita->umur_format }}</td>
                    <td>
                        @if($balita->latestPemeriksaan)
                            <span class="badge {{ $balita->latestPemeriksaan->status_color }}">{{ $balita->latestPemeriksaan->status_label }}</span>
                        @else
                            <span class="badge bg-gray-100 text-gray-500">Belum Diperiksa</span>
                        @endif
                    </td>
                    <td>
                        @if($balita->latestPemeriksaan && $balita->latestPemeriksaan->status_berat_badan)
                            <span class="badge {{ $balita->latestPemeriksaan->bb_status_color }}">{{ $balita->latestPemeriksaan->bb_status_label }}</span>
                        @else
                            <span class="badge bg-gray-100 text-gray-500">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align: center; color: #94a3b8;">Belum ada data balita.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top: 1rem;">{{ $balitas->links() }}</div>
</div>

{{-- Recent Pemeriksaan --}}
<div style="background: #fff; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #f1f5f9;">
    <h3 style="font-size: 1rem; font-weight: 600; color: #0f172a; margin: 0 0 1rem;">Pemeriksaan Terbaru</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nama Balita</th>
                    <th>TB (cm)</th>
                    <th>BB (kg)</th>
                    <th>Z-Score TB/U</th>
                    <th>Status Stunting</th>
                    <th>Z-Score BB/U</th>
                    <th>Status Gizi BB</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentPemeriksaans as $p)
                <tr>
                    <td>{{ $p->tanggal_pemeriksaan->format('d/m/Y') }}</td>
                    <td style="font-weight: 500;">{{ $p->balita->nama ?? '-' }}</td>
                    <td>{{ $p->tinggi_badan }}</td>
                    <td>{{ $p->berat_badan }}</td>
                    <td style="font-family: monospace;">{{ number_format($p->zscore_tb_u, 2) }}</td>
                    <td><span class="badge {{ $p->status_color }}">{{ $p->status_label }}</span></td>
                    <td style="font-family: monospace;">{{ $p->zscore_bb_u !== null ? number_format($p->zscore_bb_u, 2) : '-' }}</td>
                    <td><span class="badge {{ $p->bb_status_color }}">{{ $p->bb_status_label }}</span></td>
                </tr>
                @empty
                <tr><td colspan="8" style="text-align: center; color: #94a3b8;">Belum ada pemeriksaan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
