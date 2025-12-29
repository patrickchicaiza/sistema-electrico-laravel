@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-plus me-2"></i> Crear Nuevo Usuario
                    </h1>
                    <p class="text-muted mb-0">
                        Registra un nuevo usuario en el sistema
                    </p>
                </div>

                <div>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Volver a Usuarios
                    </a>
                </div>
            </div>

            <!-- Formulario -->
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fas fa-user-edit me-2"></i> Información del Usuario
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('users.store') }}" method="POST">
                                @csrf

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

                                <div class="row">
                                    <!-- Nombre -->
                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">
                                            Nombre completo <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('name') is-invalid @enderror" 
                                               id="name" 
                                               name="name" 
                                               value="{{ old('name') }}"
                                               placeholder="Ej: Juan Pérez"
                                               required>
                                        @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="col-md-6 mb-3">
                                        <label for="email" class="form-label">
                                            Correo electrónico <span class="text-danger">*</span>
                                        </label>
                                        <input type="email" 
                                               class="form-control @error('email') is-invalid @enderror" 
                                               id="email" 
                                               name="email" 
                                               value="{{ old('email') }}"
                                               placeholder="Ej: juan@ejemplo.com"
                                               required>
                                        @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <!-- Teléfono -->
                                    <div class="col-md-6 mb-3">
                                        <label for="telefono" class="form-label">
                                            Teléfono
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('telefono') is-invalid @enderror" 
                                               id="telefono" 
                                               name="telefono" 
                                               value="{{ old('telefono') }}"
                                               placeholder="Ej: 123456789">
                                        @error('telefono')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <!-- Dirección -->
                                    <div class="col-md-6 mb-3">
                                        <label for="direccion" class="form-label">
                                            Dirección
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('direccion') is-invalid @enderror" 
                                               id="direccion" 
                                               name="direccion" 
                                               value="{{ old('direccion') }}"
                                               placeholder="Ej: Av. Principal #123">
                                        @error('direccion')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <!-- Contraseña -->
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">
                                            Contraseña <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" 
                                                   class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" 
                                                   name="password" 
                                                   placeholder="Mínimo 8 caracteres"
                                                   required>
                                            <button type="button" class="btn btn-outline-secondary" 
                                                    onclick="togglePassword('password', this)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        <div class="form-text">
                                            La contraseña debe tener al menos 8 caracteres
                                        </div>
                                        @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <!-- Confirmar Contraseña -->
                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">
                                            Confirmar Contraseña <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group">
                                            <input type="password" 
                                                   class="form-control @error('password_confirmation') is-invalid @enderror" 
                                                   id="password_confirmation" 
                                                   name="password_confirmation" 
                                                   placeholder="Repite la contraseña"
                                                   required>
                                            <button type="button" class="btn btn-outline-secondary" 
                                                    onclick="togglePassword('password_confirmation', this)">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                        @error('password_confirmation')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>

                                    <!-- ROLES (Radio Buttons - Selección Única) -->
                                    <div class="col-12 mb-4">
                                        <label class="form-label">
                                            Rol del usuario <span class="text-danger">*</span>
                                        </label>
                                        
                                        @error('roles')
                                        <div class="alert alert-danger py-2 mb-3">
                                            <i class="fas fa-exclamation-circle me-2"></i> {{ $message }}
                                        </div>
                                        @enderror

                                        <div class="row">
                                            @php
                                                $userActual = auth()->user();
                                                $esSuperAdmin = $userActual->hasRole('super_admin');
                                                $esAdministrador = $userActual->hasRole('administrador') && !$esSuperAdmin;
                                                
                                                // Determinar qué roles puede asignar
                                                $rolesPermitidos = [];
                                                if ($esSuperAdmin) {
                                                    $rolesPermitidos = ['cliente', 'tecnico', 'administrador', 'super_admin'];
                                                } elseif ($esAdministrador) {
                                                    $rolesPermitidos = ['cliente', 'tecnico'];
                                                }
                                                
                                                $colores = [
                                                    'cliente' => 'primary',
                                                    'tecnico' => 'info',
                                                    'administrador' => 'warning',
                                                    'super_admin' => 'danger'
                                                ];
                                                
                                                $iconos = [
                                                    'cliente' => 'user',
                                                    'tecnico' => 'user-hard-hat',
                                                    'administrador' => 'user-shield',
                                                    'super_admin' => 'crown'
                                                ];
                                                
                                                $descripciones = [
                                                    'cliente' => 'Puede crear y gestionar reportes de fallas eléctricas. Límite: 3 reportes activos.',
                                                    'tecnico' => 'Atiende y resuelve reportes asignados. Puede subir evidencias y marcar como resuelto.',
                                                    'administrador' => 'Administra usuarios, asigna reportes a técnicos y ve todas las estadísticas.',
                                                    'super_admin' => 'Control total del sistema. Puede gestionar roles, permisos y todos los usuarios.'
                                                ];
                                            @endphp
                                            
                                            @foreach($rolesDisponibles as $roleName => $roleDisplay)
                                                @if(in_array($roleName, $rolesPermitidos) || $esSuperAdmin)
                                                <div class="col-md-6 mb-3">
                                                    <div class="card border-{{ $colores[$roleName] ?? 'secondary' }} role-card"
                                                         onclick="selectRole('{{ $roleName }}')"
                                                         style="cursor: pointer;">
                                                        <div class="card-body">
                                                            <div class="form-check">
                                                                <input class="form-check-input" 
                                                                       type="radio" 
                                                                       name="roles[]" 
                                                                       value="{{ $roleName }}" 
                                                                       id="role_{{ $roleName }}"
                                                                       @if(is_array(old('roles')) && in_array($roleName, old('roles'))) checked 
                                                                       @elseif(old('roles') === null && $roleName == 'cliente') checked @endif
                                                                       required>
                                                                <label class="form-check-label fw-bold" for="role_{{ $roleName }}">
                                                                    <i class="fas fa-{{ $iconos[$roleName] ?? 'user' }} me-2 text-{{ $colores[$roleName] ?? 'secondary' }}"></i>
                                                                    {{ ucfirst($roleName) }}
                                                                </label>
                                                            </div>
                                                            <p class="text-muted small mb-0 mt-2">
                                                                <i class="fas fa-info-circle me-1"></i>
                                                                {{ $descripciones[$roleName] ?? 'Sin descripción' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @else
                                                <!-- Rol no permitido -->
                                                <div class="col-md-6 mb-3">
                                                    <div class="card border-secondary opacity-50">
                                                        <div class="card-body">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" disabled>
                                                                <label class="form-check-label text-muted">
                                                                    <i class="fas fa-{{ $iconos[$roleName] ?? 'user' }} me-2"></i>
                                                                    {{ ucfirst($roleName) }}
                                                                </label>
                                                            </div>
                                                            <div class="alert alert-warning py-2 mt-2 mb-0">
                                                                <i class="fas fa-lock me-1"></i>
                                                                No tienes permiso para crear usuarios con este rol
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            @endforeach
                                        </div>
                                        
                                        <div class="form-text mt-3">
                                            <i class="fas fa-lightbulb me-1"></i>
                                            Selecciona UN solo rol para el usuario. El rol determina los permisos y capacidades en el sistema.
                                        </div>
                                    </div>
                                </div>

                                <!-- Botones -->
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-1"></i> Crear Usuario
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Información adicional -->
                    <div class="card mt-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i> Información Importante
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary">
                                        <i class="fas fa-key me-2"></i> Seguridad de Contraseñas
                                    </h6>
                                    <ul class="text-muted small">
                                        <li>La contraseña se almacena encriptada</li>
                                        <li>El usuario puede cambiarla después</li>
                                        <li>Se recomienda usar contraseñas fuertes</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary">
                                        <i class="fas fa-user-shield me-2"></i> Permisos de Roles
                                    </h6>
                                    <ul class="text-muted small">
                                        <li><strong>Cliente:</strong> Solo puede crear y ver sus reportes</li>
                                        <li><strong>Técnico:</strong> Resuelve reportes asignados</li>
                                        @if($esSuperAdmin)
                                        <li><strong>Administrador:</strong> Gestiona usuarios y reportes</li>
                                        <li><strong>Super Admin:</strong> Control total del sistema</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-check-input:checked {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }
    
    .role-card {
        transition: all 0.2s;
    }
    
    .role-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .role-card.selected {
        border-width: 2px;
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
    
    .card.border-primary .form-check-input:checked {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }
    
    .card.border-info .form-check-input:checked {
        background-color: var(--bs-info);
        border-color: var(--bs-info);
    }
    
    .card.border-warning .form-check-input:checked {
        background-color: var(--bs-warning);
        border-color: var(--bs-warning);
    }
    
    .card.border-danger .form-check-input:checked {
        background-color: var(--bs-danger);
        border-color: var(--bs-danger);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validación de contraseña en tiempo real
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        
        function validatePassword() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.setCustomValidity('Las contraseñas no coinciden');
                return false;
            } else {
                confirmPassword.setCustomValidity('');
                return true;
            }
        }
        
        password.addEventListener('keyup', validatePassword);
        confirmPassword.addEventListener('keyup', validatePassword);
        
        // Validación al enviar formulario
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            if (!validatePassword()) {
                e.preventDefault();
                alert('Las contraseñas no coinciden. Por favor, verifica.');
                return false;
            }
            
            // Verificar que se seleccionó un rol
            const selectedRole = document.querySelector('input[name="roles[]"]:checked');
            if (!selectedRole) {
                e.preventDefault();
                alert('Por favor, selecciona un rol para el usuario.');
                return false;
            }
        });
        
        // Actualizar estado visual de las tarjetas de rol
        updateRoleCards();
        
        // Cuando cambia un radio button, actualizar las tarjetas
        document.querySelectorAll('input[name="roles[]"]').forEach(radio => {
            radio.addEventListener('change', updateRoleCards);
        });
    });
    
    // Función para mostrar/ocultar contraseña
    function togglePassword(fieldId, button) {
        const field = document.getElementById(fieldId);
        const icon = button.querySelector('i');
        
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    
    // Función para seleccionar rol al hacer clic en la tarjeta
    function selectRole(roleName) {
        const radio = document.getElementById('role_' + roleName);
        if (radio && !radio.disabled) {
            radio.checked = true;
            updateRoleCards();
        }
    }
    
    // Función para actualizar el estado visual de las tarjetas de rol
    function updateRoleCards() {
        // Remover clase 'selected' de todas las tarjetas
        document.querySelectorAll('.role-card').forEach(card => {
            card.classList.remove('selected');
        });
        
        // Agregar clase 'selected' a la tarjeta del rol seleccionado
        const selectedRadio = document.querySelector('input[name="roles[]"]:checked');
        if (selectedRadio) {
            const card = selectedRadio.closest('.role-card');
            if (card) {
                card.classList.add('selected');
            }
        }
    }
</script>
@endpush
@endsection