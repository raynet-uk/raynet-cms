<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventDocument;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Public list of upcoming events.
     */
    public function index()
    {
        $events = Event::with('type')
            ->where('starts_at', '>=', Carbon::today()->startOfDay())
            ->orderBy('starts_at')
            ->paginate(10);

        return view('pages.events.index', compact('events'));
    }

    /**
     * Public event detail.
     */
    public function show(int $year, int $month, string $slug)
    {
        $event = Event::with(['type', 'documents'])
            ->whereYear('starts_at', $year)
            ->whereMonth('starts_at', $month)
            ->where('slug', $slug)
            ->firstOrFail();

        return view('events.show', compact('event'));
    }

    /**
     * Public document download.
     * Route: GET /events/documents/{document}/download
     */
    public function downloadDocument(EventDocument $document)
    {
        abort_unless(
            Storage::disk($document->disk)->exists($document->path),
            404
        );

        return Storage::disk($document->disk)
            ->download($document->path, $document->filename);
    }

    /**
     * Single-event ICS export.
     */
    public function ics(int $year, int $month, string $slug)
    {
        $event = Event::with('type')
            ->whereYear('starts_at', $year)
            ->whereMonth('starts_at', $month)
            ->where('slug', $slug)
            ->firstOrFail();

        $siteName = config('app.name', 'Liverpool RAYNET');
        $domain   = parse_url(config('app.url', 'https://example.com'), PHP_URL_HOST) ?? 'example.com';

        // UID for the event in the calendar
        $uid = 'event-' . $event->id . '@' . $domain;

        // Times in UTC, as ICS expects
        $start = $event->starts_at ?? Carbon::now();
        $end   = $event->ends_at ?: $start->copy()->addHours(2);

        $dtStart = $start->copy()->utc()->format('Ymd\THis\Z');
        $dtEnd   = $end->copy()->utc()->format('Ymd\THis\Z');
        $dtStamp = now()->utc()->format('Ymd\THis\Z');

        $summary = $event->title;

        // Build description: main description + type label if present
        $descriptionPieces = [];
        if ($event->description) {
            $descriptionPieces[] = $event->description;
        }
        if ($event->type) {
            $descriptionPieces[] = 'Type: ' . $event->type->name;
        }

        // Join with real newlines; we'll escape them to \n in ICS
        $description = implode("\n", $descriptionPieces);

        $location = $event->location ?? '';

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//' . $this->escapeIcsText($siteName) . '//EN',
            'CALSCALE:GREGORIAN',
            'BEGIN:VEVENT',
            'UID:' . $this->escapeIcsText($uid),
            'DTSTAMP:' . $dtStamp,
            'DTSTART:' . $dtStart,
            'DTEND:' . $dtEnd,
            'SUMMARY:' . $this->escapeIcsText($summary),
        ];

        if ($location !== '') {
            $lines[] = 'LOCATION:' . $this->escapeIcsText($location);
        }

        if ($description !== '') {
            $lines[] = 'DESCRIPTION:' . $this->escapeIcsText($description);
        }

        $lines[] = 'END:VEVENT';
        $lines[] = 'END:VCALENDAR';

        // ICS wants CRLF line endings
        $body = implode("\r\n", $lines) . "\r\n";

        return response($body, 200, [
            'Content-Type'        => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="event-' . $event->id . '.ics"',
        ]);
    }

    /**
     * Escape text for ICS (RFC 5545).
     */
    private function escapeIcsText(string $text): string
    {
        // Backslashes first
        $text = str_replace('\\', '\\\\', $text);
        // Then semicolons and commas
        $text = str_replace(';', '\\;', $text);
        $text = str_replace(',', '\\,', $text);
        // Newlines -> \n
        $text = str_replace(["\r\n", "\r", "\n"], '\\n', $text);

        return $text;
    }
}