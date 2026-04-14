<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Models\CommitteeAction;
use App\Models\CommitteeAvailability;
use App\Models\CommitteeExercise;
use App\Models\CommitteeNetwork;
use App\Models\CommitteeRisk;
use App\Models\CommitteeAsset;
use App\Services\ReadinessService;

class CommitteeDashboardController extends Controller
{
    public function __construct(private ReadinessService $readiness) {}

    public function index()
    {
        // Readiness metrics
        $metrics = $this->readiness->compute();

        // People & availability
        $activeOperators = CommitteeAvailability::where('is_active_operator', true)->count();
        $ops60 = CommitteeAvailability::where('deployable_60min', true)->count();
        $ops120 = CommitteeAvailability::where('deployable_120min', true)->count();

        // Training currency
        $totalActive = CommitteeAvailability::where('is_active_operator', true)->count();
        $inductionCurrent = CommitteeAvailability::where('is_active_operator', true)
            ->where('induction_current', true)->count();
        $trainingCurrencyPct = $totalActive > 0 ? round(($inductionCurrent / $totalActive) * 100) : 0;

        // Equipment serviceability
        $assets = CommitteeAsset::all();
        $totalQty = $assets->sum('quantity');
        $serviceableQty = $assets->sum('serviceable_qty');
        $equipmentPct = $totalQty > 0 ? round(($serviceableQty / $totalQty) * 100) : 0;

        // Network status summary
        $networks = CommitteeNetwork::all();
        $operationalNets = $networks->where('status', 'operational')->count();
        $totalNets = $networks->count();

        // Actions
        $openActions = CommitteeAction::whereNotIn('status', ['closed', 'cancelled'])->count();
        $overdueActions = CommitteeAction::where('due_date', '<', now())
            ->whereNotIn('status', ['closed', 'cancelled'])->count();

        // Next exercise
        $nextExercise = CommitteeExercise::where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->first();

        // Key open risks (score >= 9)
        $keyRisks = CommitteeRisk::where('status', 'open')
            ->whereRaw('likelihood * impact >= 9')
            ->orderByRaw('likelihood * impact DESC')
            ->limit(5)
            ->get();

        return view('committee.dashboard', compact(
            'metrics', 'ops60', 'ops120', 'activeOperators',
            'trainingCurrencyPct', 'equipmentPct',
            'operationalNets', 'totalNets',
            'openActions', 'overdueActions',
            'nextExercise', 'keyRisks'
        ));
    }
}
