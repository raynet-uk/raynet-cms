<?php

namespace App\Http\Controllers\Committee;

use App\Http\Controllers\Controller;
use App\Models\CommitteeAvailability;
use App\Models\User;
use Illuminate\Http\Request;

class PeopleController extends Controller
{
    public function index()
    {
        $members = User::with('availability')
            ->where('role', 'member')
            ->orWhere('role', 'committee')
            ->orderBy('name')
            ->get();

        $ops60  = $members->filter(fn($u) => $u->availability?->deployable_60min)->count();
        $ops120 = $members->filter(fn($u) => $u->availability?->deployable_120min)->count();
        $leaders = $members->filter(fn($u) => $u->availability?->is_team_leader)->count();

        return view('committee.people.index', compact('members', 'ops60', 'ops120', 'leaders'));
    }

    public function edit(User $user)
    {
        $availability = CommitteeAvailability::firstOrNew(['user_id' => $user->id]);
        return view('committee.people.edit', compact('user', 'availability'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'is_active_operator'       => 'boolean',
            'deployable_60min'         => 'boolean',
            'deployable_120min'        => 'boolean',
            'is_team_leader'           => 'boolean',
            'induction_current'        => 'boolean',
            'message_handling_current' => 'boolean',
            'digital_data_competent'   => 'boolean',
            'induction_date'           => 'nullable|date',
            'message_handling_date'    => 'nullable|date',
            'notes'                    => 'nullable|string|max:1000',
        ]);

        // Checkboxes that aren't ticked come through as absent — normalise to false
        foreach (['is_active_operator','deployable_60min','deployable_120min','is_team_leader',
                  'induction_current','message_handling_current','digital_data_competent'] as $bool) {
            $validated[$bool] = $request->boolean($bool);
        }

        CommitteeAvailability::updateOrCreate(
            ['user_id' => $user->id],
            array_merge($validated, ['updated_by' => auth()->id()])
        );

        return redirect()->route('committee.people.index')->with('success', "{$user->name} updated.");
    }
}
