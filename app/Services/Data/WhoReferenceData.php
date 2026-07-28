<?php

namespace App\Services\Data;

/**
 * WHO Child Growth Standards / Permenkes No. 2 Tahun 2020
 * Tabel referensi Z-Score untuk indeks Tinggi Badan menurut Umur (TB/U)
 *
 * Format: umur_bulan => ['median' => float, 'minus1sd' => float, 'minus2sd' => float, 'minus3sd' => float]
 *
 * Sumber:
 * - WHO Child Growth Standards: Length/height-for-age
 * - Peraturan Menteri Kesehatan RI Nomor 2 Tahun 2020 tentang Standar Antropometri Anak
 *
 * Catatan:
 * - Umur 0-24 bulan: menggunakan Panjang Badan (PB) / recumbent length
 * - Umur >24-60 bulan: menggunakan Tinggi Badan (TB) / standing height
 * - SD dihitung dari: SD = (Median - Minus1SD)
 *
 * ================================================================
 * REVISI () Ditemukan bahwa tabel Laki-laki versi lama
 * memiliki kesalahan nilai (terutama minus1sd) dibanding sumber resmi
 * WHO. Tabel di bawah ini sudah diverifikasi ulang:
 *
 *  - lakiLaki(): SUDAH DIVERIFIKASI PENUH terhadap sumber resmi WHO
 *    (boys z-score expanded table untuk 0-23 bulan, dan boys
 *    height-for-age 2-5 tahun untuk 25-60 bulan). Umur 24 bulan
 *    sengaja tetap memakai nilai Panjang Badan (recumbent) sesuai
 *    konvensi "0-24 bulan = PB" yang sudah ditulis di atas.
 *
 *  - perempuan(): SUDAH DIVERIFIKASI PENUH (0-60 bulan). Umur 24-60
 *    bulan dicocokkan ke "Girls Height-for-age 2-5 years", dan umur
 *    0-23 bulan dicocokkan ke "Girls Length-for-age Birth to 2 years"
 *    (simplified field table). Data versi lama ternyata SUDAH BENAR
 *    untuk rentang 0-23 bulan -- tidak ada perubahan di rentang itu.
 * ================================================================
 */
