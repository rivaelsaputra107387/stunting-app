<?php

namespace App\Services;

use App\Models\Pemeriksaan;

class ConfusionMatrixService
{
    /**
     * Calculate Confusion Matrix and Evaluation Metrics for Stunting Classification.
     *
     * Ground Truth (Actual): status_stunting (calculated via Z-Score TB/U)
     * Prediction (Predicted): Decision Tree classification result
     *
     * Classes: ['Normal', 'Risk of Stunting', 'Stunting']
     *
     * @param iterable $pemeriksaans
     * @param DecisionTreeService $dtService
     * @return array
     */
    public function calculate(iterable $pemeriksaans, DecisionTreeService $dtService): array
    {
        $classes = ['Normal', 'Risk of Stunting', 'Stunting'];

        // Initialize 3x3 matrix [actual][predicted] = count
        $matrix = [];
        foreach ($classes as $actual) {
            foreach ($classes as $predicted) {
                $matrix[$actual][$predicted] = 0;
            }
        }

        $totalData = 0;
        $correctPredictions = 0;
        $detailedResults = [];

        foreach ($pemeriksaans as $p) {
            $balita = $p->balita;
            if (!$balita) continue;

            $actual = $p->status_stunting ?? 'Normal';
            if (!in_array($actual, $classes)) {
                $actual = 'Normal';
            }

            // Calculate Decision Tree prediction
            $dtHasil = $dtService->classify(
                $p->umur_bulan,
                $balita->jenis_kelamin,
                (float) $p->tinggi_badan,
                (float) $p->berat_badan
            );
            $predicted = $dtHasil['status'];
            if (!in_array($predicted, $classes)) {
                $predicted = 'Normal';
            }

            $matrix[$actual][$predicted]++;
            $totalData++;

            if ($actual === $predicted) {
                $correctPredictions++;
            }

            $detailedResults[$p->id] = [
                'actual' => $actual,
                'predicted' => $predicted,
                'is_match' => ($actual === $predicted),
                'zscore_tb_u' => $p->zscore_tb_u,
                'zscore_bb_u' => $p->zscore_bb_u,
            ];
        }

        // Overall Accuracy
        $accuracy = $totalData > 0 ? round(($correctPredictions / $totalData) * 100, 2) : 0.0;

        // Calculate Precision, Recall, F1 per class
        $metricsPerClass = [];
        $sumPrecision = 0;
        $sumRecall = 0;
        $sumF1 = 0;

        foreach ($classes as $cls) {
            // True Positive (TP): actual = cls, predicted = cls
            $tp = $matrix[$cls][$cls];

            // False Positive (FP): actual != cls, predicted = cls
            $fp = 0;
            foreach ($classes as $act) {
                if ($act !== $cls) {
                    $fp += $matrix[$act][$cls];
                }
            }

            // False Negative (FN): actual = cls, predicted != cls
            $fn = 0;
            foreach ($classes as $pred) {
                if ($pred !== $cls) {
                    $fn += $matrix[$cls][$pred];
                }
            }

            // True Negative (TN): actual != cls, predicted != cls
            $tn = 0;
            foreach ($classes as $act) {
                if ($act !== $cls) {
                    foreach ($classes as $pred) {
                        if ($pred !== $cls) {
                            $tn += $matrix[$act][$pred];
                        }
                    }
                }
            }

            $precision = ($tp + $fp) > 0 ? ($tp / ($tp + $fp)) * 100 : 0.0;
            $recall = ($tp + $fn) > 0 ? ($tp / ($tp + $fn)) * 100 : 0.0;
            $f1 = ($precision + $recall) > 0 ? 2 * ($precision * $recall) / ($precision + $recall) : 0.0;

            $metricsPerClass[$cls] = [
                'tp' => $tp,
                'fp' => $fp,
                'fn' => $fn,
                'tn' => $tn,
                'precision' => round($precision, 2),
                'recall' => round($recall, 2),
                'f1_score' => round($f1, 2),
            ];

            $sumPrecision += $precision;
            $sumRecall += $recall;
            $sumF1 += $f1;
        }

        $macroPrecision = count($classes) > 0 ? round($sumPrecision / count($classes), 2) : 0.0;
        $macroRecall = count($classes) > 0 ? round($sumRecall / count($classes), 2) : 0.0;
        $macroF1 = count($classes) > 0 ? round($sumF1 / count($classes), 2) : 0.0;

        return [
            'matrix' => $matrix,
            'classes' => $classes,
            'total_data' => $totalData,
            'correct_predictions' => $correctPredictions,
            'accuracy' => $accuracy,
            'metrics_per_class' => $metricsPerClass,
            'macro_precision' => $macroPrecision,
            'macro_recall' => $macroRecall,
            'macro_f1' => $macroF1,
            'detailed_results' => $detailedResults,
        ];
    }
}
