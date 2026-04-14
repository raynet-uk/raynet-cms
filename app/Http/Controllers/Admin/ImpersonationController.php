<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Helpers\AuditLogger;

class ImpersonationController extends Controller
{
    public function impersonate(User $user)
    {
        $currentUser = auth()->user();

        if ($currentUser->id !== 2 && $user->is_admin) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Cannot impersonate an admin user.');
        }

        $this->logImpersonation($currentUser, $user, 'start');

        // Snapshot admin identity BEFORE switching (session will be regenerated)
        $adminId = auth()->id();
        $adminName = $currentUser->name;
        $adminFlag = session('admin_authenticated');
        $adminFlagName = session('admin_name');

        // Switch to the target user
        auth()->login($user);

        // Restore admin context into the new session
        session([
            'original_admin_id'       => $adminId,
            'original_admin_name'     => $adminName,
            'original_admin_flag'     => $adminFlag,
            'original_admin_flag_name'=> $adminFlagName,
        ]);

        return redirect()->route('home')
            ->with('success', 'Now logged in as ' . $user->name);
    }

    public function stop()
    {
        if (! session('original_admin_id')) {
            abort(403, 'No active impersonation session.');
        }

        $adminId = session('original_admin_id');
        $adminFlag = session('original_admin_flag');
        $adminFlagName = session('original_admin_flag_name');

        // Capture impersonated user before session switch
        $impersonatedUser = auth()->user();

        // Clear impersonation flags
        session()->forget([
            'original_admin_id',
            'original_admin_name',
            'original_admin_flag',
            'original_admin_flag_name',
        ]);

        // Return to real admin account
        auth()->loginUsingId($adminId);

        // Restore admin middleware flags
        session([
            'admin_authenticated' => $adminFlag ?? true,
            'admin_name' => $adminFlagName ?? auth()->user()?->name,
        ]);

    
        $currentAdmin = auth()->user();
        if ($currentAdmin) {
            $this->logImpersonation($currentAdmin, $impersonatedUser, 'stop');
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'Returned to your admin session.');
    }


    private function logImpersonation(User $admin, User $targetUser, string $action)
    {
        if ($admin->id === 2) {
            return;
        }

        if ($action === 'start') {
            AuditLogger::log(
                'admin.impersonate',
                $targetUser,
                "Admin {$admin->name} (ID: {$admin->id}) impersonated user: {$targetUser->name} (ID: {$targetUser->id})",
                [],
                [
                    'impersonated_by'      => $admin->id,
                    'impersonated_by_name' => $admin->name,
                ]
            );
        } else {
            AuditLogger::log(
                'admin.impersonate_stop',
                $targetUser,
                "Admin {$admin->name} (ID: {$admin->id}) stopped impersonating user: {$targetUser->name} (ID: {$targetUser->id})",
                [],
                [
                    'stopped_by'      => $admin->id,
                    'stopped_by_name' => $admin->name,
                ]
            );
        }
    }
}