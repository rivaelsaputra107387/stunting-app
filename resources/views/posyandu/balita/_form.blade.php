{{-- Shared form partial for Balita create/edit --}}
@php $b = $balita ?? null; @endphp

<div style="display: grid; gap: 1.25rem;">
    <div>
        <label class="form-label">NIK <span style="color: #ef4444;">*</span></label>
        <input type="text" name="nik" value="{{ old('nik', $b?->nik) }}" class="form-input" maxlength="16" placeholder="Masukkan 16 digit NIK" required>
        @error('nik') <p style="color: #ef4444; font-size: 0.8125rem; margin: 0.25rem 0 0;">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="form-label">Nama Balita <span style="color: #ef4444;">*</span></label>
        <input type="text" name="nama" value="{{ old('nama', $b?->nama) }}" class="form-input" placeholder="Nama lengkap balita" required>
        @error('nama') <p style="color: #ef4444; font-size: 0.8125rem; margin: 0.25rem 0 0;">{{ $message }}</p> @enderror
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
        <div>
            <label class="form-label">Jenis Kelamin <span style="color: #ef4444;">*</span></label>
            <select name="jenis_kelamin" class="form-input" required>
                <option value="">-- Pilih --</option>
                <option value="L" {{ old('jenis_kelamin', $b?->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki</option>
                <option value="P" {{ old('jenis_kelamin', $b?->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan</option>
            </select>
            @error('jenis_kelamin') <p style="color: #ef4444; font-size: 0.8125rem; margin: 0.25rem 0 0;">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="form-label">Tanggal Lahir <span style="color: #ef4444;">*</span></label>
            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $b?->tanggal_lahir?->format('Y-m-d')) }}" class="form-input" required>
            @error('tanggal_lahir') <p style="color: #ef4444; font-size: 0.8125rem; margin: 0.25rem 0 0;">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label class="form-label">Nama Orang Tua <span style="color: #ef4444;">*</span></label>
        <input type="text" name="nama_orangtua" value="{{ old('nama_orangtua', $b?->nama_orangtua) }}" class="form-input" placeholder="Nama ibu/ayah" required>
        @error('nama_orangtua') <p style="color: #ef4444; font-size: 0.8125rem; margin: 0.25rem 0 0;">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="form-label">Alamat</label>
        <textarea name="alamat" class="form-input" rows="2" placeholder="Alamat lengkap (opsional)">{{ old('alamat', $b?->alamat) }}</textarea>
        @error('alamat') <p style="color: #ef4444; font-size: 0.8125rem; margin: 0.25rem 0 0;">{{ $message }}</p> @enderror
    </div>
</div>
