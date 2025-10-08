<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('providers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->boolean('enabled')->default(false);
            $table->string('access_object');
            $table->string('auth_route');

            $table->index('name');
        });
    }
};
