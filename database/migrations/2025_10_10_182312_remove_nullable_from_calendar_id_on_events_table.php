<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dropForeign(['calendar_id']);
            $table->foreignId('calendar_id')->nullable(false)->change()->constrained()->restrictOnUpdate()->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dropForeign(['calendar_id']);
            $table->foreignId('calendar_id')->nullable()->change()->constrained()->restrictOnUpdate()->restrictOnDelete();
        });
    }
};
