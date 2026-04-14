<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CoreSettingsController extends Controller
{
    public function index()
    {
        return view('core::admin.settings', [
            'siteName'    => Setting::get('site_name',    'Liverpool RAYNET'),
            'siteTagline' => Setting::get('site_tagline', 'Robust, Resilient, Radio'),
            'siteLogoPath'=> Setting::get('site_logo_path', ''),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'site_name'    => ['nullable', 'string', 'max:80'],
            'site_tagline' => ['nullable', 'string', 'max:120'],
            'site_logo'    => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        if ($request->filled('site_name')) {
            Setting::set('site_name', trim($request->site_name));
        }

        if ($request->filled('site_tagline')) {
            Setting::set('site_tagline', trim($request->site_tagline));
        }

        if ($request->hasFile('site_logo')) {
            // Delete old logo if stored in our managed folder
            $old = Setting::get('site_logo_path', '');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }

            $path = $request->file('site_logo')->storeAs(
                'site',
                'logo.' . $request->file('site_logo')->getClientOriginalExtension(),
                'public'
            );

            Setting::set('site_logo_path', $path);
        }

        if ($request->boolean('remove_logo')) {
            $old = Setting::get('site_logo_path', '');
            if ($old && Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
            Setting::set('site_logo_path', '');
        }

        return redirect()->route('admin.core.settings')
            ->with('success', 'Site settings updated.');
    }
}
