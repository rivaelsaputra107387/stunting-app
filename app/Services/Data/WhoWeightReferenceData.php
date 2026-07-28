<?php

namespace App\Services\Data;

/**
 * WHO Child Growth Standards / Permenkes No. 2 Tahun 2020
 * Tabel referensi Z-Score untuk indeks Berat Badan menurut Umur (BB/U)
 *
 * Format: umur_bulan => ['median' => float, 'minus1sd' => float, 'minus2sd' => float, 'minus3sd' => float]
 *
 * Sumber:
 * - WHO Child Growth Standards: Weight-for-age (Simplified field tables, Birth to 5 years)
 * - Peraturan Menteri Kesehatan RI Nomor 2 Tahun 2020 tentang Standar Antropometri Anak
 *
 * ================================================================
 * REVISI (12 Juli 2026): Tabel versi lama punya kesalahan nilai yang
 * membesar seiring bertambahnya umur (terutama minus2sd & minus3sd,
 * selisih bisa sampai ~1,4 di umur 60 bulan). Tabel di bawah ini
 * SUDAH DIVERIFIKASI PENUH (laki-laki & perempuan, 0-60 bulan)
 * terhadap tabel resmi WHO "Weight-for-age: Birth to 5 years
 * (Simplified field tables, z-scores)".
 * ================================================================
 */
