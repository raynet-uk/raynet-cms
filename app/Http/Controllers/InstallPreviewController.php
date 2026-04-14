<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InstallPreviewController extends Controller
{
    protected array $rules1 = [
        'group_name'            => ['required', 'string', 'max:80'],
        'group_number'          => ['nullable', 'string', 'max:20'],
        'group_callsign'        => ['nullable', 'string', 'max:20'],
        'group_region'          => ['nullable', 'string', 'max:80'],
        'gc_name'               => ['required', 'string', 'max:80'],
        'gc_email'              => ['required', 'email', 'max:120'],
        'support_request_email' => ['required', 'email', 'max:120'],
        'site_url'              => ['required', 'url', 'max:120'],
        'raynet_zone'           => ['nullable', 'string', 'max:20'],
    ];

    protected array $rules2 = [
        'name'     => ['required', 'string', 'max:100'],
        'callsign' => ['required', 'string', 'max:15'],
        'email'    => ['required', 'email', 'max:120'],
        'password' => ['required', 'string', 'min:10', 'confirmed'],
    ];

    public function index()
    {
        return view('install.index', [
            'step'    => 'index',
            'preview' => true,
            'groupName' => '',
        ]);
    }

    public function step1()
    {
        return view('install.index', [
            'step'    => 'step1',
            'preview' => true,
            'groupName' => '',
        ]);
    }

    public function step1Post(Request $request)
    {
        $validated = $request->validate($this->rules1);

        // Simulate what would be saved
        $wouldSave = [
            'group_name'            => $validated['group_name'],
            'group_number'          => $validated['group_number'] ?? '(not set)',
            'group_callsign'        => $validated['group_callsign'] ?? '(not set)',
            'group_region'          => $validated['group_region'] ?? '(not set)',
            'gc_name'               => $validated['gc_name'],
            'gc_email'              => $validated['gc_email'],
            'support_request_email' => $validated['support_request_email'],
            'site_url'              => $validated['site_url'],
            'raynet_zone'           => $validated['raynet_zone'] ?? '(not set)',
            'site_name'             => $validated['group_name'],
        ];

        session(['preview_step1' => $wouldSave]);

        return view('install.index', [
            'step'      => 'step2',
            'preview'   => true,
            'groupName' => $validated['group_name'],
            'dryRun'    => [
                'title'   => '✓ Step 1 validated — this is what would be saved to the settings table:',
                'type'    => 'success',
                'items'   => collect($wouldSave)->map(fn($v, $k) => [
                    'key'   => $k,
                    'value' => $v,
                ])->values()->all(),
            ],
        ]);
    }

    public function step2()
    {
        return view('install.index', [
            'step'    => 'step2',
            'preview' => true,
            'groupName' => '',
        ]);
    }

    public function step2Post(Request $request)
    {
        // Use modified rules — email uniqueness check skipped in preview
        $rules = $this->rules2;
        $rules['email'] = ['required', 'email', 'max:120']; // no unique check

        $validated = $request->validate($rules);

        $wouldCreate = [
            'name'              => $validated['name'],
            'callsign'          => strtoupper($validated['callsign']),
            'email'             => $validated['email'],
            'password'          => '(hashed — not shown)',
            'email_verified_at' => now()->toDateTimeString(),
            'is_admin'          => 'true',
            'is_super_admin'    => 'true (first user)',
        ];

        session(['preview_step2' => $wouldCreate]);

        return view('install.index', [
            'step'      => 'step3',
            'preview'   => true,
            'groupName' => session('preview_group_name', 'Your Group'),
            'dryRun'    => [
                'title' => '✓ Step 2 validated — this is what would be created in the users table:',
                'type'  => 'success',
                'items' => collect($wouldCreate)->map(fn($v, $k) => [
                    'key'   => $k,
                    'value' => $v,
                ])->values()->all(),
            ],
        ]);
    }

    public function step3()
    {
        return view('install.index', [
            'step'    => 'step3',
            'preview' => true,
            'groupName' => 'Preview Group',
        ]);
    }

    public function complete(Request $request)
    {
        $step1 = session('preview_step1', []);
        $step2 = session('preview_step2', []);

        return view('install.preview-complete', [
            'preview'        => true,
            'groupSettings'  => $step1,
            'adminAccount'   => $step2,
            'artisanCommands'=> [
                ['cmd' => 'php artisan route:clear',  'detail' => 'Clears cached route list'],
                ['cmd' => 'php artisan view:clear',   'detail' => 'Clears all compiled Blade views'],
                ['cmd' => 'php artisan cache:clear',  'detail' => 'Flushes the application cache'],
            ],
            'databaseWrites' => [
                ['table' => 'settings', 'action' => 'UPSERT', 'detail' => count($step1) . ' settings rows written (group_name, gc_email, site_url etc.)'],
                ['table' => 'settings', 'action' => 'SET',    'detail' => "installed = '1' — marks setup as complete"],
                ['table' => 'users',    'action' => 'INSERT', 'detail' => 'New admin user created (' . ($step2['email'] ?? 'email') . ')'],
                ['table' => 'model_has_roles', 'action' => 'INSERT', 'detail' => 'Super-admin role assigned to new user'],
            ],
            'redirects' => [
                ['from' => '/install/complete', 'to' => '/admin/login', 'detail' => 'With success flash: "Installation complete! Log in with your admin account."'],
            ],
        ]);
    }
}