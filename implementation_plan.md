# Implementasi Validasi Input & Deteksi Status Gizi (BB/U) — Implementation Plan

Rencana kerja ini bertujuan untuk mengintegrasikan dua peningkatan penting pada sistem untuk mencegah kesalahan input (*human error*) serta memanfaatkan atribut Berat Badan secara klinis sesuai dengan dokumen pendukung (PDF Bab I):
1. **Opsi A (Validasi Input Dinamis):** Mencegah kader menginput data yang tidak logis (seperti bayi 8 bulan diinput berat 1 kg).
2. **Opsi B (Status Gizi BB/U):** Menghitung dan menampilkan status gizi Berat Badan menurut Umur (BB/U) berdasarkan standar Permenkes No. 2 Tahun 2020 untuk mendeteksi *Underweight* (*Sangat Kurang*, *Kurang*, *Normal*, *Risiko Berat Badan Lebih*).

---

## User Review Required

> [!IMPORTANT]
> **Skema Database & Migrasi Ulang:**
> Penambahan kolom baru (`status_berat_badan` dan `zscore_bb_u`) pada tabel `pemeriksaans` memerlukan jalannya perintah `migrate:fresh --seed` yang akan mereset database.
>
> **Standar Klasifikasi BB/U (Permenkes No. 2 Tahun 2020):**
> - **Sangat Kurang** (Z-Score < -3 SD) → Merah (`bg-rose-100 text-rose-800`)
> - **Kurang** (-3 SD <= Z-Score < -2 SD) → Kuning (`bg-amber-100 text-amber-800`)
> - **Normal** (-2 SD <= Z-Score <= +1 SD) → Hijau (`bg-emerald-100 text-emerald-800`)
> - **Risiko Berat Badan Lebih** (Z-Score > +1 SD) → Biru (`bg-blue-100 text-blue-800`)

---

## Proposed Changes

### 1. Database & Models (Fase Model & Skema)

#### [MODIFY] [2024_01_01_000013_create_pemeriksaans_table.php](file:///d:/LARAVEL-PROJECT/stunting-app/database/migrations/2024_01_01_000013_create_pemeriksaans_table.php)
Menambahkan kolom status berat badan dan Z-score BB/U:
```php
Schema::create('pemeriksaans', function (Blueprint $table) {
    // ... kolom yang sudah ada ...
    $table->enum('status_stunting', ['Normal', 'Risk of Stunting', 'Stunting'])->nullable();
    $table->decimal('zscore_tb_u', 5, 2)->nullable();
    
    // BARU: Kolom untuk Opsi B
    $table->enum('status_berat_badan', ['Sangat Kurang', 'Kurang', 'Normal', 'Risiko Berat Badan Lebih'])->nullable();
    $table->decimal('zscore_bb_u', 5, 2)->nullable();
    
    $table->text('catatan')->nullable();
    $table->timestamps();
    $table->unique(['balita_id', 'tanggal_pemeriksaan']);
});
```

#### [MODIFY] [Pemeriksaan.php](file:///d:/LARAVEL-PROJECT/stunting-app/app/Models/Pemeriksaan.php)
Menambahkan field baru ke `$fillable`, cast, dan accessor warna untuk status berat badan:
```php
protected $fillable = [
    'balita_id',
    'posyandu_id',
    'tanggal_pemeriksaan',
    'umur_bulan',
    'tinggi_badan',
    'berat_badan',
    'status_stunting',
    'zscore_tb_u',
    // BARU
    'status_berat_badan',
    'zscore_bb_u',
    'catatan',
];

protected function casts(): array
{
    return [
        'tanggal_pemeriksaan' => 'date',
        'tinggi_badan' => 'decimal:2',
        'berat_badan' => 'decimal:2',
        'zscore_tb_u' => 'decimal:2',
        'zscore_bb_u' => 'decimal:2', // BARU
    ];
}

// Accessor warna badge BB/U
public function getBbStatusColorAttribute(): string
{
    return match ($this->status_berat_badan) {
        'Sangat Kurang' => 'bg-rose-100 text-rose-800',
        'Kurang' => 'bg-amber-100 text-amber-800',
        'Normal' => 'bg-emerald-100 text-emerald-800',
        'Risiko Berat Badan Lebih' => 'bg-blue-100 text-blue-800',
        default => 'bg-gray-100 text-gray-800',
    };
}
```

