<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('cache')) { Schema::create('cache', function (Blueprint $t) {}); }
        if (!Schema::hasTable('cache_locks')) { Schema::create('cache_locks', function (Blueprint $t) {}); }
    }

    public function down(): void
    {
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');
    }
};
