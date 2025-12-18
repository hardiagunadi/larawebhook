<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webhook Logs</title>
    <style>
        :root {
            --bg: #0b1622;
            --panel: #0f253a;
            --text: #e5edf5;
            --muted: #9fb4c9;
            --accent: #57c2ff;
            --accent-2: #f6b73c;
            --border: #18344f;
            --danger: #ff7b7b;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: "Inter", "Segoe UI", system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at 20% 20%, rgba(87, 194, 255, 0.08), transparent 25%), radial-gradient(circle at 80% 0%, rgba(246, 183, 60, 0.08), transparent 20%), var(--bg);
            color: var(--text);
            min-height: 100vh;
        }
        .wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 32px 20px 80px;
        }
        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        h1 {
            margin: 0;
            font-size: 28px;
            letter-spacing: -0.02em;
        }
        .note {
            color: var(--muted);
            margin-top: 6px;
            font-size: 14px;
        }
        form {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 18px;
        }
        label {
            display: flex;
            gap: 6px;
            align-items: center;
            color: var(--muted);
            font-size: 14px;
        }
        select, input[type="number"] {
            background: var(--panel);
            border: 1px solid var(--border);
            color: var(--text);
            border-radius: 8px;
            padding: 8px 10px;
            min-width: 120px;
        }
        button {
            background: linear-gradient(135deg, var(--accent), #4aa0e2);
            color: #0c1928;
            border: none;
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(87, 194, 255, 0.25);
            transition: transform 0.12s ease, box-shadow 0.12s ease;
        }
        button:hover { transform: translateY(-1px); box-shadow: 0 12px 28px rgba(87, 194, 255, 0.3); }
        button:active { transform: translateY(0); box-shadow: 0 8px 18px rgba(87, 194, 255, 0.2); }
        .panel {
            margin-top: 22px;
            background: rgba(15, 37, 58, 0.9);
            border: 1px solid var(--border);
            border-radius: 14px;
            overflow: hidden;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }
        th {
            background: rgba(24, 52, 79, 0.7);
            text-align: left;
            color: var(--muted);
            font-weight: 600;
            letter-spacing: 0.01em;
        }
        tr:nth-child(even) td { background: rgba(15, 37, 58, 0.5); }
        .tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 9px;
            border-radius: 999px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
            letter-spacing: 0.03em;
            background: rgba(87, 194, 255, 0.15);
            color: var(--accent);
            border: 1px solid rgba(87, 194, 255, 0.25);
        }
        .tag.warn { background: rgba(246, 183, 60, 0.15); color: var(--accent-2); border-color: rgba(246, 183, 60, 0.25); }
        .tag.danger { background: rgba(255, 123, 123, 0.15); color: var(--danger); border-color: rgba(255, 123, 123, 0.2); }
        .payload {
            background: #0c1d2d;
            border: 1px solid var(--border);
            border-radius: 10px;
            padding: 10px;
            margin: 6px 0 0;
            color: #c9e1ff;
            white-space: pre-wrap;
            word-break: break-word;
            font-family: "JetBrains Mono", "SFMono-Regular", Consolas, monospace;
            font-size: 12px;
        }
        .muted { color: var(--muted); }
        .meta { color: var(--muted); font-size: 12px; margin-top: 4px; }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 6px 12px;
            margin: 6px 0 0;
        }
        .grid span { display: block; color: var(--text); }
        .grid small { color: var(--muted); }
        .pill {
            display: inline-block;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border);
            padding: 6px 10px;
            border-radius: 8px;
            color: var(--muted);
        }
        @media (max-width: 768px) {
            th, td { padding: 10px 12px; }
            table { font-size: 13px; }
        }
    </style>
</head>
<body>
<div class="wrap">
    <header>
        <div>
            <h1>Webhook Monitor</h1>
            <div class="note">Menampilkan data dari tabel <code>session_events</code> dan <code>message_events</code>.</div>
        </div>
        <div class="pill">GET /webhook</div>
    </header>

    <form method="get" action="{{ url('/webhook') }}">
        <label>
            Jenis:
            <select name="type">
                <option value="message" {{ $type === 'message' ? 'selected' : '' }}>Message events</option>
                <option value="session" {{ $type === 'session' ? 'selected' : '' }}>Session events</option>
            </select>
        </label>
        <label>
            Limit:
            <input type="number" name="limit" value="{{ $limit }}" min="1" max="200">
        </label>
        <button type="submit">Refresh</button>
    </form>

    <div class="panel">
        <table>
            <thead>
            <tr>
                <th style="width: 80px;">ID</th>
                <th>Info</th>
                <th>Payload</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($logs as $log)
                @php
                    $payloadText = is_array($log->payload) || is_object($log->payload)
                        ? json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                        : (string) $log->payload;
                    $isMessage = $type === 'message';
                @endphp
                <tr>
                    <td>
                        <div class="tag">{{ strtoupper($type) }}</div>
                        <div class="meta">#{{ $log->id }}</div>
                        <div class="meta">{{ $log->created_at }}</div>
                    </td>
                    <td>
                        <div class="grid">
                            <div>
                                <small>Session</small>
                                <span>{{ $log->session ?? '—' }}</span>
                            </div>
                            @if ($isMessage)
                                <div>
                                    <small>Direction</small>
                                    <span class="{{ ($log->direction ?? '') === 'out' ? '' : 'muted' }}">{{ $log->direction ?? '—' }}</span>
                                </div>
                                <div>
                                    <small>From</small>
                                    <span>{{ $log->from ?? '—' }}</span>
                                </div>
                                <div>
                                    <small>To</small>
                                    <span>{{ $log->to ?? '—' }}</span>
                                </div>
                                <div>
                                    <small>Type</small>
                                    <span>{{ $log->type ?? '—' }}</span>
                                </div>
                                <div>
                                    <small>Message ID</small>
                                    <span class="muted">{{ $log->message_id ?? '—' }}</span>
                                </div>
                            @else
                                <div>
                                    <small>Status</small>
                                    <span>{{ $log->status ?? '—' }}</span>
                                </div>
                                <div>
                                    <small>Event</small>
                                    <span>{{ $log->event ?? '—' }}</span>
                                </div>
                                <div>
                                    <small>Phone</small>
                                    <span>{{ $log->phone ?? '—' }}</span>
                                </div>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="payload">{{ $payloadText }}</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="muted" style="text-align:center; padding:28px;">Tidak ada data.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
