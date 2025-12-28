<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();

            // Código único para el reporte
            $table->string('codigo')->unique();

            // Relación con cliente que reporta
            $table->foreignId('user_id')->constrained('users');

            // Relación con técnico asignado (puede ser null inicialmente)
            $table->foreignId('tecnico_asignado_id')
                ->nullable()
                ->constrained('users');

            // Información del reporte
            $table->text('descripcion');
            $table->text('direccion');
            $table->enum('prioridad', ['alta', 'media', 'baja'])->default('media');
            $table->enum('estado', [
                'pendiente',
                'asignado',
                'en_proceso',
                'resuelto',
                'cancelado'
            ])->default('pendiente');

            // Campos para solución (llenados por técnico)
            $table->text('solucion')->nullable();
            $table->timestamp('fecha_cierre')->nullable();

            // Timestamps automáticos
            $table->timestamps();

            // Índices para optimizar consultas
            $table->index('estado');
            $table->index('prioridad');
            $table->index('user_id');
            $table->index('tecnico_asignado_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};