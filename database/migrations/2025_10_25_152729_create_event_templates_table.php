<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_templates', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('calendar_id')->constrained();
            $table->string('name');
            $table->string('event_title');
            $table->boolean('event_all_day')->default(false);
            $table->string('event_timezone')->nullable();
            $table->text('event_description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_templates');
    }
};
