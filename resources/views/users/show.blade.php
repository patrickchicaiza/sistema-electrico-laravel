@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Encabezado con navegación -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('users.index') }}">
                                    <i class="fas fa-arrow-left me-1"></i> Volver a usuarios
                                </a>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-circle me-2"></i> Perfil de Usuario
                    </h1>
                </div>

                <div class="d-flex gap-2">
                    <!-- Botón imprimir -->
                    <button class="btn btn-outline-secondary" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Imprimir
                    </button>
                    
                    @can('editar-usuarios')
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Editar Usuario
                    </a>
                    @endcan
                </div>
            </div>

            <!-- Grid principal: 2 columnas -->
            <div class="row">
                <!-- Columna izquierda: Información personal -->
                <div class="col-lg-8">
                    <!-- Card de información básica -->
                    <div class="card mb-4">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-id-card me-2"></i> Información Personal
                            </h5>
                            <div>
                                @if($user->id == auth()->id())
                                <span class="badge bg-info">Tú</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Nombre completo</label>
                                    <div class="fw-bold h5">{{ $user->name }}</div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Email</label>
                                    <div class="fw-bold">
                                        {{ $user->email }}
                                        @if($user->email_verified_at)
                                        <span class="badge bg-success ms-2">
                                            <i class="fas fa-check-circle"></i> Verificado
                                        </span>
                                        @else
                                        <span class="badge bg-warning ms-2">
                                            <i class="fas fa-clock"></i> Pendiente
                                        </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Teléfono</label>
                                    <div class="fw-bold">
                                        @if($user->telefono)
                                            {{ $user->telefono }}
                                        @else
                                            <span class="text-muted">No registrado</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Dirección</label>
                                    <div class="fw-bold">
                                        @if($user->direccion)
                                            {{ $user->direccion }}
                                        @else
                                            <span class="text-muted">No registrada</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Fecha de registro</label>
                                    <div class="fw-bold">
                                        {{ $user->created_at->format('d/m/Y H:i') }}
                                        <small class="text-muted">({{ $user->created_at->diffForHumans() }})</small>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small mb-1">Última actualización</label>
                                    <div class="fw-bold">
                                        {{ $user->updated_at->format('d/m/Y H:i') }}
                                        <small class="text-muted">({{ $user->updated_at->diffForHumans() }})</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card de roles y permisos -->
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fas fa-user-shield me-2"></i> Roles y Permisos
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <!-- Roles asignados -->
                                <div class="col-md-6 mb-4">
                                    <h6 class="text-muted mb-3">Roles asignados</h6>
                                    @php
                                        $colores = [
                                            'super_admin' => 'danger',
                                            'administrador' => 'warning',
                                            'tecnico' => 'info',
                                            'cliente' => 'primary'
                                        ];
                                    @endphp
                                    
                                    @foreach($user->getRoleNames() as $role)
                                    <div class="d-flex align-items-center mb-2">
                                        <span class="badge bg-{{ $colores[$role] ?? 'secondary' }} p-2 me-2" style="font-size: 1em;">
                                            @switch($role)
                                                @case('super_admin')
                                                    👑 {{ ucfirst($role) }}
                                                    @break
                                                @case('administrador')
                                                    🛡️ {{ ucfirst($role) }}
                                                    @break
                                                @case('tecnico')
                                                    🔧 {{ ucfirst($role) }}
                                                    @break
                                                @case('cliente')
                                                    👤 {{ ucfirst($role) }}
                                                    @break
                                                @default
                                                    {{ ucfirst($role) }}
                                            @endswitch
                                        </span>
                                        <small class="text-muted">
                                            {{ $user->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    @endforeach
                                </div>

                                <!-- Permisos directos -->
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-3">Permisos directos</h6>
                                    @if($user->getDirectPermissions()->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($user->getDirectPermissions() as $permiso)
                                            <span class="badge bg-light text-dark border">
                                                {{ $permiso->name }}
                                            </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-light">
                                            <i class="fas fa-info-circle me-2"></i>
                                            No tiene permisos directos asignados
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: Estadísticas y acciones -->
                <div class="col-lg-4">
                    <!-- Card de avatar y acciones -->
                    <div class="card mb-4">
                        <div class="card-body text-center">
                            <!-- Avatar grande -->
                            <div class="mb-3">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto"
                                    style="width: 100px; height: 100px; font-size: 40px;">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            </div>
                            
                            <h5 class="mb-1">{{ $user->name }}</h5>
                            <p class="text-muted mb-3">
                                {{ $user->getRoleNames()->first() }}
                            </p>
                            
                            <div class="d-grid gap-2">
                                <!-- Botón eliminar (con confirmación) -->
                                @can('eliminar-usuarios')
                                @if(auth()->id() != $user->id && 
                                    !($user->hasRole('super_admin') && !auth()->user()->hasRole('super_admin')))
                                <button type="button" class="btn btn-outline-danger" 
                                        data-bs-toggle="modal" data-bs-target="#modalEliminar">
                                    <i class="fas fa-trash me-1"></i> Eliminar Usuario
                                </button>
                                @endif
                                @endcan
                                
                                <!-- Botón cambiar contraseña (solo para el propio usuario o admin) -->
                                @if(auth()->id() == $user->id || auth()->user()->hasRole('administrador') || auth()->user()->hasRole('super_admin'))
                                <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalCambiarPassword">
                                    <i class="fas fa-key me-1"></i> Cambiar Contraseña
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Card de estadísticas según rol -->
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-bar me-2"></i> Estadísticas
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($user->hasRole('cliente'))
                                <!-- Estadísticas para cliente -->
                                <div class="mb-3">
                                    <h6 class="text-muted small mb-2">Reportes creados</h6>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="h4 mb-1 text-warning">
                                                {{ $user->reportesComoCliente()->where('estado', 'pendiente')->count() }}
                                            </div>
                                            <small class="text-muted">Pendientes</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="h4 mb-1 text-primary">
                                                {{ $user->reportesComoCliente()->whereIn('estado', ['asignado', 'en_proceso'])->count() }}
                                            </div>
                                            <small class="text-muted">En proceso</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="h4 mb-1 text-success">
                                                {{ $user->reportesComoCliente()->where('estado', 'resuelto')->count() }}
                                            </div>
                                            <small class="text-muted">Resueltos</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="border-top pt-3">
                                    <h6 class="text-muted small mb-2">Límite de reportes</h6>
                                    <div class="progress" style="height: 10px;">
                                        @php
                                            $reportesActivos = $user->reportesComoCliente()
                                                ->whereIn('estado', ['pendiente', 'asignado', 'en_proceso'])
                                                ->count();
                                            $porcentaje = ($reportesActivos / 3) * 100;
                                        @endphp
                                        <div class="progress-bar bg-{{ $reportesActivos >= 3 ? 'danger' : 'info' }}" 
                                             style="width: {{ $porcentaje }}%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-1">
                                        <small class="text-muted">{{ $reportesActivos }}/3 activos</small>
                                        <small class="text-muted">{{ round($porcentaje) }}%</small>
                                    </div>
                                </div>
                                
                            @elseif($user->hasRole('tecnico'))
                                <!-- Estadísticas para técnico -->
                                <div class="mb-3">
                                    <h6 class="text-muted small mb-2">Reportes asignados</h6>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="h4 mb-1 text-info">
                                                {{ $user->reportesComoTecnico()->where('estado', 'asignado')->count() }}
                                            </div>
                                            <small class="text-muted">Por atender</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="h4 mb-1 text-primary">
                                                {{ $user->reportesComoTecnico()->where('estado', 'en_proceso')->count() }}
                                            </div>
                                            <small class="text-muted">En reparación</small>
                                        </div>
                                        <div class="col-4">
                                            <div class="h4 mb-1 text-success">
                                                {{ $user->reportesComoTecnico()->where('estado', 'resuelto')->count() }}
                                            </div>
                                            <small class="text-muted">Completados</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="border-top pt-3">
                                    <h6 class="text-muted small mb-2">Eficiencia</h6>
                                    @php
                                        $totalAsignados = $user->reportesComoTecnico()->count();
                                        $resueltos = $user->reportesComoTecnico()->where('estado', 'resuelto')->count();
                                        $eficiencia = $totalAsignados > 0 ? round(($resueltos / $totalAsignados) * 100) : 0;
                                    @endphp
                                    <div class="text-center">
                                        <div class="display-4 mb-1 {{ $eficiencia >= 80 ? 'text-success' : ($eficiencia >= 60 ? 'text-warning' : 'text-danger') }}">
                                            {{ $eficiencia }}%
                                        </div>
                                        <small class="text-muted">Tasa de resolución</small>
                                    </div>
                                </div>
                                
                            @elseif($user->hasRole('administrador') || $user->hasRole('super_admin'))
                                <!-- Estadísticas para administrador -->
                                <div class="text-center py-3">
                                    <div class="display-4 text-primary mb-2">
                                        <i class="fas fa-user-shield"></i>
                                    </div>
                                    <h6 class="text-muted">Administrador del sistema</h6>
                                    <p class="text-muted small">
                                        Acceso completo a todas las funcionalidades
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Card de actividad reciente -->
                    <div class="card">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                <i class="fas fa-history me-2"></i> Actividad Reciente
                            </h6>
                        </div>
                        <div class="card-body">
                            @php
                                // Obtener actividad según rol
                                if ($user->hasRole('cliente')) {
                                    $actividad = $user->reportesComoCliente()
                                        ->latest()
                                        ->limit(3)
                                        ->get();
                                } elseif ($user->hasRole('tecnico')) {
                                    $actividad = $user->reportesComoTecnico()
                                        ->latest()
                                        ->limit(3)
                                        ->get();
                                } else {
                                    $actividad = collect(); // Administradores no tienen actividad específica
                                }
                            @endphp
                            
                            @if($actividad->count() > 0)
                                <div class="timeline">
                                    @foreach($actividad as $item)
                                    <div class="timeline-item mb-3">
                                        <div class="timeline-marker 
                                            @if($item->estado == 'resuelto') bg-success
                                            @elseif($item->estado == 'en_proceso') bg-primary
                                            @elseif($item->estado == 'asignado') bg-info
                                            @else bg-warning @endif">
                                        </div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1 small">
                                                    Reporte {{ $item->codigo }}
                                                </h6>
                                                <small class="text-muted">
                                                    {{ $item->updated_at->diffForHumans() }}
                                                </small>
                                            </div>
                                            <p class="mb-0 small text-truncate">
                                                {{ $item->descripcion }}
                                            </p>
                                            <span class="badge bg-{{ $item->prioridad == 'alta' ? 'danger' : 'secondary' }}">
                                                {{ $item->prioridad }}
                                            </span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                
                                @if($user->hasRole('cliente'))
                                <div class="mt-3">
                                    <a href="{{ route('reportes.index') }}" class="btn btn-sm btn-outline-primary w-100">
                                        <i class="fas fa-list me-1"></i> Ver todos los reportes
                                    </a>
                                </div>
                                @endif
                                
                            @else
                                <div class="text-center py-3">
                                    <div class="display-4 text-muted mb-2">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <p class="text-muted small mb-0">
                                        @if($user->hasRole('cliente'))
                                            No ha creado reportes aún
                                        @elseif($user->hasRole('tecnico'))
                                            No tiene reportes asignados
                                        @else
                                            Sin actividad registrada
                                        @endif
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para eliminar usuario -->
@can('eliminar-usuarios')
@if(auth()->id() != $user->id && 
    !($user->hasRole('super_admin') && !auth()->user()->hasRole('super_admin')))
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de eliminar al usuario <strong>{{ $user->name }}</strong>?</p>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>¡Advertencia!</strong> Esta acción no se puede deshacer.
                </div>
                <ul class="text-muted">
                    <li>Email: {{ $user->email }}</li>
                    <li>Rol: {{ $user->getRoleNames()->first() }}</li>
                    <li>Registrado: {{ $user->created_at->format('d/m/Y') }}</li>
                </ul>
                @if($user->reportesComoCliente()->count() > 0 || $user->reportesComoTecnico()->count() > 0)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Este usuario tiene reportes asociados que podrían quedar huérfanos.
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Sí, eliminar usuario</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endcan

<!-- Modal para cambiar contraseña -->
@if(auth()->id() == $user->id || auth()->user()->hasRole('administrador') || auth()->user()->hasRole('super_admin'))
<div class="modal fade" id="modalCambiarPassword" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-key me-2"></i>
                    Cambiar Contraseña
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nueva contraseña</label>
                        <input type="password" name="password" class="form-control" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirmar contraseña</label>
                        <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                    </div>
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="hidden" name="roles[]" value="{{ $user->getRoleNames()->first() }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Cambiar contraseña</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@push('styles')
<style>
    .avatar {
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline-item {
        position: relative;
        margin-bottom: 15px;
    }
    
    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    
    .timeline-content {
        padding-left: 0;
    }
    
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 0.5rem;
    }
    
    @media print {
        .btn, .modal, .breadcrumb {
            display: none !important;
        }
        
        .container-fluid {
            padding: 0;
        }
        
        .card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
        }
        
        .card-body {
            padding: 10px !important;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tooltips
        var tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltips.map(function(tooltip) {
            return new bootstrap.Tooltip(tooltip);
        });
        
        // Validación del formulario de cambio de contraseña
        const formCambiarPassword = document.querySelector('form[action*="users"]');
        if (formCambiarPassword && formCambiarPassword.querySelector('input[name="password"]')) {
            formCambiarPassword.addEventListener('submit', function(e) {
                const password = this.querySelector('input[name="password"]').value;
                const confirm = this.querySelector('input[name="password_confirmation"]').value;
                
                if (password !== confirm) {
                    e.preventDefault();
                    alert('Las contraseñas no coinciden');
                    return;
                }
                
                if (password.length < 8) {
                    e.preventDefault();
                    alert('La contraseña debe tener al menos 8 caracteres');
                    return;
                }
                
                if (!confirm('¿Estás seguro de cambiar la contraseña?')) {
                    e.preventDefault();
                }
            });
        }
        
        // Auto-focus en el modal de cambiar contraseña
        const modalPassword = document.getElementById('modalCambiarPassword');
        if (modalPassword) {
            modalPassword.addEventListener('shown.bs.modal', function() {
                this.querySelector('input[name="password"]').focus();
            });
        }
    });
</script>
@endpush
@endsection