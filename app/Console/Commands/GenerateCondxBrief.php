<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

/**
 * GenerateCondxBrief
 *
 * Usage:
 *   php artisan condx:generate
 *   php artisan condx:generate --date=2025-11-23
 *
 * This command:
 *   - Pulls live data (SFI, Kp) from NOAA SWPC JSON feeds once per run.
 *   - Falls back to sensible defaults if remote calls fail.
 *   - Builds a UK-style Markdown brief in your exact house style.
 *   - Saves (on the LOCAL disk only):
 *        storage/app/condx/uk-propagation-brief-YYYY-MM-DD.md
 *        storage/app/condx/YYYY-MM-DD.json
 *        storage/app/condx/latest.md
 */
class GenerateCondxBrief extends Command
{
    /**
     * The console command name/signature.
     */
    protected $signature = 'condx:generate {--date= : Force generation for a specific date (YYYY-MM-DD)}';

    /**
     * Description shown in `php artisan list`.
     */
    protected $description = 'Generate the daily UK Propagation Brief (Markdown + JSON) from live data where possible';

    /**
     * Main entry point for the command.
     */
    public function handle(): int
    {
        // Decide which date to generate:
        // - Default: today (server time)
        // - Override: --date=YYYY-MM-DD
        $date = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : now()->startOfDay();

        $this->info('Generating UK Propagation Brief for ' . $date->toDateString());

        // 1) Gather data (live where possible, with fallbacks)
        $data = $this->gatherData();

        // 2) Turn that data into Markdown text in your full house-style format
        $markdown = $this->buildMarkdownBrief($date, $data);

        // 3) Save Markdown + JSON + "latest" into storage/app/condx ON LOCAL DISK
        $disk       = Storage::disk('local'); // <— force local disk
        $dir        = 'condx';
        $dateString = $date->toDateString();

        // Ensure storage/app/condx exists
        if (! $disk->exists($dir)) {
            $disk->makeDirectory($dir);
        }

        // Daily Markdown brief:
        //   storage/app/condx/uk-propagation-brief-YYYY-MM-DD.md
        $disk->put("{$dir}/uk-propagation-brief-{$dateString}.md", $markdown);

        // Matching JSON payload:
        //   storage/app/condx/YYYY-MM-DD.json
        $disk->put("{$dir}/{$dateString}.json", json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Convenience "latest" copy:
        //   storage/app/condx/latest.md
        $disk->put("{$dir}/latest.md", $markdown);

        $this->info('Saved files (local disk):');
        $this->line('- ' . storage_path("app/{$dir}/uk-propagation-brief-{$dateString}.md"));
        $this->line('- ' . storage_path("app/{$dir}/{$dateString}.json"));
        $this->line('- ' . storage_path("app/{$dir}/latest.md"));

        return self::SUCCESS;
    }

    /**
     * Gather live data (where possible) with sensible defaults.
     *
     * Strategy:
     *  - Start with a default "safe" dataset (what you had before).
     *  - Try to override SFI and Kp from NOAA JSON feeds.
     *  - Compute textual ranges/outlooks from those numbers.
     */
    protected function gatherData(): array
    {
        // ---------- 1) Baseline defaults ----------
        $data = [
            // Solar / HF headline numbers
            'sfi'                 => 121,
            'sunspot_number'      => 51,

            // Textual Kp ranges are strings like "2–3"
            'kp_range'            => '2–3',
            'kp_max_range'        => '3–4',
            'kp_outlook'          => 'Quiet–Unsettled',

            // Solar wind in km/s, stored as a string "400–450"
            'solar_wind_range'    => '400–450',
            'blackout_risk'       => 'Low',

            // MUF and NVIS window
            'muf_low'             => 15,
            'muf_high'            => 18,
            'muf_peak'            => 17,

            'nvis_window'         => '08:00–11:00 UTC',
            'nvis_best_bands'     => '40 m (~7 MHz) and 60 m (~5.3 MHz)',
            'nvis_best_times'     => '40 m around 08:30–10:30 local and 60 m from ~14:30–17:30 local',

            // Region context
            'region_label'        => 'Liverpool/Merseyside Micro-Note',
            'region_area'         => 'Zone 10 (NW England)',

            // Confidence
            'confidence'          => 'Medium',

            // Actionable bullets
            'actionable_notes' => [
                'Try 40 m NVIS ~08:00–11:00 UTC for intra-UK nets.',
                'Use 20 m/17 m ~12:00–16:00 UTC for UK⇄near-Europe links.',
                'Standby 60 m from ~14:00–18:00 UTC for regional fallback.',
                'Do not count on sporadic-E or strong tropo openings today.',
                'Monitor Kp — if it rises to ≥4 then consider 144 MHz auroral/long-haul options.',
            ],

            // Source URLs
            'sources' => [
                'Met Office Space Weather forecast: https://weather.metoffice.gov.uk/specialist-forecasts/space-weather',
                'Kp/aurora forecast: https://www.spaceweatherlive.com/en/auroral-activity/aurora-forecast.html',
                'MUF/ionosonde description: https://www.propquest.co.uk/about.php',
            ],
        ];

        // ---------- 2) Live SFI from NOAA (solar-radio-flux.json) ----------
        try {
            $sfi = $this->fetchCurrentSfi();
            if ($sfi !== null) {
                $data['sfi'] = (int) round($sfi);
                $this->info('Live SFI from NOAA: ' . $data['sfi']);
            } else {
                $this->warn('Could not resolve live SFI – using default ' . $data['sfi']);
            }
        } catch (\Throwable $e) {
            $this->warn('SFI fetch failed: ' . $e->getMessage());
        }

        // ---------- 3) Live Kp from NOAA (planetary_k_index_1m.json) ----------
        try {
            $kp = $this->fetchCurrentKp();
            if ($kp !== null) {
                $this->info('Live Kp (approx): ' . $kp);

                // e.g. Kp=2.3 → "2–3"
                $data['kp_range']     = $this->formatKpRange($kp);
                $data['kp_max_range'] = $this->formatKpRange(min($kp + 1.0, 9.0));

                // Outlook + blackout risk from Kp band (very crude heuristic)
                if ($kp < 3.0) {
                    $data['kp_outlook']     = 'Quiet';
                    $data['blackout_risk']  = 'Low';
                } elseif ($kp < 5.0) {
                    $data['kp_outlook']     = 'Quiet–Unsettled';
                    $data['blackout_risk']  = 'Low–Moderate';
                } elseif ($kp < 7.0) {
                    $data['kp_outlook']     = 'Active';
                    $data['blackout_risk']  = 'Moderate–High';
                } else {
                    $data['kp_outlook']     = 'Storm';
                    $data['blackout_risk']  = 'High';
                }
            } else {
                $this->warn('Could not resolve live Kp – using defaults: ' . $data['kp_range']);
            }
        } catch (\Throwable $e) {
            $this->warn('Kp fetch failed: ' . $e->getMessage());
        }

        // MUF & regional stuff stays default for now.
        return $data;
    }

    /**
     * Build the Markdown brief text to match your exact multi-paragraph format.
     */
    protected function buildMarkdownBrief(Carbon $date, array $d): string
    {
        $dateStr = $date->toDateString();

        // Title
        $titleLine = "UK Propagation Brief — {$dateStr}";

        // Solar/Geo paragraph
        $solarLine = sprintf(
            'Solar/Geo: SFI about %d sfu, sunspot count ~%d. ' .
            'Kp currently ~%s, forecast for next 24 h %s (Kp max ~%s). ' .
            'Solar wind speed ~%s km/s, Bz weak and variable. Radio blackout risk: %s.',
            $d['sfi'],
            $d['sunspot_number'],
            $d['kp_range'],
            $d['kp_outlook'],
            $d['kp_max_range'],
            $d['solar_wind_range'],
            $d['blackout_risk']
        );

        $implicationLine =
            'Implication: HF propagation mostly normal; no major enhancements.';

        // HF paragraph
        $hfLine = sprintf(
            'HF (1.8–30 MHz): Recent ionosonde/MUF charts suggest MUF(3000 km) over UK ~%d–%d MHz, ' .
            'peak around %d MHz. Best daytime bands: 20 m (~14 MHz) and 17 m (~18 MHz) for UK⇄near-Europe. ' .
            'Evening: 40 m (~7 MHz) and 60 m (~5.3 MHz) good for NVIS. D-layer absorption low. ' .
            'Notable path: UK⇄EI/GM/GI on 20 m midday. NVIS window ~%s.',
            $d['muf_low'],
            $d['muf_high'],
            $d['muf_peak'],
            $d['nvis_window']
        );

        // VHF/UHF paragraph – fixed text for now
        $vhfLine =
            'VHF/UHF (50/70/144/432): Tropo/ducting outlook: Low — no high-pressure ridge flagged over UK. ' .
            'Sporadic-E: Very low (off-season). Auroral voice/data: unlikely (Kp < 4). ' .
            'Aircraft scatter/RS: Standard background, no special window.';

        // Digital & specials paragraph – mostly fixed
        $digitalLine =
            'Digital & Specials: MSK144 meteor scatter – no major shower peaks today. ' .
            'FT8/FT4: 20 m/17 m midday remain best for EU short-path. ' .
            'Satellite/contests: No major alerts or special event passes affecting today.';

        // Liverpool / Zone 10 micro-note
        $regionLine = sprintf(
            '%s: For %s your best NVIS window is %s; ' .
            '2 m/70 cm tropo or aurora to EI/GM or North Wales remains negligible today — rely HF.',
            $d['region_label'],
            $d['region_area'],
            $d['nvis_best_times']
        );

        // Actionable notes – Markdown bullet list
        $actionableHeader  = 'Actionable Notes:';
        $actionableBullets = $this->formatBulletList($d['actionable_notes']);

        // Confidence
        $confidenceLine = sprintf(
            'Confidence: %s — UK space-weather and MUF indicators align reasonably, ' .
            'but foF2/MUF data are inferred rather than directly measured for all paths.',
            $d['confidence']
        );

        // Sources
        $sourcesHeader = 'Sources:';
        $sourcesLines  = $this->formatSources($d['sources']);

        return <<<MD
{$titleLine}

{$solarLine}
{$implicationLine}

{$hfLine}

{$vhfLine}

{$digitalLine}

{$regionLine}

{$actionableHeader}
{$actionableBullets}

{$confidenceLine}

{$sourcesHeader}
{$sourcesLines}
MD;
    }

    /**
     * Format an array of bullet strings as a Markdown list.
     */
    protected function formatBulletList(array $items): string
    {
        if (empty($items)) {
            return '- (no actionable notes available)';
        }

        $lines = array_map(
            fn ($item) => ' - ' . $item,
            $items
        );

        return implode("\n", $lines);
    }

    /**
     * Format the sources list, one per line, each prefixed with " • ".
     */
    protected function formatSources(array $sources): string
    {
        if (empty($sources)) {
            return ' • (no sources recorded)';
        }

        $lines = array_map(
            fn ($src) => ' • ' . $src,
            $sources
        );

        // Each on its own line; double space before newline = Markdown line break
        return "\n" . implode("  \n", $lines);
    }

    /**
     * Fetch the current Solar Flux Index (F10.7cm) from NOAA.
     *
     * Data source:
     *   https://services.swpc.noaa.gov/json/solar-radio-flux.json
     */
    protected function fetchCurrentSfi(): ?float
    {
        $response = Http::timeout(10)
            ->acceptJson()
            ->get('https://services.swpc.noaa.gov/json/solar-radio-flux.json');

        if (! $response->ok()) {
            return null;
        }

        $json = $response->json();

        if (! is_array($json) || count($json) < 2) {
            return null;
        }

        // First row is headers, remaining rows are data
        $headers = array_shift($json);
        $lastRow = end($json);

        if (! is_array($headers) || ! is_array($lastRow)) {
            return null;
        }

        $row = @array_combine($headers, $lastRow);
        if (! is_array($row)) {
            return null;
        }

        // Try a few likely key names
        $candidate = $row['f10.7'] ?? $row['observed_flux'] ?? $row['flux'] ?? null;

        return is_numeric($candidate) ? (float) $candidate : null;
    }

    /**
     * Fetch the current (approx) planetary Kp index from NOAA.
     *
     * Data source:
     *   https://services.swpc.noaa.gov/json/planetary_k_index_1m.json
     */
    protected function fetchCurrentKp(): ?float
    {
        $response = Http::timeout(10)
            ->acceptJson()
            ->get('https://services.swpc.noaa.gov/json/planetary_k_index_1m.json');

        if (! $response->ok()) {
            return null;
        }

        $json = $response->json();

        if (! is_array($json) || count($json) < 2) {
            return null;
        }

        $headers = array_shift($json);
        $lastRow = end($json);

        if (! is_array($headers) || ! is_array($lastRow)) {
            return null;
        }

        $row = @array_combine($headers, $lastRow);
        if (! is_array($row)) {
            return null;
        }

        $candidate = $row['kp'] ?? $row['kp_index'] ?? null;

        return is_numeric($candidate) ? (float) $candidate : null;
    }

    /**
     * Turn a single Kp value into a textual range like "2–3".
     *
     * e.g. 2.2 → "2–3"
     */
    protected function formatKpRange(float $kp): string
    {
        $low  = max(0, floor($kp));
        $high = min(9, ceil($kp));

        if ($low === $high) {
            return (string) $low;
        }

        return $low . '–' . $high;
    }
}