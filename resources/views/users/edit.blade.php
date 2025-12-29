@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
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
                            <i class="fas fa-user-edit me-2"></i> Editar Usuario
                        </h1>
                        <p class="text-muted mb-0">Actualiza la información del usuario</p>
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye me-1"></i> Ver
                        </a>
                    </div>
                </div>

                <!-- Mensajes de error -->
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

                <!-- Formulario de edición -->
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('users.update', $user->id) }}" method="POST">
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
                                        placeholder="Ej: Juan Pérez">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-1"></i> Correo electrónico *
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                        name="email" value="{{ old('email', $user->email) }}" required
                                        placeholder="Ej: juan@example.com">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
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

                            <!-- Cambio de contraseña (opcional) -->
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

                            <!-- Selección de roles (UN SOLO ROL) -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-user-shield me-2"></i> Asignación de rol *
                                    </h6>
                                </div>
                                <div class="card-body">
                                    @if($rolesDisponibles->count() > 0)
                                        <div class="row">
                                            @php
                                                // Obtener el rol actual del usuario (solo el primero)
                                                $rolActual = $user->getRoleNames()->first();
                                            @endphp
                                            
                                            @foreach($rolesDisponibles as $roleName => $roleDisplay)
                                                <div class="col-md-6 mb-3">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="roles[]"
                                                            value="{{ $roleName }}" id="role_{{ $roleName }}"
                                                            @if(old('roles', [$rolActual])[0] == $roleName) checked @endif
                                                            @if($roleName == 'super_admin' && !auth()->user()->hasRole('super_admin'))
                                                            disabled @endif
                                                            @if($roleName == 'administrador' && auth()->user()->hasRole('administrador') && !auth()->user()->hasRole('super_admin'))
                                                            disabled @endif>
                                                        <label class="form-check-label w-100" for="role_{{ $roleName }}">
                                                            @php
                                                                $colores = [
                                                                    'super_admin' => 'danger',
                                                                    'administrador' => 'warning',
                                                                    'tecnico' => 'info',
                                                                    'cliente' => 'primary'
                                                                ];
                                                                $iconos = [
                                                                    'super_admin' => 'crown',
                                                                    'administrador' => 'user-shield',
                                                                    'tecnico' => 'user-hard-hat',
                                                                    'cliente' => 'user'
                                                                ];
                                                            @endphp
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge bg-{{ $colores[$roleName] ?? 'secondary' }} me-3" style="font-size: 1.2em; padding: 8px 12px;">
                                                                    <i class="fas fa-{{ $iconos[$roleName] ?? 'user' }} fa-lg"></i>
                                                                </span>
                                                                <div>
                                                                    <strong class="d-block">{{ ucfirst($roleName) }}</strong>
                                                                    @if($roleName == 'super_admin')
                                                                        <small class="text-muted">Acceso total al sistema</small>
                                                                    @elseif($roleName == 'administrador')
                                                                        <small class="text-muted">Gestiona reportes y usuarios</small>
                                                                    @elseif($roleName == 'tecnico')
                                                                        <small class="text-muted">Atiende reportes asignados</small>
                                                                    @elseif($roleName == 'cliente')
                                                                        <small class="text-muted">Crea y sigue reportes</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        @error('roles')
                                            <div class="alert alert-danger mt-2">
                                                <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                            </div>
                                        @enderror

                                        <!-- Nota sobre permisos -->
                                        @if(auth()->user()->hasRole('administrador') && !auth()->user()->hasRole('super_admin'))
                                            <div class="alert alert-info mt-3">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Nota:</strong> Como Administrador, solo puedes asignar roles de <strong>Cliente</strong> o <strong>Técnico</strong>.
                                            </div>
                                        @endif

                                        @if(!auth()->user()->hasRole('super_admin'))
                                            <div class="alert alert-info mt-3">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Nota:</strong> Solo los Super Administradores pueden asignar roles de <strong>Administrador</strong> o <strong>Super Admin</strong>.
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            No hay roles disponibles para asignar.
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Información del usuario -->
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i> Información del usuario
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-muted">ID del usuario</label>
                                            <p class="mb-0"><strong>#{{ $user->id }}</strong></p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-muted">Fecha de registro</label>
                                            <p class="mb-0">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-muted">Última actualización</label>
                                            <p class="mb-0">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
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
                                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>

                                <div class="d-flex gap-2">
                                    <button type="reset" class="btn btn-outline-secondary">
                                        <i class="fas fa-redo me-1"></i> Restablecer
                                    </button>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Actualizar usuario
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .form-check-label {
                cursor: pointer;
                padding: 12px;
                border: 2px solid #dee2e6;
                border-radius: 8px;
                transition: all 0.2s;
            }

            .form-check-label:hover {
                border-color: #0d6efd;
                background-color: #f8f9fa;
            }

            .form-check-input:checked~.form-check-label {
                border-color: #0d6efd;
                background-color: #e7f3ff;
                box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
            }

            .card-header.bg-light {
                background-color: #f8f9fa !important;
                border-bottom: 1px solid #dee2e6;
            }

            .form-check-input {
                margin-top: 0.5rem;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Validación de formulario
                const form = document.querySelector('form');
                form.addEventListener('submit', function (e) {
                    const password = document.getElementById('password').value;
                    const passwordConfirm = document.getElementById('password_confirmation').value;

                    // Validar que las contraseñas coincidan si se ingresaron
                    if (password && password !== passwordConfirm) {
                        e.preventDefault();
                        alert('Las contraseñas no coinciden. Por favor verifica.');
                        document.getElementById('password_confirmation').focus();
                        return false;
                    }

                    // Validar que se seleccione un rol (con radio buttons, esto no debería ser necesario)
                    const rolSeleccionado = document.querySelector('input[name="roles[]"]:checked');
                    if (!rolSeleccionado) {
                        e.preventDefault();
                        alert('Debes seleccionar un rol para el usuario.');
                        return false;
                    }

                    // Confirmación antes de actualizar
                    if (!confirm('¿Estás seguro de actualizar la información de este usuario?')) {
                        e.preventDefault();
                        return false;
                    }
                });

                // Deshabilitar roles no permitidos según el usuario actual
                const userEsSuperAdmin = {{ auth()->user()->hasRole('super_admin') ? 'true' : 'false' }};
                const userEsAdmin = {{ auth()->user()->hasRole('administrador') ? 'true' : 'false' }};
                
                if (!userEsSuperAdmin) {
                    // Deshabilitar super_admin para no super_admins
                    const superAdminRadio = document.getElementById('role_super_admin');
                    if (superAdminRadio) superAdminRadio.disabled = true;
                    
                    // Deshabilitar administrador para administradores normales
                    if (userEsAdmin && !userEsSuperAdmin) {
                        const adminRadio = document.getElementById('role_administrador');
                        if (adminRadio) adminRadio.disabled = true;
                    }
                }

                // Asegurar que solo un rol pueda ser seleccionado
                const roleRadios = document.querySelectorAll('input[name="roles[]"]');
                roleRadios.forEach(radio => {
                    radio.addEventListener('change', function () {
                        if (this.checked) {
                            roleRadios.forEach(otherRadio => {
                                if (otherRadio !== this) {
                                    otherRadio.checked = false;
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection