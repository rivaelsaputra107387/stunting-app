<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

$excelPath = __DIR__ . '/Data posyandu kelurahan sukahaji bulan juli.xlsx';
$spreadsheet = IOFactory::load($excelPath);
$sheet = $spreadsheet->getActiveSheet();
$rows = $sheet->toArray(null, true, true, true);

$posyList = [];

for ($i = 2; $i <= count($rows); $i++) {
    $row = $rows[$i];
    $posy = trim((string)($row['I'] ?? ''));
    $rw = trim((string)($row['K'] ?? ''));
    if ($posy !== '') {
        $key = "$posy | RW: $rw";
        if (!isset($posyList[$key])) {
            $posyList[$key] = 0;
        }
        $posyList[$key]++;
    }
}

echo "=== UNIQUE POSYANDU VALUES IN EXCEL (" . count($posyList) . ") ===\n";
foreach ($posyList as $posyRw => $count) {
    echo "$posyRw => $count balita\n";
}
