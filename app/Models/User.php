<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'telefono',
        'direccion',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * RELACIONES
     */

    // Un usuario (CLIENTE) puede tener MUCHOS reportes que él creó
    public function reportesComoCliente()
    {
        return $this->hasMany(Reporte::class, 'user_id');
    }

    // Un usuario (TÉCNICO) puede tener MUCHOS reportes asignados a él
    public function reportesComoTecnico()
    {
        return $this->hasMany(Reporte::class, 'tecnico_asignado_id');
    }

    /**
     * SCOPES ÚTILES
     */

    // Scope para usuarios con rol específico
    public function scopeConRol($query, $rol)
    {
        return $query->whereHas('roles', function ($q) use ($rol) {
            $q->where('name', $rol);
        });
    }

    // Scope para técnicos disponibles (sin muchos reportes pendientes)
    public function scopeTecnicosDisponibles($query)
    {
        return $query->conRol('tecnico')
            ->whereDoesntHave('reportesComoTecnico', function ($q) {
                $q->whereIn('estado', ['asignado', 'en_proceso']);
            }, '>=', 3); // Máximo 3 reportes activos
    }

    /**
     * ATRIBUTOS CALCULADOS
     */

    // Verificar si es cliente
    public function getEsClienteAttribute()
    {
        return $this->hasRole('cliente');
    }

    // Verificar si es técnico
    public function getEsTecnicoAttribute()
    {
        return $this->hasRole('tecnico');
    }

    // Verificar si es administrador
    public function getEsAdministradorAttribute()
    {
        return $this->hasRole('administrador') || $this->hasRole('super_admin');
    }

    // Contar reportes activos (para validar límite de 3)
    public function getReportesActivosCountAttribute()
    {
        return $this->reportesComoCliente()
            ->whereIn('estado', ['pendiente', 'asignado', 'en_proceso'])
            ->count();
    }

    // Verificar si puede crear más reportes
    public function getPuedeCrearReporteAttribute()
    {
        return $this->esCliente && $this->reportes_activos_count < 3;
    }
}