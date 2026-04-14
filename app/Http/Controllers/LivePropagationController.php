<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\AlertStatus;

class LivePropagationController extends Controller
{
    public function index()
    {
        $data = [
            'sfi' => '--',
            'sunspots' => '--',
            'solarWind' => '--',
            'latestKp' => '--',
            'xray' => '--',
            'aIndex' => '--',
            'muf' => '--',
            'auroraProb' => 5,
        ];

        try {
            // -----------------------------
            // 1. Solar Flux & Sunspots
            // -----------------------------
            $f107Response = Http::timeout(10)->get('https://services.swpc.noaa.gov/json/f107_cm_flux.json');
            if ($f107Response->ok()) {
                $f107Data = $f107Response->json();
                if (!empty($f107Data)) {
                    $latest = end($f107Data);
                    $data['sfi'] = $latest['f107'] ?? '--';
                    $data['sunspots'] = $latest['ssn'] ?? '--';
                }
            }

            // -----------------------------
            // 2. Solar Wind (km/s)
            // -----------------------------
            $windResponse = Http::timeout(10)->get('https://services.swpc.noaa.gov/json/rtsw/rtsw_wind_1m.json');
            if ($windResponse->ok()) {
                $windData = $windResponse->json();
                if (!empty($windData)) {
                    $lastWind = end($windData);
                    $data['solarWind'] = $lastWind['speed'] ?? '--';
                }
            }

            // -----------------------------
            // 3. Planetary K Index
            // -----------------------------
            $kpResponse = Http::timeout(10)->get('https://services.swpc.noaa.gov/json/planetary_k_index_1m.json');
            if ($kpResponse->ok()) {
                $kpData = $kpResponse->json();
                if (!empty($kpData)) {
                    $latestKp = end($kpData);
                    $data['latestKp'] = $latestKp['kp_index'] ?? '--';
                }
            }

            // -----------------------------
            // 4. X-ray Flux
            // -----------------------------
            $xrayResponse = Http::timeout(10)->get('https://services.swpc.noaa.gov/json/goes/primary/xrays-1-day.json');
            if ($xrayResponse->ok()) {
                $xrayData = $xrayResponse->json();
                if (!empty($xrayData)) {
                    $latestXray = end($xrayData);
                    $data['xray'] = $latestXray['short_band_flux'] ?? '--';
                }
            }

            // -----------------------------
            // 5. Planetary A Index
            // -----------------------------
            $aIndexResponse = Http::timeout(10)->get('https://services.swpc.noaa.gov/json/planetary_k_index.json');
            if ($aIndexResponse->ok()) {
                $aData = $aIndexResponse->json();
                if (!empty($aData)) {
                    $data['aIndex'] = $aData[0]['a_index'] ?? '--';
                }
            }

            // -----------------------------
            // 6. MUF Estimate
            // -----------------------------
            if (is_numeric($data['sfi']) && is_numeric($data['solarWind']) && is_numeric($data['latestKp'])) {
                $data['muf'] = round(($data['sfi']/10) + ($data['solarWind']/50) - ($data['latestKp']*1.5), 1);
            }

            // -----------------------------
            // 7. Aurora Probability
            // -----------------------------
            if (is_numeric($data['latestKp'])) {
                $kp = (float) $data['latestKp'];
                $data['auroraProb'] = $kp >= 4 ? min(100, ($kp-3)*25) : 5;
            }

        } catch (\Exception $e) {
            \Log::error('LivePropagation API error: '.$e->getMessage());
        }

        // -----------------------------
        // 8. RAYNET Alert
        // -----------------------------
        $alertStatus = AlertStatus::query()->latest()->first();

        return view('data-dashboard', [
            'sfi' => is_numeric($data['sfi']) ? round($data['sfi'],1) : $data['sfi'],
            'sunspots' => $data['sunspots'],
            'solarWind' => is_numeric($data['solarWind']) ? round($data['solarWind'],0) : $data['solarWind'],
            'latestKp' => $data['latestKp'],
            'xray' => is_numeric($data['xray']) ? round($data['xray'],2) : $data['xray'],
            'aIndex' => $data['aIndex'],
            'muf' => is_numeric($data['muf']) ? round($data['muf'],1) : $data['muf'],
            'auroraProb' => $data['auroraProb'],
            'alertStatus' => $alertStatus,
        ]);
    }
}