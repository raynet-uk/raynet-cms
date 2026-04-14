<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Models\CommitteeAction;
use App\Models\User;
use Illuminate\Http\Request;

class ActionsController extends Controller
{
    public function index()
    {
        $open = CommitteeAction::with('owner')
            ->whereNotIn('status', ['closed', 'cancelled'])
            ->orderByRaw("FIELD(priority,'critical','high','medium','low')")
            ->orderBy('due_date')
            ->get();

        $closed = CommitteeAction::with('owner')
            ->whereIn('status', ['closed', 'cancelled'])
            ->orderByDesc('closed_date')
            ->limit(20)
            ->get();

        $overdue = $open->filter(fn($a) => $a->isOverdue())->count();

        return view('committee.actions.index', compact('open', 'closed', 'overdue'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('committee.actions.form', ['action' => new CommitteeAction(), 'users' => $users]);
    }

    public function store(Request $request)
    {
        CommitteeAction::create(array_merge(
            $this->validated($request),
            ['created_by' => auth()->id()]
        ));
        return redirect()->route('committee.actions.index')->with('success', 'Action logged.');
    }

    public function edit(CommitteeAction $action)
    {
        $users = User::orderBy('name')->get();
        return view('committee.actions.form', compact('action', 'users'));
    }

    public function update(Request $request, CommitteeAction $action)
    {
        $data = $this->validated($request);

        // Auto-set closed_date when status set to closed
        if ($data['status'] === 'closed' && !$action->closed_date) {
            $data['closed_date'] = now()->toDateString();
        }

        $action->update($data);
        return redirect()->route('committee.actions.index')->with('success', 'Action updated.');
    }

    public function close(Request $request, CommitteeAction $action)
    {
        $request->validate(['closure_notes' => 'nullable|string|max:1000']);

        $action->update([
            'status'        => 'closed',
            'closed_date'   => now()->toDateString(),
            'closure_notes' => $request->closure_notes,
        ]);

        return back()->with('success', 'Action closed.');
    }

    public function destroy(CommitteeAction $action)
    {
        $action->delete();
        return redirect()->route('committee.actions.index')->with('success', 'Action removed.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string|max:1000',
            'source'        => 'required|in:exercise,deployment,risk,committee,inspection,other',
            'source_ref'    => 'nullable|string|max:80',
            'owner_id'      => 'nullable|exists:users,id',
            'due_date'      => 'nullable|date',
            'priority'      => 'required|in:low,medium,high,critical',
            'status'        => 'required|in:open,in_progress,closed,overdue,cancelled',
            'closure_notes' => 'nullable|string|max:1000',
        ]);
    }
}
