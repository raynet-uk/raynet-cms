<?php

namespace App\Http\Controllers;

use App\Models\MemberUnavailability;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AvailabilityController extends Controller
{
    public function index(): View
    {
        $user   = auth()->user();
        $periods = MemberUnavailability::where('user_id', $user->id)
            ->current()
            ->orderBy('from_date')
            ->get();

        $past = MemberUnavailability::where('user_id', $user->id)
            ->where('to_date', '<', now()->toDateString())
            ->orderByDesc('from_date')
            ->limit(10)
            ->get();

        return view('member.availability', compact('periods', 'past'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'from_date' => ['required', 'date', 'after_or_equal:today'],
            'to_date'   => ['required', 'date', 'after_or_equal:from_date'],
            'reason'    => ['nullable', 'string', 'max:200'],
        ], [
            'from_date.after_or_equal' => 'The start date cannot be in the past.',
            'to_date.after_or_equal'   => 'The end date must be on or after the start date.',
        ]);

        $data['user_id'] = auth()->id();

        // Check for overlapping periods
        $overlap = MemberUnavailability::where('user_id', auth()->id())
            ->current()
            ->overlapping(
                \Carbon\Carbon::parse($data['from_date']),
                \Carbon\Carbon::parse($data['to_date'])
            )
            ->first();

        if ($overlap) {
            return back()
                ->withInput()
                ->withErrors(['from_date' => "This overlaps an existing unavailability period ({$overlap->date_range})."]);
        }

        MemberUnavailability::create($data);

        return back()->with('success', 'Unavailability period added.');
    }

    public function destroy(MemberUnavailability $unavailability): RedirectResponse
    {
        abort_unless($unavailability->user_id === auth()->id(), 403);

        $unavailability->delete();

        return back()->with('success', 'Unavailability period removed.');
    }
}