class WhoWeightReferenceData
{
    /**
     * Tabel referensi BB/U untuk Laki-laki (0-60 bulan)
     * Sumber: WHO Child Growth Standards (terverifikasi penuh, 12 Juli 2026)
     *
     * @return array<int, array{median: float, minus1sd: float, minus2sd: float, minus3sd: float}>
     */
    public static function lakiLaki(): array
    {
        return [
            0  => ['median' => 3.3, 'minus1sd' => 2.9, 'minus2sd' => 2.5, 'minus3sd' => 2.1],
            1  => ['median' => 4.5, 'minus1sd' => 3.9, 'minus2sd' => 3.4, 'minus3sd' => 2.9],
            2  => ['median' => 5.6, 'minus1sd' => 4.9, 'minus2sd' => 4.3, 'minus3sd' => 3.8],
            3  => ['median' => 6.4, 'minus1sd' => 5.7, 'minus2sd' => 5.0, 'minus3sd' => 4.4],
            4  => ['median' => 7.0, 'minus1sd' => 6.2, 'minus2sd' => 5.6, 'minus3sd' => 4.9],
            5  => ['median' => 7.5, 'minus1sd' => 6.7, 'minus2sd' => 6.0, 'minus3sd' => 5.3],
            6  => ['median' => 7.9, 'minus1sd' => 7.1, 'minus2sd' => 6.4, 'minus3sd' => 5.7],
            7  => ['median' => 8.3, 'minus1sd' => 7.4, 'minus2sd' => 6.7, 'minus3sd' => 5.9],
            8  => ['median' => 8.6, 'minus1sd' => 7.7, 'minus2sd' => 6.9, 'minus3sd' => 6.2],
            9  => ['median' => 8.9, 'minus1sd' => 8.0, 'minus2sd' => 7.1, 'minus3sd' => 6.4],
            10 => ['median' => 9.2, 'minus1sd' => 8.2, 'minus2sd' => 7.4, 'minus3sd' => 6.6],
            11 => ['median' => 9.4, 'minus1sd' => 8.4, 'minus2sd' => 7.6, 'minus3sd' => 6.8],
            12 => ['median' => 9.6, 'minus1sd' => 8.6, 'minus2sd' => 7.7, 'minus3sd' => 6.9],
            13 => ['median' => 9.9, 'minus1sd' => 8.8, 'minus2sd' => 7.9, 'minus3sd' => 7.1],
            14 => ['median' => 10.1, 'minus1sd' => 9.0, 'minus2sd' => 8.1, 'minus3sd' => 7.2],
            15 => ['median' => 10.3, 'minus1sd' => 9.2, 'minus2sd' => 8.3, 'minus3sd' => 7.4],
            16 => ['median' => 10.5, 'minus1sd' => 9.4, 'minus2sd' => 8.4, 'minus3sd' => 7.5],
            17 => ['median' => 10.7, 'minus1sd' => 9.6, 'minus2sd' => 8.6, 'minus3sd' => 7.7],
            18 => ['median' => 10.9, 'minus1sd' => 9.8, 'minus2sd' => 8.8, 'minus3sd' => 7.8],
            19 => ['median' => 11.1, 'minus1sd' => 10.0, 'minus2sd' => 8.9, 'minus3sd' => 8.0],
            20 => ['median' => 11.3, 'minus1sd' => 10.1, 'minus2sd' => 9.1, 'minus3sd' => 8.1],
            21 => ['median' => 11.5, 'minus1sd' => 10.3, 'minus2sd' => 9.2, 'minus3sd' => 8.2],
            22 => ['median' => 11.8, 'minus1sd' => 10.5, 'minus2sd' => 9.4, 'minus3sd' => 8.4],
            23 => ['median' => 12.0, 'minus1sd' => 10.7, 'minus2sd' => 9.5, 'minus3sd' => 8.5],
            24 => ['median' => 12.2, 'minus1sd' => 10.8, 'minus2sd' => 9.7, 'minus3sd' => 8.6],
            25 => ['median' => 12.4, 'minus1sd' => 11.0, 'minus2sd' => 9.8, 'minus3sd' => 8.8],
            26 => ['median' => 12.5, 'minus1sd' => 11.2, 'minus2sd' => 10.0, 'minus3sd' => 8.9],
            27 => ['median' => 12.7, 'minus1sd' => 11.3, 'minus2sd' => 10.1, 'minus3sd' => 9.0],
            28 => ['median' => 12.9, 'minus1sd' => 11.5, 'minus2sd' => 10.2, 'minus3sd' => 9.1],
            29 => ['median' => 13.1, 'minus1sd' => 11.7, 'minus2sd' => 10.4, 'minus3sd' => 9.2],
            30 => ['median' => 13.3, 'minus1sd' => 11.8, 'minus2sd' => 10.5, 'minus3sd' => 9.4],
            31 => ['median' => 13.5, 'minus1sd' => 12.0, 'minus2sd' => 10.7, 'minus3sd' => 9.5],
            32 => ['median' => 13.7, 'minus1sd' => 12.1, 'minus2sd' => 10.8, 'minus3sd' => 9.6],
            33 => ['median' => 13.8, 'minus1sd' => 12.3, 'minus2sd' => 10.9, 'minus3sd' => 9.7],
            34 => ['median' => 14.0, 'minus1sd' => 12.4, 'minus2sd' => 11.0, 'minus3sd' => 9.8],
            35 => ['median' => 14.2, 'minus1sd' => 12.6, 'minus2sd' => 11.2, 'minus3sd' => 9.9],
            36 => ['median' => 14.3, 'minus1sd' => 12.7, 'minus2sd' => 11.3, 'minus3sd' => 10.0],
            37 => ['median' => 14.5, 'minus1sd' => 12.9, 'minus2sd' => 11.4, 'minus3sd' => 10.1],
            38 => ['median' => 14.7, 'minus1sd' => 13.0, 'minus2sd' => 11.5, 'minus3sd' => 10.2],
            39 => ['median' => 14.8, 'minus1sd' => 13.1, 'minus2sd' => 11.6, 'minus3sd' => 10.3],
            40 => ['median' => 15.0, 'minus1sd' => 13.3, 'minus2sd' => 11.8, 'minus3sd' => 10.4],
            41 => ['median' => 15.2, 'minus1sd' => 13.4, 'minus2sd' => 11.9, 'minus3sd' => 10.5],
            42 => ['median' => 15.3, 'minus1sd' => 13.6, 'minus2sd' => 12.0, 'minus3sd' => 10.6],
            43 => ['median' => 15.5, 'minus1sd' => 13.7, 'minus2sd' => 12.1, 'minus3sd' => 10.7],
            44 => ['median' => 15.7, 'minus1sd' => 13.8, 'minus2sd' => 12.2, 'minus3sd' => 10.8],
            45 => ['median' => 15.8, 'minus1sd' => 14.0, 'minus2sd' => 12.4, 'minus3sd' => 10.9],
            46 => ['median' => 16.0, 'minus1sd' => 14.1, 'minus2sd' => 12.5, 'minus3sd' => 11.0],
            47 => ['median' => 16.2, 'minus1sd' => 14.3, 'minus2sd' => 12.6, 'minus3sd' => 11.1],
            48 => ['median' => 16.3, 'minus1sd' => 14.4, 'minus2sd' => 12.7, 'minus3sd' => 11.2],
            49 => ['median' => 16.5, 'minus1sd' => 14.5, 'minus2sd' => 12.8, 'minus3sd' => 11.3],
            50 => ['median' => 16.7, 'minus1sd' => 14.7, 'minus2sd' => 12.9, 'minus3sd' => 11.4],
            51 => ['median' => 16.8, 'minus1sd' => 14.8, 'minus2sd' => 13.1, 'minus3sd' => 11.5],
            52 => ['median' => 17.0, 'minus1sd' => 15.0, 'minus2sd' => 13.2, 'minus3sd' => 11.6],
            53 => ['median' => 17.2, 'minus1sd' => 15.1, 'minus2sd' => 13.3, 'minus3sd' => 11.7],
            54 => ['median' => 17.3, 'minus1sd' => 15.2, 'minus2sd' => 13.4, 'minus3sd' => 11.8],
            55 => ['median' => 17.5, 'minus1sd' => 15.4, 'minus2sd' => 13.5, 'minus3sd' => 11.9],
            56 => ['median' => 17.7, 'minus1sd' => 15.5, 'minus2sd' => 13.6, 'minus3sd' => 12.0],
            57 => ['median' => 17.8, 'minus1sd' => 15.6, 'minus2sd' => 13.7, 'minus3sd' => 12.1],
            58 => ['median' => 18.0, 'minus1sd' => 15.8, 'minus2sd' => 13.8, 'minus3sd' => 12.2],
            59 => ['median' => 18.2, 'minus1sd' => 15.9, 'minus2sd' => 14.0, 'minus3sd' => 12.3],
            60 => ['median' => 18.3, 'minus1sd' => 16.0, 'minus2sd' => 14.1, 'minus3sd' => 12.4],
        ];
    }

