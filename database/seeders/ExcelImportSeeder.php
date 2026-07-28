<?php

namespace Database\Seeders;

use App\Models\Balita;
use App\Models\Pemeriksaan;
use App\Models\Posyandu;
use App\Services\DecisionTreeService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ExcelImportSeeder extends Seeder
{
    /**
     * Run database seeds for Excel file data.
     */
    public function run(): void
    {
        $excelPath = base_path('Data posyandu kelurahan sukahaji bulan juli.xlsx');

        if (!file_exists($excelPath)) {
            $this->command->error("File Excel tidak ditemukan di: {$excelPath}");
            return;
        }

        $dtService = new DecisionTreeService();
        $spreadsheet = IOFactory::load($excelPath);

        $totalBalita = 0;
        $totalPemeriksaan = 0;

        // Pre-cache Posyandu list by uppercase name
        $posyanduCache = Posyandu::all()->keyBy(function ($item) {
            return strtoupper(trim($item->nama));
        });

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $rows = $sheet->toArray(null, true, true, true);

            if (empty($rows) || count($rows) < 2) {
                continue;
            }

            // Cari Baris Header (di baris 1)
            $headerRowIndex = 1;
            foreach ($rows as $rIdx => $row) {
                $rowStr = strtoupper(implode(' ', array_values($row)));
                if (str_contains($rowStr, 'NAMA_ANAK') || str_contains($rowStr, 'POSY') || str_contains($rowStr, 'NIK')) {
                    $headerRowIndex = $rIdx;
                    break;
                }
            }

            $dummyNikCounter = 1;

            foreach ($rows as $rowIndex => $row) {
                if ($rowIndex <= $headerRowIndex) {
                    continue;
                }

                // Ambil Nama Anak (Kolom C)
                $namaBalita = trim(str_replace("\xc2\xa0", '', (string)($row['C'] ?? '')));
                if (empty($namaBalita) || is_numeric($namaBalita) || strtoupper($namaBalita) === 'NAMA_ANAK' || strtoupper($namaBalita) === 'JUMLAH') {
                    continue;
                }

                // Match Posyandu dari Kolom I (POSY) dan Kolom K (RW)
                $posyRaw = trim(str_replace("\xc2\xa0", '', (string)($row['I'] ?? '')));
                $rwRaw = trim(str_replace("\xc2\xa0", '', (string)($row['K'] ?? '')));
                $posyandu = $this->matchPosyandu($posyRaw, $rwRaw, $posyanduCache);

                // NIK (Kolom B)
                $nikRaw = trim(str_replace("\xc2\xa0", '', (string)($row['B'] ?? '')));
                $nik = preg_replace('/[^0-9]/', '', $nikRaw);
                if (strlen($nik) !== 16) {
                    $nik = '3273' . str_pad((string)($posyandu->id * 100000 + $dummyNikCounter), 12, '0', STR_PAD_LEFT);
                    $dummyNikCounter++;
                }

                // Jenis Kelamin (Kolom E)
                $jkRaw = strtoupper(trim((string)($row['E'] ?? 'L')));
                $jenisKelamin = (str_contains($jkRaw, 'P') || str_contains($jkRaw, 'PEREMPUAN') || str_contains($jkRaw, 'W')) ? 'P' : 'L';

                // Tanggal Lahir (Kolom D)
                $tglLahirVal = trim(str_replace("\xc2\xa0", '', (string)($row['D'] ?? '')));
                $tglLahir = Carbon::now()->subMonths(12)->format('Y-m-d');

                if (is_numeric($tglLahirVal)) {
                    $tglLahir = Carbon::instance(ExcelDate::excelToDateTimeObject($tglLahirVal))->format('Y-m-d');
                } elseif (!empty($tglLahirVal)) {
                    try {
                        $tglLahir = Carbon::parse($tglLahirVal)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // fallback jika parse gagal
                    }
                }

                // Nama Ortu (Kolom H)
                $namaOrtu = trim(str_replace("\xc2\xa0", '', (string)($row['H'] ?? '')));
                if (empty($namaOrtu)) {
                    $namaOrtu = 'Orang Tua ' . $namaBalita;
                }

                // Alamat (Kolom L + RT J + RW K)
                $jalanVal = trim(str_replace("\xc2\xa0", '', (string)($row['L'] ?? '')));
                $rtVal = trim((string)($row['J'] ?? ''));
                $rwVal = trim((string)($row['K'] ?? ''));

                $alamatParts = [];
                if (!empty($jalanVal)) $alamatParts[] = $jalanVal;
                if (!empty($rtVal)) $alamatParts[] = "RT " . str_pad($rtVal, 2, '0', STR_PAD_LEFT);
                if (!empty($rwVal)) $alamatParts[] = "RW " . str_pad($rwVal, 2, '0', STR_PAD_LEFT);

                $alamat = !empty($alamatParts) ? implode(' ', $alamatParts) : 'Kelurahan Sukahaji';

                // Tanggal Pemeriksaan (Kolom M: TANGGALUKUR)
                $tglUkurVal = trim(str_replace("\xc2\xa0", '', (string)($row['M'] ?? '')));
                $tanggalPemeriksaan = '2026-07-01';

                if (is_numeric($tglUkurVal)) {
                    $tanggalPemeriksaan = Carbon::instance(ExcelDate::excelToDateTimeObject($tglUkurVal))->format('Y-m-d');
                } elseif (!empty($tglUkurVal)) {
                    try {
                        $tanggalPemeriksaan = Carbon::parse($tglUkurVal)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // fallback
                    }
                }

                // Umur Bulan (Kolom G: umur_bulan)
                $umurBulanVal = trim((string)($row['G'] ?? ''));
                $umurBulan = (int) preg_replace('/[^0-9]/', '', $umurBulanVal);
                if ($umurBulan < 0 || $umurBulan > 60) {
                    // Hitung dari tgl lahir & tgl pemeriksaan
                    $tglLahirCarbon = Carbon::parse($tglLahir);
                    $tglPemeriksaanCarbon = Carbon::parse($tanggalPemeriksaan);
                    $umurBulan = max(0, min(60, (int)$tglLahirCarbon->diffInMonths($tglPemeriksaanCarbon)));
                }

                // Berat Badan (Kolom N: BERAT)
                $bbRaw = str_replace(',', '.', trim((string)($row['N'] ?? '9.5')));
                $beratBadan = (float) preg_replace('/[^0-9.]/', '', $bbRaw);
                if ($beratBadan <= 0) $beratBadan = 9.5;

                // Tinggi Badan (Kolom O: TINGGI)
                $tbRaw = str_replace(',', '.', trim((string)($row['O'] ?? '75')));
                $tinggiBadan = (float) preg_replace('/[^0-9.]/', '', $tbRaw);
                if ($tinggiBadan <= 0) $tinggiBadan = 75.0;

                // Simpan atau Update Balita
                $balita = Balita::updateOrCreate(
                    ['nik' => $nik],
                    [
                        'posyandu_id'   => $posyandu->id,
                        'nama'          => $namaBalita,
                        'jenis_kelamin' => $jenisKelamin,
                        'tanggal_lahir' => $tglLahir,
                        'nama_orangtua' => $namaOrtu,
                        'alamat'        => $alamat,
                    ]
                );
                $totalBalita++;

                // Klasifikasi Decision Tree & Z-Score
                $dtResult = $dtService->classify(
                    umurBulan: $umurBulan,
                    jenisKelamin: $jenisKelamin,
                    tinggiBadan: $tinggiBadan,
                    beratBadan: $beratBadan
                );

                Pemeriksaan::updateOrCreate(
                    [
                        'balita_id'           => $balita->id,
                        'tanggal_pemeriksaan' => Carbon::parse($tanggalPemeriksaan)->startOfDay(),
                    ],
                    [
                        'posyandu_id'        => $posyandu->id,
                        'umur_bulan'         => $umurBulan,
                        'tinggi_badan'       => $tinggiBadan,
                        'berat_badan'        => $beratBadan,
                        'status_stunting'    => $dtResult['status'],
                        'zscore_tb_u'        => $dtResult['zscore'],
                        'status_berat_badan' => $dtResult['status_berat'],
                        'zscore_bb_u'        => $dtResult['zscore_bb_u'],
                        'catatan'            => 'Diimpor dari Excel Kelurahan Sukahaji',
                    ]
                );
                $totalPemeriksaan++;
            }
        }

        $this->command->info("Impor Berhasil! Total Balita: {$totalBalita}, Total Pemeriksaan: {$totalPemeriksaan}");

        $this->command->info("Melatih model Machine Learning Decision Tree (C4.5)...");
        $c45Service = new \App\Services\C45Service();
        $c45Service->train();

        $this->command->info("Menerapkan prediksi C4.5 ke semua data pemeriksaan...");
        $semuaPemeriksaan = Pemeriksaan::with('balita')->get();
        foreach ($semuaPemeriksaan as $p) {
            if (!$p->balita) continue;
            
            $dtPrediction = $c45Service->predict(
                $p->umur_bulan,
                $p->balita->jenis_kelamin,
                (float) $p->tinggi_badan,
                (float) $p->berat_badan
            );

            $p->update(['status_dt' => $dtPrediction]);
        }
        $this->command->info("Prediksi C4.5 selesai diterapkan.");
    }

    /**
     * Mencocokkan string POSY dari Excel ke Posyandu di DB
     */
    private function matchPosyandu(string $posyRaw, string $rwRaw, &$cache): Posyandu
    {
        $raw = strtoupper(trim($posyRaw));

        if (str_contains($raw, 'MELATI 1')) {
            $nama = 'Melati 1';
        } elseif (str_contains($raw, 'MELATI 2')) {
            $nama = 'Melati 2';
        } elseif (str_contains($raw, 'MELATI 3')) {
            $nama = 'Melati 3';
        } elseif (str_contains($raw, 'DAHLIA 1')) {
            $nama = 'Dahlia 1';
        } elseif (str_contains($raw, 'DAHLIA 2')) {
            $nama = 'Dahlia 2';
        } elseif (str_contains($raw, 'DAHLIA 3')) {
            $nama = 'Dahlia 3';
        } elseif (str_contains($raw, 'CEMPAKA 1')) {
            $nama = 'Cempaka 1';
        } elseif (str_contains($raw, 'CEMPAKA 2')) {
            $nama = 'Cempaka 2';
        } elseif (str_contains($raw, 'CEMPAKA 3')) {
            $nama = 'Cempaka 3';
        } elseif (str_contains($raw, 'MAWAR MELATI 1')) {
            $nama = 'Mawar Melati 1';
        } elseif (str_contains($raw, 'MAWAR MELATI 2')) {
            $nama = 'Mawar Melati 2';
        } elseif (str_contains($raw, 'BAKTI IBU 1')) {
            $nama = 'Bakti Ibu 1';
        } elseif (str_contains($raw, 'BAKTI IBU 2')) {
            $nama = 'Bakti Ibu 2';
        } elseif (str_contains($raw, 'FLAMBOYAN 1')) {
            $nama = 'Flamboyan 1';
        } elseif (str_contains($raw, 'FLAMBOYAN 2')) {
            $nama = 'Flamboyan 2';
        } elseif (str_contains($raw, 'FLAMBOYAN 3')) {
            $nama = 'Flamboyan 3';
        } elseif (str_contains($raw, 'MELATI RW 07') || str_contains($raw, 'MELATI 07')) {
            $nama = 'Melati RW 07';
        } elseif (str_contains($raw, 'KENANGA 1')) {
            $nama = 'Kenanga 1';
        } elseif (str_contains($raw, 'KENANGA 2')) {
            $nama = 'Kenanga 2';
        } elseif (str_contains($raw, 'MELATI MEKAR')) {
            $nama = 'Melati Mekar';
        } elseif (str_contains($raw, 'MELATI') && (str_contains($raw, '10') || str_contains($raw, '*'))) {
            $nama = 'Melati RW 10';
        } else {
            $nama = trim(preg_replace('/\s+RW\s*\d+.*/i', '', $posyRaw));
        }

        $upperNama = strtoupper($nama);

        if (isset($cache[$upperNama])) {
            return $cache[$upperNama];
        }

        // Search in DB if not in cache
        $posyandu = Posyandu::where('nama', $nama)->first();

        if (!$posyandu) {
            $rwNum = preg_replace('/[^0-9]/', '', $rwRaw);
            $rwFormatted = !empty($rwNum) ? 'RW ' . str_pad($rwNum, 2, '0', STR_PAD_LEFT) : 'RW 01';
            $posyandu = Posyandu::create([
                'nama'   => $nama,
                'rw'     => $rwFormatted,
                'alamat' => 'Kelurahan Sukahaji',
            ]);
        }

        $cache[$upperNama] = $posyandu;

        return $posyandu;
    }
}

