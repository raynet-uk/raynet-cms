<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsLicence;
use Illuminate\Http\Request;

class CmsLicenceController extends Controller
{
    public function index()
    {
        $licences = CmsLicence::orderByDesc('created_at')->get();
        return view('admin.cms-licences.index', compact('licences'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'group_name'   => ['required', 'string', 'max:80'],
            'group_number' => ['nullable', 'string', 'max:20'],
            'gc_name'      => ['nullable', 'string', 'max:80'],
            'gc_email'     => ['nullable', 'email', 'max:120'],
            'notes'        => ['nullable', 'string', 'max:255'],
        ]);

        $licence = CmsLicence::generate(
            $request->group_name,
            $request->group_number ?? '',
            $request->gc_name ?? '',
            $request->gc_email ?? '',
            $request->notes ?? ''
        );

        return back()->with('success', "Licence generated: {$licence->key}");
    }

    public function revoke(CmsLicence $licence)
    {
        $licence->update(['is_active' => false]);
        return back()->with('status', "Licence {$licence->key} revoked.");
    }

    public function destroy(CmsLicence $licence)
    {
        $licence->delete();
        return back()->with('status', "Licence deleted.");
    }
}
