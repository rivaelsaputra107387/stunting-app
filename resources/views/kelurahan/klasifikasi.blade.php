@extends('layouts.app')
@section('title', 'Klasifikasi Stunting & Confusion Matrix')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0;">Klasifikasi Stunting & Evaluasi Decision Tree</h1>
    <p style="color: #64748b; font-size: 0.875rem; margin: 0.25rem 0 0;">Visualisasi hasil klasifikasi Z-Score, Prediksi Decision Tree, dan Perhitungan Confusion Matrix</p>
</div>

{{-- Filter Form --}}
<div style="background: #fff; border-radius: 0.75rem; padding: 1.25rem; margin-bottom: 1.5rem; border: 1px solid #f1f5f9;">
    <form method="GET" style="display: flex; gap: 0.75rem; align-items: flex-end; flex-wrap: wrap;">
        <div>
            <label class="form-label">Posyandu</label>
            <select name="posyandu_id" class="form-input" style="min-width: 180px;">
                <option value="">Semua Posyandu</option>
                @foreach($posyandus as $pos)
                    <option value="{{ $pos->id }}" {{ request('posyandu_id') == $pos->id ? 'selected' : '' }}>{{ $pos->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="form-label">Status Stunting</label>
            <select name="status_stunting" class="form-input" style="min-width: 160px;">
                <option value="">Semua Status</option>
                <option value="Normal" {{ request('status_stunting') == 'Normal' ? 'selected' : '' }}>Normal</option>
                <option value="Risk of Stunting" {{ request('status_stunting') == 'Risk of Stunting' ? 'selected' : '' }}>Risk of Stunting</option>
                <option value="Stunting" {{ request('status_stunting') == 'Stunting' ? 'selected' : '' }}>Stunting</option>
            </select>
        </div>
        <div>
            <label class="form-label">Bulan</label>
            <select name="bulan" class="form-input" style="min-width: 130px;">
                <option value="">Semua</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('bulan') == $m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                @endfor
            </select>
        </div>
        <div>
            <label class="form-label">Tahun</label>
            <select name="tahun" class="form-input" style="min-width: 100px;">
                <option value="">Semua</option>
                @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                    <option value="{{ $y }}" {{ request('tahun') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter Data</button>

        @if(request('posyandu_id') || request('status_stunting') || request('bulan') || request('tahun'))
            <a href="{{ route('kelurahan.klasifikasi.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>
</div>

{{-- Action Button & Metric Summary Cards --}}
@if(isset($evaluation) && $evaluation['total_data'] > 0)
<div style="margin-bottom: 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
        <h2 style="font-size: 1.125rem; font-weight: 600; color: #0f172a; margin: 0;">Evaluasi Performa Model (Confusion Matrix)</h2>
        
        <form method="POST" action="{{ route('kelurahan.klasifikasi.proses', request()->query()) }}" onsubmit="return confirm('Yakin ingin mengklasifikasi ulang data pemeriksaan?')">
            @csrf
            <button type="submit" class="btn btn-warning">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 4v6h6M23 20v-6h-6"/><path d="M20.49 9A9 9 0 005.64 5.64L1 10m22 4l-4.64 4.36A9 9 0 013.51 15"/></svg>
                Proses Ulang Klasifikasi (Decision Tree)
            </button>
        </form>
    </div>

    {{-- Score Cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
        <div style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); color: white; padding: 1.25rem; border-radius: 1rem; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);">
            <div style="font-size: 0.8125rem; opacity: 0.9; text-transform: uppercase; font-weight: 600; letter-spacing: 0.05em;">Akurasi (Accuracy)</div>
            <div style="font-size: 2rem; font-weight: 800; margin-top: 0.25rem;">{{ $evaluation['accuracy'] }}%</div>
            <div style="font-size: 0.75rem; opacity: 0.8; margin-top: 0.25rem;">{{ $evaluation['correct_predictions'] }} tepat dari {{ $evaluation['total_data'] }} data</div>
        </div>

        <div style="background: #fff; border: 1px solid #e2e8f0; padding: 1.25rem; border-radius: 1rem;">
            <div style="font-size: 0.8125rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Macro Precision</div>
            <div style="font-size: 1.75rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">{{ $evaluation['macro_precision'] }}%</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Presisi rata-rata seluruh kelas</div>
        </div>

        <div style="background: #fff; border: 1px solid #e2e8f0; padding: 1.25rem; border-radius: 1rem;">
            <div style="font-size: 0.8125rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Macro Recall</div>
            <div style="font-size: 1.75rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">{{ $evaluation['macro_recall'] }}%</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Sensitivitas deteksi rata-rata</div>
        </div>

        <div style="background: #fff; border: 1px solid #e2e8f0; padding: 1.25rem; border-radius: 1rem;">
            <div style="font-size: 0.8125rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">Macro F1-Score</div>
            <div style="font-size: 1.75rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">{{ $evaluation['macro_f1'] }}%</div>
            <div style="font-size: 0.75rem; color: #94a3b8; margin-top: 0.25rem;">Keseimbangan Precision & Recall</div>
        </div>
    </div>

    {{-- Confusion Matrix Grid & Detailed Metrics --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
        {{-- Matrix Table --}}
        <div style="background: #fff; border-radius: 1rem; padding: 1.25rem; border: 1px solid #e2e8f0;">
            <h3 style="font-size: 0.9375rem; font-weight: 600; color: #0f172a; margin-top: 0; margin-bottom: 1rem;">Matriks Kontingensi (Aktual vs Prediksi)</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; text-align: center; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                            <th style="padding: 0.5rem; text-align: left;">Aktual \ Prediksi</th>
                            @foreach($evaluation['classes'] as $cls)
                                <th style="padding: 0.5rem;">{{ $cls }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluation['classes'] as $actual)
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td style="padding: 0.625rem; font-weight: 600; text-align: left; background: #f8fafc;">{{ $actual }}</td>
                                @foreach($evaluation['classes'] as $predicted)
                                    @php $val = $evaluation['matrix'][$actual][$predicted]; @endphp
                                    <td style="padding: 0.625rem; font-weight: 700; {{ $actual === $predicted ? 'background: #f0fdf4; color: #166534;' : ($val > 0 ? 'background: #fef2f2; color: #991b1b;' : 'color: #94a3b8;') }}">
                                        {{ $val }}
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <p style="font-size: 0.75rem; color: #94a3b8; margin: 0.75rem 0 0;">
                <span style="display: inline-block; width: 10px; height: 10px; background: #f0fdf4; border: 1px solid #bbf7d0; margin-right: 4px; border-radius: 2px;"></span> Hijau: Prediksi Benar (Diagonal)
                <span style="display: inline-block; width: 10px; height: 10px; background: #fef2f2; border: 1px solid #fecaca; margin-left: 12px; margin-right: 4px; border-radius: 2px;"></span> Merah: Prediksi Meleset
            </p>
        </div>

        {{-- Metrics Per Class --}}
        <div style="background: #fff; border-radius: 1rem; padding: 1.25rem; border: 1px solid #e2e8f0;">
            <h3 style="font-size: 0.9375rem; font-weight: 600; color: #0f172a; margin-top: 0; margin-bottom: 1rem;">Metrik Per Kategori</h3>
            <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                        <th style="padding: 0.5rem;">Kelas</th>
                        <th style="padding: 0.5rem; text-align: center;">Precision</th>
                        <th style="padding: 0.5rem; text-align: center;">Recall</th>
                        <th style="padding: 0.5rem; text-align: center;">F1-Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($evaluation['metrics_per_class'] as $clsName => $m)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 0.5rem; font-weight: 600;">{{ $clsName }}</td>
                            <td style="padding: 0.5rem; text-align: center; font-family: monospace;">{{ $m['precision'] }}%</td>
                            <td style="padding: 0.5rem; text-align: center; font-family: monospace;">{{ $m['recall'] }}%</td>
                            <td style="padding: 0.5rem; text-align: center; font-family: monospace; font-weight: 700; color: #0284c7;">{{ $m['f1_score'] }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

{{-- Results Table --}}
<div style="background: #fff; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #f1f5f9;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h3 style="font-size: 1rem; font-weight: 600; color: #0f172a; margin: 0;">
            Data Pemeriksaan & Prediksi ({{ $pemeriksaans->total() }} data)
        </h3>
    </div>

    @if($pemeriksaans->isEmpty())
        <p style="color: #94a3b8; font-size: 0.875rem; text-align: center; padding: 2rem 0;">Tidak ada data pemeriksaan ditemukan.</p>
    @else
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Posyandu</th>
                        <th>Balita</th>
                        <th>JK</th>
                        <th>Umur</th>
                        <th>TB / BB</th>
                        <th>Label Prediksi Awal (Z-Score)</th>
                        <th>Hasil Decision Tree</th>
                        <th>Kecocokan</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pemeriksaans as $i => $p)
                    @php
                        $balita = $p->balita;
                        $actualLabel = $p->status_stunting ?? 'Normal';
                        
                        // C4.5 Decision Tree prediction
                        $predictedLabel = $p->status_dt ?? $c45Service->predict(
                            $p->umur_bulan,
                            $balita->jenis_kelamin ?? 'L',
                            (float)$p->tinggi_badan,
                            (float)$p->berat_badan
                        );
                        $isMatch = ($actualLabel === $predictedLabel);
                    @endphp
                    <tr>
                        <td>{{ $pemeriksaans->firstItem() + $i }}</td>
                        <td>{{ $p->posyandu->nama ?? '-' }}</td>
                        <td style="font-weight: 500;">
                            <div>{{ $balita->nama ?? '-' }}</div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">NIK: {{ $balita->nik ?? '-' }}</div>
                        </td>
                        <td>{{ $balita->jenis_kelamin ?? '-' }}</td>
                        <td>{{ $p->umur_bulan }} bln</td>
                        <td>{{ $p->tinggi_badan }} cm / {{ $p->berat_badan }} kg</td>
                        
                        {{-- Label Prediksi Awal Z-Score --}}
                        <td>
                            <span class="badge {{ $p->status_color }}">{{ $p->status_label }}</span>
                            <div style="font-size: 0.75rem; color: #64748b; font-family: monospace;">Z: {{ number_format($p->zscore_tb_u, 2) }}</div>
                        </td>

                        {{-- Hasil Decision Tree --}}
                        <td>
                            @php
                                $dtColor = match($predictedLabel) {
                                    'Normal' => 'bg-emerald-100 text-emerald-800',
                                    'Risk of Stunting' => 'bg-amber-100 text-amber-800',
                                    'Stunting' => 'bg-rose-100 text-rose-800',
                                    default => 'bg-gray-100 text-gray-800'
                                };
                            @endphp
                            <span class="badge {{ $dtColor }}">{{ $predictedLabel }}</span>
                        </td>

                        {{-- Match / Mismatch status --}}
                        <td>
                            @if($isMatch)
                                <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.75rem; color: #166534; font-weight: 600; background: #f0fdf4; padding: 2px 8px; border-radius: 9999px; border: 1px solid #bbf7d0;">
                                    ✓ Sesuai
                                </span>
                            @else
                                <span style="display: inline-flex; align-items: center; gap: 4px; font-size: 0.75rem; color: #991b1b; font-weight: 600; background: #fef2f2; padding: 2px 8px; border-radius: 9999px; border: 1px solid #fecaca;">
                                    ✕ Meleset
                                </span>
                            @endif
                        </td>

                        <td>{{ $p->tanggal_pemeriksaan->format('d/m/Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">{{ $pemeriksaans->links() }}</div>
    @endif
</div>
@endsection
