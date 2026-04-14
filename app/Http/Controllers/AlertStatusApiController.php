<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AlertStatus;

class AlertStatusApiController extends Controller
{
    /**
     * Handle GET or POST for global alert status
     */
    public function handle(Request $request)
    {
        // GET request: return current alert status
        if ($request->isMethod('get')) {
            $alert = AlertStatus::first();

            if (!$alert) {
                return response()->json([
                    'success' => false,
                    'message' => 'No alert record found.',
                ], 404);
            }

            return response()->json([
                'success'  => true,
                'level'    => $alert->level,
                'headline' => $alert->headline,
                'message'  => $alert->message,
                'meta'     => $alert->meta(),
            ]);
        }

        // POST request: update the alert status
        $request->validate([
            'level'    => 'required|integer|between:1,5',
            'headline' => 'nullable|string|max:100',
            'message'  => 'nullable|string|max:500',
        ]);

        $alert = AlertStatus::firstOrNew();

        $alert->level = $request->level;
        if ($request->has('headline')) $alert->headline = $request->headline;
        if ($request->has('message'))  $alert->message  = $request->message;

        $alert->save();

        return response()->json([
            'success'  => true,
            'level'    => $alert->level,
            'headline' => $alert->headline,
            'message'  => $alert->message,
            'meta'     => $alert->meta(),
        ]);
    }
}