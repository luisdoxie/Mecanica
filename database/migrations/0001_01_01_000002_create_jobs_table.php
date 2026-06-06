<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('jobs')) { Schema::create('jobs', function (Blueprint $t) {}); }
        if (!Schema::hasTable('job_batches')) { Schema::create('job_batches', function (Blueprint $t) {}); }
        if (!Schema::hasTable('failed_jobs')) { Schema::create('failed_jobs', function (Blueprint $t) {}); }
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};
