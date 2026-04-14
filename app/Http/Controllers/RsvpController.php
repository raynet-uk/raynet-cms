<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRsvp;
use Illuminate\Http\Request;

class RsvpController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'status' => ['required', 'in:attending,maybe,declined'],
            'note'   => ['nullable', 'string', 'max:200'],
        ]);

        EventRsvp::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => auth()->id()],
            ['status'   => $request->status, 'note' => $request->note]
        );

        return back()->with('rsvp_saved', $request->status);
    }

    public function destroy(Event $event)
    {
        EventRsvp::where('event_id', $event->id)
                 ->where('user_id', auth()->id())
                 ->delete();

        return back()->with('rsvp_saved', 'removed');
    }
}