class WhoReferenceData
{
    /**
     * Tabel referensi TB/U untuk Laki-laki (0-60 bulan)
     * Sumber: WHO Child Growth Standards (terverifikasi penuh, 12 Juli 2026)
     *
     * @return array<int, array{median: float, minus1sd: float, minus2sd: float, minus3sd: float}>
     */
    public static function lakiLaki(): array
    {
        return [
            0  => ['median' => 49.9, 'minus1sd' => 48.0, 'minus2sd' => 46.1, 'minus3sd' => 44.2],
            1  => ['median' => 54.7, 'minus1sd' => 52.7, 'minus2sd' => 50.8, 'minus3sd' => 48.8],
            2  => ['median' => 58.4, 'minus1sd' => 56.4, 'minus2sd' => 54.4, 'minus3sd' => 52.4],
            3  => ['median' => 61.4, 'minus1sd' => 59.4, 'minus2sd' => 57.3, 'minus3sd' => 55.3],
            4  => ['median' => 63.9, 'minus1sd' => 61.8, 'minus2sd' => 59.7, 'minus3sd' => 57.7],
            5  => ['median' => 65.9, 'minus1sd' => 63.8, 'minus2sd' => 61.7, 'minus3sd' => 59.6],
            6  => ['median' => 67.6, 'minus1sd' => 65.5, 'minus2sd' => 63.4, 'minus3sd' => 61.2],
            7  => ['median' => 69.2, 'minus1sd' => 67.0, 'minus2sd' => 64.8, 'minus3sd' => 62.6],
            8  => ['median' => 70.6, 'minus1sd' => 68.4, 'minus2sd' => 66.2, 'minus3sd' => 64.0],
            9  => ['median' => 72.0, 'minus1sd' => 69.7, 'minus2sd' => 67.5, 'minus3sd' => 65.2],
            10 => ['median' => 73.3, 'minus1sd' => 71.0, 'minus2sd' => 68.7, 'minus3sd' => 66.4],
            11 => ['median' => 74.5, 'minus1sd' => 72.2, 'minus2sd' => 69.9, 'minus3sd' => 67.6],
            12 => ['median' => 75.7, 'minus1sd' => 73.4, 'minus2sd' => 71.0, 'minus3sd' => 68.6],
            13 => ['median' => 76.9, 'minus1sd' => 74.5, 'minus2sd' => 72.1, 'minus3sd' => 69.7],
            14 => ['median' => 78.0, 'minus1sd' => 75.6, 'minus2sd' => 73.1, 'minus3sd' => 70.6],
            15 => ['median' => 79.2, 'minus1sd' => 76.6, 'minus2sd' => 74.1, 'minus3sd' => 71.6],
            16 => ['median' => 80.2, 'minus1sd' => 77.6, 'minus2sd' => 75.0, 'minus3sd' => 72.5],
            17 => ['median' => 81.2, 'minus1sd' => 78.6, 'minus2sd' => 76.0, 'minus3sd' => 73.3],
            18 => ['median' => 82.3, 'minus1sd' => 79.6, 'minus2sd' => 76.9, 'minus3sd' => 74.2],
            19 => ['median' => 83.2, 'minus1sd' => 80.5, 'minus2sd' => 77.7, 'minus3sd' => 75.0],
            20 => ['median' => 84.2, 'minus1sd' => 81.4, 'minus2sd' => 78.6, 'minus3sd' => 75.8],
            21 => ['median' => 85.1, 'minus1sd' => 82.3, 'minus2sd' => 79.4, 'minus3sd' => 76.5],
            22 => ['median' => 86.1, 'minus1sd' => 83.1, 'minus2sd' => 80.2, 'minus3sd' => 77.3],
            23 => ['median' => 86.9, 'minus1sd' => 83.9, 'minus2sd' => 80.9, 'minus3sd' => 78.0],
            24 => ['median' => 87.8, 'minus1sd' => 84.8, 'minus2sd' => 81.7, 'minus3sd' => 78.7],
            25 => ['median' => 88.0, 'minus1sd' => 84.9, 'minus2sd' => 81.7, 'minus3sd' => 78.6],
            26 => ['median' => 88.8, 'minus1sd' => 85.6, 'minus2sd' => 82.5, 'minus3sd' => 79.3],
            27 => ['median' => 89.6, 'minus1sd' => 86.4, 'minus2sd' => 83.1, 'minus3sd' => 79.9],
            28 => ['median' => 90.4, 'minus1sd' => 87.1, 'minus2sd' => 83.8, 'minus3sd' => 80.5],
            29 => ['median' => 91.2, 'minus1sd' => 87.8, 'minus2sd' => 84.5, 'minus3sd' => 81.1],
            30 => ['median' => 91.9, 'minus1sd' => 88.5, 'minus2sd' => 85.1, 'minus3sd' => 81.7],
            31 => ['median' => 92.7, 'minus1sd' => 89.2, 'minus2sd' => 85.7, 'minus3sd' => 82.3],
            32 => ['median' => 93.4, 'minus1sd' => 89.9, 'minus2sd' => 86.4, 'minus3sd' => 82.8],
            33 => ['median' => 94.1, 'minus1sd' => 90.5, 'minus2sd' => 86.9, 'minus3sd' => 83.4],
            34 => ['median' => 94.8, 'minus1sd' => 91.1, 'minus2sd' => 87.5, 'minus3sd' => 83.9],
            35 => ['median' => 95.4, 'minus1sd' => 91.8, 'minus2sd' => 88.1, 'minus3sd' => 84.4],
            36 => ['median' => 96.1, 'minus1sd' => 92.4, 'minus2sd' => 88.7, 'minus3sd' => 85.0],
            37 => ['median' => 96.7, 'minus1sd' => 93.0, 'minus2sd' => 89.2, 'minus3sd' => 85.5],
            38 => ['median' => 97.4, 'minus1sd' => 93.6, 'minus2sd' => 89.8, 'minus3sd' => 86.0],
            39 => ['median' => 98.0, 'minus1sd' => 94.2, 'minus2sd' => 90.3, 'minus3sd' => 86.5],
            40 => ['median' => 98.6, 'minus1sd' => 94.7, 'minus2sd' => 90.9, 'minus3sd' => 87.0],
            41 => ['median' => 99.2, 'minus1sd' => 95.3, 'minus2sd' => 91.4, 'minus3sd' => 87.5],
            42 => ['median' => 99.9, 'minus1sd' => 95.9, 'minus2sd' => 91.9, 'minus3sd' => 88.0],
            43 => ['median' => 100.4, 'minus1sd' => 96.4, 'minus2sd' => 92.4, 'minus3sd' => 88.4],
            44 => ['median' => 101.0, 'minus1sd' => 97.0, 'minus2sd' => 93.0, 'minus3sd' => 88.9],
            45 => ['median' => 101.6, 'minus1sd' => 97.5, 'minus2sd' => 93.5, 'minus3sd' => 89.4],
            46 => ['median' => 102.2, 'minus1sd' => 98.1, 'minus2sd' => 94.0, 'minus3sd' => 89.8],
            47 => ['median' => 102.8, 'minus1sd' => 98.6, 'minus2sd' => 94.4, 'minus3sd' => 90.3],
            48 => ['median' => 103.3, 'minus1sd' => 99.1, 'minus2sd' => 94.9, 'minus3sd' => 90.7],
            49 => ['median' => 103.9, 'minus1sd' => 99.7, 'minus2sd' => 95.4, 'minus3sd' => 91.2],
            50 => ['median' => 104.4, 'minus1sd' => 100.2, 'minus2sd' => 95.9, 'minus3sd' => 91.6],
            51 => ['median' => 105.0, 'minus1sd' => 100.7, 'minus2sd' => 96.4, 'minus3sd' => 92.1],
            52 => ['median' => 105.6, 'minus1sd' => 101.2, 'minus2sd' => 96.9, 'minus3sd' => 92.5],
            53 => ['median' => 106.1, 'minus1sd' => 101.7, 'minus2sd' => 97.4, 'minus3sd' => 93.0],
            54 => ['median' => 106.7, 'minus1sd' => 102.3, 'minus2sd' => 97.8, 'minus3sd' => 93.4],
            55 => ['median' => 107.2, 'minus1sd' => 102.8, 'minus2sd' => 98.3, 'minus3sd' => 93.9],
            56 => ['median' => 107.8, 'minus1sd' => 103.3, 'minus2sd' => 98.8, 'minus3sd' => 94.3],
            57 => ['median' => 108.3, 'minus1sd' => 103.8, 'minus2sd' => 99.3, 'minus3sd' => 94.7],
            58 => ['median' => 108.9, 'minus1sd' => 104.3, 'minus2sd' => 99.7, 'minus3sd' => 95.2],
            59 => ['median' => 109.4, 'minus1sd' => 104.8, 'minus2sd' => 100.2, 'minus3sd' => 95.6],
            60 => ['median' => 110.0, 'minus1sd' => 105.3, 'minus2sd' => 100.7, 'minus3sd' => 96.1],
        ];
    }

