<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendars', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnUpdate()->restrictOnDelete();
            $table->string('external_user_id');
            $table->string('external_calendar_id');
            $table->text('external_token');
            $table->text('external_refresh_token');
            $table->timestamp('external_token_expires_at');
            $table->timestamps();

            $table->index('external_user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendars');
    }
};
