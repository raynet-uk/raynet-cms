<?php
namespace App\Console\Commands;
use App\Models\Setting;
use App\Models\User;
use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class PushReportToCommandCentre extends Command
{
    protected $signature   = 'raynet:push-report';
    protected $description = 'Push group stats to RAYNET Command Centre';

    public function handle(): int
    {
        $licenceKey = Setting::get('cms_licence_key', '');
        $endpoint   = 'https://command.nathandillon.co.uk/api/reporting/push';

        if (!$licenceKey) { $this->warn('No licence key found.'); return 0; }

        $volunteerHours = User::sum('volunteering_hours_this_year') ?? 0;

        $trainingCompletions = 0;
        try {
            $trainingCompletions = \DB::table('lms_course_user')
                ->where('completed', 1)->whereYear('updated_at', now()->year)->count();
        } catch (\Throwable $e) {}

        $alertStatus = 'normal';
        try { $alertStatus = \App\Models\AlertStatus::first()?->status ?? 'normal'; } catch (\Throwable $e) {}

        $payload = [
            'cms_version' => '1.0.0',
            'site_url'    => config('app.url'),
            'members' => [
                'total'                     => User::count(),
                'active'                    => User::whereNotNull('email_verified_at')->where('registration_pending', 0)->count(),
                'pending'                   => User::where('registration_pending', 1)->count(),
                'attended_event_this_year'  => User::where('attended_event_this_year', 1)->count(),
                'volunteer_hours_this_year' => round((float)$volunteerHours, 1),
            ],
            'events' => [
                'total_this_year' => Event::whereYear('starts_at', now()->year)->count(),
                'upcoming'        => Event::where('starts_at', '>', now())->count(),
            ],
            'training' => [
                'completions_this_year' => $trainingCompletions,
            ],
            'alert_status' => $alertStatus,
        ];

        try {
            $response = Http::timeout(15)->withHeaders(['X-CMS-Licence' => $licenceKey])->post($endpoint, $payload);
            if ($response->successful()) {
                $this->info('✓ Report pushed successfully — ' . ($response->json('group') ?? ''));
                return 0;
            }
            $this->warn('Error: ' . $response->status() . ' ' . $response->json('error', ''));
            return 1;
        } catch (\Throwable $e) {
            $this->warn('Could not reach Command Centre: ' . $e->getMessage());
            return 1;
        }
    }
}
