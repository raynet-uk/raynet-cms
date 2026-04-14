<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Support\Carbon;

class MembersController extends Controller
{
    /**
     * Reminder to self: Members' hub – show upcoming events and useful links.
     */
    public function __invoke()
{
    $condx = null;
    $path = public_path('Condx/propagation-brief.json');

    if (file_exists($path)) {
        $json = file_get_contents($path);
        $condx = json_decode($json, true);
    }

    return view('pages.members-public', [
        'condx' => $condx,
    ]);
}
}
