<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DmrLogStreamController extends Controller
{
    private const WS_HOST    = 'm0kkn.dragon-net.pl';
    private const WS_PORT    = 9002;
    private const WS_PATH    = '/';
    private const MAX_SECS   = 55;  // stay under typical 60s server timeout
    private const READ_TIMEOUT = 0.1; // seconds per read attempt

    /**
     * SSE endpoint — browser connects once over HTTPS,
     * we proxy the HBLink WebSocket server-side and push lines as events.
     *
     * Route:  GET /members/dmr-network/stream
     * Auth:   auth + verified (handled by route group)
     */
    public function stream(Request $request): StreamedResponse
    {
        return response()->stream(function () {
            // SSE headers already set by ->stream(), but we need to
            // flush buffers and disable output buffering
            if (ob_get_level()) ob_end_flush();
            
            @ini_set('zlib.output_compression', 0);

            // Send a keepalive comment immediately so the browser
            // knows the connection is alive
            $this->sseComment('Liverpool RAYNET HBLink proxy connected');
            $this->sseEvent('status', json_encode(['state' => 'connecting']));

            // Open TCP socket to HBLink WebSocket server
            $socket = $this->openWebSocket();

            if (!$socket) {
                $this->sseEvent('status', json_encode([
                    'state'   => 'error',
                    'message' => 'Could not connect to HBLink server',
                ]));
                return;
            }

            $this->sseEvent('status', json_encode(['state' => 'connected']));

            $start    = time();
            $partial  = '';

            // Read loop — runs for up to MAX_SECS then we close
            // (browser will reconnect automatically via EventSource)
            while (
                !connection_aborted() &&
                (time() - $start) < self::MAX_SECS
            ) {
                $data = @fread($socket, 65536);

                if ($data === false || feof($socket)) {
                    break;
                }

                if ($data !== '') {
                    $partial .= $data;

                    // WebSocket frames arrive as binary-framed data.
                    // Parse and emit each complete text frame.
                    $frames = $this->extractFrames($partial);
                    foreach ($frames['messages'] as $msg) {
                        if (trim($msg) !== '') {
                            // Each line in the message becomes its own event
                            foreach (explode("\n", $msg) as $line) {
                                $line = trim($line);
                                if ($line !== '') {
                                    $this->sseEvent('log', json_encode(['line' => $line]));
                                }
                            }
                        }
                    }
                    $partial = $frames['remainder'];
                }

                // Keepalive ping every ~5s to prevent proxy timeouts
                if ((time() - $start) % 5 === 0) {
                    $this->sseComment('ping');
                }
            }

            fclose($socket);

            // Tell client to reconnect immediately
            $this->sseEvent('status', json_encode([
                'state'   => 'reconnect',
                'message' => 'Stream ended — reconnecting',
            ]));

        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-store',
            'X-Accel-Buffering' => 'no',   // nginx: disable proxy buffering
            'Connection'        => 'keep-alive',
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────────

    private function openWebSocket(): mixed
    {
        $host = self::WS_HOST;
        $port = self::WS_PORT;

        $ctx = stream_context_create([
            'socket' => ['tcp_nodelay' => true],
        ]);

        $socket = @stream_socket_client(
            "tcp://{$host}:{$port}",
            $errno,
            $errstr,
            5, // connect timeout
            STREAM_CLIENT_CONNECT,
            $ctx
        );

        if (!$socket) return null;

        stream_set_blocking($socket, false);
        stream_set_timeout($socket, 0, (int)(self::READ_TIMEOUT * 1000000));

        // WebSocket HTTP upgrade handshake
        $key      = base64_encode(random_bytes(16));
        $headers  = "GET " . self::WS_PATH . " HTTP/1.1\r\n"
                  . "Host: {$host}:{$port}\r\n"
                  . "Upgrade: websocket\r\n"
                  . "Connection: Upgrade\r\n"
                  . "Sec-WebSocket-Key: {$key}\r\n"
                  . "Sec-WebSocket-Version: 13\r\n"
                  . "Origin: http://{$host}\r\n"
                  . "\r\n";

        fwrite($socket, $headers);

        // Read the HTTP 101 response (blocking briefly)
        stream_set_blocking($socket, true);
        $response = '';
        $deadline = microtime(true) + 3;
        while (microtime(true) < $deadline) {
            $line = fgets($socket, 4096);
            if ($line === false) break;
            $response .= $line;
            if (str_ends_with($response, "\r\n\r\n")) break;
        }
        stream_set_blocking($socket, false);

        // Verify upgrade succeeded
        if (!str_contains($response, '101')) {
            fclose($socket);
            return null;
        }

        return $socket;
    }

    /**
     * Parse WebSocket frames from a raw byte string.
     * Returns ['messages' => [...text frames...], 'remainder' => string]
     *
     * Only handles unmasked text frames (server→client are never masked).
     */
    private function extractFrames(string $data): array
    {
        $messages  = [];
        $pos       = 0;
        $len       = strlen($data);

        while ($pos < $len) {
            if ($pos + 2 > $len) break; // need at least 2 bytes

            $byte1   = ord($data[$pos]);
            $byte2   = ord($data[$pos + 1]);
            $opcode  = $byte1 & 0x0F;
            $masked  = ($byte2 & 0x80) !== 0;
            $payLen  = $byte2 & 0x7F;
            $pos    += 2;

            // Extended payload length
            if ($payLen === 126) {
                if ($pos + 2 > $len) { $pos -= 2; break; }
                $payLen = unpack('n', substr($data, $pos, 2))[1];
                $pos   += 2;
            } elseif ($payLen === 127) {
                if ($pos + 8 > $len) { $pos -= 2; break; }
                // Large frames — skip (HBmonitor text is tiny)
                $pos += 8;
            }

            // Masking key (client→server only, shouldn't happen here)
            $maskKey = '';
            if ($masked) {
                if ($pos + 4 > $len) { $pos -= 2; break; }
                $maskKey = substr($data, $pos, 4);
                $pos    += 4;
            }

            // Payload
            if ($pos + $payLen > $len) {
                $pos -= 2; // rewind, wait for more data
                if ($payLen > 126) $pos -= 8;
                if ($masked) $pos -= 4;
                break;
            }

            $payload = substr($data, $pos, $payLen);
            $pos    += $payLen;

            // Unmask if needed
            if ($masked && $maskKey !== '') {
                for ($i = 0; $i < strlen($payload); $i++) {
                    $payload[$i] = chr(ord($payload[$i]) ^ ord($maskKey[$i % 4]));
                }
            }

            // Opcode 1 = text frame, opcode 8 = close
            if ($opcode === 1) {
                $messages[] = $payload;
            } elseif ($opcode === 8) {
                break; // server sent close frame
            }
            // Opcode 0 = continuation, 2 = binary, 9 = ping, 10 = pong — ignore
        }

        return [
            'messages'  => $messages,
            'remainder' => substr($data, $pos),
        ];
    }

    private function sseEvent(string $event, string $data): void
    {
        echo "event: {$event}\n";
        // SSE data must not contain raw newlines — encode in JSON already handles this
        echo "data: {$data}\n\n";
        flush();
    }

    private function sseComment(string $comment): void
    {
        echo ": {$comment}\n\n";
        flush();
    }
}