<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AprsService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.aprs.fi/api/get';

    // APRS.fi free tier: max 20 callsigns per request, 1 req/sec
    private int $chunkSize = 20;

    public function __construct()
    {
        $this->apiKey = config('services.aprs.key', '');
    }

    /**
     * Look up APRS positions for an array of callsigns.
     * Automatically chunks into batches of 20.
     *
     * Returns a flat array keyed by uppercase callsign:
     *   ['M7NDN' => [...entry...], 'M7NDN-4' => [...entry...]]
     */
    public function getLocations(array $callsigns): array
    {
        if (empty($callsigns) || empty($this->apiKey)) {
            return [];
        }

        // Deduplicate and uppercase
        $callsigns = array_unique(array_map('strtoupper', array_filter($callsigns)));
        $results   = [];

        foreach (array_chunk($callsigns, $this->chunkSize) as $chunk) {
            try {
                $response = Http::timeout(10)->get($this->baseUrl, [
                    'name'   => implode(',', $chunk),
                    'what'   => 'loc',
                    'apikey' => $this->apiKey,
                    'format' => 'json',
                ]);

                if (! $response->ok()) {
                    Log::warning('APRS.fi HTTP error', ['status' => $response->status()]);
                    continue;
                }

                $data = $response->json();

                if (($data['result'] ?? '') !== 'ok') {
                    Log::warning('APRS.fi API error', ['response' => $data]);
                    continue;
                }

                foreach ($data['entries'] ?? [] as $entry) {
                    $key           = strtoupper($entry['name'] ?? '');
                    $results[$key] = $entry;
                }

            } catch (\Throwable $e) {
                Log::error('APRS.fi request failed', ['error' => $e->getMessage()]);
            }

            // Respect free-tier rate limit between chunks
            if (count($callsigns) > $this->chunkSize) {
                usleep(1_100_000); // 1.1 seconds
            }
        }

        return $results;
    }

    /**
     * Build the full list of callsigns to query for a collection of users.
     * Includes base callsign + SSID variant if aprs_ssid is set.
     */
    public function buildCallsignList(\Illuminate\Support\Collection $users): array
    {
        $callsigns = [];

        foreach ($users as $user) {
            $base = trim(strtoupper($user->callsign ?? ''));
            if (! $base || $base === 'TEST') {
                continue;
            }

            $callsigns[] = $base;

            $ssid = trim($user->aprs_ssid ?? '');
            if ($ssid !== '' && $ssid !== '0') {
                $callsigns[] = $base . '-' . ltrim($ssid, '-');
            }
        }

        return array_unique($callsigns);
    }

    /**
     * Format a Unix timestamp as a human-readable "X ago" string.
     */
    public function formatAge(int $timestamp): string
    {
        $diff = now()->timestamp - $timestamp;

        return match (true) {
            $diff <  60       => 'Just now',
            $diff <  3600     => floor($diff / 60) . ' min ago',
            $diff <  86400    => floor($diff / 3600) . ' hr ago',
            $diff <  604800   => floor($diff / 86400) . ' days ago',
            default           => date('d M Y H:i', $timestamp) . ' UTC',
        };
    }

    /**
     * Return a CSS colour class based on how old a position is.
     */
    public function freshnessClass(int $timestamp): string
    {
        $diff = now()->timestamp - $timestamp;

        return match (true) {
            $diff <  1800  => 'fresh-green',   // < 30 min
            $diff <  7200  => 'fresh-amber',   // < 2 hr
            $diff <  86400 => 'fresh-grey',    // < 24 hr
            default        => 'fresh-old',
        };
    }
}
