@extends('layouts.app')
@section('title', 'Dashboard Kelurahan')

@section('content')
<div style="margin-bottom: 1.5rem;">
    <h1 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin: 0;">Dashboard Kelurahan Sukahaji</h1>
    <p style="color: #64748b; font-size: 0.875rem; margin: 0.25rem 0 0;">Ringkasan data stunting seluruh posyandu — {{ now()->translatedFormat('d F Y') }}</p>
</div>

{{-- Stats Cards --}}
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
    <div class="stat-card" style="border-left: 4px solid #2563eb;">
        <div style="color: #64748b; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Total Balita</div>
        <div style="font-size: 2rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">{{ $totalBalita }}</div>
        <div style="color: #94a3b8; font-size: 0.75rem; margin-top: 0.25rem;">Terdaftar di seluruh posyandu</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #10b981;">
        <div style="color: #64748b; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Total Posyandu</div>
        <div style="font-size: 2rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">{{ $totalPosyandu }}</div>
        <div style="color: #94a3b8; font-size: 0.75rem; margin-top: 0.25rem;">Aktif di kelurahan</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #f59e0b;">
        <div style="color: #64748b; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Pemeriksaan Bulan Ini</div>
        <div style="font-size: 2rem; font-weight: 700; color: #0f172a; margin-top: 0.25rem;">{{ $pemeriksaanBulanIni }}</div>
        <div style="color: #94a3b8; font-size: 0.75rem; margin-top: 0.25rem;">{{ now()->translatedFormat('F Y') }}</div>
    </div>
    <div class="stat-card" style="border-left: 4px solid #ef4444;">
        <div style="color: #64748b; font-size: 0.75rem; font-weight: 500; text-transform: uppercase; letter-spacing: 0.05em;">Prevalensi Stunting</div>
        <div style="font-size: 2rem; font-weight: 700; color: {{ $persenStunting > 20 ? '#ef4444' : '#f59e0b' }}; margin-top: 0.25rem;">{{ $persenStunting }}%</div>
        <div style="color: #94a3b8; font-size: 0.75rem; margin-top: 0.25rem;">{{ $statusCounts['risk'] + $statusCounts['stunting'] }} dari {{ $totalBalita }} balita</div>
    </div>
</div>

{{-- Charts Row --}}
<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem; margin-bottom: 2rem;">
    {{-- Pie/Doughnut Chart --}}
    <div style="background: #fff; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #f1f5f9;">
        <h3 style="font-size: 0.9375rem; font-weight: 600; color: #0f172a; margin: 0 0 1rem;">Distribusi Status Stunting</h3>
        <canvas id="pieChart" style="max-height: 280px;"></canvas>
    </div>

    {{-- Line Chart --}}
    <div style="background: #fff; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #f1f5f9;">
        <h3 style="font-size: 0.9375rem; font-weight: 600; color: #0f172a; margin: 0 0 1rem;">Tren Stunting 12 Bulan Terakhir</h3>
        <canvas id="lineChart" style="max-height: 280px;"></canvas>
    </div>
</div>

{{-- Bar Chart --}}
<div style="background: #fff; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #f1f5f9; margin-bottom: 2rem;">
    <h3 style="font-size: 0.9375rem; font-weight: 600; color: #0f172a; margin: 0 0 1rem;">Jumlah Kasus Stunting per Posyandu</h3>
    <canvas id="barChart" style="max-height: 350px;"></canvas>
</div>

{{-- Ranking Table --}}
<div style="background: #fff; border-radius: 1rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.08); border: 1px solid #f1f5f9;">
    <h3 style="font-size: 0.9375rem; font-weight: 600; color: #0f172a; margin: 0 0 1rem;">Ranking Posyandu — Prevalensi Stunting</h3>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Posyandu</th>
                    <th>RW</th>
                    <th>Total Balita</th>
                    <th>Stunting</th>
                    <th>Prevalensi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rankingPosyandu as $i => $pos)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight: 500;">
                        <a href="{{ route('kelurahan.posyandu.show', $pos) }}" style="color: #2563eb; text-decoration: none;">{{ $pos->nama }}</a>
                    </td>
                    <td>{{ $pos->rw }}</td>
                    <td>{{ $pos->balitas_count }}</td>
                    <td>
                        @if($pos->stunting_count > 0)
                            <span class="badge bg-rose-100 text-rose-800">{{ $pos->stunting_count }}</span>
                        @else
                            <span class="badge bg-emerald-100 text-emerald-800">0</span>
                        @endif
                    </td>
                    <td>
                        @php $persen = $pos->balitas_count > 0 ? round(($pos->stunting_count / $pos->balitas_count) * 100, 1) : 0; @endphp
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <div style="flex: 1; background: #f1f5f9; border-radius: 9999px; height: 6px; max-width: 100px;">
                                <div style="background: {{ $persen > 20 ? '#ef4444' : ($persen > 0 ? '#f59e0b' : '#10b981') }}; height: 100%; border-radius: 9999px; width: {{ min($persen, 100) }}%;"></div>
                            </div>
                            <span style="font-size: 0.8125rem; color: #64748b;">{{ $persen }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    // Color palette
    const colors = {
        normal: '#10b981',
        risk: '#f59e0b',
        stunting: '#ef4444',
    };

    // Pie Chart — Status Distribution
    new Chart(document.getElementById('pieChart'), {
        type: 'doughnut',
        data: {
            labels: ['Normal', 'Risk of Stunting', 'Stunting'],
            datasets: [{
                data: [{{ $statusCounts['normal'] }}, {{ $statusCounts['risk'] }}, {{ $statusCounts['stunting'] }}],
                backgroundColor: [colors.normal, colors.risk, colors.stunting],
                borderWidth: 0,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } }
            },
            cutout: '60%',
        }
    });

    // Line Chart — 12-month Trend
    const trendData = @json($trendData);
    new Chart(document.getElementById('lineChart'), {
        type: 'line',
        data: {
            labels: trendData.map(d => d.label),
            datasets: [
                {
                    label: 'Normal',
                    data: trendData.map(d => d.normal),
                    borderColor: colors.normal,
                    backgroundColor: colors.normal + '20',
                    fill: true,
                    tension: 0.4,
                },
                {
                    label: 'Risk of Stunting',
                    data: trendData.map(d => d.risk),
                    borderColor: colors.risk,
                    backgroundColor: colors.risk + '20',
                    fill: true,
                    tension: 0.4,
                },
                {
                    label: 'Stunting',
                    data: trendData.map(d => d.stunting),
                    borderColor: colors.stunting,
                    backgroundColor: colors.stunting + '20',
                    fill: true,
                    tension: 0.4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });

    // Bar Chart — Stunting per Posyandu
    const posyanduData = @json($stuntingPerPosyandu);
    new Chart(document.getElementById('barChart'), {
        type: 'bar',
        data: {
            labels: posyanduData.map(d => d.nama),
            datasets: [{
                label: 'Kasus Stunting',
                data: posyanduData.map(d => d.total_stunting),
                backgroundColor: posyanduData.map(d => d.total_stunting > 3 ? colors.stunting + 'CC' : d.total_stunting > 0 ? colors.risk + 'CC' : colors.normal + 'CC'),
                borderRadius: 6,
                barThickness: 20,
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            plugins: {
                legend: { display: false }
            },
            scales: {
                x: { beginAtZero: true, ticks: { stepSize: 1 } }
            }
        }
    });
</script>
@endpush
