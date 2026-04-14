<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Models\CommitteeAsset;
use Illuminate\Http\Request;

class AssetsController extends Controller
{
    public function index()
    {
        $assets = CommitteeAsset::orderBy('asset_type')->orderBy('description')->get();

        $totalQty       = $assets->sum('quantity');
        $serviceableQty = $assets->sum('serviceable_qty');
        $overdueTests   = $assets->filter(fn($a) => $a->isTestOverdue())->count();

        return view('committee.assets.index', compact('assets', 'totalQty', 'serviceableQty', 'overdueTests'));
    }

    public function create()
    {
        return view('committee.assets.form', ['asset' => new CommitteeAsset()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        CommitteeAsset::create(array_merge($data, ['created_by' => auth()->id()]));
        return redirect()->route('committee.assets.index')->with('success', 'Asset added.');
    }

    public function edit(CommitteeAsset $asset)
    {
        return view('committee.assets.form', compact('asset'));
    }

    public function update(Request $request, CommitteeAsset $asset)
    {
        $asset->update($this->validated($request));
        return redirect()->route('committee.assets.index')->with('success', 'Asset updated.');
    }

    public function destroy(CommitteeAsset $asset)
    {
        $asset->delete();
        return redirect()->route('committee.assets.index')->with('success', 'Asset removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'asset_type'          => 'required|string|max:80',
            'description'         => 'required|string|max:255',
            'serial_number'       => 'nullable|string|max:80',
            'quantity'            => 'required|integer|min:1',
            'serviceable_qty'     => 'required|integer|min:0',
            'last_test_date'      => 'nullable|date',
            'power_runtime_hours' => 'nullable|numeric|min:0',
            'location'            => 'nullable|string|max:120',
            'owner'               => 'nullable|string|max:120',
            'notes'               => 'nullable|string|max:1000',
        ]);
    }
}
