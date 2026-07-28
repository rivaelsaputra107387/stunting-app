@extends('layouts.app')
@section('title', 'Data Balita')

@section('content')
<div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0;">Data Balita</h1>
        <p style="color: #64748b; font-size: 0.875rem; margin: 0.25rem 0 0;">Kelola data balita di posyandu Anda</p>
    </div>
    <a href="{{ route('posyandu.balita.create') }}" class="btn btn-primary">
        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Tambah Balita
    </a>
</div>

{{-- Search --}}
<div style="background: #fff; border-radius: 0.75rem; padding: 1rem; margin-bottom: 1rem; border: 1px solid #f1f5f9;">
    <form method="GET" style="display: flex; gap: 0.75rem; align-items: center; flex-wrap: wrap;">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIK..." class="form-input" style="max-width: 300px;">
        <button type="submit" class="btn btn-secondary">Cari</button>
        @if(request('search'))
            <a href="{{ route('posyandu.balita.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div style="background: #fff; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #f1f5f9;">
    @if($balitas->isEmpty())
        <p style="color: #94a3b8; font-size: 0.875rem; text-align: center; padding: 2rem 0;">Belum ada data balita.</p>
    @else
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>JK</th>
                        <th>Tgl Lahir</th>
                        <th>Umur</th>
                        <th>Orang Tua</th>
                        <th>Status Terakhir</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($balitas as $i => $balita)
                    <tr>
                        <td>{{ $balitas->firstItem() + $i }}</td>
                        <td style="font-family: monospace; font-size: 0.8125rem;">{{ $balita->nik }}</td>
                        <td style="font-weight: 500;">{{ $balita->nama }}</td>
                        <td>
                            <span class="badge {{ $balita->jenis_kelamin === 'L' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                {{ $balita->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </span>
                        </td>
                        <td>{{ $balita->tanggal_lahir->format('d/m/Y') }}</td>
                        <td>{{ $balita->umur_format }}</td>
                        <td>{{ $balita->nama_orangtua }}</td>
                        <td>
                            @if($balita->latestPemeriksaan)
                                <span class="badge {{ $balita->latestPemeriksaan->status_color }}">{{ $balita->latestPemeriksaan->status_label }}</span>
                            @else
                                <span class="badge bg-gray-100 text-gray-500">Belum Diperiksa</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.375rem;">
                                <a href="{{ route('posyandu.balita.edit', $balita) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form method="POST" action="{{ route('posyandu.balita.destroy', $balita) }}" onsubmit="return confirm('Yakin hapus data balita ini?')">
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
            {{ $balitas->links() }}
        </div>
    @endif
</div>
@endsection
