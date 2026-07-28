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

        foreach ($spreadsheet->getAllSheets() as $sheet) {
            $sheetTitle = trim($sheet->getTitle());
            $rows = $sheet->toArray(null, true, true, true);

            if (empty($rows)) {
                continue;
            }

            // Tentukan Posyandu & RW dari Sheet
            $posyanduNama = $sheetTitle;
            $posyanduRw = 'RW 01';

            if (preg_match('/RW\s*(\d+)/i', $sheetTitle, $matches)) {
                $posyanduRw = 'RW ' . str_pad($matches[1], 2, '0', STR_PAD_LEFT);
            }

            // Temukan atau Buat Posyandu
            $posyandu = Posyandu::firstOrCreate(
                ['nama' => $posyanduNama],
                ['rw' => $posyanduRw, 'alamat' => 'Kelurahan Sukahaji']
            );

            // Cari Baris Header (yang berisi NAMA, NIK, JK, dsb)
            $headerRowIndex = null;
            $colMap = [];

            foreach ($rows as $rowIndex => $row) {
                $rowValues = array_map('strtoupper', array_map('strval', array_values($row)));
                $rowString = implode(' ', $rowValues);

                if (str_contains($rowString, 'NAMA') || str_contains($rowString, 'BALITA') || str_contains($rowString, 'TB') || str_contains($rowString, 'BB')) {
                    $headerRowIndex = $rowIndex;

                    foreach ($row as $colLetter => $cellValue) {
                        $cellUpper = strtoupper(trim((string)$cellValue));

                        if (str_contains($cellUpper, 'NIK')) {
                            $colMap['nik'] = $colLetter;
                        } elseif (str_contains($cellUpper, 'NAMA') && !isset($colMap['nama'])) {
                            $colMap['nama'] = $colLetter;
                        } elseif (str_contains($cellUpper, 'JK') || str_contains($cellUpper, 'SEX') || str_contains($cellUpper, 'KELAMIN')) {
                            $colMap['jk'] = $colLetter;
                        } elseif (str_contains($cellUpper, 'LAHIR') || str_contains($cellUpper, 'TGL')) {
                            $colMap['tgl_lahir'] = $colLetter;
                        } elseif (str_contains($cellUpper, 'ORTU') || str_contains($cellUpper, 'IBU') || str_contains($cellUpper, 'AYAH')) {
                            $colMap['nama_ortu'] = $colLetter;
                        } elseif (str_contains($cellUpper, 'ALAMAT') || str_contains($cellUpper, 'RT')) {
                            $colMap['alamat'] = $colLetter;
                        } elseif (str_contains($cellUpper, 'UMUR') || str_contains($cellUpper, 'USIA')) {
                            $colMap['umur'] = $colLetter;
                        } elseif (str_contains($cellUpper, 'TB') || str_contains($cellUpper, 'PB') || str_contains($cellUpper, 'TINGGI')) {
                            $colMap['tb'] = $colLetter;
                        } elseif (str_contains($cellUpper, 'BB') || str_contains($cellUpper, 'BERAT')) {
                            $colMap['bb'] = $colLetter;
                        }
                    }
                    break;
                }
            }

            // Jika header tidak terdeteksi via keyword, gunakan default posisi kolom
            if (!$headerRowIndex) {
                $headerRowIndex = 1;
            }

            // Iterasi baris data setelah header
            $dummyNikCounter = 1;
            foreach ($rows as $rowIndex => $row) {
                if ($rowIndex <= $headerRowIndex) {
                    continue;
                }

                // Ambil nilai per kolom
                $namaBalita = trim((string)($row[$colMap['nama'] ?? 'B'] ?? $row['B'] ?? ''));
                if (empty($namaBalita) || is_numeric($namaBalita) || strtoupper($namaBalita) === 'NAMA' || strtoupper($namaBalita) === 'JUMLAH') {
                    continue;
                }

                $nik = preg_replace('/[^0-9]/', '', (string)($row[$colMap['nik'] ?? 'A'] ?? ''));
                if (strlen($nik) !== 16) {
                    $nik = '3273' . str_pad((string)($posyandu->id * 10000 + $dummyNikCounter), 12, '0', STR_PAD_LEFT);
                    $dummyNikCounter++;
                }

                $jkRaw = strtoupper(trim((string)($row[$colMap['jk'] ?? 'C'] ?? 'L')));
                $jenisKelamin = (str_contains($jkRaw, 'P') || str_contains($jkRaw, 'W')) ? 'P' : 'L';

                // Parsing Tanggal Lahir
                $tglLahirVal = $row[$colMap['tgl_lahir'] ?? 'D'] ?? null;
                $tglLahir = Carbon::now()->subMonths(12)->format('Y-m-d');

                if (is_numeric($tglLahirVal)) {
                    $tglLahir = Carbon::instance(ExcelDate::excelToDateTimeObject($tglLahirVal))->format('Y-m-d');
                } elseif (!empty($tglLahirVal)) {
                    try {
                        $tglLahir = Carbon::parse($tglLahirVal)->format('Y-m-d');
                    } catch (\Exception $e) {
                        // fallback
                    }
                }

                $namaOrtu = trim((string)($row[$colMap['nama_ortu'] ?? 'E'] ?? 'Orang Tua Balita'));
                if (empty($namaOrtu)) {
                    $namaOrtu = 'Orang Tua ' . $namaBalita;
                }

                $alamat = trim((string)($row[$colMap['alamat'] ?? 'F'] ?? $posyanduNama));

                // Umur, TB, BB
                $umurBulan = (int) preg_replace('/[^0-9]/', '', (string)($row[$colMap['umur'] ?? 'G'] ?? '12'));
                if ($umurBulan < 0 || $umurBulan > 60) {
                    $umurBulan = 12;
                }

                $tbRaw = str_replace(',', '.', (string)($row[$colMap['tb'] ?? 'H'] ?? '75'));
                $tinggiBadan = (float) preg_replace('/[^0-9.]/', '', $tbRaw);
                if ($tinggiBadan <= 0) $tinggiBadan = 75.0;

                $bbRaw = str_replace(',', '.', (string)($row[$colMap['bb'] ?? 'I'] ?? '9.5'));
                $beratBadan = (float) preg_replace('/[^0-9.]/', '', $bbRaw);
                if ($beratBadan <= 0) $beratBadan = 9.5;

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

                // Tanggal Pemeriksaan Juli 2026
                $tanggalPemeriksaan = '2026-07-15';

                Pemeriksaan::updateOrCreate(
                    [
                        'balita_id'           => $balita->id,
                        'tanggal_pemeriksaan' => $tanggalPemeriksaan,
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
                        'catatan'            => 'Diimpor dari Excel Kelurahan Sukahaji (Juli 2026)',
                    ]
                );
                $totalPemeriksaan++;
            }
        }

        $this->command->info("Impor Berhasil! Total Balita: {$totalBalita}, Total Pemeriksaan: {$totalPemeriksaan}");
    }
}
