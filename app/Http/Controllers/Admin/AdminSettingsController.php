<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminSettingsController extends Controller
{
    public function index()
    {
        return view('admin.settings', [
            'supportEmail'            => Setting::get('support_request_email', ''),
            'registrationNotifyEmail' => Setting::get('registration_notify_email', ''),
            'headerCode'              => Setting::get('header_code', ''),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'support_request_email'    => ['nullable', 'email', 'max:255'],
            'registration_notify_email'=> ['nullable', 'email', 'max:255'],
            'header_code'              => ['nullable', 'string'],
            'site_name'                => ['nullable', 'string', 'max:80'],
            'site_tagline'             => ['nullable', 'string', 'max:120'],
            'site_logo'                => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        if ($request->has('support_request_email')) {
            Setting::set('support_request_email', $request->support_request_email ?? '');
        }

        if ($request->has('registration_notify_email')) {
            Setting::set('registration_notify_email', $request->registration_notify_email ?? '');
        }

        if ($request->has('header_code')) {
            Setting::set('header_code', $request->header_code ?? '');
        }

        if ($request->has('site_name')) {
            Setting::set('site_name', trim($request->site_name ?? ''));
        }

        if ($request->has('site_tagline')) {
            Setting::set('site_tagline', trim($request->site_tagline ?? ''));
        }

        // Remove logo
        if ($request->boolean('remove_logo')) {
            $old = Setting::get('site_logo_path', '');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            Setting::set('site_logo_path', '');
        }

        // Upload new logo
        if ($request->hasFile('site_logo')) {
            $old = Setting::get('site_logo_path', '');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            $ext  = $request->file('site_logo')->getClientOriginalExtension();
            $path = $request->file('site_logo')->storeAs('site', 'logo.' . $ext, 'public');
            Setting::set('site_logo_path', $path);
        }

        return redirect()->route('admin.settings')->with('status', 'Settings saved.');
    }
}