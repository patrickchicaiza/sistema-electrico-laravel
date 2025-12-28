<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo',
        'user_id',
        'tecnico_asignado_id',
        'descripcion',
        'direccion',
        'prioridad',
        'estado',
        'solucion',
        'fecha_cierre'
    ];

    protected $casts = [
        'fecha_cierre' => 'datetime',
    ];

    /**
     * RELACIONES
     */

    // Un reporte pertenece a un CLIENTE (quien lo creó)
    public function cliente()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Un reporte puede tener un TÉCNICO asignado
    public function tecnico()
    {
        return $this->belongsTo(User::class, 'tecnico_asignado_id');
    }

    // Un reporte puede tener MUCHAS evidencias (fotos)
    public function evidencias()
    {
        return $this->hasMany(Evidencia::class);
    }

    /**
     * SCOPES ÚTILES
     */

    // Scope para reportes pendientes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    // Scope para reportes asignados a un técnico específico
    public function scopeAsignadosATecnico($query, $tecnicoId)
    {
        return $query->where('tecnico_asignado_id', $tecnicoId)
            ->whereIn('estado', ['asignado', 'en_proceso']);
    }

    // Scope para reportes activos (no resueltos ni cancelados)
    public function scopeActivos($query)
    {
        return $query->whereNotIn('estado', ['resuelto', 'cancelado']);
    }

    // Scope por prioridad
    public function scopePrioridad($query, $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }

    /**
     * EVENTOS (OBSERVERS) - Para generar código automático
     */

    protected static function boot()
    {
        parent::boot();

        // Generar código automáticamente antes de crear
        static::creating(function ($reporte) {
            if (empty($reporte->codigo)) {
                $reporte->codigo = self::generarCodigo();
            }
        });
    }

    /**
     * MÉTODOS ESTÁTICOS
     */

    // Generar código único: REP-2025-001
    public static function generarCodigo()
    {
        $year = date('Y');
        $lastReport = self::where('codigo', 'like', "REP-{$year}-%")
            ->orderBy('codigo', 'desc')
            ->first();

        if ($lastReport) {
            $lastNumber = (int) substr($lastReport->codigo, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return "REP-{$year}-{$newNumber}";
    }

    /**
     * ATRIBUTOS CALCULADOS
     */

    // Verificar si está pendiente
    public function getEstaPendienteAttribute()
    {
        return $this->estado === 'pendiente';
    }

    // Verificar si está asignado
    public function getEstaAsignadoAttribute()
    {
        return $this->estado === 'asignado';
    }

    // Verificar si está en proceso
    public function getEstaEnProcesoAttribute()
    {
        return $this->estado === 'en_proceso';
    }

    // Verificar si está resuelto
    public function getEstaResueltoAttribute()
    {
        return $this->estado === 'resuelto';
    }

    // Tiempo transcurrido desde creación
    public function getTiempoTranscurridoAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    // Verificar si tiene técnico asignado
    public function getTieneTecnicoAttribute()
    {
        return !is_null($this->tecnico_asignado_id);
    }
}