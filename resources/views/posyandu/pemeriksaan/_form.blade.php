{{-- Shared form partial for Pemeriksaan create/edit --}}
@php $p = $pemeriksaan ?? null; @endphp

<div style="display: grid; gap: 1.25rem;">
    <div>
        <label class="form-label">Pilih Balita <span style="color: #ef4444;">*</span></label>
        <select name="balita_id" class="form-input" required id="balitaSelect">
            <option value="">-- Pilih Balita --</option>
            @foreach($balitas as $balita)
                <option value="{{ $balita->id }}"
                    data-tgl-lahir="{{ $balita->tanggal_lahir->format('Y-m-d') }}"
                    {{ old('balita_id', $p?->balita_id) == $balita->id ? 'selected' : '' }}>
                    {{ $balita->nama }} — {{ $balita->nik }} ({{ $balita->jenis_kelamin === 'L' ? 'L' : 'P' }})
                </option>
            @endforeach
        </select>
        @error('balita_id') <p style="color: #ef4444; font-size: 0.8125rem; margin: 0.25rem 0 0;">{{ $message }}</p> @enderror
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div>
            <label class="form-label">Tanggal Pemeriksaan <span style="color: #ef4444;">*</span></label>
            <input type="date" name="tanggal_pemeriksaan" value="{{ old('tanggal_pemeriksaan', $p?->tanggal_pemeriksaan?->format('Y-m-d')) }}" class="form-input" required id="tglPemeriksaan">
            @error('tanggal_pemeriksaan') <p style="color: #ef4444; font-size: 0.8125rem; margin: 0.25rem 0 0;">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="form-label">Umur (otomatis)</label>
            <input type="text" id="umurDisplay" class="form-input" style="background: #f8fafc; color: #64748b;" readonly placeholder="Pilih balita & tanggal" value="{{ $p ? $p->umur_bulan . ' bulan' : '' }}">
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div>
            <label class="form-label">Tinggi Badan (cm) <span style="color: #ef4444;">*</span></label>
            <input type="number" step="0.01" name="tinggi_badan" value="{{ old('tinggi_badan', $p?->tinggi_badan) }}" class="form-input" placeholder="Contoh: 75.5" min="20" max="130" required>
            @error('tinggi_badan') <p style="color: #ef4444; font-size: 0.8125rem; margin: 0.25rem 0 0;">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="form-label">Berat Badan (kg) <span style="color: #ef4444;">*</span></label>
            <input type="number" step="0.01" name="berat_badan" value="{{ old('berat_badan', $p?->berat_badan) }}" class="form-input" placeholder="Contoh: 8.5" min="1" max="40" required>
            @error('berat_badan') <p style="color: #ef4444; font-size: 0.8125rem; margin: 0.25rem 0 0;">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="form-label">Catatan</label>
        <textarea name="catatan" class="form-input" rows="2" placeholder="Catatan tambahan (opsional)">{{ old('catatan', $p?->catatan) }}</textarea>
        @error('catatan') <p style="color: #ef4444; font-size: 0.8125rem; margin: 0.25rem 0 0;">{{ $message }}</p> @enderror
    </div>

    {{-- Info box --}}
    <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 0.5rem; padding: 0.75rem 1rem;">
        <p style="color: #166534; font-size: 0.8125rem; margin: 0;">
            <strong>ℹ️ Info:</strong> Status stunting dan Z-Score akan dihitung otomatis oleh sistem berdasarkan standar WHO / Permenkes No. 2 Tahun 2020.
        </p>
    </div>
</div>

@push('scripts')
<script>
    // Auto-calculate age when balita or date changes
    function calculateAge() {
        const select = document.getElementById('balitaSelect');
        const tglPemeriksaan = document.getElementById('tglPemeriksaan');
        const umurDisplay = document.getElementById('umurDisplay');

        const selectedOption = select.options[select.selectedIndex];
        const tglLahir = selectedOption?.dataset?.tglLahir;

        if (tglLahir && tglPemeriksaan.value) {
            const birth = new Date(tglLahir);
            const check = new Date(tglPemeriksaan.value);
            let months = (check.getFullYear() - birth.getFullYear()) * 12;
            months -= birth.getMonth();
            months += check.getMonth();
            if (check.getDate() < birth.getDate()) months--;
            months = Math.max(0, months);
            umurDisplay.value = months + ' bulan';
        } else {
            umurDisplay.value = '';
        }
    }

    document.getElementById('balitaSelect').addEventListener('change', calculateAge);
    document.getElementById('tglPemeriksaan').addEventListener('change', calculateAge);
</script>
@endpush
