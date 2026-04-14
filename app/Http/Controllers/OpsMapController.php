<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\AprsService;

class OpsMapController extends Controller
{
    // Liverpool city centre used for radius check
    private const RAYNET_LAT    = 53.4084;
    private const RAYNET_LNG    = -2.9916;
    private const RAYNET_RADIUS_KM = 50;

    public function aprs(): \Illuminate\Http\JsonResponse
    {
        try {
            $apiKey = config('services.aprs.key');

            $users = \App\Models\User::whereNotNull('callsign')
                ->where('callsign', '!=', '')->where('callsign', '!=', 'TEST')
                ->whereNull('suspended_at')
                ->get(['id', 'name', 'callsign', 'aprs_ssid', 'operator_title']);

            // Build member lookup keyed by uppercase callsign.
            // We only query BASE callsigns -- aprs.fi returns ALL SSIDs when you
            // search the base (M7NDN returns M7NDN-1, M7NDN-4, M7NDN-D etc).
            $memberByBase    = []; // base => user
            $memberByCallsign = []; // any variant => user (populated after results come in)
            foreach ($users as $u) {
                $base = strtoupper(trim($u->callsign));
                $memberByBase[$base] = $u;
            }

            // Build full query list: base callsign + common SSIDs.
            // aprs.fi does NOT auto-expand base callsigns to their SSIDs,
            // so we must include variants explicitly. Keep to common SSIDs
            // to avoid rate limits: base, -1 to -9, and letter suffixes -D/-R/-W.
            $queryList = [];
            foreach (array_keys($memberByBase) as $base) {
                $queryList[] = $base;
                for ($i = 1; $i <= 9; $i++) $queryList[] = $base . '-' . $i;
                foreach (['D','R','W','G','S','P'] as $l) $queryList[] = $base . '-' . $l;
            }

            $stations = [];

            foreach (array_chunk(array_unique($queryList), 20) as $chunk) {
                $nameStr = implode(',', $chunk);

                // loc: positions, objects, DMR gateways
                // wx: weather stations
                foreach (['loc', 'wx'] as $what) {
                    try {
                        $resp = Http::timeout(10)
                            ->withHeaders(['User-Agent' => 'RAYNET-CMS/1.0 (+' . \App\Helpers\RaynetSetting::siteUrl() . ')'])
                            ->get('https://api.aprs.fi/api/get', [
                                'apikey' => $apiKey,
                                'name'   => $nameStr,
                                'what'   => $what,
                                'format' => 'json',
                            ]);

                        if (! $resp->successful()) continue;
                        $data = $resp->json();
                        if (($data['result'] ?? '') !== 'ok') continue;

                        foreach ($data['entries'] ?? [] as $e) {
                            $call = strtoupper($e['name'] ?? '');
                            if (!$call) continue;
                            $lat  = (float)($e['lat'] ?? 0);
                            $lng  = (float)($e['lng'] ?? 0);
                            if (!$lat || !$lng) continue;

                            $ts   = (int)($e['lasttime'] ?? $e['time'] ?? 0);
                            $diff = now()->timestamp - $ts;

                            // Keep fresher data if duplicate
                            if (isset($stations[$call]) && $stations[$call]['diff'] <= $diff) continue;

                            $stations[$call] = [
                                'call'    => $call,
                                'lat'     => $lat,
                                'lng'     => $lng,
                                'speed'   => (float)($e['speed'] ?? 0),
                                'alt'     => (float)($e['altitude'] ?? 0),
                                'comment' => $e['comment'] ?? '',
                                'type'    => $e['type'] ?? ($what === 'wx' ? 'w' : 'l'),
                                'diff'    => $diff,
                            ];
                        }
                    } catch (\Throwable $e) {
                        continue;
                    }
                }
            }

            // Build GeoJSON features
            $features = [];
            foreach ($stations as $call => $s) {
                // Match back to member -- check exact call, then strip SSID
                $baseCall = strtoupper(explode('-', $call)[0]);
                $user     = $memberByBase[$call] ?? $memberByBase[$baseCall] ?? null;

                $distKm   = $this->haversineKm(self::RAYNET_LAT, self::RAYNET_LNG, $s['lat'], $s['lng']);
                $inRadius = $distKm <= self::RAYNET_RADIUS_KM;
                $diff     = (int)$s['diff'];

                $features[] = [
                    'type'     => 'Feature',
                    'geometry' => ['type' => 'Point', 'coordinates' => [$s['lng'], $s['lat']]],
                    'properties' => [
                        'call'     => $call,
                        'name'     => $user?->name ?? $call,
                        'title'    => $user?->operator_title ?? '',
                        'speed'    => $s['speed'],
                        'alt'      => $s['alt'],
                        'comment'  => $s['comment'],
                        'type'     => $s['type'],
                        'isMember' => $user !== null,
                        'inRadius' => $inRadius,
                        'distKm'   => round($distKm, 1),
                        'age'      => $diff < 60 ? 'Just now'
                                   : ($diff < 3600  ? floor($diff/60)  .' min ago'
                                   : ($diff < 86400 ? floor($diff/3600).' hr ago'
                                   : 'Over a day ago')),
                        'fresh'    => $diff < 1800 ? 'green'
                                   : ($diff < 7200  ? 'amber' : 'grey'),
                    ],
                ];
            }

            // Build roster -- all members, showing all active SSID variants
            $roster = [];
            foreach ($users as $u) {
                $base   = strtoupper(trim($u->callsign));
                $active = [];
                foreach ($stations as $call => $s) {
                    $callBase = strtoupper(explode('-', $call)[0]);
                    if ($callBase !== $base) continue;
                    $distKm = $this->haversineKm(self::RAYNET_LAT, self::RAYNET_LNG, $s['lat'], $s['lng']);
                    $diff   = (int)$s['diff'];
                    $active[] = [
                        'callsign' => $call,
                        'type'     => $s['type'],
                        'age'      => $diff < 60 ? 'Just now'
                                   : ($diff < 3600  ? floor($diff/60)  .' min ago'
                                   : ($diff < 86400 ? floor($diff/3600).' hr ago' : 'Over a day ago')),
                        'fresh'    => $diff < 1800 ? 'green' : ($diff < 7200 ? 'amber' : 'grey'),
                        'inRadius' => $distKm <= self::RAYNET_RADIUS_KM,
                        'distKm'   => round($distKm, 1),
                        'speed'    => $s['speed'],
                        'diff'     => $diff,
                    ];
                }
                usort($active, fn($a,$b) => $a['diff'] <=> $b['diff']);

                $bestFresh = 'offline';
                if (!empty($active)) {
                    $minDiff   = $active[0]['diff'];
                    $bestFresh = $minDiff < 1800 ? 'green' : ($minDiff < 7200 ? 'amber' : 'grey');
                }

                $roster[] = [
                    'name'     => $u->name,
                    'callsign' => $base,
                    'title'    => $u->operator_title ?? '',
                    'onAir'    => !empty($active),
                    'fresh'    => $bestFresh,
                    'stations' => $active,
                ];
            }

            usort($roster, function($a, $b) {
                if ($b['onAir'] !== $a['onAir']) return $b['onAir'] <=> $a['onAir'];
                $o = ['green'=>0,'amber'=>1,'grey'=>2,'offline'=>3];
                $fa = $o[$a['fresh']] ?? 3;
                $fb = $o[$b['fresh']] ?? 3;
                return $fa !== $fb ? $fa <=> $fb : strcmp($a['name'], $b['name']);
            });

            $memberCount   = count(array_filter($features, fn($f) => $f['properties']['isMember']));
            $inRadiusCount = count(array_filter($features, fn($f) => $f['properties']['isMember'] && $f['properties']['inRadius']));

            return response()->json([
                'type'     => 'FeatureCollection',
                'features' => $features,
                'count'    => count($features),
                'members'  => $memberCount,
                'inRadius' => $inRadiusCount,
                'roster'   => $roster,
            ]);

        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    private function haversineKm(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $R  = 6371;
        $dL = deg2rad($lat2 - $lat1);
        $dG = deg2rad($lng2 - $lng1);
        $a  = sin($dL/2)**2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dG/2)**2;
        return $R * 2 * atan2(sqrt($a), sqrt(1-$a));
    }

        public function flood(): \Illuminate\Http\JsonResponse
    {
        $data = Cache::remember('ops_map_flood_v3', 300, function () {
            try {
                $alerts = Http::timeout(10)
                    ->get('https://environment.data.gov.uk/flood-monitoring/api/2.0/floods', ['min-severity' => 1, '_limit' => 200])
                    ->json();

                $allStations = [];
                $queries = [
                    ['lat' => 53.4, 'long' => -2.9, 'dist' => 50, 'parameter' => 'level', '_limit' => 100],
                    ['riverName' => 'Mersey',  'parameter' => 'level', '_limit' => 50],
                    ['riverName' => 'Irwell',  'parameter' => 'level', '_limit' => 30],
                    ['riverName' => 'Weaver',  'parameter' => 'level', '_limit' => 20],
                    ['riverName' => 'Douglas', 'parameter' => 'level', '_limit' => 20],
                ];
                foreach ($queries as $params) {
                    $resp = Http::timeout(8)->get('https://environment.data.gov.uk/flood-monitoring/api/2.0/stations', $params)->json();
                    foreach ($resp['items'] ?? [] as $st) {
                        if (!empty($st['lat']) && !empty($st['long'])) {
                            $allStations[$st['@id'] ?? uniqid()] = $st;
                        }
                    }
                }

                return ['alerts' => $alerts, 'stations' => array_values($allStations)];
            } catch (\Throwable $e) {
                return ['error' => $e->getMessage()];
            }
        });

        if (isset($data['error'])) {
            return response()->json(['error' => $data['error']], 500);
        }

        $sevColour = [1 => '#64748b', 2 => '#f59e0b', 3 => '#ef4444', 4 => '#7c2d12'];
        $sevLabel  = [1 => 'Low', 2 => 'Moderate', 3 => 'High', 4 => 'Severe'];
        $alertItems = $data['alerts']['items'] ?? [];
        $alertBySev = [];
        foreach ($alertItems as $a) {
            $id = strtolower($a['floodAreaID'] ?? '');
            $sev = $a['severity']['value'] ?? 1;
            $alertBySev[$id] = max($alertBySev[$id] ?? 0, $sev);
        }

        $features = [];
        foreach ($data['stations'] as $st) {
            $rid = strtolower($st['catchmentName'] ?? $st['riverName'] ?? '');
            $sev = 0;
            foreach ($alertBySev as $id => $s) {
                if (strlen($rid) >= 4 && str_contains($id, substr($rid, 0, 4))) {
                    $sev = max($sev, $s);
                }
            }
            $features[] = [
                'type'     => 'Feature',
                'geometry' => ['type' => 'Point', 'coordinates' => [(float)$st['long'], (float)$st['lat']]],
                'properties' => [
                    'label'    => $st['label'] ?? 'Station',
                    'river'    => $st['riverName'] ?? '',
                    'town'     => $st['town'] ?? '',
                    'severity' => $sev,
                    'colour'   => $sevColour[$sev] ?? '#0ea5e9',
                    'sevLabel' => $sevLabel[$sev] ?? 'Monitoring',
                ],
            ];
        }

        return response()->json([
            'type'         => 'FeatureCollection',
            'features'     => $features,
            'alertCount'   => count($alertItems),
            'stationCount' => count($data['stations']),
        ]);
    }

    // Debug endpoint: visit /ops-map/meshtastic-debug to inspect raw API responses
    public function meshtasticDebug(): \Illuminate\Http\JsonResponse
    {
        $urls = [
            'https://api.meshtastic.org/api/v1/nodes?limit=2',
            'https://meshtastic.liamcottle.net/api/v1/nodes?limit=2',
            'https://meshmap.net/api/v1/nodes',
        ];
        $results = [];
        foreach ($urls as $url) {
            try {
                $resp = Http::timeout(10)
                    ->withHeaders(['Accept' => 'application/json', 'User-Agent' => 'Liverpool-RAYNET/1.0'])
                    ->get($url);
                $body = $resp->body();
                $results[$url] = [
                    'status'     => $resp->status(),
                    'first_500'  => substr($body, 0, 500),
                    'length'     => strlen($body),
                    'first_char' => trim($body)[0] ?? '',
                    'is_json'    => json_decode($body) !== null,
                ];
            } catch (\Throwable $e) {
                $results[$url] = ['error' => $e->getMessage()];
            }
        }
        return response()->json($results);
    }

    // Official meshtastic/map API -> Liam Cottle -> meshmap.net
    public function meshtastic(): \Illuminate\Http\JsonResponse
    {
        $data = Cache::remember('ops_mesh_v5', 180, function () {
            $endpoints = [
                'https://api.meshtastic.org/api/v1/nodes?limit=5000',
                'https://api.meshtastic.org/api/v1/nodes',
                'https://meshtastic.liamcottle.net/api/v1/nodes?limit=5000',
                'https://meshtastic.liamcottle.net/api/v1/nodes',
                'https://meshmap.net/api/v1/nodes',
            ];

            foreach ($endpoints as $url) {
                try {
                    $resp = Http::timeout(15)
                        ->withHeaders([
                            'Accept'     => 'application/json',
                            'User-Agent' => 'RAYNET-CMS/1.0 (' . \App\Helpers\RaynetSetting::siteUrl() . ')',
                            'Referer'    => \App\Helpers\RaynetSetting::siteUrl(),
                        ])
                        ->get($url);

                    if (! $resp->successful()) continue;

                    $body = trim($resp->body());
                    if (empty($body) || ($body[0] !== '[' && $body[0] !== '{')) continue;

                    $decoded = json_decode($body, true);
                    if (json_last_error() !== JSON_ERROR_NONE || ! is_array($decoded)) continue;

                    return ['raw' => $decoded, 'source' => $url];
                } catch (\Throwable $e) {
                    continue;
                }
            }

            return ['error' => 'All Meshtastic endpoints unavailable'];
        });

        if (isset($data['error'])) {
            return response()->json(['features' => [], 'count' => 0, 'error' => $data['error']]);
        }

        $raw = $data['raw'];

        if (isset($raw['nodes']) && is_array($raw['nodes'])) {
            $nodes = $raw['nodes'];
        } elseif (isset($raw['data']) && is_array($raw['data'])) {
            $nodes = $raw['data'];
        } elseif (isset($raw[0])) {
            $nodes = $raw;
        } else {
            $nodes = [];
        }

        $features = [];
        foreach ($nodes as $n) {
            if (! is_array($n)) continue;

            $lat = $n['latitude']  ?? $n['lat'] ?? null;
            $lng = $n['longitude'] ?? $n['lon'] ?? $n['lng'] ?? null;

            if ($lat === null && isset($n['position'])) {
                $pos = $n['position'];
                $lat = $pos['latitude']  ?? $pos['latitude_i']  ?? $pos['lat'] ?? null;
                $lng = $pos['longitude'] ?? $pos['longitude_i'] ?? $pos['lon'] ?? null;
            }

            if ($lat === null || $lng === null) continue;

            if (is_int($lat) && abs($lat) > 1000) $lat = $lat / 1e7;
            if (is_int($lng) && abs($lng) > 1000) $lng = $lng / 1e7;

            $lat = (float) $lat;
            $lng = (float) $lng;

            if ($lat < 49 || $lat > 61 || $lng < -8 || $lng > 2) continue;

            $name = $n['long_name']
                 ?? $n['longName']
                 ?? ($n['user']['longName'] ?? $n['user']['long_name'] ?? null)
                 ?? $n['node_id'] ?? $n['id'] ?? 'Unknown node';

            $short = $n['short_name']
                  ?? $n['shortName']
                  ?? ($n['user']['shortName'] ?? $n['user']['short_name'] ?? '');

            $hw = $n['hardware_model_name']
               ?? $n['hardware']
               ?? $n['hwModel']
               ?? $n['hw_model']
               ?? ($n['user']['hwModel'] ?? '');

            $lastHeard = $n['updated_at']
                      ?? $n['last_heard']
                      ?? $n['lastHeard']
                      ?? null;

            $lastStr = 'Unknown';
            if ($lastHeard) {
                $ts = is_numeric($lastHeard) ? (int) $lastHeard : strtotime($lastHeard);
                if ($ts && $ts > 0) {
                    $diff = now()->timestamp - $ts;
                    $lastStr = $diff < 60 ? 'Just now'
                             : ($diff < 3600  ? floor($diff / 60)   . ' min ago'
                             : ($diff < 86400 ? floor($diff / 3600) . ' hr ago'
                             : date('d M H:i', $ts)));
                }
            }

            $features[] = [
                'type'     => 'Feature',
                'geometry' => ['type' => 'Point', 'coordinates' => [$lng, $lat]],
                'properties' => [
                    'name'     => $name,
                    'short'    => $short,
                    'hw'       => $hw,
                    'lastSeen' => $lastStr,
                    'id'       => $n['node_id'] ?? $n['id'] ?? $n['nodeId'] ?? '',
                ],
            ];
        }

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $features,
            'count'    => count($features),
            'source'   => $data['source'] ?? '',
        ]);
    }

    public function power(): \Illuminate\Http\JsonResponse
    {
        $data = Cache::remember('ops_map_power_v2', 300, function () {
            try {
                $resp = Http::timeout(10)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 Liverpool-RAYNET/1.0',
                        'Accept'     => 'application/json',
                        'Referer'    => 'https://poweroutage.com/uk',
                    ])
                    ->get('https://poweroutage.com/api/web/v1/outages', [
                        'country' => 'uk',
                        'format'  => 'json',
                    ]);

                if ($resp->successful()) {
                    $body = trim($resp->body());
                    if ($body && ($body[0] === '[' || $body[0] === '{')) {
                        $decoded = json_decode($body, true);
                        if (is_array($decoded)) {
                            return ['source' => 'poweroutage.com', 'data' => $decoded];
                        }
                    }
                }
            } catch (\Throwable $e) {
                // fall through
            }

            return [
                'source' => 'none',
                'note'   => 'No public API available for Electricity North West.',
            ];
        });

        $features = [];
        if (($data['source'] ?? '') === 'poweroutage.com') {
            $outages = $data['data'];
            if (isset($outages['outages'])) $outages = $outages['outages'];
            if (! is_array($outages)) $outages = [];

            foreach ($outages as $o) {
                $lat = $o['latitude']  ?? $o['lat'] ?? null;
                $lng = $o['longitude'] ?? $o['lng'] ?? null;
                if (! $lat || ! $lng) continue;
                if ($lat < 53.0 || $lat > 54.0 || $lng < -3.5 || $lng > -2.0) continue;
                $features[] = [
                    'type'     => 'Feature',
                    'geometry' => ['type' => 'Point', 'coordinates' => [(float)$lng, (float)$lat]],
                    'properties' => [
                        'area'   => $o['area'] ?? $o['region'] ?? $o['postcode'] ?? 'Unknown',
                        'homes'  => $o['customersAffected'] ?? $o['affected'] ?? $o['homes'] ?? 0,
                        'status' => $o['status'] ?? 'Active',
                        'dno'    => $o['operator'] ?? $o['dno'] ?? 'ENW',
                        'eta'    => $o['eta'] ?? $o['restorationTime'] ?? '',
                    ],
                ];
            }
        }

        return response()->json([
            'type'     => 'FeatureCollection',
            'features' => $features,
            'count'    => count($features),
            'source'   => $data['source'] ?? 'none',
            'note'     => $data['note'] ?? null,
        ]);
    }

    public function weather(): \Illuminate\Http\JsonResponse
    {
        $data = Cache::remember('ops_map_rainviewer', 120, function () {
            try {
                return Http::timeout(10)->get('https://api.rainviewer.com/public/weather-maps.json')->json();
            } catch (\Throwable $e) {
                return ['error' => $e->getMessage()];
            }
        });
        return response()->json($data);
    }

    public function wind(): \Illuminate\Http\JsonResponse
    {
        $data = Cache::remember('ops_map_wind', 600, function () {
            try {
                return Http::timeout(10)->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude'        => 53.4084,
                    'longitude'       => -2.9916,
                    'current'         => 'wind_speed_10m,wind_gusts_10m,wind_direction_10m,weather_code,temperature_2m',
                    'wind_speed_unit' => 'mph',
                    'timezone'        => 'Europe/London',
                ])->json();
            } catch (\Throwable $e) {
                return ['error' => $e->getMessage()];
            }
        });
        return response()->json($data);
    }

    public function coverage(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'sites' => [
                ['name' => 'GB3MR - Billinge Hill', 'freq' => '145.7875 MHz CTCSS 94.8Hz', 'lat' => 53.4912, 'lng' => -2.7012, 'radiusKm' => 30, 'colour' => '#C8102E'],
                ['name' => 'GB3WL - Winter Hill',   'freq' => '145.6625 MHz CTCSS 94.8Hz', 'lat' => 53.6088, 'lng' => -2.5541, 'radiusKm' => 35, 'colour' => '#003366'],
                ['name' => 'GB7DG - Diggle',        'freq' => '439.9125 MHz',              'lat' => 53.5488, 'lng' => -2.0112, 'radiusKm' => 25, 'colour' => '#7c3aed'],
            ],
            'note' => 'Approximate coverage - replace with MEARL export',
        ]);
    }
}