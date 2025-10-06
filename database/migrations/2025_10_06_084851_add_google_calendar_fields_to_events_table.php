<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->foreignId('calendar_id')->after('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('external_identifier')->nullable()->after('calendar_id');

            $table->index('external_identifier');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dropForeign(['calendar_id']);
            $table->dropIndex(['external_identifier']);
            $table->dropColumn(['external_identifier', 'calendar_id']);
        });
    }
};
