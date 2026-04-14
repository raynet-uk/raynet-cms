<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EquipmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Equipment::with('user')->where('is_active', true);

        if ($s = $request->input('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('make', 'like', "%{$s}%")
                  ->orWhere('model', 'like', "%{$s}%")
                  ->orWhere('callsign', 'like', "%{$s}%")
                  ->orWhere('serial_number', 'like', "%{$s}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%"));
            });
        }

        if ($type = $request->input('type')) {
            $query->where('equipment_type', $type);
        }

        if ($request->input('overdue')) {
            $query->where(function ($q) {
                $q->where('last_tested_date', '<', now()->subYear())
                  ->orWhereNull('last_tested_date')
                  ->orWhere('next_test_due', '<', now());
            });
        }

        $equipment   = $query->orderByRaw("CASE WHEN next_test_due < NOW() OR (last_tested_date < DATE_SUB(NOW(), INTERVAL 1 YEAR)) THEN 0 ELSE 1 END, make, model")->paginate(40)->withQueryString();
        $members     = User::orderBy('name')->get(['id','name','callsign']);
        $overdueCount = Equipment::where('is_active', true)
            ->where(fn($q) => $q->where('last_tested_date', '<', now()->subYear())
                ->orWhereNull('last_tested_date')
                ->orWhere('next_test_due', '<', now()))
            ->count();

        return view('admin.equipment.index', compact('equipment', 'members', 'overdueCount'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id'          => ['nullable', 'exists:users,id'],
            'make'             => ['required', 'string', 'max:80'],
            'model'            => ['required', 'string', 'max:80'],
            'serial_number'    => ['nullable', 'string', 'max:80'],
            'callsign'         => ['nullable', 'string', 'max:20'],
            'licence_class'    => ['nullable', 'string', 'max:40'],
            'equipment_type'   => ['required', 'in:' . implode(',', array_keys(Equipment::TYPES))],
            'last_tested_date' => ['nullable', 'date'],
            'next_test_due'    => ['nullable', 'date'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ]);

        Equipment::create($data);

        return back()->with('success', "Equipment added: {$data['make']} {$data['model']}.");
    }

    public function update(Request $request, Equipment $equipment): RedirectResponse
    {
        $data = $request->validate([
            'user_id'          => ['nullable', 'exists:users,id'],
            'make'             => ['required', 'string', 'max:80'],
            'model'            => ['required', 'string', 'max:80'],
            'serial_number'    => ['nullable', 'string', 'max:80'],
            'callsign'         => ['nullable', 'string', 'max:20'],
            'licence_class'    => ['nullable', 'string', 'max:40'],
            'equipment_type'   => ['required', 'in:' . implode(',', array_keys(Equipment::TYPES))],
            'last_tested_date' => ['nullable', 'date'],
            'next_test_due'    => ['nullable', 'date'],
            'notes'            => ['nullable', 'string', 'max:1000'],
        ]);

        $equipment->update($data);

        return back()->with('success', "Equipment updated: {$equipment->display_name}.");
    }

    public function destroy(Equipment $equipment): RedirectResponse
    {
        $name = $equipment->display_name;
        $equipment->update(['is_active' => false]);

        return back()->with('success', "{$name} removed from registry.");
    }

    public function export(): StreamedResponse
    {
        $rows = Equipment::with('user')->where('is_active', true)->orderBy('make')->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="equipment-registry-' . now()->format('Y-m-d') . '.csv"',
        ];

        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Member', 'Make', 'Model', 'Serial No', 'Callsign', 'Licence Class', 'Type', 'Last Tested', 'Next Test Due', 'Status', 'Notes']);
            foreach ($rows as $e) {
                $status = $e->testStatusBadge()['label'];
                fputcsv($out, [
                    $e->user?->name ?? '—',
                    $e->make, $e->model,
                    $e->serial_number ?? '',
                    $e->callsign ?? '',
                    $e->licence_class ?? '',
                    $e->type_label,
                    $e->last_tested_date?->format('d/m/Y') ?? '',
                    $e->next_test_due?->format('d/m/Y') ?? '',
                    $status,
                    $e->notes ?? '',
                ]);
            }
            fclose($out);
        }, 200, $headers);
    }
}