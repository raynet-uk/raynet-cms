<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CmsLicence extends Model
{
    protected $fillable = [
        'key', 'group_name', 'group_number', 'gc_name', 'gc_email',
        'notes', 'is_active', 'activated_at', 'activated_by_ip', 'activated_site_url',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'activated_at' => 'datetime',
    ];

    public static function generate(
        string $groupName = '',
        string $groupNumber = '',
        string $gcName = '',
        string $gcEmail = '',
        string $notes = ''
    ): self {
        // Format: RAYNET-{GROUPREF}-{RANDOM16}
        $groupRef = strtoupper(preg_replace('/[^A-Z0-9]/', '', $groupNumber ?: $groupName));
        $groupRef = substr($groupRef, 0, 8) ?: 'GROUP';
        $random   = strtoupper(Str::random(16));
        $key      = "RAYNET-{$groupRef}-{$random}";

        return self::create([
            'key'          => $key,
            'group_name'   => $groupName,
            'group_number' => $groupNumber,
            'gc_name'      => $gcName,
            'gc_email'     => $gcEmail,
            'notes'        => $notes,
            'is_active'    => true,
        ]);
    }

    public function isUsed(): bool
    {
        return $this->activated_at !== null;
    }

    public function activate(string $ip, string $siteUrl): void
    {
        $this->update([
            'activated_at'      => now(),
            'activated_by_ip'   => $ip,
            'activated_site_url'=> $siteUrl,
        ]);
    }
}
