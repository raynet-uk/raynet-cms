<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CmsLicence;
use Illuminate\Http\Request;

class CmsLicenceApiController extends Controller
{
    public function validateLicence(Request $request)
    {
        $key = trim($request->input('key', ''));

        if (empty($key)) {
            return response()->json([
                'valid'   => false,
                'message' => 'No licence key provided.',
            ], 400);
        }

        $licence = CmsLicence::where('key', $key)->first();

        if (!$licence) {
            return response()->json([
                'valid'   => false,
                'message' => 'Invalid licence key.',
            ], 404);
        }

        if (!$licence->is_active) {
            return response()->json([
                'valid'   => false,
                'message' => 'This licence has been revoked.',
            ], 403);
        }

        if ($licence->isUsed()) {
            return response()->json([
                'valid'   => false,
                'message' => 'This licence key has already been used.',
                'used_at' => $licence->activated_at->toDateTimeString(),
                'used_by' => $licence->activated_site_url,
            ], 409);
        }

        // Activate the licence
        $licence->activate(
            $request->ip(),
            $request->input('site_url', 'unknown')
        );

        return response()->json([
            'valid'        => true,
            'message'      => 'Licence activated successfully.',
            'group_name'   => $licence->group_name,
            'group_number' => $licence->group_number,
            'gc_name'      => $licence->gc_name,
            'gc_email'     => $licence->gc_email,
        ]);
    }

    public function check(Request $request)
    {
        $key = trim($request->input('key', ''));
        $licence = CmsLicence::where('key', $key)->first();

        if (!$licence || !$licence->is_active) {
            return response()->json(['valid' => false], 404);
        }

        if ($licence->isUsed()) {
            return response()->json(['valid' => false, 'message' => 'Already used'], 409);
        }

        return response()->json([
            'valid'        => true,
            'group_name'   => $licence->group_name,
            'group_number' => $licence->group_number,
            'gc_name'      => $licence->gc_name,
            'gc_email'     => $licence->gc_email,
        ]);
    }
}