---

### 2. Validasi & Kalkulasi (Fase Logic)

#### [NEW] [WhoWeightReferenceData.php](file:///d:/LARAVEL-PROJECT/stunting-app/app/Services/Data/WhoWeightReferenceData.php)
Membuat file referensi baru berisi tabel standar berat badan menurut umur (BB/U) laki-laki & perempuan (0-60 bulan) dari Permenkes No. 2 Tahun 2020.

#### [MODIFY] [PemeriksaanRequest.php](file:///d:/LARAVEL-PROJECT/stunting-app/app/Http/Requests/PemeriksaanRequest.php)
Mengubah validasi agar mengecek rentang logis tinggi dan berat badan secara dinamis berdasarkan usia balita:
```php
public function rules(): array
{
    return [
        'balita_id'           => 'required|exists:balitas,id',
        'tanggal_pemeriksaan' => 'required|date|before_or_equal:today',
        'tinggi_badan'        => [
            'required',
            'numeric',
            function ($attribute, $value, $fail) {
                $balita = \App\Models\Balita::find($this->balita_id);
                if (!$balita) return;
                $umur = $balita->umur_bulan;
                
                // Aturan validasi dinamis tinggi badan berdasarkan kelompok umur
                if ($umur <= 6 && ($value < 40 || $value > 80)) {
                    $fail("Tinggi badan untuk bayi 0-6 bulan harus di antara 40 - 80 cm.");
                } elseif ($umur > 6 && $umur <= 12 && ($value < 55 || $value > 90)) {
                    $fail("Tinggi badan untuk bayi 7-12 bulan harus di antara 55 - 90 cm.");
                } elseif ($umur > 12 && $umur <= 24 && ($value < 65 || $value > 105)) {
                    $fail("Tinggi badan untuk anak 1-2 tahun harus di antara 65 - 105 cm.");
                } elseif ($umur > 24 && $umur <= 36 && ($value < 75 || $value > 115)) {
                    $fail("Tinggi badan untuk anak 2-3 tahun harus di antara 75 - 115 cm.");
                } elseif ($umur > 36 && ($value < 80 || $value > 130)) {
                    $fail("Tinggi badan untuk anak 3-5 tahun harus di antara 80 - 130 cm.");
                }
            }
        ],
        'berat_badan'         => [
            'required',
            'numeric',
            function ($attribute, $value, $fail) {
                $balita = \App\Models\Balita::find($this->balita_id);
                if (!$balita) return;
                $umur = $balita->umur_bulan;

                // Aturan validasi dinamis berat badan untuk mencegah salah ketik
                if ($umur <= 6 && ($value < 1.5 || $value > 12.0)) {
                    $fail("Berat badan untuk bayi 0-6 bulan harus di antara 1.5 - 12 kg.");
                } elseif ($umur > 6 && $umur <= 12 && ($value < 4.0 || $value > 15.0)) {
                    $fail("Berat badan untuk bayi 7-12 bulan harus di antara 4.0 - 15 kg. Data '{$value} kg' dianggap tidak valid (potensi salah ketik).");
                } elseif ($umur > 12 && $umur <= 24 && ($value < 6.0 || $value > 20.0)) {
                    $fail("Berat badan untuk anak 1-2 tahun harus di antara 6.0 - 20 kg.");
                } elseif ($umur > 24 && $umur <= 36 && ($value < 8.0 || $value > 25.0)) {
                    $fail("Berat badan untuk anak 2-3 tahun harus di antara 8.0 - 25 kg.");
                } elseif ($umur > 36 && ($value < 10.0 || $value > 35.0)) {
                    $fail("Berat badan untuk anak 3-5 tahun harus di antara 10.0 - 35 kg.");
                }
            }
        ],
        'catatan'             => 'nullable|string|max:1000',
    ];
}
```

