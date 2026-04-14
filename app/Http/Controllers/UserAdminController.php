<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\QRZLookup;
use Illuminate\Support\Facades\DB;

class UserAdminController extends Controller
{
    public function index()
    {
        $users = User::orderBy('name')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function edit($id, QRZLookup $qrz = null)
    {
        $user = User::findOrFail($id);

        $activityLogs = ActivityLog::where('user_id', $id)
            ->orderByDesc('event_date')
            ->orderByDesc('created_at')
            ->get();

        $qrzData = null;
        if ($qrz && $user->callsign) {
            $qrzData = $qrz->lookup($user->callsign);
        }

        // Sessions for the Sessions tab
        $sessions = DB::table('sessions')
            ->where('user_id', $id)
            ->orderByDesc('last_activity')
            ->get();

        return view('admin.users.edit', compact('user', 'activityLogs', 'qrzData', 'sessions'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'nullable|email|max:255',
            'login'    => 'nullable|string|max:50',
            'role'     => 'nullable|string|max:50',
            'level'    => 'nullable|integer|min:0',
            'status'   => 'required|string',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name    = $request->name;
        $user->email   = $request->email;
        $user->login   = $request->login ?? $user->login;
        $user->role    = $request->role;
        $user->level   = $request->level;
        $user->status  = $request->status;
        $user->is_admin = $request->has('is_admin');

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('admin.users.index')
            ->with('status', 'User updated successfully.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users.index')
            ->with('status', 'User deleted successfully.');
    }

    public function promote($id)
    {
        $user = User::findOrFail($id);

        $exists = DB::table('operators')->where('name', $user->name)->exists();

        if (!$exists) {
            DB::table('operators')->insert([
                'name'       => $user->name,
                'callsign'   => '',
                'role'       => 'Operator',
                'status'     => 'Active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            return redirect()->back()
                ->with('status', "{$user->name} has been promoted to Operator!");
        }

        return redirect()->back()
            ->with('status', "{$user->name} is already an Operator.");
    }
}