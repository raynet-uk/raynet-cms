<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReadinessIndicator extends Model
{
    protected $fillable = [
        'code', 'category', 'category_weight', 'indicator_name',
        'evidence_examples', 'anchor_0', 'anchor_3', 'anchor_5',
        'indicator_weight', 'sort_order',
    ];

    public function score(): HasOne
    {
        return $this->hasOne(ReadinessScore::class, 'indicator_id');
    }

    /**
     * Points contributed at a given raw score.
     * Max points = indicator_weight (when raw_score = 5).
     */
    public function computePoints(int $rawScore): float
    {
        return round(($rawScore / 5) * $this->indicator_weight, 2);
    }

    /**
     * Category display order.
     */
    public static function categoryOrder(): array
    {
        return [
            'Mobilisation & availability',
            'Competence & leadership',
            'Communications capability',
            'Sustainment & logistics',
            'Interoperability & procedures',
            'Exercising, assurance & improvement',
        ];
    }
}
