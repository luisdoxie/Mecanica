<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repuestos_utilizados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_id')->constrained('ordenes_trabajo')->cascadeOnDelete();
            $table->string('nombre');
            $table->string('origen')->nullable();
            $table->string('calidad_observada')->nullable();
            $table->decimal('costo', 10, 2)->default(0);
            $table->integer('cantidad')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repuestos_utilizados');
    }
};
