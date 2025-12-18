<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('message_events', function (Blueprint $table) {
            $table->id();
            $table->string('session', 120)->nullable()->index();
            $table->string('direction', 20)->nullable();
            $table->string('from', 255)->nullable();
            $table->string('to', 255)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('message_id', 191)->nullable();
            $table->json('payload');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_events');
    }
};
