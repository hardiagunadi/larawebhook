<?php

use App\Http\Controllers\WebhookController;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::withoutMiddleware([VerifyCsrfToken::class])->group(function () {
    Route::post('/webhook/session', [WebhookController::class, 'session']);
    Route::post('/webhook/message', [WebhookController::class, 'message']);
});

Route::get('/webhook', [WebhookController::class, 'dashboard']);
Route::get('/webhook/logs/{type}', [WebhookController::class, 'logs']);
