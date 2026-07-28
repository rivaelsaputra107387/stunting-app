@extends('layouts.app')
@section('title', 'Data Pemeriksaan')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0;">Data Pemeriksaan</h1>
        <p style="color: #64748b; font-size: 0.875rem; margin: 0.25rem 0 0;">Riwayat pemeriksaan bulanan balita</p>
    </div>
    <a href="{{ route('posyandu.pemeriksaan.create') }}" class="btn btn-primary">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Tambah Pemeriksaan
    </a>
</div>

{{-- Filters --}}
<div style="background: #fff; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem; border: 1px solid #f1f5f9;">
    <form method="GET" style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama balita..." class="form-input" style="max-width: 200px;">
        <select name="bulan" class="form-input" style="max-width: 140px;">
            <option value="">Semua Bulan</option>
            @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                </option>
            @endfor
        </select>
        <select name="tahun" class="form-input" style="max-width: 120px;">
            <option value="">Semua Tahun</option>
            @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endfor
        </select>
        <button type="submit" class="btn btn-secondary">Filter</button>
        @if(request()->hasAny(['search', 'bulan', 'tahun']))
            <a href="{{ route('posyandu.pemeriksaan.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div style="background: #fff; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #f1f5f9;">
    @if($pemeriksaans->isEmpty())
        <p style="color: #94a3b8; font-size: 0.875rem; text-align: center; padding: 2rem 0;">Belum ada data pemeriksaan.</p>
    @else
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Balita</th>
                        <th>Umur</th>
                        <th>JK</th>
                        <th>TB (cm)</th>
                        <th>BB (kg)</th>
                        <th>Z-Score TB/U</th>
                        <th>Status Stunting</th>
                        <th>Z-Score BB/U</th>
                        <th>Status Gizi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pemeriksaans as $i => $p)
                    <tr>
                        <td>{{ $pemeriksaans->firstItem() + $i }}</td>
                        <td>{{ $p->tanggal_pemeriksaan->format('d/m/Y') }}</td>
                        <td style="font-weight: 500;">{{ $p->balita->nama ?? '-' }}</td>
                        <td>{{ $p->umur_bulan }} bln</td>
                        <td>{{ $p->balita->jenis_kelamin ?? '-' }}</td>
                        <td>{{ $p->tinggi_badan }}</td>
                        <td>{{ $p->berat_badan }}</td>
                        <td style="font-family: monospace;">{{ number_format($p->zscore_tb_u, 2) }}</td>
                        <td><span class="badge {{ $p->status_color }}">{{ $p->status_label }}</span></td>
                        <td style="font-family: monospace;">{{ $p->zscore_bb_u !== null ? number_format($p->zscore_bb_u, 2) : '-' }}</td>
                        <td><span class="badge {{ $p->bb_status_color }}">{{ $p->bb_status_label }}</span></td>
                        <td>
                            <div style="display: flex; gap: 0.375rem;">
                                <a href="{{ route('posyandu.pemeriksaan.edit', $p) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form method="POST" action="{{ route('posyandu.pemeriksaan.destroy', $p) }}" onsubmit="return confirm('Yakin hapus data pemeriksaan ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $pemeriksaans->links() }}
        </div>
    @endif
</div>
@endsection