    /**
     * Tabel referensi TB/U untuk Perempuan (0-60 bulan)
     * Sumber: WHO Child Growth Standards (terverifikasi penuh, 12 Juli 2026)
     *
     * @return array<int, array{median: float, minus1sd: float, minus2sd: float, minus3sd: float}>
     */
    public static function perempuan(): array
    {
        return [
            // ==== Diverifikasi terhadap "Girls Length-for-age: Birth to 2 years" ====
            0  => ['median' => 49.1, 'minus1sd' => 47.3, 'minus2sd' => 45.4, 'minus3sd' => 43.6],
            1  => ['median' => 53.7, 'minus1sd' => 51.7, 'minus2sd' => 49.8, 'minus3sd' => 47.8],
            2  => ['median' => 57.1, 'minus1sd' => 55.0, 'minus2sd' => 53.0, 'minus3sd' => 51.0],
            3  => ['median' => 59.8, 'minus1sd' => 57.7, 'minus2sd' => 55.6, 'minus3sd' => 53.5],
            4  => ['median' => 62.1, 'minus1sd' => 59.9, 'minus2sd' => 57.8, 'minus3sd' => 55.6],
            5  => ['median' => 64.0, 'minus1sd' => 61.8, 'minus2sd' => 59.6, 'minus3sd' => 57.4],
            6  => ['median' => 65.7, 'minus1sd' => 63.5, 'minus2sd' => 61.2, 'minus3sd' => 58.9],
            7  => ['median' => 67.3, 'minus1sd' => 65.0, 'minus2sd' => 62.7, 'minus3sd' => 60.3],
            8  => ['median' => 68.7, 'minus1sd' => 66.4, 'minus2sd' => 64.0, 'minus3sd' => 61.7],
            9  => ['median' => 70.1, 'minus1sd' => 67.7, 'minus2sd' => 65.3, 'minus3sd' => 62.9],
            10 => ['median' => 71.5, 'minus1sd' => 69.0, 'minus2sd' => 66.5, 'minus3sd' => 64.1],
            11 => ['median' => 72.8, 'minus1sd' => 70.3, 'minus2sd' => 67.7, 'minus3sd' => 65.2],
            12 => ['median' => 74.0, 'minus1sd' => 71.4, 'minus2sd' => 68.9, 'minus3sd' => 66.3],
            13 => ['median' => 75.2, 'minus1sd' => 72.6, 'minus2sd' => 70.0, 'minus3sd' => 67.3],
            14 => ['median' => 76.4, 'minus1sd' => 73.7, 'minus2sd' => 71.0, 'minus3sd' => 68.3],
            15 => ['median' => 77.5, 'minus1sd' => 74.8, 'minus2sd' => 72.0, 'minus3sd' => 69.3],
            16 => ['median' => 78.6, 'minus1sd' => 75.8, 'minus2sd' => 73.0, 'minus3sd' => 70.2],
            17 => ['median' => 79.7, 'minus1sd' => 76.8, 'minus2sd' => 74.0, 'minus3sd' => 71.1],
            18 => ['median' => 80.7, 'minus1sd' => 77.8, 'minus2sd' => 74.9, 'minus3sd' => 72.0],
            19 => ['median' => 81.7, 'minus1sd' => 78.8, 'minus2sd' => 75.8, 'minus3sd' => 72.8],
            20 => ['median' => 82.7, 'minus1sd' => 79.7, 'minus2sd' => 76.7, 'minus3sd' => 73.7],
            21 => ['median' => 83.7, 'minus1sd' => 80.6, 'minus2sd' => 77.5, 'minus3sd' => 74.5],
            22 => ['median' => 84.6, 'minus1sd' => 81.5, 'minus2sd' => 78.4, 'minus3sd' => 75.2],
            23 => ['median' => 85.5, 'minus1sd' => 82.3, 'minus2sd' => 79.2, 'minus3sd' => 76.0],
            // ==== Diverifikasi terhadap "Girls Height-for-age: 2 to 5 years" ====
            // Umur 24 bulan tetap pakai nilai Panjang Badan (PB/recumbent), konsisten
            // dengan konvensi "0-24 bulan = PB" -- nilai lama sudah benar untuk PB.
            24 => ['median' => 86.4, 'minus1sd' => 83.2, 'minus2sd' => 80.0, 'minus3sd' => 76.7],
            25 => ['median' => 86.6, 'minus1sd' => 83.3, 'minus2sd' => 80.0, 'minus3sd' => 76.8],
            26 => ['median' => 87.4, 'minus1sd' => 84.1, 'minus2sd' => 80.8, 'minus3sd' => 77.5],
            27 => ['median' => 88.3, 'minus1sd' => 84.9, 'minus2sd' => 81.5, 'minus3sd' => 78.1],
            28 => ['median' => 89.1, 'minus1sd' => 85.7, 'minus2sd' => 82.2, 'minus3sd' => 78.8],
            29 => ['median' => 89.9, 'minus1sd' => 86.4, 'minus2sd' => 82.9, 'minus3sd' => 79.5],
            30 => ['median' => 90.7, 'minus1sd' => 87.1, 'minus2sd' => 83.6, 'minus3sd' => 80.1],
            31 => ['median' => 91.4, 'minus1sd' => 87.9, 'minus2sd' => 84.3, 'minus3sd' => 80.7],
            32 => ['median' => 92.2, 'minus1sd' => 88.6, 'minus2sd' => 84.9, 'minus3sd' => 81.3],
            33 => ['median' => 92.9, 'minus1sd' => 89.3, 'minus2sd' => 85.6, 'minus3sd' => 81.9],
            34 => ['median' => 93.6, 'minus1sd' => 89.9, 'minus2sd' => 86.2, 'minus3sd' => 82.5],
            35 => ['median' => 94.4, 'minus1sd' => 90.6, 'minus2sd' => 86.8, 'minus3sd' => 83.1],
            36 => ['median' => 95.1, 'minus1sd' => 91.2, 'minus2sd' => 87.4, 'minus3sd' => 83.6],
            37 => ['median' => 95.7, 'minus1sd' => 91.9, 'minus2sd' => 88.0, 'minus3sd' => 84.2],
            38 => ['median' => 96.4, 'minus1sd' => 92.5, 'minus2sd' => 88.6, 'minus3sd' => 84.7],
            39 => ['median' => 97.1, 'minus1sd' => 93.1, 'minus2sd' => 89.2, 'minus3sd' => 85.3],
            40 => ['median' => 97.7, 'minus1sd' => 93.8, 'minus2sd' => 89.8, 'minus3sd' => 85.8],
            41 => ['median' => 98.4, 'minus1sd' => 94.4, 'minus2sd' => 90.4, 'minus3sd' => 86.3],
            42 => ['median' => 99.0, 'minus1sd' => 95.0, 'minus2sd' => 90.9, 'minus3sd' => 86.8],
            43 => ['median' => 99.7, 'minus1sd' => 95.6, 'minus2sd' => 91.5, 'minus3sd' => 87.4],
            44 => ['median' => 100.3, 'minus1sd' => 96.2, 'minus2sd' => 92.0, 'minus3sd' => 87.9],
            45 => ['median' => 100.9, 'minus1sd' => 96.7, 'minus2sd' => 92.5, 'minus3sd' => 88.4],
            46 => ['median' => 101.5, 'minus1sd' => 97.3, 'minus2sd' => 93.1, 'minus3sd' => 88.9],
            47 => ['median' => 102.1, 'minus1sd' => 97.9, 'minus2sd' => 93.6, 'minus3sd' => 89.3],
            48 => ['median' => 102.7, 'minus1sd' => 98.4, 'minus2sd' => 94.1, 'minus3sd' => 89.8],
            49 => ['median' => 103.3, 'minus1sd' => 99.0, 'minus2sd' => 94.6, 'minus3sd' => 90.3],
            50 => ['median' => 103.9, 'minus1sd' => 99.5, 'minus2sd' => 95.1, 'minus3sd' => 90.7],
            51 => ['median' => 104.5, 'minus1sd' => 100.1, 'minus2sd' => 95.6, 'minus3sd' => 91.2],
            52 => ['median' => 105.0, 'minus1sd' => 100.6, 'minus2sd' => 96.1, 'minus3sd' => 91.7],
            53 => ['median' => 105.6, 'minus1sd' => 101.1, 'minus2sd' => 96.6, 'minus3sd' => 92.1],
            54 => ['median' => 106.2, 'minus1sd' => 101.6, 'minus2sd' => 97.1, 'minus3sd' => 92.6],
            55 => ['median' => 106.7, 'minus1sd' => 102.2, 'minus2sd' => 97.6, 'minus3sd' => 93.0],
            56 => ['median' => 107.3, 'minus1sd' => 102.7, 'minus2sd' => 98.1, 'minus3sd' => 93.4],
            57 => ['median' => 107.8, 'minus1sd' => 103.2, 'minus2sd' => 98.5, 'minus3sd' => 93.9],
            58 => ['median' => 108.4, 'minus1sd' => 103.7, 'minus2sd' => 99.0, 'minus3sd' => 94.3],
            59 => ['median' => 108.9, 'minus1sd' => 104.2, 'minus2sd' => 99.5, 'minus3sd' => 94.7],
            60 => ['median' => 109.4, 'minus1sd' => 104.7, 'minus2sd' => 99.9, 'minus3sd' => 95.2],
        ];
    }
}