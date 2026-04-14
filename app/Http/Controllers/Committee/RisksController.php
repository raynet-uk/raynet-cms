<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Models\CommitteeRisk;
use App\Models\User;
use Illuminate\Http\Request;

class RisksController extends Controller
{
    public function index()
    {
        $risks = CommitteeRisk::with('owner')
            ->where('status', '!=', 'closed')
            ->orderByRaw('likelihood * impact DESC')
            ->get();

        $closed = CommitteeRisk::where('status', 'closed')
            ->orderByDesc('updated_at')->limit(10)->get();

        return view('committee.risks.index', compact('risks', 'closed'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('committee.risks.form', ['risk' => new CommitteeRisk(), 'users' => $users]);
    }

    public function store(Request $request)
    {
        CommitteeRisk::create(array_merge(
            $this->validated($request),
            ['created_by' => auth()->id()]
        ));
        return redirect()->route('committee.risks.index')->with('success', 'Risk logged.');
    }

    public function edit(CommitteeRisk $risk)
    {
        $users = User::orderBy('name')->get();
        return view('committee.risks.form', compact('risk', 'users'));
    }

    public function update(Request $request, CommitteeRisk $risk)
    {
        $risk->update($this->validated($request));
        return redirect()->route('committee.risks.index')->with('success', 'Risk updated.');
    }

    public function destroy(CommitteeRisk $risk)
    {
        $risk->delete();
        return redirect()->route('committee.risks.index')->with('success', 'Risk removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category'    => 'nullable|string|max:80',
            'likelihood'  => 'required|integer|min:1|max:5',
            'impact'      => 'required|integer|min:1|max:5',
            'mitigation'  => 'nullable|string|max:1000',
            'status'      => 'required|in:open,mitigated,accepted,closed',
            'review_date' => 'nullable|date',
            'owner_id'    => 'nullable|exists:users,id',
        ]);
    }
}
