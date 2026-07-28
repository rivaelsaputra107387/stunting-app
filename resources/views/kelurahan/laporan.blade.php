@extends('layouts.app')
@section('title', 'Laporan')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0;">Cetak Laporan</h1>
    <p style="color: #64748b; font-size: 0.875rem; margin: 0.25rem 0 0;">Unduh laporan stunting dalam format PDF atau Excel</p>
</div>

<div style="background: #fff; border-radius: 1rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #f1f5f9; max-width: 640px;">
    <h3 style="font-size: 1rem; font-weight: 600; color: #0f172a; margin: 0 0 1.5rem;">Filter Laporan</h3>

    <div style="display: grid; gap: 1.25rem; margin-bottom: 2rem;">
        <div>
            <label class="form-label">Posyandu</label>
            <select id="posyandu_id" class="form-input">
                <option value="">Semua Posyandu</option>
                @foreach($posyandus as $pos)
                    <option value="{{ $pos->id }}">{{ $pos->nama }} ({{ $pos->rw }})</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="form-label">Status Stunting</label>
            <select id="status_stunting" class="form-input">
                <option value="">Semua Status</option>
                <option value="Normal">Normal</option>
                <option value="Risk of Stunting">Risk of Stunting</option>
                <option value="Stunting">Stunting</option>
            </select>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <div>
                <label class="form-label">Bulan</label>
                <select id="bulan" class="form-input">
                    <option value="">Semua</option>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ now()->month == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="form-label">Tahun</label>
                <select id="tahun" class="form-input">
                    <option value="">Semua</option>
                    @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                        <option value="{{ $y }}" {{ date('Y') == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <button onclick="downloadReport('pdf')" class="btn" style="background: #dc2626; color: #fff; padding: 0.75rem 1.5rem;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M12 18v-6M9 15l3 3 3-3"/></svg>
            Download PDF
        </button>
        <button onclick="downloadReport('excel')" class="btn" style="background: #16a34a; color: #fff; padding: 0.75rem 1.5rem;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6M8 13h2M8 17h2M14 13h2M14 17h2"/></svg>
            Download Excel
        </button>
    </div>
</div>

{{-- Info --}}
<div style="margin-top: 1.5rem; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.75rem; padding: 1rem 1.25rem; max-width: 640px;">
    <p style="color: #166534; font-size: 0.8125rem; margin: 0;">
        <strong>💡 Tips:</strong> Laporan PDF berisi kop surat, tabel data, dan ringkasan statistik. Laporan Excel berisi data lengkap dalam format spreadsheet yang bisa diolah lebih lanjut.
    </p>
</div>
@endsection

@push('scripts')
<script>
function downloadReport(format) {
    const posyandu_id = document.getElementById('posyandu_id').value;
    const status_stunting = document.getElementById('status_stunting').value;
    const bulan = document.getElementById('bulan').value;
    const tahun = document.getElementById('tahun').value;

    const params = new URLSearchParams();
    if (posyandu_id) params.append('posyandu_id', posyandu_id);
    if (status_stunting) params.append('status_stunting', status_stunting);
    if (bulan) params.append('bulan', bulan);
    if (tahun) params.append('tahun', tahun);

    const baseUrl = format === 'pdf'
        ? '{{ route("kelurahan.laporan.pdf") }}'
        : '{{ route("kelurahan.laporan.excel") }}';

    window.location.href = baseUrl + '?' + params.toString();
}
</script>
@endpush
