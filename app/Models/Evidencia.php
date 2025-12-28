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
    public function getUrlImagenAttribute()
    {
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