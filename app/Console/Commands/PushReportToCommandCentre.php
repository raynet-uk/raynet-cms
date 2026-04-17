<?php
namespace App\Console\Commands;
use App\Models\Setting;
use App\Models\User;
use App\Models\Event;
use App\Models\AlertStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class PushReportToCommandCentre extends Command
{
    protected $signature   = 'raynet:push-report';
    protected $description = 'Push everything to RAYNET Command Centre';

    public function handle(): int
    {
        $licenceKey = Setting::get('cms_licence_key', '');
        $endpoint   = 'https://command.nathandillon.co.uk/api/reporting/push';
        if (!$licenceKey) { $this->warn('No licence key.'); return 0; }

        $this->info('Gathering data...');

        // ── Members ───────────────────────────────────────────────────────
        $allUsers = User::orderBy('name')->get();
        $memberList = $allUsers->map(fn($u) => [
            'name'                      => $u->name,
            'callsign'                  => $u->callsign ?? null,
            'email'                     => $u->email,
            'is_admin'                  => (bool)($u->getAttributes()['is_admin'] ?? false),
            'is_super_admin'            => (bool)($u->getAttributes()['is_super_admin'] ?? false),
            'registration_pending'      => (bool)($u->registration_pending ?? false),
            'email_verified'            => !is_null($u->email_verified_at),
            'attended_event_this_year'  => (bool)($u->attended_event_this_year ?? false),
            'volunteer_hours_this_year' => round((float)($u->volunteering_hours_this_year ?? 0), 1),
            'licence_class'             => $u->licence_class ?? null,
            'dmr_id'                    => $u->dmr_id ?? null,
            'joined_at'                 => $u->created_at?->format('Y-m-d'),
            'last_seen'                 => $u->updated_at?->format('Y-m-d'),
        ])->toArray();

        // ── Events ────────────────────────────────────────────────────────
        $events = Event::with(['assignments.user', 'type'])
            ->orderByDesc('starts_at')
            ->take(100)
            ->get()
            ->map(fn($e) => [
                'id'            => $e->id,
                'title'         => $e->title,
                'type'          => $e->type?->name ?? 'Event',
                'type_colour'   => $e->type?->colour ?? '#003366',
                'description'   => $e->description,
                'location'      => $e->location,
                'starts_at'     => $e->starts_at?->toIso8601String(),
                'ends_at'       => $e->ends_at?->toIso8601String(),
                'is_past'       => $e->starts_at?->isPast() ?? false,
                'is_private'    => (bool)($e->is_private ?? false),
                'slug'          => $e->slug ?? null,
                'lat'           => $e->lat ?? null,
                'lng'           => $e->lng ?? null,
                'crew_count'    => $e->assignments?->count() ?? 0,
                'team'          => $e->assignments?->map(fn($a) => [
                    'name'      => $a->user?->name,
                    'callsign'  => $a->user?->callsign,
                    'role'      => $a->role,
                    'status'    => $a->status,
                    'location'  => $a->location_name,
                    'lat'       => $a->lat,
                    'lng'       => $a->lng,
                    'frequency' => $a->frequency,
                    'mode'      => $a->mode,
                ])->toArray() ?? [],
            ])->toArray();

        // ── Training ──────────────────────────────────────────────────────
        $trainingCompletions = 0;
        $courseStats = [];
        try {
            $trainingCompletions = DB::table('lms_progress')
                ->where('completed', 1)->whereYear('updated_at', now()->year)->count();
            $courseStats = DB::table('lms_courses as c')
                ->leftJoin('lms_progress as p', 'c.id', '=', 'p.course_id')
                ->select('c.title', DB::raw('COUNT(DISTINCT p.user_id) as enrolled'), DB::raw('SUM(p.completed) as completed'))
                ->groupBy('c.id', 'c.title')
                ->get()->toArray();
        } catch (\Throwable $e) {}

        // ── Alert status ──────────────────────────────────────────────────
        $alert = null;
        try { $alert = AlertStatus::first(); } catch (\Throwable $e) {}

        // ── Activity logs ─────────────────────────────────────────────────
        $recentActivity = [];
        try {
            $recentActivity = DB::table('activity_logs')
                ->orderByDesc('activity_date')
                ->take(50)
                ->get()
                ->map(fn($l) => [
                    'date'  => $l->activity_date,
                    'type'  => $l->activity_type ?? 'activity',
                    'hours' => $l->hours ?? 0,
                    'notes' => $l->notes ?? null,
                ])->toArray();
        } catch (\Throwable $e) {}

        // ── Settings ──────────────────────────────────────────────────────
        $groupInfo = [
            'name'        => Setting::get('group_name', ''),
            'number'      => Setting::get('group_number', ''),
            'callsign'    => Setting::get('group_callsign', ''),
            'region'      => Setting::get('group_region', ''),
            'zone'        => Setting::get('raynet_zone', ''),
            'gc_name'     => Setting::get('gc_name', ''),
            'gc_email'    => Setting::get('gc_email', ''),
            'site_url'    => Setting::get('site_url', config('app.url')),
        ];

        // ── Build full payload ────────────────────────────────────────────
        $payload = [
            'cms_version' => '1.0.0',
            'site_url'    => config('app.url'),
            'group_info'  => $groupInfo,
            'members' => [
                'total'                     => $allUsers->count(),
                'active'                    => $allUsers->whereNotNull('email_verified_at')->where('registration_pending', 0)->count(),
                'pending'                   => $allUsers->where('registration_pending', 1)->count(),
                'attended_event_this_year'  => $allUsers->where('attended_event_this_year', 1)->count(),
                'volunteer_hours_this_year' => round((float)$allUsers->sum('volunteering_hours_this_year'), 1),
                'list'                      => $memberList,
            ],
            'events' => [
                'total_this_year' => Event::whereYear('starts_at', now()->year)->count(),
                'upcoming'        => Event::where('starts_at', '>', now())->count(),
                'past_this_year'  => Event::whereYear('starts_at', now()->year)->where('starts_at', '<', now())->count(),
                'list'            => $events,
            ],
            'training' => [
                'completions_this_year' => $trainingCompletions,
                'courses'               => $courseStats,
            ],
            'alert_status'    => $alert?->status ?? 'normal',
            'alert_updated'   => $alert ? (string)$alert->updated_at : null,
            'activity_logs'   => $recentActivity,
        ];

        try {
            $response = Http::timeout(30)->withHeaders(['X-CMS-Licence' => $licenceKey])->post($endpoint, $payload);
            if ($response->successful()) {
                $this->info('✓ Full report pushed successfully');
                $this->line('  Members: ' . count($memberList));
                $this->line('  Events:  ' . count($events));
                return 0;
            }
            $this->warn('Error ' . $response->status() . ': ' . $response->json('error', ''));
            return 1;
        } catch (\Throwable $e) {
            $this->warn('Failed: ' . $e->getMessage());
            return 1;
        }
    }
}
