<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'reporte_id',
        'imagen_path',
        'tipo',
        'descripcion'
    ];

    /**
     * RELACIONES
     */

    // Una evidencia pertenece a UN reporte
    public function reporte()
    {
        return $this->belongsTo(Reporte::class);
    }

    /**
     * ATRIBUTOS CALCULADOS
     */

    // URL completa de la imagen
    // En app/Models/Evidencia.php
    public function getUrlImagenAttribute()
    {
        // Si ya es una URL completa, retornarla
        if (filter_var($this->imagen_path, FILTER_VALIDIDATE_URL)) {
            return $this->imagen_path;
        }

        // Si empieza con 'http', agregar // para evitar problemas
        if (str_starts_with($this->imagen_path, 'http')) {
            return $this->imagen_path;
        }

        // Usar Storage si el archivo existe
        if (Storage::exists($this->imagen_path)) {
            return Storage::url($this->imagen_path);
        }

        // Fallback a asset()
        return asset('storage/' . $this->imagen_path);
    }

    // Ruta física del archivo
    public function getRutaCompletaAttribute()
    {
        return storage_path('app/public/' . $this->imagen_path);
    }

    // Nombre del archivo
    public function getNombreArchivoAttribute()
    {
        return basename($this->imagen_path);
    }
}