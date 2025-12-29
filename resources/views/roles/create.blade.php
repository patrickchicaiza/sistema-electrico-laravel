@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header con navegación -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('roles.index') }}">
                                    <i class="fas fa-arrow-left me-1"></i> Volver a roles
                                </a>
                            </li>
                        </ol>
                    </nav>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-plus-circle me-2"></i> Crear Nuevo Rol
                    </h1>
                    <p class="text-muted mb-0">
                        Define un nuevo rol con sus permisos correspondientes
                    </p>
                </div>
            </div>

            <!-- Formulario principal -->
            <div class="row">
                <!-- Columna izquierda: Formulario básico -->
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">
                                <i class="fas fa-info-circle me-2"></i> Información Básica
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('roles.store') }}" method="POST" id="formCrearRol">
                                @csrf

                                <!-- Nombre del rol -->
                                <div class="mb-4">
                                    <label for="name" class="form-label">
                                        Nombre del Rol <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}"
                                           placeholder="Ej: supervisor, auditor, coordinador"
                                           required
                                           autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Solo letras minúsculas, números, guiones y guiones bajos.
                                        <span class="text-danger fw-bold">No se pueden usar: super_admin, administrador, tecnico, cliente</span>
                                    </div>
                                </div>

                                <!-- Sección de permisos -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="form-label">
                                            Permisos <span class="text-danger">*</span>
                                            <span class="badge bg-secondary" id="contadorPermisos">0 seleccionados</span>
                                        </label>
                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="seleccionarTodos">
                                                <i class="fas fa-check-square me-1"></i> Seleccionar todos
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" id="deseleccionarTodos">
                                                <i class="fas fa-square me-1"></i> Deseleccionar todos
                                            </button>
                                        </div>
                                    </div>

                                    @php
                                        $user = auth()->user();
                                        $permisosProhibidos = [];
                                        if (!$user->hasRole('super_admin')) {
                                            $permisosProhibidos = ['eliminar-usuarios', 'eliminar-roles', 'eliminar-reportes'];
                                        }
                                    @endphp

                                    @if($permissions->count() > 0)
                                        @foreach($permissions as $categoria => $grupoPermisos)
                                        <div class="card mb-3 border-primary">
                                            <div class="card-header bg-primary bg-opacity-10 border-primary">
                                                <div class="form-check">
                                                    <input class="form-check-input categoria-checkbox" 
                                                           type="checkbox" 
                                                           id="categoria_{{ $categoria }}"
                                                           data-categoria="{{ $categoria }}">
                                                    <label class="form-check-label fw-bold text-primary" for="categoria_{{ $categoria }}">
                                                        <i class="fas fa-folder me-1"></i>
                                                        {{ ucfirst($categoria) }}
                                                        <span class="badge bg-primary ms-2">{{ $grupoPermisos->count() }}</span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="row">
                                                    @foreach($grupoPermisos as $permiso)
                                                    @php
                                                        $esProhibido = in_array($permiso->name, $permisosProhibidos);
                                                    @endphp
                                                    <div class="col-md-6 mb-2">
                                                        <div class="form-check">
                                                            <input class="form-check-input permiso-checkbox" 
                                                                   type="checkbox" 
                                                                   name="permissions[]" 
                                                                   value="{{ $permiso->id }}"
                                                                   id="permiso_{{ $permiso->id }}"
                                                                   data-categoria="{{ $categoria }}"
                                                                   {{ old('permissions') && in_array($permiso->id, old('permissions')) ? 'checked' : '' }}
                                                                   {{ $esProhibido ? 'disabled' : '' }}>
                                                            <label class="form-check-label {{ $esProhibido ? 'text-muted' : '' }}" 
                                                                   for="permiso_{{ $permiso->id }}"
                                                                   title="{{ $permiso->name }}">
                                                                {{ str_replace('-', ' ', $permiso->name) }}
                                                                @if($esProhibido)
                                                                <span class="badge bg-danger ms-1">Restringido</span>
                                                                @endif
                                                            </label>
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                        
                                        @error('permissions')
                                            <div class="alert alert-danger">
                                                <i class="fas fa-exclamation-circle me-2"></i> {{ $message }}
                                            </div>
                                        @enderror
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            No hay permisos disponibles en el sistema.
                                        </div>
                                    @endif
                                </div>

                                <!-- Botones de acción -->
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-times me-1"></i> Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-success" id="btnCrearRol">
                                        <i class="fas fa-save me-1"></i> Crear Rol
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: Guía y ayuda -->
                <div class="col-lg-4">
                    <!-- Tarjeta de ayuda -->
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                <i class="fas fa-question-circle me-2"></i> Guía de Creación
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <h6 class="text-primary">📝 Nombres de Rol</h6>
                                <ul class="small text-muted mb-0">
                                    <li>Usa nombres descriptivos</li>
                                    <li>Solo minúsculas y guiones</li>
                                    <li>Ej: supervisor-tecnico, auditor-calidad</li>
                                </ul>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-primary">🔐 Permisos Recomendados</h6>
                                <ul class="small text-muted mb-0">
                                    <li><strong>Supervisor:</strong> ver-reportes, asignar-reportes</li>
                                    <li><strong>Auditor:</strong> ver-reportes, ver-usuarios</li>
                                    <li><strong>Coordinador:</strong> todos menos eliminar</li>
                                </ul>
                            </div>
                            <div class="mb-3">
                                <h6 class="text-primary">⚠️ Permisos Restringidos</h6>
                                <ul class="small text-muted mb-0">
                                    @if(!$user->hasRole('super_admin'))
                                    <li class="text-danger">eliminar-usuarios</li>
                                    <li class="text-danger">eliminar-roles</li>
                                    <li class="text-danger">eliminar-reportes</li>
                                    <li><small>Solo para Super Admin</small></li>
                                    @else
                                    <li class="text-success">Todos los permisos disponibles</li>
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Tarjeta de permisos del sistema -->
                    <div class="card">
                        <div class="card-header bg-white">
                            <h6 class="mb-0">
                                <i class="fas fa-shield-alt me-2"></i> Roles del Sistema
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush small">
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-danger me-2">👑</span>
                                        <div>
                                            <strong class="text-danger">super_admin</strong>
                                            <div class="text-muted">Acceso total al sistema</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-warning me-2">🛡️</span>
                                        <div>
                                            <strong class="text-warning">administrador</strong>
                                            <div class="text-muted">Gestiona usuarios y reportes</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-info me-2">🔧</span>
                                        <div>
                                            <strong class="text-info">tecnico</strong>
                                            <div class="text-muted">Atiende reportes asignados</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="list-group-item border-0 px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary me-2">👤</span>
                                        <div>
                                            <strong class="text-primary">cliente</strong>
                                            <div class="text-muted">Crea y sigue sus reportes</div>
                                        </div>
                                    </div>
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
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 0.5rem;
    }
    
    .card-header.bg-primary.bg-opacity-10 {
        background-color: rgba(13, 110, 253, 0.1) !important;
    }
    
    .permiso-checkbox:disabled + label {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    #contadorPermisos {
        font-size: 0.8em;
        vertical-align: middle;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos del DOM
        const form = document.getElementById('formCrearRol');
        const contadorPermisos = document.getElementById('contadorPermisos');
        const btnCrearRol = document.getElementById('btnCrearRol');
        const checkboxesPermisos = document.querySelectorAll('.permiso-checkbox:not(:disabled)');
        const checkboxesCategorias = document.querySelectorAll('.categoria-checkbox');
        
        // Actualizar contador de permisos seleccionados
        function actualizarContador() {
            const seleccionados = document.querySelectorAll('.permiso-checkbox:checked:not(:disabled)').length;
            contadorPermisos.textContent = `${seleccionados} seleccionados`;
            contadorPermisos.className = `badge ${seleccionados > 0 ? 'bg-success' : 'bg-secondary'}`;
            
            // Habilitar/deshabilitar botón de crear
            btnCrearRol.disabled = seleccionados === 0;
        }
        
        // Seleccionar todos los permisos
        document.getElementById('seleccionarTodos').addEventListener('click', function() {
            checkboxesPermisos.forEach(checkbox => {
                checkbox.checked = true;
            });
            checkboxesCategorias.forEach(checkbox => {
                checkbox.checked = true;
            });
            actualizarContador();
        });
        
        // Deseleccionar todos los permisos
        document.getElementById('deseleccionarTodos').addEventListener('click', function() {
            checkboxesPermisos.forEach(checkbox => {
                checkbox.checked = false;
            });
            checkboxesCategorias.forEach(checkbox => {
                checkbox.checked = false;
            });
            actualizarContador();
        });
        
        // Manejar selección/deselección por categoría
        checkboxesCategorias.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const categoria = this.dataset.categoria;
                const permisosCategoria = document.querySelectorAll(
                    `.permiso-checkbox[data-categoria="${categoria}"]:not(:disabled)`
                );
                
                permisosCategoria.forEach(permiso => {
                    permiso.checked = this.checked;
                });
                actualizarContador();
            });
        });
        
        // Actualizar estado de checkbox de categoría
        checkboxesPermisos.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const categoria = this.dataset.categoria;
                const permisosCategoria = document.querySelectorAll(
                    `.permiso-checkbox[data-categoria="${categoria}"]:not(:disabled)`
                );
                const checkboxCategoria = document.querySelector(
                    `#categoria_${categoria}`
                );
                
                // Contar cuántos están seleccionados en esta categoría
                const seleccionados = Array.from(permisosCategoria).filter(cb => cb.checked).length;
                const total = permisosCategoria.length;
                
                // Actualizar estado del checkbox de categoría
                if (seleccionados === 0) {
                    checkboxCategoria.checked = false;
                    checkboxCategoria.indeterminate = false;
                } else if (seleccionados === total) {
                    checkboxCategoria.checked = true;
                    checkboxCategoria.indeterminate = false;
                } else {
                    checkboxCategoria.checked = false;
                    checkboxCategoria.indeterminate = true;
                }
                
                actualizarContador();
            });
        });
        
        // Validación del formulario
        form.addEventListener('submit', function(e) {
            const nombre = document.getElementById('name').value.trim();
            const permisosSeleccionados = document.querySelectorAll('.permiso-checkbox:checked:not(:disabled)').length;
            
            // Validar nombre
            if (!nombre.match(/^[a-z0-9_-]+$/)) {
                e.preventDefault();
                alert('El nombre solo puede contener letras minúsculas, números, guiones y guiones bajos.');
                return;
            }
            
            // Validar permisos
            if (permisosSeleccionados === 0) {
                e.preventDefault();
                alert('Debes seleccionar al menos un permiso para el rol.');
                return;
            }
            
            // Validar nombres reservados
            const nombresReservados = ['super_admin', 'administrador', 'tecnico', 'cliente'];
            if (nombresReservados.includes(nombre.toLowerCase())) {
                e.preventDefault();
                alert('No puedes usar nombres de roles reservados del sistema.');
                return;
            }
            
            // Mostrar confirmación
            if (!confirm(`¿Crear el rol "${nombre}" con ${permisosSeleccionados} permisos?`)) {
                e.preventDefault();
            }
        });
        
        // Inicializar contador
        actualizarContador();
        
        // Validar nombre en tiempo real
        document.getElementById('name').addEventListener('input', function() {
            const nombre = this.value.toLowerCase();
            const nombresReservados = ['super_admin', 'administrador', 'tecnico', 'cliente'];
            
            if (nombresReservados.includes(nombre)) {
                this.classList.add('is-invalid');
                this.nextElementSibling.innerHTML = `
                    <div class="invalid-feedback d-block">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Este nombre está reservado para roles del sistema.
                    </div>
                `;
            } else if (!nombre.match(/^[a-z0-9_-]*$/)) {
                this.classList.add('is-invalid');
                this.nextElementSibling.innerHTML = `
                    <div class="invalid-feedback d-block">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Solo letras minúsculas, números, guiones y guiones bajos.
                    </div>
                `;
            } else {
                this.classList.remove('is-invalid');
                this.nextElementSibling.innerHTML = `
                    <div class="form-text">
                        <i class="fas fa-info-circle me-1"></i>
                        Solo letras minúsculas, números, guiones y guiones bajos.
                        <span class="text-danger fw-bold">No se pueden usar: super_admin, administrador, tecnico, cliente</span>
                    </div>
                `;
            }
        });
    });
</script>
@endpush
@endsection