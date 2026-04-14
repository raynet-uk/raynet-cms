<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Spatie\Permission\Models\Role;

class UserRoleController extends Controller
{
    /**
     * Display the roles management page.
     */
    public function index(Request $request)
    {
        $search     = $request->get('search', '');
        $roleFilter = $request->get('role', '');
        $sort       = $request->get('sort', 'name');

        $query = User::query()
            ->when($search, fn($q) => $q->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('callsign', 'like', "%{$search}%");
            }))
            ->when($roleFilter, fn($q) => $q->whereHas('roles', fn($q) => $q->where('name', $roleFilter)))
            ->with('roles');

        // Sorting
        match($sort) {
            'name_desc'  => $query->orderByDesc('name'),
            'role'       => $query->orderBy('name'), // role sort handled client-side; fallback to name
            'joined'     => $query->orderByDesc('created_at'),
            'joined_asc' => $query->orderBy('created_at'),
            default      => $query->orderBy('name'),
        };

        $users = $query->paginate(50)->withQueryString();

        $roles      = Role::orderBy('name')->get();
        $roleCounts = Role::withCount('users')->orderBy('name')->get();

        return view('admin.users.roles', compact('users', 'roles', 'roleCounts', 'search', 'roleFilter'));
    }

    /**
     * Update a single user's role.
     */
    public function update(Request $request, User $user): RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        // Prevent demoting a super-admin unless you are one
        $isJson = request()->expectsJson();

        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            if ($isJson) return response()->json(['success' => false, 'message' => 'Only a super-admin can change another super-admin role.'], 403);
            return back()->with('error', 'Only a super-admin can change another super-admin\'s role.');
        }

        if ($user->id === auth()->id()) {
            if ($isJson) return response()->json(['success' => false, 'message' => 'You cannot change your own role.'], 403);
            return back()->with('error', 'You cannot change your own role.');
        }

        $oldRole = $user->getRoleNames()->first() ?? 'none';
        $newRole = $request->role;

        $user->syncRoles([$newRole]);

        // Keep legacy boolean columns in sync
        $user->update([
            'is_admin'       => in_array($newRole, ['admin', 'super-admin']),
            'is_super_admin' => $newRole === 'super-admin',
        ]);

\App\Helpers\AuditLogger::log(
    'user.role_changed',
    $user,
    "Role changed from {$oldRole} to {$newRole} for {$user->name}",
    ['role' => $oldRole],
    ['role' => $newRole]
);
        // Return JSON for AJAX inline saves, redirect for form posts
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$user->name}'s role changed to {$newRole}.",
            ]);
        }

        return back()->with('success', "{$user->name}'s role changed from {$oldRole} to {$newRole}.");
    }

    /**
     * Bulk update roles for multiple users.
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'user_ids'   => ['required', 'array'],
            'user_ids.*' => ['exists:users,id'],
            'role'       => ['required', 'string', 'exists:roles,name'],
        ]);

        $newRole    = $request->role;
        $count      = 0;
        $skipped    = 0;

        foreach ($request->user_ids as $userId) {
            $user = User::find($userId);
            if (!$user) continue;

            // Skip super-admins and self
            if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) { $skipped++; continue; }
            if ($user->id === auth()->id()) { $skipped++; continue; }

            $user->syncRoles([$newRole]);
            $user->update([
                'is_admin'       => in_array($newRole, ['admin', 'super-admin']),
                'is_super_admin' => $newRole === 'super-admin',
            ]);
            $count++;
        }

        $msg = "Updated {$count} users to {$newRole}.";
        if ($skipped) $msg .= " Skipped {$skipped} (super-admins or self).";

        return back()->with('success', $msg);
    }
}