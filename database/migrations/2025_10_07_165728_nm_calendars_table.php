<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calendars', function (Blueprint $table): void {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
                'external_user_id',
                'external_token',
                'external_refresh_token',
                'external_token_expires_at',
                'external_calendar_id',
                'external_calendar_name',
                'external_calendar_description',
                'external_calendar_timezone',
            ]);

            $table->string('external_id')->after('id');
            $table->string('name')->after('external_id');
            $table->text('description')->nullable()->after('name');
            $table->boolean('enabled')->default(true)->after('description');
            $table->softDeletes();

            $table->unique('external_id');
            $table->index('name');
        });

        Schema::create('calendar_connector', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('calendar_id')->constrained()->restrictOnUpdate()->restrictOnDelete();
            $table->foreignId('connector_id')->constrained()->restrictOnUpdate()->restrictOnDelete();
        });
    }
};
