<?php

namespace App\Services;

use App\Models\Pemeriksaan;
use Illuminate\Support\Facades\Cache;

class C45Service
{
    private const CACHE_KEY = 'c45_decision_tree_model';

    /**
     * Train the C4.5 model using data from the database and cache the tree.
     */
    public function train(): void
    {
        // Get all data for training
        $pemeriksaans = Pemeriksaan::with('balita')->get();

        if ($pemeriksaans->isEmpty()) {
            return;
        }

        $dataset = [];
        foreach ($pemeriksaans as $p) {
            if (!$p->balita || !$p->status_stunting) {
                continue;
            }

            $dataset[] = [
                'umur_bulan' => $p->umur_bulan,
                'jenis_kelamin' => $p->balita->jenis_kelamin,
                'tinggi_badan' => (float) $p->tinggi_badan,
                'berat_badan' => (float) $p->berat_badan,
                'label' => $p->status_stunting
            ];
        }

        if (empty($dataset)) {
            return;
        }

        $attributes = ['umur_bulan', 'jenis_kelamin', 'tinggi_badan', 'berat_badan'];
        $tree = $this->buildTree($dataset, $attributes);

        Cache::put(self::CACHE_KEY, $tree);
    }

    /**
     * Predict using the trained tree.
     */
    public function predict(int $umurBulan, string $jenisKelamin, float $tinggiBadan, float $beratBadan): string
    {
        $tree = Cache::get(self::CACHE_KEY);

        if (!$tree) {
            // Fallback if not trained
            return 'Normal';
        }

        $instance = [
            'umur_bulan' => $umurBulan,
            'jenis_kelamin' => $jenisKelamin,
            'tinggi_badan' => $tinggiBadan,
            'berat_badan' => $beratBadan
        ];

        return $this->traverseTree($tree, $instance);
    }

    private function traverseTree(array $node, array $instance): string
    {
        if (isset($node['label'])) {
            return $node['label'];
        }

        $attribute = $node['attribute'];
        $val = $instance[$attribute];

        if ($node['is_continuous']) {
            if ($val <= $node['threshold']) {
                return $this->traverseTree($node['children']['left'], $instance);
            } else {
                return $this->traverseTree($node['children']['right'], $instance);
            }
        } else {
            if (isset($node['children'][$val])) {
                return $this->traverseTree($node['children'][$val], $instance);
            }
            // fallback to majority if unseen category
            return $node['majority_class'];
        }
    }

    private function buildTree(array $dataset, array $attributes, int $depth = 0, int $maxDepth = 4): array
    {
        $labels = array_column($dataset, 'label');
        $uniqueLabels = array_unique($labels);

        // If all instances have the same label, return it as leaf
        if (count($uniqueLabels) === 1) {
            return ['label' => reset($uniqueLabels)];
        }

        // If no attributes left or max depth reached, return majority class
        if (empty($attributes) || $depth >= $maxDepth) {
            return ['label' => $this->getMajorityClass($labels)];
        }

        $bestSplit = $this->findBestSplit($dataset, $attributes);

        if ($bestSplit['gain'] <= 0) {
            return ['label' => $this->getMajorityClass($labels)];
        }

        $node = [
            'attribute' => $bestSplit['attribute'],
            'is_continuous' => $bestSplit['is_continuous'],
            'majority_class' => $this->getMajorityClass($labels),
            'children' => []
        ];

        // Remove the split attribute if it's categorical
        $nextAttributes = $attributes;
        if (!$bestSplit['is_continuous']) {
            $nextAttributes = array_diff($attributes, [$bestSplit['attribute']]);
        }

        foreach ($bestSplit['splits'] as $key => $subset) {
            if (empty($subset)) {
                $node['children'][$key] = ['label' => $this->getMajorityClass($labels)];
            } else {
                $node['children'][$key] = $this->buildTree($subset, $nextAttributes, $depth + 1, $maxDepth);
            }
        }

        if ($bestSplit['is_continuous']) {
            $node['threshold'] = $bestSplit['threshold'];
        }

        return $node;
    }

    private function findBestSplit(array $dataset, array $attributes): array
    {
        $baseEntropy = $this->calculateEntropy(array_column($dataset, 'label'));
        $bestGain = -1;
        $bestSplit = null;

        foreach ($attributes as $attribute) {
            $isContinuous = in_array($attribute, ['umur_bulan', 'tinggi_badan', 'berat_badan']);

            if ($isContinuous) {
                // Sort by attribute value
                usort($dataset, fn($a, $b) => $a[$attribute] <=> $b[$attribute]);
                
                // Find all possible thresholds (midpoints between class changes)
                $thresholds = [];
                for ($i = 0; $i < count($dataset) - 1; $i++) {
                    if ($dataset[$i]['label'] !== $dataset[$i+1]['label'] && $dataset[$i][$attribute] !== $dataset[$i+1][$attribute]) {
                        $thresholds[] = ($dataset[$i][$attribute] + $dataset[$i+1][$attribute]) / 2;
                    }
                }

                foreach ($thresholds as $threshold) {
                    $splits = ['left' => [], 'right' => []];
                    foreach ($dataset as $row) {
                        if ($row[$attribute] <= $threshold) {
                            $splits['left'][] = $row;
                        } else {
                            $splits['right'][] = $row;
                        }
                    }

                    $gain = $this->calculateInformationGain($baseEntropy, count($dataset), $splits);
                    if ($gain > $bestGain) {
                        $bestGain = $gain;
                        $bestSplit = [
                            'attribute' => $attribute,
                            'is_continuous' => true,
                            'threshold' => $threshold,
                            'splits' => $splits,
                            'gain' => $gain
                        ];
                    }
                }
            } else {
                $splits = [];
                foreach ($dataset as $row) {
                    $val = $row[$attribute];
                    $splits[$val][] = $row;
                }

                $gain = $this->calculateInformationGain($baseEntropy, count($dataset), $splits);
                if ($gain > $bestGain) {
                    $bestGain = $gain;
                    $bestSplit = [
                        'attribute' => $attribute,
                        'is_continuous' => false,
                        'splits' => $splits,
                        'gain' => $gain
                    ];
                }
            }
        }

        return $bestSplit ?? ['gain' => 0];
    }

    private function calculateEntropy(array $labels): float
    {
        $counts = array_count_values($labels);
        $total = count($labels);
        $entropy = 0;

        foreach ($counts as $count) {
            $p = $count / $total;
            $entropy -= $p * log($p, 2);
        }

        return $entropy;
    }

    private function calculateInformationGain(float $baseEntropy, int $totalItems, array $splits): float
    {
        $newEntropy = 0;

        foreach ($splits as $subset) {
            $subsetCount = count($subset);
            if ($subsetCount > 0) {
                $p = $subsetCount / $totalItems;
                $newEntropy += $p * $this->calculateEntropy(array_column($subset, 'label'));
            }
        }

        return $baseEntropy - $newEntropy;
    }

    private function getMajorityClass(array $labels): string
    {
        $counts = array_count_values($labels);
        arsort($counts);
        return array_key_first($counts);
    }
}
