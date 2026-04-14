<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class QRZLookup
{
    protected string $sessionKey = '';

    public function lookup(string $callsign): ?array
    {
        $callsign = strtoupper(trim($callsign));

        return Cache::remember("qrz_{$callsign}", 3600, function () use ($callsign) {
            try {
                $sessionKey = $this->getSessionKey();
                if (!$sessionKey) return null;

                $response = Http::timeout(5)->get('https://xmldata.qrz.com/xml/current/', [
                    's'        => $sessionKey,
                    'callsign' => $callsign,
                ]);

                if (!$response->ok()) return null;

                $xml = simplexml_load_string($response->body());
                if (!$xml) return null;

                $calldata = $xml->Callsign ?? null;
                if (!$calldata) return null;

                return [
                    'callsign' => (string)($calldata->call ?? $callsign),
                    'name'     => trim((string)($calldata->fname ?? '') . ' ' . (string)($calldata->name ?? '')),
                    'country'  => (string)($calldata->country ?? ''),
                    'state'    => (string)($calldata->state ?? ''),
                    'class'    => (string)($calldata->class ?? ''),
                    'email'    => (string)($calldata->email ?? ''),
                    'image'    => (string)($calldata->image ?? ''),
                    'bio'      => (string)($calldata->bio ?? ''),
                    'grid'     => (string)($calldata->grid ?? ''),
                ];
            } catch (\Throwable $e) {
                return null;
            }
        });
    }

    protected function getSessionKey(): string
    {
        return Cache::remember('qrz_session_key', 3000, function () {
            try {
                $username = config('services.qrz.username', '');
                $password = config('services.qrz.password', '');

                if (!$username || !$password) return '';

                $response = Http::timeout(5)->get('https://xmldata.qrz.com/xml/current/', [
                    'username' => $username,
                    'password' => $password,
                    'agent'    => 'Liverpool-RAYNET/1.0',
                ]);

                if (!$response->ok()) return '';

                $xml = simplexml_load_string($response->body());
                if (!$xml) return '';

                return (string)($xml->Session->Key ?? '');
            } catch (\Throwable $e) {
                return '';
            }
        });
    }
}
