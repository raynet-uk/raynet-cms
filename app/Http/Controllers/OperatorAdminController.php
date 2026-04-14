<?php

namespace App\Http\Controllers;

use App\Models\Operator;
use App\Models\Role;
use Illuminate\Http\Request;

class OperatorAdminController extends Controller
{
    /**
     * Operator admin – list + add/edit operators.
     */
    public function index(Request $request)
    {
        $operators = Operator::orderBy('name')
            ->orderBy('callsign')
            ->paginate(15);

        $editingOperator = null;

        if ($request->filled('edit')) {
            $editingOperator = Operator::find($request->input('edit'));
        }

        // I pull roles from the roles table so the dropdown stays centralised
        $roles = Role::orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $statuses = ['Active', 'Training', 'On hold', 'Inactive'];

        return view('admin.operators.index', compact(
            'operators',
            'editingOperator',
            'roles',
            'statuses'
        ));
    }

    /**
     * Store a new operator.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'callsign' => 'nullable|string|max:50',
            'role'     => 'nullable|string|max:255',
            'level'    => 'nullable|string|max:50',
            'status'   => 'required|string|max:50',
            'is_admin' => 'nullable|boolean',
        ]);

        $data['is_admin'] = (bool) $request->boolean('is_admin');

        Operator::create($data);

        return redirect()
            ->route('admin.operators')
            ->with('status', 'Operator created.');
    }

    /**
     * Update an operator.
     */
    public function update(Request $request, int $id)
    {
        $operator = Operator::findOrFail($id);

        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'callsign' => 'nullable|string|max:50',
            'role'     => 'nullable|string|max:255',
            'level'    => 'nullable|string|max:50',
            'status'   => 'required|string|max:50',
            'is_admin' => 'nullable|boolean',
        ]);

        $data['is_admin'] = (bool) $request->boolean('is_admin');

        $operator->update($data);

        return redirect()
            ->route('admin.operators', ['edit' => $operator->id])
            ->with('status', 'Operator updated.');
    }

    /**
     * Delete an operator record.
     */
    public function delete(int $id)
    {
        $operator = Operator::findOrFail($id);
        $operator->delete();

        return redirect()
            ->route('admin.operators')
            ->with('status', 'Operator deleted.');
    }
}