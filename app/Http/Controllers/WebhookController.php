<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebhookController extends Controller
{
    /**
     * Terima event sesi dari wa-gateway dan log ke database.
     */
    public function session(Request $request)
    {
        $payload = $request->all();

        DB::table('session_events')->insert([
            'session'    => data_get($payload, 'session') ?? data_get($payload, 'sessionId') ?? data_get($payload, 'device'),
            'status'     => data_get($payload, 'status'),
            'event'      => data_get($payload, 'event') ?? data_get($payload, 'type'),
            'phone'      => data_get($payload, 'phone') ?? data_get($payload, 'jid'),
            'payload'    => $payload,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Terima event pesan dari wa-gateway dan log ke database.
     */
    public function message(Request $request)
    {
        $payload = $request->all();
        $fromMe = data_get($payload, 'fromMe');

        DB::table('message_events')->insert([
            'session'    => data_get($payload, 'session') ?? data_get($payload, 'sessionId') ?? data_get($payload, 'device'),
            'direction'  => data_get($payload, 'direction') ?? ($fromMe === true ? 'out' : ($fromMe === false ? 'in' : null)),
            'from'       => data_get($payload, 'from') ?? data_get($payload, 'sender') ?? data_get($payload, 'jid'),
            'to'         => data_get($payload, 'to') ?? data_get($payload, 'receiver'),
            'type'       => data_get($payload, 'type') ?? data_get($payload, 'message.type'),
            'message_id' => data_get($payload, 'id') ?? data_get($payload, 'key.id') ?? data_get($payload, 'message_id'),
            'payload'    => $payload,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Ambil log event terakhir untuk inspeksi cepat.
     */
    public function logs(Request $request, string $type)
    {
        $table = match ($type) {
            'session' => 'session_events',
            'message' => 'message_events',
            default   => null,
        };

        if (! $table) {
            abort(404);
        }

        $limit = max(1, min((int) $request->query('limit', 50), 200));

        $data = DB::table($table)
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        return response()->json(['data' => $data]);
    }

    /**
     * Tampilkan log melalui Blade sederhana untuk inspeksi manual.
     */
    public function dashboard(Request $request)
    {
        $type = $request->query('type', 'message');

        $table = match ($type) {
            'session' => 'session_events',
            'message' => 'message_events',
            default   => 'message_events',
        };

        $limit = max(1, min((int) $request->query('limit', 50), 200));

        $logs = DB::table($table)
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                $payload = $row->payload;
                if (is_string($payload)) {
                    $decoded = json_decode($payload, true);
                    $row->payload = $decoded ?: $payload;
                }

                return $row;
            });

        return view('webhook.logs', [
            'type'  => $type,
            'limit' => $limit,
            'logs'  => $logs,
        ]);
    }
}
