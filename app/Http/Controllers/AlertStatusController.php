<?php

namespace App\Http\Controllers;

use App\Models\AlertStatus;
use Illuminate\Http\Request;

class AlertStatusController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'level'    => 'required|integer|min:1|max:5',
            'headline' => 'nullable|string|max:255',
            'message'  => 'nullable|string',
        ]);

        $status = AlertStatus::query()->first() ?? new AlertStatus();
        $status->fill($data);
        $status->save();

        return redirect()
            ->route('admin.dashboard')
            ->with('status', 'Alert level updated.');
    }
}