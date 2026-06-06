<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) { Schema::create('users', function (Blueprint $t) {}); }
        if (!Schema::hasTable('password_reset_tokens')) { Schema::create('password_reset_tokens', function (Blueprint $t) {}); }
        if (!Schema::hasTable('sessions')) { Schema::create('sessions', function (Blueprint $t) {}); }
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
