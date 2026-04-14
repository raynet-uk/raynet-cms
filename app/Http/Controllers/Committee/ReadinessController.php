<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Models\ReadinessIndicator;
use App\Models\ReadinessScore;
use App\Services\ReadinessService;
use Illuminate\Http\Request;

class ReadinessController extends Controller
{
    public function __construct(private ReadinessService $readiness) {}

    public function index()
    {
        $metrics = $this->readiness->compute();
        return view('committee.readiness.index', compact('metrics'));
    }

    public function matrix()
    {
        $indicators = ReadinessIndicator::with('score')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('category');

        return view('committee.readiness.matrix', compact('indicators'));
    }

    public function updateScore(Request $request, ReadinessIndicator $indicator)
    {
        $validated = $request->validate([
            'raw_score'     => 'required|integer|min:0|max:5',
            'evidence_ref'  => 'nullable|string|max:255',
            'evidence_date' => 'nullable|date|before_or_equal:today',
            'notes'         => 'nullable|string|max:1000',
        ]);

        ReadinessScore::updateOrCreate(
            ['indicator_id' => $indicator->id],
            array_merge($validated, ['scored_by' => auth()->id()])
        );

        return back()->with('success', "Score updated for {$indicator->code}.");
    }

    public function lrf()
    {
        $metrics = $this->readiness->compute();
        $serviceLevels = \DB::table('committee_service_levels')->get();
        $statement = $this->readiness->buildPublishedStatement($metrics, $serviceLevels->toArray());

        return view('committee.readiness.lrf', compact('metrics', 'serviceLevels', 'statement'));
    }

    public function updateServiceLevels(Request $request)
    {
        $validated = $request->validate([
            'levels'                 => 'required|array',
            'levels.*.key'           => 'required|string|max:80',
            'levels.*.value'         => 'nullable|string|max:500',
        ]);

        foreach ($validated['levels'] as $level) {
            \DB::table('committee_service_levels')
                ->where('key', $level['key'])
                ->update([
                    'value'      => $level['value'],
                    'updated_by' => auth()->id(),
                    'updated_at' => now(),
                ]);
        }

        return back()->with('success', 'Service levels saved.');
    }
}
