<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vehiculos')) {
            Schema::create('vehiculos', function (Blueprint $table) {
                // Table already exists in the database
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};
