<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id();

            // Relación con el reporte
            $table->foreignId('reporte_id')
                ->constrained('reportes')
                ->onDelete('cascade'); // Si eliminas reporte, eliminas evidencias

            // Ruta de la imagen (en storage)
            $table->string('imagen_path');

            // Tipo de evidencia (opcional)
            $table->enum('tipo', ['antes', 'durante', 'despues'])->default('antes');

            // Descripción opcional
            $table->text('descripcion')->nullable();

            $table->timestamps();

            // Índices
            $table->index('reporte_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evidencias');
    }
};