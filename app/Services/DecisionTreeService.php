<?php

namespace App\Services;

use App\Services\Data\WhoReferenceData;
use App\Services\Data\WhoWeightReferenceData;

/**
 * Decision Tree Service untuk Klasifikasi Status Stunting
 *
 * Menggunakan standar WHO / Permenkes No. 2 Tahun 2020
 * Indeks: Tinggi Badan menurut Umur (TB/U) dan Berat Badan menurut Umur (BB/U)
 *
 * Alur Decision Tree:
 * 1. Input: Umur (bulan), Jenis Kelamin, Tinggi Badan, Berat Badan
 * 2. Ambil data referensi (median & SD) berdasarkan umur + jenis kelamin
 * 3. Hitung Z-Score = (TB_anak - Median) / SD
 * 4. Klasifikasi:
 *    - Z-Score >= -2      → Normal
 *    - -3 <= Z-Score < -2 → Pendek (Stunting)
 *    - Z-Score < -3       → Sangat Pendek (Severe Stunting)
 */
class DecisionTreeService
{
    /**
     * Klasifikasi status stunting berdasarkan Decision Tree.
     *
     * @param int    $umurBulan    Umur balita dalam bulan (0-60)
     * @param string $jenisKelamin 'L' (Laki-laki) atau 'P' (Perempuan)
     * @param float  $tinggiBadan  Tinggi/panjang badan dalam cm
     * @param float  $beratBadan   Berat badan dalam kg (untuk data lengkap)
     * @return array{status: string, zscore: float, status_label: string, status_berat: string, zscore_bb_u: float}
     */
    public function classify(int $umurBulan, string $jenisKelamin, float $tinggiBadan, float $beratBadan): array
    {
        // Clamp umur ke range yang valid (0-60 bulan)
        $umurBulan = max(0, min(60, $umurBulan));

        // Step 1: Ambil referensi TB/U
        $referensi = $this->getReferensi($umurBulan, $jenisKelamin);
        // Hitung Z-Score TB/U
        $zscore = $this->hitungZScore($tinggiBadan, $referensi);
        // Tentukan status stunting
        $status = $this->tentukanStatus($zscore);

        // Step 2: Ambil referensi BB/U (Untuk deteksi gizi berat badan / underweight)
        $referensiBerat = $this->getReferensiBerat($umurBulan, $jenisKelamin);
        // Hitung Z-Score BB/U
        $zscoreBbu = $this->hitungZScore($beratBadan, $referensiBerat);
        // Tentukan status berat badan
        $statusBerat = $this->tentukanStatusBerat($zscoreBbu);

        return [
            'status'       => $status,
            'zscore'       => round($zscore, 2),
            'status_label' => $this->getStatusLabel($status),
            'status_berat' => $statusBerat,
            'zscore_bb_u'  => round($zscoreBbu, 2),
        ];
    }

    /**
     * Hitung Z-Score TB/U.
     *
     * Rumus: Z-Score = (TB_anak - Median) / SD
     * Dimana SD = Median - Minus1SD
     *
     * @param float $tinggiBadan Tinggi badan anak (cm)
     * @param array $referensi   Data referensi WHO
     * @return float
     */
    private function hitungZScore(float $tinggiBadan, array $referensi): float
    {
        $median = $referensi['median'];
        $sd = $median - $referensi['minus1sd']; // 1 SD = selisih median ke -1SD

        // Hindari division by zero
        if ($sd <= 0) {
            return 0.0;
        }

        return ($tinggiBadan - $median) / $sd;
    }

    /**
     * Dapatkan data referensi WHO berdasarkan umur dan jenis kelamin.
     *
     * @param int    $umurBulan
     * @param string $jenisKelamin
     * @return array{median: float, minus1sd: float, minus2sd: float, minus3sd: float}
     */
    private function getReferensi(int $umurBulan, string $jenisKelamin): array
    {
        $tabel = match (strtoupper($jenisKelamin)) {
            'L' => WhoReferenceData::lakiLaki(),
            'P' => WhoReferenceData::perempuan(),
            default => WhoReferenceData::lakiLaki(),
        };

        // Jika umur ada di tabel, gunakan langsung
        if (isset($tabel[$umurBulan])) {
            return $tabel[$umurBulan];
        }

        // Fallback: gunakan umur terdekat
        $closestAge = collect(array_keys($tabel))
            ->sortBy(fn($age) => abs($age - $umurBulan))
            ->first();

        return $tabel[$closestAge];
    }

    /**
     * Tentukan status stunting berdasarkan Z-Score TB/U.
     *
     * Berdasarkan Permenkes No. 2 Tahun 2020:
     * - Z-Score >= -2 SD      → Normal
     * - -3 SD <= Z-Score < -2 SD → Pendek (Stunting)
     * - Z-Score < -3 SD       → Sangat Pendek (Severe Stunting)
     *
     * @param float $zscore
     * @return string 'normal' | 'pendek' | 'sangat_pendek'
     */
    private function tentukanStatus(float $zscore): string
    {
        if ($zscore >= -2) {
            return 'Normal';
        }

        if ($zscore >= -3) {
            return 'Risk of Stunting';
        }

        return 'Stunting';
    }

    /**
     * Get human-readable status label.
     *
     * @param string $status
     * @return string
     */
    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'Normal'           => 'Normal',
            'Risk of Stunting' => 'Risk of Stunting',
            'Stunting'         => 'Stunting',
            default            => 'Tidak Diketahui',
        };
    }

    /**
     * Dapatkan data referensi WHO BB/U berdasarkan umur dan jenis kelamin.
     *
     * @param int    $umurBulan
     * @param string $jenisKelamin
     * @return array{median: float, minus1sd: float, minus2sd: float, minus3sd: float}
     */
    private function getReferensiBerat(int $umurBulan, string $jenisKelamin): array
    {
        $tabel = match (strtoupper($jenisKelamin)) {
            'L' => WhoWeightReferenceData::lakiLaki(),
            'P' => WhoWeightReferenceData::perempuan(),
            default => WhoWeightReferenceData::lakiLaki(),
        };

        if (isset($tabel[$umurBulan])) {
            return $tabel[$umurBulan];
        }

        $closestAge = collect(array_keys($tabel))
            ->sortBy(fn($age) => abs($age - $umurBulan))
            ->first();

        return $tabel[$closestAge];
    }

    /**
     * Tentukan status berat badan berdasarkan Z-Score BB/U.
     *
     * Berdasarkan Permenkes No. 2 Tahun 2020:
     * - Z-Score < -3 SD           → Sangat Kurang
     * - -3 SD <= Z-Score < -2 SD  → Kurang
     * - -2 SD <= Z-Score <= +1 SD → Normal
     * - Z-Score > +1 SD           → Risiko Berat Badan Lebih
     *
     * @param float $zscore
     * @return string
     */
    private function tentukanStatusBerat(float $zscore): string
    {
        if ($zscore < -3) {
            return 'Sangat Kurang';
        }

        if ($zscore < -2) {
            return 'Kurang';
        }

        if ($zscore <= 1) {
            return 'Normal';
        }

        return 'Risiko Berat Badan Lebih';
    }
}
