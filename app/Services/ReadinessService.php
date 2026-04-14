<?php

namespace App\Services;

use App\Models\ReadinessIndicator;
use Illuminate\Support\Collection;

class ReadinessService
{
    /**
     * Load all indicators with their current scores, grouped by category.
     * Returns an array with full dashboard metrics.
     */
    public function compute(): array
    {
        $indicators = ReadinessIndicator::with('score')
            ->orderBy('sort_order')
            ->get();

        $overallScore = 0;
        $totalWithEvidence = 0;
        $categories = [];

        foreach ($indicators as $ind) {
            $raw = $ind->score?->raw_score ?? 0;
            $points = $ind->computePoints($raw);

            $overallScore += $points;

            if ($ind->score?->isEvidenceCurrent()) {
                $totalWithEvidence++;
            }

            $cat = $ind->category;
            if (!isset($categories[$cat])) {
                $categories[$cat] = [
                    'name' => $cat,
                    'weight' => $ind->category_weight,
                    'points' => 0,
                    'indicators' => [],
                    'status' => 'red',
                ];
            }
            $categories[$cat]['points'] += $points;
            $categories[$cat]['indicators'][] = $ind;
        }

        // Category status
        foreach ($categories as &$cat) {
            $pct = ($cat['points'] / $cat['weight']) * 100;
            $cat['pct'] = round($pct);
            $cat['status'] = $this->categoryStatus($pct);
        }

        // Assurance
        $assurancePct = $indicators->count() > 0
            ? round(($totalWithEvidence / $indicators->count()) * 100)
            : 0;

        return [
            'overall_score'    => round($overallScore),
            'assurance_pct'    => $assurancePct,
            'assurance_grade'  => $this->assuranceGrade($assurancePct),
            'readiness_band'   => $this->readinessBand(round($overallScore)),
            'categories'       => $this->sortCategories($categories),
            'indicators'       => $indicators,
        ];
    }

    public function assuranceGrade(int $pct): string
    {
        return match(true) {
            $pct >= 90 => 'A',
            $pct >= 75 => 'B',
            $pct >= 60 => 'C',
            $pct >= 40 => 'D',
            default    => 'E',
        };
    }

    public function readinessBand(int $score): string
    {
        return match(true) {
            $score >= 85 => 'Operationally strong',
            $score >= 70 => 'Deployable with manageable gaps',
            $score >= 55 => 'Limited capability for defined tasks',
            $score >= 40 => 'Developmental',
            default      => 'Not fit to present externally',
        };
    }

    public function readinessBandColour(int $score): string
    {
        return match(true) {
            $score >= 85 => 'green',
            $score >= 70 => 'blue',
            $score >= 55 => 'amber',
            $score >= 40 => 'orange',
            default      => 'red',
        };
    }

    private function categoryStatus(float $pct): string
    {
        return match(true) {
            $pct >= 80 => 'green',
            $pct >= 60 => 'amber',
            default    => 'red',
        };
    }

    private function sortCategories(array $categories): array
    {
        $order = ReadinessIndicator::categoryOrder();
        uksort($categories, fn($a, $b) => array_search($a, $order) <=> array_search($b, $order));
        return array_values($categories);
    }

    /**
     * Build the published statement string (for LRF export).
     */
    public function buildPublishedStatement(array $metrics, array $serviceLevels): string
    {
        $sl = collect($serviceLevels)->pluck('value', 'key');

        return sprintf(
            '%s Readiness Score: %d/100. Assurance Grade: %s. ' .
            'Declared service level: %s operators deployable within 60 minutes; ' .
            '%s within 120 minutes; on-call team leader/controller: %s; ' .
            'modes: %s, %s; independent endurance: %s hours; ' .
            '24-hour operation: %s; area: %s; ' .
            'geographic limits: %s; caveats: %s',
            $sl['organisation_name'] ?? 'Liverpool RAYNET',
            $metrics['overall_score'],
            $metrics['assurance_grade'],
            $sl['operators_60min'] ?? '—',
            $sl['operators_120min'] ?? '—',
            $sl['oncall_team_leader'] ?? '—',
            $sl['voice_modes'] ?? '—',
            $sl['alternative_bearers'] ?? '—',
            $sl['endurance_hours'] ?? '—',
            $sl['sustain_24hr'] ?? '—',
            $sl['operating_area'] ?? '—',
            $sl['geographic_limits'] ?? '—',
            $sl['key_caveats'] ?? '—',
        );
    }
}
