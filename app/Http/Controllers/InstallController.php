<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class InstallController extends Controller
{
    public function index()
    {
        if ($this->isInstalled()) {
            return redirect('/');
        }
        return view('install.index', ['step' => 'index', 'groupName' => '']);
    }

    public function step1()
    {
        if ($this->isInstalled()) return redirect('/');
        return view('install.index', ['step' => 'step1', 'groupName' => '']);
    }

    public function step1Post(Request $request)
    {
        $request->validate([
            'group_name'     => ['required', 'string', 'max:80'],
            'group_number'   => ['nullable', 'string', 'max:20'],
            'group_callsign' => ['nullable', 'string', 'max:20'],
            'group_region'   => ['nullable', 'string', 'max:80'],
            'gc_name'        => ['required', 'string', 'max:80'],
            'gc_email'       => ['required', 'email', 'max:120'],
            'support_request_email' => ['required', 'email', 'max:120'],
            'site_url'       => ['required', 'url', 'max:120'],
            'raynet_zone'    => ['nullable', 'string', 'max:20'],
        ]);

        $fields = [
            'group_name', 'group_number', 'group_callsign', 'group_region',
            'gc_name', 'gc_email', 'support_request_email', 'site_url', 'raynet_zone',
        ];

        foreach ($fields as $field) {
            Setting::set($field, $request->input($field, ''));
        }

        // Also set site_name from group_name
        Setting::set('site_name', $request->input('group_name'));

        return redirect()->route('install.step2');
    }

    public function step2()
    {
        if ($this->isInstalled()) return redirect('/');
        return view('install.index', ['step' => 'step2', 'groupName' => '']);
    }

    public function step2Post(Request $request)
    {
        $request->validate([
            'name'                  => ['required', 'string', 'max:100'],
            'callsign'              => ['required', 'string', 'max:15'],
            'email'                 => ['required', 'email', 'max:120', 'unique:users,email'],
            'password'              => ['required', 'string', 'min:10', 'confirmed'],
        ]);

        $user = User::create([
            'name'              => $request->name,
            'callsign'          => strtoupper($request->callsign),
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'email_verified_at' => now(),
            'is_admin'          => true,
            'is_super_admin'    => true,
        ]);

        // Assign super-admin role if roles table exists
        try {
            DB::table('model_has_roles')->insert([
                'role_id'    => DB::table('roles')->where('name', 'super-admin')->value('id') ?? 1,
                'model_type' => 'App\Models\User',
                'model_id'   => $user->id,
            ]);
        } catch (\Throwable $e) {
            // Roles not set up yet — fine, is_admin flag is enough
        }

        return redirect()->route('install.step3');
    }

    public function step3()
    {
        if ($this->isInstalled()) return redirect('/');
        return view('install.index', [
            'step' => 'step3', 'groupName' => Setting::get('group_name'),
        ]);
    }

    public function complete(Request $request)
    {
        Setting::set('installed', '1');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('cache:clear');

        return redirect()->route('admin.login')
            ->with('success', 'Installation complete! Log in with your admin account.');
    }

    protected function isInstalled(): bool
    {
        try {
            return Setting::get('installed', '0') === '1';
        } catch (\Throwable $e) {
            return false;
        }
    }
}
