<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // personas ya se crea en 0001_01_01_000000 junto con users y sessions
    }

    public function down(): void
    {
        //
    }
};
