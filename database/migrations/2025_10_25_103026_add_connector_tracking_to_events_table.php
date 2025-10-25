<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->foreignId('created_by_connector_id')
                ->nullable()
                ->after('calendar_id')
                ->constrained('connectors')
                ->onUpdate('restrict')
                ->onDelete('restrict');

            $table->foreignId('last_modified_by_connector_id')
                ->nullable()
                ->after('created_by_connector_id')
                ->constrained('connectors')
                ->onUpdate('restrict')
                ->onDelete('restrict');
        });

        // Backfill existing events with a connector from their calendar
        DB::table('events')
            ->whereNull('created_by_connector_id')
            ->orWhereNull('last_modified_by_connector_id')
            ->chunkById(100, function ($events): void {
                foreach ($events as $event) {
                    $connectorId = DB::table('calendar_connector')
                        ->where('calendar_connector.calendar_id', $event->calendar_id)
                        ->value('calendar_connector.connector_id');

                    if (is_null($connectorId)) {
                        $connectorId = 1;
                    }

                    DB::table('events')
                        ->where('id', $event->id)
                        ->update([
                            'created_by_connector_id' => $connectorId,
                            'last_modified_by_connector_id' => $connectorId,
                        ]);
                }
            });

        Schema::table('events', function (Blueprint $table): void {
            $table->foreignId('last_modified_by_connector_id')
                ->nullable(false)
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dropForeign(['created_by_connector_id']);
            $table->dropForeign(['last_modified_by_connector_id']);
            $table->dropColumn(['created_by_connector_id', 'last_modified_by_connector_id']);
        });
    }
};
