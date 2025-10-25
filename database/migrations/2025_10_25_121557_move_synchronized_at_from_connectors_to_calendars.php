<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('calendars', function (Blueprint $table): void {
            $table->timestamp('synchronized_at')->nullable()->after('enabled');
        });

        Schema::table('connectors', function (Blueprint $table): void {
            $table->dropColumn('synchronized_at');
        });
    }

    public function down(): void
    {
        Schema::table('connectors', function (Blueprint $table): void {
            $table->timestamp('synchronized_at')->nullable()->after('enabled');
        });

        Schema::table('calendars', function (Blueprint $table): void {
            $table->dropColumn('synchronized_at');
        });
    }
};
