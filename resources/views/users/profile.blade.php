@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-circle me-2"></i> Mi Perfil
                    </h1>
                    <p class="text-muted mb-0">Actualiza tu información personal</p>
                </div>

                <div class="d-flex gap-2">
                    @if(auth()->user()->es_cliente)
                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-clipboard-list me-1"></i> Mis Reportes
                    </a>
                    @endif
                    
                    @if(auth()->user()->es_tecnico)
                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-tasks me-1"></i> Reportes Asignados
                    </a>
                    @endif
                </div>
            </div>

            <!-- Mensajes -->
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Por favor corrige los siguientes errores:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Formulario de perfil -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Información básica -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-1"></i> Nombre completo *
                                </label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name', $user->name) }}" required
                                    placeholder="Tu nombre completo">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-1"></i> Correo electrónico
                                </label>
                                <input type="email" class="form-control" id="email"
                                    value="{{ $user->email }}" readonly>
                                <small class="text-muted">El correo no se puede cambiar</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">
                                    <i class="fas fa-phone me-1"></i> Teléfono
                                </label>
                                <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                    id="telefono" name="telefono" value="{{ old('telefono', $user->telefono) }}"
                                    placeholder="Ej: 3001234567">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="direccion" class="form-label">
                                    <i class="fas fa-map-marker-alt me-1"></i> Dirección
                                </label>
                                <input type="text" class="form-control @error('direccion') is-invalid @enderror"
                                    id="direccion" name="direccion" value="{{ old('direccion', $user->direccion) }}"
                                    placeholder="Ej: Calle 123 #45-67">
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Cambio de contraseña -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-key me-2"></i> Cambio de contraseña (opcional)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">
                                            Nueva contraseña
                                        </label>
                                        <input type="password"
                                            class="form-control @error('password') is-invalid @enderror" id="password"
                                            name="password" placeholder="Dejar en blanco para no cambiar">
                                        <small class="text-muted">Mínimo 8 caracteres</small>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">
                                            Confirmar contraseña
                                        </label>
                                        <input type="password" class="form-control" id="password_confirmation"
                                            name="password_confirmation" placeholder="Repite la contraseña">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Información del usuario -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i> Información de la cuenta
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Rol</label>
                                        <p class="mb-0">
                                            @php
                                                $roles = $user->getRoleNames();
                                                $colores = [
                                                    'super_admin' => 'danger',
                                                    'administrador' => 'warning',
                                                    'tecnico' => 'info',
                                                    'cliente' => 'primary'
                                                ];
                                            @endphp
                                            @foreach($roles as $role)
                                                <span class="badge bg-{{ $colores[$role] ?? 'secondary' }}">
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
                                            @endforeach
                                        </p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Fecha de registro</label>
                                        <p class="mb-0">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">ID de usuario</label>
                                        <p class="mb-0">#{{ $user->id }}</p>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Email verificado</label>
                                        <p class="mb-0">
                                            @if($user->email_verified_at)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i> Sí
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock me-1"></i> Pendiente
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="d-flex justify-content-between">
                            <button type="reset" class="btn btn-outline-secondary">
                                <i class="fas fa-redo me-1"></i> Restablecer
                            </button>

                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Actualizar perfil
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Estadísticas según rol -->
            @if(auth()->user()->es_cliente)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i> Mis Estadísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="display-4 text-primary">{{ $user->reportes_activos_count }}</div>
                            <small class="text-muted">Reportes activos</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="display-4 text-success">{{ $user->reportesComoCliente()->where('estado', 'resuelto')->count() }}</div>
                            <small class="text-muted">Resueltos</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="display-4 text-warning">{{ $user->reportesComoCliente()->where('estado', 'pendiente')->count() }}</div>
                            <small class="text-muted">Pendientes</small>
                        </div>
                        <div class="col-md-3 text-center">
                            @php
                                $diasRegistro = $user->created_at->diffInDays();
                            @endphp
                            <div class="display-4 text-info">{{ $diasRegistro }}</div>
                            <small class="text-muted">Días en el sistema</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(auth()->user()->es_tecnico)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i> Mis Estadísticas como Técnico
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="display-4 text-info">{{ $user->reportesComoTecnico()->whereIn('estado', ['asignado', 'en_proceso'])->count() }}</div>
                            <small class="text-muted">Reportes activos</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="display-4 text-success">{{ $user->reportesComoTecnico()->where('estado', 'resuelto')->count() }}</div>
                            <small class="text-muted">Resueltos</small>
                        </div>
                        <div class="col-md-3 text-center">
                            @php
                                $prioridadAlta = $user->reportesComoTecnico()->where('prioridad', 'alta')->whereIn('estado', ['asignado', 'en_proceso'])->count();
                            @endphp
                            <div class="display-4 text-danger">{{ $prioridadAlta }}</div>
                            <small class="text-muted">Urgentes activos</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="display-4 text-secondary">{{ $user->reportesComoTecnico()->count() }}</div>
                            <small class="text-muted">Total asignados</small>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .display-4 {
        font-size: 2.5rem;
        font-weight: bold;
    }
    
    .card-header.bg-light {
        background-color: #f8f9fa !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validación del formulario
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;

            // Validar que las contraseñas coincidan si se ingresaron
            if (password && password !== passwordConfirm) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor verifica.');
                document.getElementById('password_confirmation').focus();
                return false;
            }

            // Confirmación antes de actualizar
            if (!confirm('¿Actualizar tu información personal?')) {
                e.preventDefault();
                return false;
            }
        });

        // Restablecer formulario
        document.querySelector('button[type="reset"]').addEventListener('click', function() {
            if (confirm('¿Restablecer todos los campos a sus valores originales?')) {
                // El navegador maneja el reset automáticamente
            }
        });
    });
</script>
@endpush
@endsection