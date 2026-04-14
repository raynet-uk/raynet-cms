<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use SimpleXMLElement;
use Throwable;

class RefreshRsgbNews extends Command
{
    protected $signature = 'rsgb:refresh-news';
    protected $description = 'Fetch latest RSGB news from RSS and cache it';

    public function handle()
    {
        $rssUrl = 'https://rsgb.org/main/blog/category/news/feed/';

        $this->info("Starting refresh from: $rssUrl");

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'RAYNET-Liverpool-Dashboard/1.0 (+https://raynet-liverpool.uk)',
            ])->timeout(12)->get($rssUrl);

            if ($response->failed()) {
                throw new \Exception("HTTP failed: " . $response->status());
            }

            $xml = $response->body();
            $xmlLength = strlen($xml);
            $this->info("Fetched XML, length: $xmlLength bytes");

            Log::info("RSGB RSS fetch success, length: $xmlLength");
            Log::debug("XML preview: " . substr($xml, 0, 400));

            libxml_use_internal_errors(true);
            $rss = new SimpleXMLElement($xml);

            // Register all namespaces from the feed
            $rss->registerXPathNamespace('content', 'http://purl.org/rss/1.0/modules/content/');
            $rss->registerXPathNamespace('wfw', 'http://wellformedweb.org/CommentAPI/');
            $rss->registerXPathNamespace('dc', 'http://purl.org/dc/elements/1.1/');
            $rss->registerXPathNamespace('atom', 'http://www.w3.org/2005/Atom');
            $rss->registerXPathNamespace('sy', 'http://purl.org/rss/1.0/modules/syndication/');
            $rss->registerXPathNamespace('slash', 'http://purl.org/rss/1.0/modules/slash/');

            $channelTitle = trim((string) ($rss->channel->title ?? 'No title'));
            $this->info("Channel title: $channelTitle");
            Log::info("Channel title: $channelTitle");

            $items = $rss->xpath('//item');
            $itemCount = count($items);
            $this->info("Found items via XPath: $itemCount");

            Log::info("XPath item count: $itemCount");

            $headlines = [];

            foreach ($items as $item) {
                $title = trim((string) $item->title);
                $link  = trim((string) $item->link);
                $pubDate = (string) $item->pubDate;

                if (!$title || !$link) continue;

                $date = date('d M', strtotime($pubDate)) ?: '—';

                $headlines[] = [
                    'title' => $title,
                    'link'  => $link,
                    'date'  => $date,
                ];

                $this->line(" - Added: $title ($date)");
            }

            if (empty($headlines)) {
                $this->error("No headlines extracted! Check logs.");
                Log::warning("No headlines - XML may have parsing issue");
            } else {
                $count = count($headlines);
                $this->info("Success! Extracted $count headlines");
                Log::info("Cached $count RSGB headlines");
            }

            Cache::forever('rsgb_news', [
                'headlines'  => $headlines,
                'updated_at' => now()->toDateTimeString(),
            ]);

        } catch (Throwable $e) {
            $this->error("Error: " . $e->getMessage());
            Log::error("RSGB refresh error: " . $e->getMessage());
        }
    }
}