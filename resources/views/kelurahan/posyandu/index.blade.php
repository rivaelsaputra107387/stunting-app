@extends('layouts.app')
@section('title', 'Data Posyandu')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0;">Data Posyandu</h1>
    <p style="color: #64748b; font-size: 0.875rem; margin: 0.25rem 0 0;">Daftar 21 posyandu di Kelurahan Sukahaji</p>
</div>

<div style="background: #fff; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #f1f5f9;">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Posyandu</th>
                    <th>RW</th>
                    <th>Jumlah Balita</th>
                    <th>Jumlah Pemeriksaan</th>
                    <th>Kasus Stunting</th>
                    <th>Prevalensi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($posyandus as $i => $pos)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight: 500;">{{ $pos->nama }}</td>
                    <td><span class="badge bg-blue-100 text-blue-800">{{ $pos->rw }}</span></td>
                    <td>{{ $pos->balitas_count }}</td>
                    <td>{{ $pos->pemeriksaans_count }}</td>
                    <td>
                        @if($pos->stunting_count > 0)
                            <span class="badge bg-rose-100 text-rose-800">{{ $pos->stunting_count }}</span>
                        @else
                            <span class="badge bg-emerald-100 text-emerald-800">0</span>
                        @endif
                    </td>
                    <td>
                        @php $persen = $pos->balitas_count > 0 ? round(($pos->stunting_count / $pos->balitas_count) * 100, 1) : 0; @endphp
                        {{ $persen }}%
                    </td>
                    <td>
                        <a href="{{ route('kelurahan.posyandu.show', $pos) }}" class="btn btn-info btn-sm">Detail</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