    /**
     * Tabel referensi BB/U untuk Perempuan (0-60 bulan)
     * Sumber: WHO Child Growth Standards (terverifikasi penuh, 12 Juli 2026)
     *
     * @return array<int, array{median: float, minus1sd: float, minus2sd: float, minus3sd: float}>
     */
    public static function perempuan(): array
    {
        return [
            0  => ['median' => 3.2, 'minus1sd' => 2.8, 'minus2sd' => 2.4, 'minus3sd' => 2.0],
            1  => ['median' => 4.2, 'minus1sd' => 3.6, 'minus2sd' => 3.2, 'minus3sd' => 2.7],
            2  => ['median' => 5.1, 'minus1sd' => 4.5, 'minus2sd' => 3.9, 'minus3sd' => 3.4],
            3  => ['median' => 5.8, 'minus1sd' => 5.2, 'minus2sd' => 4.5, 'minus3sd' => 4.0],
            4  => ['median' => 6.4, 'minus1sd' => 5.7, 'minus2sd' => 5.0, 'minus3sd' => 4.4],
            5  => ['median' => 6.9, 'minus1sd' => 6.1, 'minus2sd' => 5.4, 'minus3sd' => 4.8],
            6  => ['median' => 7.3, 'minus1sd' => 6.5, 'minus2sd' => 5.7, 'minus3sd' => 5.1],
            7  => ['median' => 7.6, 'minus1sd' => 6.8, 'minus2sd' => 6.0, 'minus3sd' => 5.3],
            8  => ['median' => 7.9, 'minus1sd' => 7.0, 'minus2sd' => 6.3, 'minus3sd' => 5.6],
            9  => ['median' => 8.2, 'minus1sd' => 7.3, 'minus2sd' => 6.5, 'minus3sd' => 5.8],
            10 => ['median' => 8.5, 'minus1sd' => 7.5, 'minus2sd' => 6.7, 'minus3sd' => 5.9],
            11 => ['median' => 8.7, 'minus1sd' => 7.7, 'minus2sd' => 6.9, 'minus3sd' => 6.1],
            12 => ['median' => 8.9, 'minus1sd' => 7.9, 'minus2sd' => 7.0, 'minus3sd' => 6.3],
            13 => ['median' => 9.2, 'minus1sd' => 8.1, 'minus2sd' => 7.2, 'minus3sd' => 6.4],
            14 => ['median' => 9.4, 'minus1sd' => 8.3, 'minus2sd' => 7.4, 'minus3sd' => 6.6],
            15 => ['median' => 9.6, 'minus1sd' => 8.5, 'minus2sd' => 7.6, 'minus3sd' => 6.7],
            16 => ['median' => 9.8, 'minus1sd' => 8.7, 'minus2sd' => 7.7, 'minus3sd' => 6.9],
            17 => ['median' => 10.0, 'minus1sd' => 8.9, 'minus2sd' => 7.9, 'minus3sd' => 7.0],
            18 => ['median' => 10.2, 'minus1sd' => 9.1, 'minus2sd' => 8.1, 'minus3sd' => 7.2],
            19 => ['median' => 10.4, 'minus1sd' => 9.2, 'minus2sd' => 8.2, 'minus3sd' => 7.3],
            20 => ['median' => 10.6, 'minus1sd' => 9.4, 'minus2sd' => 8.4, 'minus3sd' => 7.5],
            21 => ['median' => 10.9, 'minus1sd' => 9.6, 'minus2sd' => 8.6, 'minus3sd' => 7.6],
            22 => ['median' => 11.1, 'minus1sd' => 9.8, 'minus2sd' => 8.7, 'minus3sd' => 7.8],
            23 => ['median' => 11.3, 'minus1sd' => 10.0, 'minus2sd' => 8.9, 'minus3sd' => 7.9],
            24 => ['median' => 11.5, 'minus1sd' => 10.2, 'minus2sd' => 9.0, 'minus3sd' => 8.1],
            25 => ['median' => 11.7, 'minus1sd' => 10.3, 'minus2sd' => 9.2, 'minus3sd' => 8.2],
            26 => ['median' => 11.9, 'minus1sd' => 10.5, 'minus2sd' => 9.4, 'minus3sd' => 8.4],
            27 => ['median' => 12.1, 'minus1sd' => 10.7, 'minus2sd' => 9.5, 'minus3sd' => 8.5],
            28 => ['median' => 12.3, 'minus1sd' => 10.9, 'minus2sd' => 9.7, 'minus3sd' => 8.6],
            29 => ['median' => 12.5, 'minus1sd' => 11.1, 'minus2sd' => 9.8, 'minus3sd' => 8.8],
            30 => ['median' => 12.7, 'minus1sd' => 11.2, 'minus2sd' => 10.0, 'minus3sd' => 8.9],
            31 => ['median' => 12.9, 'minus1sd' => 11.4, 'minus2sd' => 10.1, 'minus3sd' => 9.0],
            32 => ['median' => 13.1, 'minus1sd' => 11.6, 'minus2sd' => 10.3, 'minus3sd' => 9.1],
            33 => ['median' => 13.3, 'minus1sd' => 11.7, 'minus2sd' => 10.4, 'minus3sd' => 9.3],
            34 => ['median' => 13.5, 'minus1sd' => 11.9, 'minus2sd' => 10.5, 'minus3sd' => 9.4],
            35 => ['median' => 13.7, 'minus1sd' => 12.0, 'minus2sd' => 10.7, 'minus3sd' => 9.5],
            36 => ['median' => 13.9, 'minus1sd' => 12.2, 'minus2sd' => 10.8, 'minus3sd' => 9.6],
            37 => ['median' => 14.0, 'minus1sd' => 12.4, 'minus2sd' => 10.9, 'minus3sd' => 9.7],
            38 => ['median' => 14.2, 'minus1sd' => 12.5, 'minus2sd' => 11.1, 'minus3sd' => 9.8],
            39 => ['median' => 14.4, 'minus1sd' => 12.7, 'minus2sd' => 11.2, 'minus3sd' => 9.9],
            40 => ['median' => 14.6, 'minus1sd' => 12.8, 'minus2sd' => 11.3, 'minus3sd' => 10.1],
            41 => ['median' => 14.8, 'minus1sd' => 13.0, 'minus2sd' => 11.5, 'minus3sd' => 10.2],
            42 => ['median' => 15.0, 'minus1sd' => 13.1, 'minus2sd' => 11.6, 'minus3sd' => 10.3],
            43 => ['median' => 15.2, 'minus1sd' => 13.3, 'minus2sd' => 11.7, 'minus3sd' => 10.4],
            44 => ['median' => 15.3, 'minus1sd' => 13.4, 'minus2sd' => 11.8, 'minus3sd' => 10.5],
            45 => ['median' => 15.5, 'minus1sd' => 13.6, 'minus2sd' => 12.0, 'minus3sd' => 10.6],
            46 => ['median' => 15.7, 'minus1sd' => 13.7, 'minus2sd' => 12.1, 'minus3sd' => 10.7],
            47 => ['median' => 15.9, 'minus1sd' => 13.9, 'minus2sd' => 12.2, 'minus3sd' => 10.8],
            48 => ['median' => 16.1, 'minus1sd' => 14.0, 'minus2sd' => 12.3, 'minus3sd' => 10.9],
            49 => ['median' => 16.3, 'minus1sd' => 14.2, 'minus2sd' => 12.4, 'minus3sd' => 11.0],
            50 => ['median' => 16.4, 'minus1sd' => 14.3, 'minus2sd' => 12.6, 'minus3sd' => 11.1],
            51 => ['median' => 16.6, 'minus1sd' => 14.5, 'minus2sd' => 12.7, 'minus3sd' => 11.2],
            52 => ['median' => 16.8, 'minus1sd' => 14.6, 'minus2sd' => 12.8, 'minus3sd' => 11.3],
            53 => ['median' => 17.0, 'minus1sd' => 14.8, 'minus2sd' => 12.9, 'minus3sd' => 11.4],
            54 => ['median' => 17.2, 'minus1sd' => 14.9, 'minus2sd' => 13.0, 'minus3sd' => 11.5],
            55 => ['median' => 17.3, 'minus1sd' => 15.1, 'minus2sd' => 13.2, 'minus3sd' => 11.6],
            56 => ['median' => 17.5, 'minus1sd' => 15.2, 'minus2sd' => 13.3, 'minus3sd' => 11.7],
            57 => ['median' => 17.7, 'minus1sd' => 15.3, 'minus2sd' => 13.4, 'minus3sd' => 11.8],
            58 => ['median' => 17.9, 'minus1sd' => 15.5, 'minus2sd' => 13.5, 'minus3sd' => 11.9],
            59 => ['median' => 18.0, 'minus1sd' => 15.6, 'minus2sd' => 13.6, 'minus3sd' => 12.0],
            60 => ['median' => 18.2, 'minus1sd' => 15.8, 'minus2sd' => 13.7, 'minus3sd' => 12.1],
        ];
    }
}