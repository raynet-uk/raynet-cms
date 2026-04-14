<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BackfillEventSlugs extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'events:backfill-slugs';

    /**
     * The console command description.
     */
    protected $description = 'Generate slugs for events that are missing them, ensuring uniqueness.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Scanning for events without slugs…');

        $events = Event::whereNull('slug')
            ->orWhere('slug', '')
            ->orderBy('id')
            ->get();

        if ($events->isEmpty()) {
            $this->info('No events need slugs. Nothing to do.');
            return self::SUCCESS;
        }

        $this->info("Found {$events->count()} event(s) without slugs.");

        foreach ($events as $event) {
            // Base slug from title; fall back to event-{id} if title is empty
            $baseSlug = Str::slug($event->title ?: 'event-' . $event->id);

            if ($baseSlug === '') {
                $baseSlug = 'event-' . $event->id;
            }

            $slug   = $baseSlug;
            $suffix = 2;

            // Ensure uniqueness across the table
            while (
                Event::where('slug', $slug)
                    ->where('id', '!=', $event->id)
                    ->exists()
            ) {
                $slug = $baseSlug . '-' . $suffix;
                $suffix++;
            }

            $event->slug = $slug;
            $event->save();

            $this->line("• Event #{$event->id} \"{$event->title}\" → slug: {$slug}");
        }

        $this->info('Done. All missing slugs have been backfilled.');
        return self::SUCCESS;
    }
}