#### [MODIFY] [DecisionTreeService.php](file:///d:/LARAVEL-PROJECT/stunting-app/app/Services/DecisionTreeService.php)
Mengintegrasikan perhitungan Z-score BB/U dan status berat badannya di method `classify`:
```php
public function classify(int $umurBulan, string $jenisKelamin, float $tinggiBadan, float $beratBadan): array
{
    $umurBulan = max(0, min(60, $umurBulan));

    // 1. Hitung Status Stunting (TB/U) - MURNI STUNTING
    $refTbu = $this->getReferensi($umurBulan, $jenisKelamin);
    $zscoreTbu = $this->hitungZScore($tinggiBadan, $refTbu);
    $statusStunting = $this->tentukanStatusStunting($zscoreTbu);

    // 2. Hitung Status Berat Badan (BB/U) - BARU (OPSI B)
    $refBbu = $this->getReferensiBerat($umurBulan, $jenisKelamin);
    $zscoreBbu = $this->hitungZScoreBbu($beratBadan, $refBbu);
    $statusBerat = $this->tentukanStatusBerat($zscoreBbu);

    return [
        'status'             => $statusStunting,
        'zscore'             => round($zscoreTbu, 2),
        'status_label'       => $this->getStatusLabel($statusStunting),
        // Tambahan BB/U
        'status_berat'       => $statusBerat,
        'zscore_bb_u'        => round($zscoreBbu, 2),
    ];
}
```

---

### 3. Controllers & UI Views (Fase Presentasi Data)

#### [MODIFY] [PemeriksaanController.php](file:///d:/LARAVEL-PROJECT/stunting-app/app/Http/Controllers/Posyandu/PemeriksaanController.php)
Menyimpan data `status_berat_badan` dan `zscore_bb_u` ke dalam database saat create & update pemeriksaan.

#### [MODIFY] [Halaman List Pemeriksaan (Blade)](file:///d:/LARAVEL-PROJECT/stunting-app/resources/views/posyandu/pemeriksaan/index.blade.php)
Menambahkan kolom baru **Status BB/U** di tabel sehingga pengguna (kader/kelurahan) bisa melihat dua status gizi sekaligus:
* Kolom 1: Status Stunting (TB/U) - *Badge Hijau/Kuning/Merah*
* Kolom 2: Status Berat Badan (BB/U) - *Badge Hijau/Kuning/Merah/Biru*

#### [MODIFY] [Laporan PDF & Excel](file:///d:/LARAVEL-PROJECT/stunting-app/resources/views/laporan/pdf.blade.php)
Memasukkan kolom **Berat Badan** dan **Status BB/U** ke dalam baris tabel laporan PDF untuk pertanggungjawaban data antropometri yang lengkap.

---

## Verification Plan

### Automated & Manual Verification
1. **Validasi Input Test (Opsi A):**
   * Masuk ke menu input pemeriksaan, pilih balita usia 8 bulan.
   * Coba input berat badan **1 kg** atau **3 kg**.
   * Pastikan sistem menolak dengan pesan error *"Berat badan untuk bayi 7-12 bulan harus di antara 4.0 - 15 kg."*
2. **Kalkulasi Status BB/U Test (Opsi B):**
   * Input balita usia 8 bulan dengan tinggi 70 cm dan berat badan **6.5 kg** (di bawah -2 SD standar BB/U).
   * Pastikan hasil klasifikasi menunjukkan:
     * **Status Stunting (TB/U):** Normal
     * **Status Berat Badan (BB/U):** Kurang (Underweight)
3. **Ekspor Laporan PDF:**
   * Unduh file PDF dan pastikan kolom Berat Badan beserta Status BB/U tercetak rapi di dalam tabel laporan.
