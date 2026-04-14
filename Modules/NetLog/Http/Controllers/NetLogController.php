<?php

namespace Modules\NetLog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NetLogController extends Controller
{
    public function index()
    {
        $sessions = DB::table('net_sessions')->orderByDesc('created_at')->paginate(20);
        $open     = DB::table('net_sessions')->where('status', 'open')->first();
        return view('netlog::admin.index', compact('sessions', 'open'));
    }

    public function openSession(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'frequency'   => 'nullable|string|max:30',
            'net_control' => 'nullable|string|max:20',
            'notes'       => 'nullable|string',
        ]);

        DB::table('net_sessions')->insert([
            ...$data,
            'status'     => 'open',
            'opened_by'  => auth()->id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Net session opened.');
    }

    public function closeSession(int $id)
    {
        DB::table('net_sessions')->where('id', $id)->update([
            'status'     => 'closed',
            'closed_at'  => now(),
            'updated_at' => now(),
        ]);
        return back()->with('success', 'Session closed.');
    }

    public function deleteSession(int $id)
    {
        DB::table('net_sessions')->where('id', $id)->delete();
        return back()->with('success', 'Session deleted.');
    }

    public function addCheckin(Request $request, int $id)
    {
        $data = $request->validate([
            'callsign'      => 'required|string|max:20',
            'name'          => 'nullable|string|max:100',
            'location'      => 'nullable|string|max:100',
            'signal_report' => 'nullable|string|max:10',
            'message'       => 'nullable|string',
        ]);

        DB::table('net_checkins')->insert([
            ...$data,
            'net_session_id' => $id,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return back()->with('success', "Check-in recorded for {$data['callsign']}.");
    }

    public function deleteCheckin(int $id)
    {
        DB::table('net_checkins')->where('id', $id)->delete();
        return back()->with('success', 'Check-in removed.');
    }

    public function export(int $id)
    {
        $session  = DB::table('net_sessions')->findOrFail($id);
        $checkins = DB::table('net_checkins')->where('net_session_id', $id)->orderBy('created_at')->get();

        $csv = "Callsign,Name,Location,Signal Report,Message,Time\n";
        foreach ($checkins as $c) {
            $csv .= implode(',', array_map(
                fn($v) => '"' . str_replace('"', '""', $v ?? '') . '"',
                [$c->callsign, $c->name, $c->location, $c->signal_report, $c->message, $c->created_at]
            )) . "\n";
        }

        $filename = 'net-log-' . \Illuminate\Support\Str::slug($session->name) . '-' . date('Ymd') . '.csv';

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
