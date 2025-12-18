# Catatan Penting Webhook

- Endpoint UI monitoring: buka `/webhook` (pilih `type` message/session, atur `limit` 1-200).
- Endpoint API JSON cepat: `/webhook/logs/message` dan `/webhook/logs/session` (opsional `?limit=50`).
- Penerima webhook:
  - `POST /webhook/session` menyimpan event ke tabel `session_events`.
  - `POST /webhook/message` menyimpan event ke tabel `message_events`.
- Payload tersimpan mentah di kolom `payload` (JSON), sehingga setiap skema gateway masih tercapture.
- Jalankan server lokal: `php artisan serve` lalu akses UI/endpoint di browser atau Postman.
