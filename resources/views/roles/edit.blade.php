@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-edit me-2"></i> Editar Rol
                    </h1>
                    <p class="text-muted mb-0">
                        Modifica los permisos y nombre del rol: <strong>{{ $role->name }}</strong>
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Volver a roles
                    </a>
                    <a href="{{ route('roles.show', $role->id) }}" class="btn btn-outline-info">
                        <i class="fas fa-eye me-1"></i> Ver detalles
                    </a>
                </div>
            </div>

            <!-- Alerta si es rol protegido -->
            @if(in_array($role->name, ['super_admin', 'administrador', 'tecnico', 'cliente']))
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Rol del Sistema</strong> - Este rol es esencial para el funcionamiento del sistema. 
                Se recomienda no modificar sus permisos principales.
            </div>
            @endif

            <!-- Formulario -->
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Nombre del rol -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-tag me-1"></i> Nombre del Rol *
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $role->name) }}"
                                       placeholder="Ej: supervisor, auditor, etc."
                                       required>
                                
                                <div class="form-text">
                                    Solo letras minúsculas, números, guiones y guiones bajos. 
                                    Ej: supervisor_tecnico, auditor-calidad
                                </div>
                                
                                @error('name')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle me-1"></i> {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-info-circle me-1"></i> Información del Rol
                                </label>
                                <div class="border rounded p-3 bg-light">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted d-block">ID</small>
                                            <strong>#{{ $role->id }}</strong>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Creado</small>
                                            <strong>{{ $role->created_at->format('d/m/Y') }}</strong>
                                        </div>
                                        <div class="col-6 mt-2">
                                            <small class="text-muted d-block">Usuarios</small>
                                            <strong class="text-info">{{ $role->users()->count() }}</strong>
                                        </div>
                                        <div class="col-6 mt-2">
                                            <small class="text-muted d-block">Permisos actuales</small>
                                            <strong class="text-primary">{{ $role->permissions->count() }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sección de permisos -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-key me-2"></i> Permisos Asignados *
                                </h5>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="selectAll">
                                    <label class="form-check-label" for="selectAll">Seleccionar todos</label>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Selecciona los permisos que tendrá este rol. Los usuarios con este rol podrán realizar las acciones seleccionadas.
                            </div>

                            @error('permissions')
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-circle me-2"></i> {{ $message }}
                            </div>
                            @enderror

                            <!-- Permisos agrupados por categoría -->
                            @foreach($permissions as $categoria => $grupoPermisos)
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-folder me-1"></i>
                                        {{ ucfirst($categoria) }}
                                        <span class="badge bg-primary ms-2">{{ $grupoPermisos->count() }}</span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach($grupoPermisos as $permiso)
                                        <div class="col-md-4 col-lg-3 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input permiso-checkbox" 
                                                       type="checkbox" 
                                                       name="permissions[]" 
                                                       value="{{ $permiso->id }}"
                                                       id="permiso_{{ $permiso->id }}"
                                                       {{ in_array($permiso->id, $rolePermissions) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="permiso_{{ $permiso->id }}">
                                                    <span class="badge bg-info me-1">
                                                        {{ str_replace('-', ' ', $permiso->name) }}
                                                    </span>
                                                </label>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            <!-- Si no hay permisos -->
                            @if(count($permissions) == 0)
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No hay permisos disponibles para asignar.
                            </div>
                            @endif
                        </div>

                        <!-- Botones -->
                        <div class="d-flex justify-content-between align-items-center border-top pt-4">
                            <div>
                                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-1"></i> Cancelar
                                </a>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="reset" class="btn btn-outline-warning">
                                    <i class="fas fa-redo me-1"></i> Restablecer
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sección de usuarios asignados -->
            @if($role->users()->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i> Usuarios con este Rol
                        <span class="badge bg-info ms-2">{{ $role->users()->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Importante:</strong> Si eliminas este rol, estos usuarios perderán sus permisos.
                        Considera reasignarlos a otro rol antes de eliminar.
                    </div>
                    
                    <div class="row">
                        @foreach($role->users()->limit(6)->get() as $usuario)
                        <div class="col-md-4 col-lg-2 mb-3">
                            <div class="border rounded p-2 text-center">
                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2"
                                     style="width: 40px; height: 40px; font-size: 16px;">
                                    {{ substr($usuario->name, 0, 1) }}
                                </div>
                                <div class="small">
                                    <strong>{{ $usuario->name }}</strong>
                                </div>
                                <div class="text-muted x-small">
                                    {{ $usuario->email }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    @if($role->users()->count() > 6)
                    <div class="text-center mt-3">
                        <a href="#" class="btn btn-sm btn-outline-primary">
                            Ver todos los {{ $role->users()->count() }} usuarios
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    .avatar {
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .form-check-label {
        cursor: pointer;
        width: 100%;
    }
    
    .form-check-input:checked + .form-check-label .badge {
        background-color: #0d6efd !important;
        color: white;
    }
    
    .card-header.bg-light {
        background-color: #f8f9fa !important;
    }
    
    .x-small {
        font-size: 0.75rem;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Seleccionar/Deseleccionar todos los permisos
        const selectAllCheckbox = document.getElementById('selectAll');
        const permisoCheckboxes = document.querySelectorAll('.permiso-checkbox');
        
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                permisoCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
            });
            
            // Actualizar estado de "Seleccionar todos" cuando cambian permisos individuales
            permisoCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(permisoCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(permisoCheckboxes).some(cb => cb.checked);
                    
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                });
            });
            
            // Estado inicial
            const allChecked = Array.from(permisoCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(permisoCheckboxes).some(cb => cb.checked);
            
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;
        }
        
        // Validación del formulario
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const nombreRol = document.getElementById('name').value.trim();
            const permisosSeleccionados = document.querySelectorAll('.permiso-checkbox:checked');
            
            // Validar nombre
            if (!nombreRol) {
                e.preventDefault();
                alert('Por favor, ingresa un nombre para el rol.');
                return;
            }
            
            // Validar permisos
            if (permisosSeleccionados.length === 0) {
                e.preventDefault();
                alert('Debes seleccionar al menos un permiso para el rol.');
                return;
            }
            
            // Validar formato del nombre (opcional, también se valida en backend)
            const regex = /^[a-z0-9_-]+$/;
            if (!regex.test(nombreRol)) {
                e.preventDefault();
                alert('El nombre del rol solo puede contener letras minúsculas, números, guiones y guiones bajos.');
                return;
            }
            
            // Confirmación antes de guardar
            if (!confirm('¿Guardar los cambios en el rol?')) {
                e.preventDefault();
            }
        });
        
        // Restablecer formulario
        document.querySelector('button[type="reset"]').addEventListener('click', function() {
            if (confirm('¿Restablecer todos los cambios? Se perderán las modificaciones no guardadas.')) {
                // Restablecer checkboxes a su estado inicial
                @foreach($permissions as $categoria => $grupoPermisos)
                    @foreach($grupoPermisos as $permiso)
                        document.getElementById('permiso_{{ $permiso->id }}').checked = {{ in_array($permiso->id, $rolePermissions) ? 'true' : 'false' }};
                    @endforeach
                @endforeach
                
                // Actualizar estado de "Seleccionar todos"
                if (selectAllCheckbox) {
                    const allChecked = Array.from(permisoCheckboxes).every(cb => cb.checked);
                    const someChecked = Array.from(permisoCheckboxes).some(cb => cb.checked);
                    
                    selectAllCheckbox.checked = allChecked;
                    selectAllCheckbox.indeterminate = someChecked && !allChecked;
                }
            }
        });
    });
</script>
@endpush
@endsection