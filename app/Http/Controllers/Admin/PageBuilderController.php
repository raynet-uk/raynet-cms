<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PageBuilderController extends Controller
{
    protected string $pagesPath;
    protected string $backupsPath;

    public function __construct()
    {
        $this->pagesPath  = resource_path('views/pages');
        $this->backupsPath = storage_path('app/page-backups');
    }

    public function builder(string $slug)
    {
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        $path = $this->pagesPath . '/' . $slug . '.blade.php';
        abort_unless(File::exists($path), 404);

        $raw      = File::get($path);
        $modified = Carbon::createFromTimestamp(File::lastModified($path));

        // Extract CSS, @push scripts and content section
        $pageStyles  = $this->extractStyles($raw);
        $contentHtml = $this->extractContentSection($raw);

        // Parse into blocks — first check if we have saved builder blocks
        $savedKey    = 'page_builder_blocks_' . $slug;
        $savedBlocks = Setting::get($savedKey, null);

        if ($savedBlocks) {
            $blocks = json_decode($savedBlocks, true) ?? [];
        } else {
            $blocks = $this->parseHtmlToBlocks($contentHtml ?? '');
        }

        return view('admin.pages.builder', [
            'slug'       => $slug,
            'modified'   => $modified,
            'pageStyles' => $pageStyles,
            'blocks'     => $blocks,
            'blocksJson' => json_encode($blocks),
        ]);
    }

    public function saveBlocks(Request $request, string $slug)
    {
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug);
        $path = $this->pagesPath . '/' . $slug . '.blade.php';
        abort_unless(File::exists($path), 404);

        $request->validate([
            'blocks'     => ['required', 'string'],
            'page_styles'=> ['nullable', 'string'],
        ]);

        $blocks     = json_decode($request->blocks, true);
        $pageStyles = $request->input('page_styles', '');

        // Validate blocks is an array
        if (!is_array($blocks)) {
            return back()->withErrors(['blocks' => 'Invalid block data.']);
        }

        // Save blocks to settings for later editing
        Setting::set('page_builder_blocks_' . $slug, json_encode($blocks));

        // Backup current file
        $this->createBackup($slug, $path);

        // Reconstruct the blade file
        $raw         = File::get($path);
        $contentHtml = $this->renderBlocksToHtml($blocks);
        $newRaw      = $this->replaceContentSection($raw, $pageStyles, $contentHtml);

        File::put($path, $newRaw);
        Artisan::call('view:clear');

        return redirect()->route('admin.pages.builder', $slug)
            ->with('success', 'Page saved successfully.');
    }

    // ── HTML Parsing ────────────────────────────────────────────────────────

    protected function extractStyles(string $blade): string
    {
        $styles = '';
        if (preg_match_all('/<style[^>]*>(.*?)<\/style>/si', $blade, $m)) {
            $styles = implode("\n", $m[1]);
        }
        return trim($styles);
    }

    protected function extractContentSection(string $blade): ?string
    {
        if (preg_match("/@section\(['\"]content['\"]\)(.*?)@endsection/s", $blade, $m)) {
            $content = preg_replace('/<style[^>]*>.*?<\/style>/si', '', $m[1]);
            return trim($content);
        }
        return null;
    }

    protected function parseHtmlToBlocks(string $html): array
    {
        if (empty(trim($html))) {
            return [];
        }

        $segments = $this->extractTopLevelSegments($html);

        // If only 1–2 segments, go one level deeper (page likely has a single wrapper div)
        if (count($segments) <= 2) {
            foreach ($segments as $segment) {
                $inner = $this->extractInnerHtml($segment);
                if ($inner !== null) {
                    $deeper = $this->extractTopLevelSegments($inner);
                    if (count($deeper) >= 2) {
                        $segments = $deeper;
                        break;
                    }
                }
            }
        }

        $blocks = [];
        $id = 1;

        foreach ($segments as $segment) {
            $segment = trim($segment);
            if (empty($segment)) continue;
            $text   = trim(strip_tags($segment));
            $hasImg = stripos($segment, '<img') !== false;
            if (empty($text) && !$hasImg) continue;

            $blocks[] = [
                'id'   => 'block-' . $id++,
                'type' => 'html',
                'data' => ['content' => $segment],
            ];
        }

        return $blocks ?: [['id' => 'block-1', 'type' => 'html', 'data' => ['content' => $html]]];
    }

    /**
     * Extract the inner HTML of the first block-level element (to go one level deeper).
     */
    protected function extractInnerHtml(string $segment): ?string
    {
        // Match opening tag of a block element
        if (!preg_match('/^<([a-z][a-z0-9]*)[^>]*>/i', trim($segment), $m)) {
            return null;
        }
        $tag = strtolower($m[1]);
        $openLen = strlen($m[0]);
        $closeTag = '</' . $tag . '>';
        $closePos = strrpos($segment, $closeTag);
        if ($closePos === false) return null;
        return substr($segment, $openLen, $closePos - $openLen);
    }

    /**
     * Split HTML into top-level segments using a depth-tracking string parser.
     * Never uses DOMDocument::saveHTML(), so attribute values are never mangled.
     */
    protected function extractTopLevelSegments(string $html): array
    {
        $segments = [];
        $pos      = 0;
        $len      = strlen($html);

        // Void/self-closing elements
        $voidTags = ['area','base','br','col','embed','hr','img','input',
                     'link','meta','param','source','track','wbr'];

        while ($pos < $len) {
            // Skip leading whitespace between top-level nodes
            $tagStart = strpos($html, '<', $pos);
            if ($tagStart === false) {
                // Trailing text
                $text = trim(substr($html, $pos));
                if ($text !== '') $segments[] = $text;
                break;
            }

            // Collect any text before the tag
            if ($tagStart > $pos) {
                $text = trim(substr($html, $pos, $tagStart - $pos));
                if ($text !== '') $segments[] = $text;
            }

            // Read the opening tag
            $tagEnd = strpos($html, '>', $tagStart);
            if ($tagEnd === false) break;

            $openTag = substr($html, $tagStart, $tagEnd - $tagStart + 1);

            // Comments & doctype — skip
            if (strncmp($openTag, '<!--', 4) === 0 || strncmp($openTag, '<!', 2) === 0) {
                $closePos = strpos($html, strncmp($openTag, '<!--', 4) === 0 ? '-->' : '>', $tagStart + 2);
                $pos = ($closePos !== false ? $closePos + (strncmp($openTag, '<!--', 4) === 0 ? 3 : 1) : $tagEnd + 1);
                continue;
            }

            // Extract tag name
            preg_match('/^<([a-z][a-z0-9]*)/i', $openTag, $m);
            $tagName = isset($m[1]) ? strtolower($m[1]) : '';

            // Self-closing or void
            if (empty($tagName) || substr($openTag, -2) === '/>' || in_array($tagName, $voidTags)) {
                $segments[] = $openTag;
                $pos = $tagEnd + 1;
                continue;
            }

            // Walk forward tracking depth to find the matching close tag
            $depth     = 1;
            $searchPos = $tagEnd + 1;

            while ($depth > 0 && $searchPos < $len) {
                $nextOpen  = stripos($html, '<' . $tagName, $searchPos);
                $nextClose = stripos($html, '</' . $tagName, $searchPos);

                if ($nextClose === false) { $searchPos = $len; break; }

                if ($nextOpen !== false && $nextOpen < $nextClose) {
                    // Make sure it's actually an opening tag (not e.g. a word starting the same way)
                    $charAfter = $html[$nextOpen + 1 + strlen($tagName)] ?? '';
                    if (preg_match('/[\s>\/]/', $charAfter)) {
                        $depth++;
                    }
                    $searchPos = $nextOpen + 1;
                } else {
                    $depth--;
                    if ($depth === 0) {
                        $closeEnd = strpos($html, '>', $nextClose);
                        if ($closeEnd !== false) {
                            $segments[] = substr($html, $tagStart, $closeEnd - $tagStart + 1);
                            $pos = $closeEnd + 1;
                        } else {
                            $pos = $len;
                        }
                    } else {
                        $searchPos = $nextClose + 1;
                    }
                }
            }

            if ($depth > 0) {
                // Unmatched — take everything remaining
                $segments[] = substr($html, $tagStart);
                break;
            }
        }

        return array_values(array_filter($segments, fn($s) => trim($s) !== ''));
    }

        // ── HTML Rendering ──────────────────────────────────────────────────────

    protected function renderBlocksToHtml(array $blocks): string
    {
        $parts = [];
        foreach ($blocks as $block) {
            $parts[] = $this->renderBlock($block);
        }
        return implode("\n\n", array_filter($parts));
    }

    protected function renderBlock(array $block): string
    {
        $d = $block['data'] ?? [];
        switch ($block['type']) {
            case 'heading':
                $level = htmlspecialchars($d['level'] ?? 'h2');
                $text  = $d['text'] ?? '';
                $align = $d['align'] ?? 'left';
                $color = $d['color'] ?? '#003366';
                return "<{$level} style=\"text-align:{$align};color:{$color};margin:.75rem 0\">{$text}</{$level}>";

            case 'text':
                return $d['content'] ?? '';

            case 'hero':
                $bg    = $d['bg'] ?? '#003366';
                $color = $d['color'] ?? '#ffffff';
                $align = $d['align'] ?? 'center';
                $pad   = $d['padding'] ?? '3rem 1.5rem';
                $title = htmlspecialchars($d['title'] ?? '');
                $sub   = htmlspecialchars($d['subtitle'] ?? '');
                $btnT  = htmlspecialchars($d['button_text'] ?? '');
                $btnU  = htmlspecialchars($d['button_url'] ?? '#');
                $html  = "<div style=\"background:{$bg};color:{$color};padding:{$pad};text-align:{$align}\">\n";
                $html .= "    <h1 style=\"font-size:2.25rem;font-weight:bold;margin-bottom:.75rem;color:{$color}\">{$title}</h1>\n";
                if ($sub) $html .= "    <p style=\"font-size:1.1rem;opacity:.85;margin-bottom:1.5rem;max-width:600px;margin-left:auto;margin-right:auto\">{$sub}</p>\n";
                if ($btnT) $html .= "    <a href=\"{$btnU}\" style=\"display:inline-block;padding:.75rem 2rem;background:#C8102E;color:#fff;font-weight:bold;text-decoration:none\">{$btnT}</a>\n";
                $html .= "</div>";
                return $html;

            case 'two-col':
                $left  = $d['left'] ?? '<p>Left column content.</p>';
                $right = $d['right'] ?? '<p>Right column content.</p>';
                $gap   = $d['gap'] ?? '2rem';
                return "<div style=\"display:grid;grid-template-columns:1fr 1fr;gap:{$gap};padding:1.5rem 0\">\n    <div>{$left}</div>\n    <div>{$right}</div>\n</div>";

            case 'three-col':
                $c1  = $d['col1'] ?? '<p>Column 1.</p>';
                $c2  = $d['col2'] ?? '<p>Column 2.</p>';
                $c3  = $d['col3'] ?? '<p>Column 3.</p>';
                return "<div style=\"display:grid;grid-template-columns:1fr 1fr 1fr;gap:1.5rem;padding:1.5rem 0\">\n    <div>{$c1}</div>\n    <div>{$c2}</div>\n    <div>{$c3}</div>\n</div>";

            case 'alert':
                $msg   = $d['message'] ?? 'Alert message here.';
                $type  = $d['style'] ?? 'info';
                $colors = [
                    'info'    => ['bg' => '#eff6ff', 'border' => '#2563eb', 'color' => '#1e40af'],
                    'success' => ['bg' => '#eef7f2', 'border' => '#1a6b3c', 'color' => '#1a6b3c'],
                    'warning' => ['bg' => '#fffbeb', 'border' => '#f59e0b', 'color' => '#92400e'],
                    'danger'  => ['bg' => '#fdf0f2', 'border' => '#C8102E', 'color' => '#C8102E'],
                ];
                $c = $colors[$type] ?? $colors['info'];
                return "<div style=\"background:{$c['bg']};border-left:4px solid {$c['border']};color:{$c['color']};padding:1rem 1.25rem;margin:1rem 0\">{$msg}</div>";

            case 'cta':
                $title = htmlspecialchars($d['title'] ?? 'Ready to get involved?');
                $sub   = htmlspecialchars($d['subtitle'] ?? '');
                $btnT  = htmlspecialchars($d['button_text'] ?? 'Get Started');
                $btnU  = htmlspecialchars($d['button_url'] ?? '#');
                $bg    = $d['bg'] ?? '#f4f5f7';
                return "<div style=\"background:{$bg};padding:3rem 2rem;text-align:center;margin:1.5rem 0\">\n    <h2 style=\"font-size:1.75rem;font-weight:bold;color:#003366;margin-bottom:.5rem\">{$title}</h2>\n    " . ($sub ? "<p style=\"color:#6b7f96;margin-bottom:1.5rem\">{$sub}</p>\n    " : '') . "<a href=\"{$btnU}\" style=\"display:inline-block;padding:.75rem 2rem;background:#003366;color:#fff;font-weight:bold;text-decoration:none\">{$btnT}</a>\n</div>";

            case 'divider':
                $color = $d['color'] ?? '#dde2e8';
                return "<hr style=\"border:none;border-top:1px solid {$color};margin:2rem 0\">";

            case 'image':
                $src     = $d['src'] ?? '';
                $alt     = htmlspecialchars($d['alt'] ?? '');
                $caption = $d['caption'] ?? '';
                $align   = $d['align'] ?? 'center';
                $html    = "<figure style=\"text-align:{$align};margin:1.5rem 0\">\n    <img src=\"{$src}\" alt=\"{$alt}\" style=\"max-width:100%;height:auto\">\n";
                if ($caption) $html .= "    <figcaption style=\"font-size:.875rem;color:#6b7f96;margin-top:.5rem\">{$caption}</figcaption>\n";
                $html .= "</figure>";
                return $html;

            case 'html':
            default:
                return $d['content'] ?? '';
        }
    }

    protected function replaceContentSection(string $blade, string $css, string $newContent): string
    {
        // Build the new content section with styles + content
        $stylesBlock = $css ? "<style>\n{$css}\n</style>\n\n" : '';
        $newSection  = "\n{$stylesBlock}{$newContent}\n";

        // Replace existing @section('content')...@endsection
        if (preg_match("/@section\(['\"]content['\"]\)/", $blade)) {
            return preg_replace(
                "/(@section\(['\"]content['\"]\))(.*?)(@endsection)/s",
                '$1' . $newSection . '$3',
                $blade
            );
        }

        // Append if not found
        return $blade . "\n@section('content')\n{$newSection}@endsection\n";
    }

    protected function createBackup(string $slug, string $path): void
    {
        if (!File::isDirectory($this->backupsPath)) {
            File::makeDirectory($this->backupsPath, 0755, true);
        }
        File::copy($path, $this->backupsPath . '/' . $slug . '_' . now()->format('Ymd_His') . '.blade.php');
    }
}