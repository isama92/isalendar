<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('connectors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnUpdate()->restrictOnDelete();
            $table->foreignId('provider_id')->constrained()->restrictOnUpdate()->restrictOnDelete();
            $table->string('external_id');
            $table->text('access_details');
            $table->boolean('enabled')->default(false);
            $table->timestamps();

            $table->index('external_id');
        });
    }
};
