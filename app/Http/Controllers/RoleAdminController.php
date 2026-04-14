<?php

namespace App\Http\Controllers;

use App\Models\MemberRole as Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleAdminController extends Controller
{
    /**
     * Roles admin – list + add/edit in one place.
     */
    public function index(Request $request)
    {
        $roles = Role::orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $editingRole = null;

        if ($request->filled('edit')) {
            $editingRole = Role::find($request->input('edit'));
        }

        return view('admin.roles.index', compact('roles', 'editingRole'));
    }

    /**
     * Store a new role.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'sort_order'  => 'nullable|integer',
            'colour'      => 'nullable|string|max:7',
            'description' => 'nullable|string',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;

        // I auto-generate a slug from the name – unique per role
        $baseSlug = Str::slug($data['name']);
        $slug = $baseSlug;
        $i = 1;
        while (Role::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$i++;
        }
        $data['slug'] = $slug;

        Role::create($data);

        return redirect()
            ->route('admin.roles')
            ->with('status', 'Role created.');
    }

    /**
     * Update an existing role.
     */
    public function update(Request $request, int $id)
    {
        $role = Role::findOrFail($id);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'sort_order'  => 'nullable|integer',
            'colour'      => 'nullable|string|max:7',
            'description' => 'nullable|string',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;

        // If I change the name, I regenerate the slug only if it was never customised
        if ($role->name !== $data['name']) {
            $baseSlug = Str::slug($data['name']);
            $slug = $baseSlug;
            $i = 1;
            while (Role::where('slug', $slug)->where('id', '!=', $role->id)->exists()) {
                $slug = $baseSlug.'-'.$i++;
            }
            $data['slug'] = $slug;
        }

        $role->update($data);

        return redirect()
            ->route('admin.roles', ['edit' => $role->id])
            ->with('status', 'Role updated.');
    }

    /**
     * Delete a role.
     */
    public function delete(int $id)
    {
        $role = Role::findOrFail($id);

        // Later I might want to protect roles that are in use; for now I allow delete.
        $role->delete();

        return redirect()
            ->route('admin.roles')
            ->with('status', 'Role deleted.');
    }
}