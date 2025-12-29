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
                            <i class="fas fa-user-shield me-2"></i> Rol: {{ ucfirst($role->name) }}
                            @if($role->name == 'super_admin')
                                <i class="fas fa-crown text-danger ms-1"></i>
                            @endif
                        </h1>
                    </div>

                    <div class="d-flex gap-2">
                        <!-- Botón imprimir -->
                        <button class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Imprimir
                        </button>

                        <!-- Botón editar (solo si no es rol protegido) -->
                        @can('editar-roles')
                            @php
                                $rolesProtegidos = ['super_admin', 'administrador', 'tecnico', 'cliente'];
                                $esProtegido = in_array($role->name, $rolesProtegidos);
                            @endphp
                            @if(!$esProtegido || ($role->name == 'super_admin' && auth()->user()->hasRole('super_admin')))
                                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-1"></i> Editar Rol
                                </a>
                            @endif
                        @endcan
                    </div>
                </div>

                <!-- Grid principal: 2 columnas -->
                <div class="row">
                    <!-- Columna izquierda: Información principal -->
                    <div class="col-lg-8">
                        <!-- Card de información básica -->
                        <div class="card mb-4">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i> Información del Rol
                                </h5>
                                <div>
                                    @php
                                        $rolesProtegidos = ['super_admin', 'administrador', 'tecnico', 'cliente'];
                                        $esProtegido = in_array($role->name, $rolesProtegidos);
                                        $colorTipo = $esProtegido ? 'warning' : 'success';
                                        $textoTipo = $esProtegido ? 'Sistema' : 'Personalizado';
                                    @endphp
                                    <span class="badge bg-{{ $colorTipo }} px-3 py-2">
                                        <i class="fas fa-{{ $esProtegido ? 'shield-alt' : 'user-cog' }} me-1"></i>
                                        {{ $textoTipo }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small mb-1">ID del Rol</label>
                                        <div class="fw-bold">#{{ $role->id }}</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small mb-1">Nombre</label>
                                        <div class="fw-bold">
                                            {{ ucfirst($role->name) }}
                                            @if($role->name == 'super_admin')
                                                <span class="badge bg-danger ms-2">
                                                    <i class="fas fa-crown"></i> Super Admin
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small mb-1">Fecha de creación</label>
                                        <div class="fw-bold">
                                            {{ $role->created_at->format('d/m/Y H:i') }}
                                            <small class="text-muted">({{ $role->created_at->diffForHumans() }})</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small mb-1">Última actualización</label>
                                        <div class="fw-bold">
                                            {{ $role->updated_at->format('d/m/Y H:i') }}
                                            <small class="text-muted">({{ $role->updated_at->diffForHumans() }})</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small mb-1">Guard Name</label>
                                        <div class="fw-bold">
                                            <span class="badge bg-secondary">{{ $role->guard_name }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small mb-1">Total de permisos</label>
                                        <div class="fw-bold">
                                            <span class="badge bg-primary">
                                                {{ $role->permissions->count() }} permisos
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small mb-1">Usuarios con este rol</label>
                                        <div class="fw-bold">
                                            <span class="badge bg-info">
                                                {{ $role->users()->count() }} usuarios
                                            </span>
                                        </div>
                                    </div>

                                    @if($esProtegido)
                                        <div class="col-12">
                                            <div class="alert alert-warning mt-2">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Rol del sistema</strong>
                                                <p class="mb-0 small">
                                                    Este rol es parte del sistema y no puede ser eliminado.
                                                    Los roles del sistema son: super_admin, administrador, tecnico, cliente.
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card de permisos detallados -->
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-key me-2"></i> Permisos Asignados
                                    <span class="badge bg-primary">{{ $role->permissions->count() }}</span>
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($role->permissions->count() > 0)
                                    @foreach($permisosAgrupados as $categoria => $permisos)
                                        <div class="mb-4">
                                            <h6 class="text-primary mb-3 border-bottom pb-2">
                                                <i class="fas fa-folder me-1"></i>
                                                {{ ucfirst($categoria) }}
                                                <span class="badge bg-primary ms-2">{{ $permisos->count() }}</span>
                                            </h6>
                                            <div class="row g-2">
                                                @foreach($permisos as $permiso)
                                                    <div class="col-md-6">
                                                        <div class="card border-primary border-1 mb-2">
                                                            <div class="card-body py-2 px-3">
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <strong class="text-primary">
                                                                            {{ str_replace('-', ' ', $permiso->name) }}
                                                                        </strong>
                                                                        <br>
                                                                        <small class="text-muted">
                                                                            ID: {{ $permiso->id }}
                                                                        </small>
                                                                    </div>
                                                                    <div>
                                                                        <span class="badge bg-success">
                                                                            <i class="fas fa-check-circle"></i> Asignado
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-4">
                                        <div class="display-4 text-muted mb-3">
                                            <i class="fas fa-key"></i>
                                        </div>
                                        <h5 class="text-muted">Sin permisos asignados</h5>
                                        <p class="text-muted">
                                            Este rol no tiene permisos asignados. Los usuarios con este rol no podrán realizar
                                            ninguna acción.
                                        </p>
                                        @can('editar-roles')
                                            @php
                                                $rolesProtegidos = ['super_admin', 'administrador', 'tecnico', 'cliente'];
                                                $esProtegido = in_array($role->name, $rolesProtegidos);
                                            @endphp
                                            @if(!$esProtegido || ($role->name == 'super_admin' && auth()->user()->hasRole('super_admin')))
                                                <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-primary">
                                                    <i class="fas fa-plus-circle me-1"></i> Agregar Permisos
                                                </a>
                                            @endif
                                        @endcan
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha: Información adicional -->
                    <div class="col-lg-4">
                        <!-- Card de usuarios con este rol -->
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-users me-2"></i> Usuarios con este Rol
                                    <span class="badge bg-info">{{ $role->users()->count() }}</span>
                                </h6>
                            </div>
                            <div class="card-body">
                                @php
                                    $usuarios = $role->users()->with('roles')->latest()->take(5)->get();
                                    $totalUsuarios = $role->users()->count();
                                @endphp

                                @if($totalUsuarios > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($usuarios as $usuario)
                                            <div class="list-group-item border-0 px-0 py-2">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 me-3">
                                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                            style="width: 40px; height: 40px;">
                                                            {{ substr($usuario->name, 0, 1) }}
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-0">{{ $usuario->name }}</h6>
                                                        <small class="text-muted">
                                                            {{ $usuario->email }}
                                                        </small>
                                                        <br>
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            {{ $usuario->created_at->format('d/m/Y') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if($totalUsuarios > 5)
                                        <div class="text-center mt-3">
                                            <small class="text-muted">
                                                Mostrando 5 de {{ $totalUsuarios }} usuarios
                                            </small>
                                        </div>
                                    @endif

                                    <div class="mt-3">
                                        <a href="{{ route('users.index') }}?rol={{ $role->name }}"
                                            class="btn btn-sm btn-outline-primary w-100">
                                            <i class="fas fa-list me-1"></i> Ver todos los usuarios
                                        </a>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <div class="display-4 text-muted mb-3">
                                            <i class="fas fa-user-slash"></i>
                                        </div>
                                        <h6 class="text-muted">Sin usuarios asignados</h6>
                                        <p class="text-muted small mb-0">
                                            Ningún usuario tiene asignado este rol.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Card de acciones rápidas -->
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-bolt me-2"></i> Acciones Rápidas
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <!-- Crear usuario con este rol -->
                                    @can('crear-usuarios')
                                        <a href="{{ route('users.create') }}?rol={{ $role->name }}"
                                            class="btn btn-outline-success text-start">
                                            <i class="fas fa-user-plus me-2"></i> Crear usuario con este rol
                                        </a>
                                    @endcan

                                    <!-- Copiar rol -->
                                    @can('crear-roles')
                                        @php
                                            $rolesProtegidos = ['super_admin', 'administrador', 'tecnico', 'cliente'];
                                            $esProtegido = in_array($role->name, $rolesProtegidos);
                                        @endphp
                                        @if(!$esProtegido)
                                            <a href="{{ route('roles.create') }}?copiar={{ $role->id }}"
                                                class="btn btn-outline-info text-start">
                                                <i class="fas fa-copy me-2"></i> Copiar este rol
                                            </a>
                                        @endif
                                    @endcan

                                    <!-- Ver todos los roles -->
                                    <a href="{{ route('roles.index') }}" class="btn btn-outline-primary text-start">
                                        <i class="fas fa-list me-2"></i> Ver todos los roles
                                    </a>

                                    <!-- Eliminar rol (solo si cumple condiciones) -->
                                    @can('eliminar-roles')
                                        @php
                                            $rolesProtegidos = ['super_admin', 'administrador', 'tecnico', 'cliente'];
                                            $esProtegido = in_array($role->name, $rolesProtegidos);
                                            $tieneUsuarios = $role->users()->count() > 0;
                                        @endphp
                                        @if(!$esProtegido && !$tieneUsuarios && auth()->user()->hasRole('super_admin'))
                                            <button type="button" class="btn btn-outline-danger text-start" data-bs-toggle="modal"
                                                data-bs-target="#modalEliminar">
                                                <i class="fas fa-trash me-2"></i> Eliminar este rol
                                            </button>
                                        @endif
                                    @endcan
                                </div>
                            </div>
                        </div>

                        <!-- Card de estadísticas del rol -->
                        <div class="card">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-chart-bar me-2"></i> Estadísticas
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="text-center border rounded p-2">
                                            <div class="h5 mb-0 text-primary">{{ $role->permissions->count() }}</div>
                                            <small class="text-muted">Permisos</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center border rounded p-2">
                                            <div class="h5 mb-0 text-info">{{ $role->users()->count() }}</div>
                                            <small class="text-muted">Usuarios</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center border rounded p-2">
                                            <div class="h5 mb-0 text-warning">
                                                @php
                                                    $categorias = count($permisosAgrupados);
                                                @endphp
                                                {{ $categorias }}
                                            </div>
                                            <small class="text-muted">Categorías</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center border rounded p-2">
                                            <div class="h5 mb-0 text-success">
                                                @php
                                                    $diferencia = $role->created_at->diff(now());
                                                @endphp

                                                @if ($diferencia->days == 0)
                                                    Hoy
                                                @elseif ($diferencia->days == 1)
                                                    Ayer
                                                @elseif ($diferencia->days < 7)
                                                    {{ $diferencia->days }} días
                                                @elseif ($diferencia->days < 30)
                                                    {{ floor($diferencia->days / 7) }} semanas
                                                @elseif ($diferencia->days < 365)
                                                    {{ floor($diferencia->days / 30) }} meses
                                                @else
                                                    {{ floor($diferencia->days / 365) }} años
                                                @endif
                                            </div>
                                            <small class="text-muted">Días creado</small>
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

    <!-- Modal para eliminar rol -->
    @can('eliminar-roles')
        @php
            $rolesProtegidos = ['super_admin', 'administrador', 'tecnico', 'cliente'];
            $esProtegido = in_array($role->name, $rolesProtegidos);
            $tieneUsuarios = $role->users()->count() > 0;
        @endphp
        @if(!$esProtegido && !$tieneUsuarios && auth()->user()->hasRole('super_admin'))
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
                            <p>¿Estás seguro de eliminar el rol <strong>{{ $role->name }}</strong>?</p>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>¡Atención!</strong> Esta acción no se puede deshacer.
                            </div>
                            <ul class="text-muted">
                                <li>ID: #{{ $role->id }}</li>
                                <li>Permisos: {{ $role->permissions->count() }}</li>
                                <li>Creado: {{ $role->created_at->format('d/m/Y') }}</li>
                                <li>Tipo: Personalizado</li>
                            </ul>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Requisitos cumplidos:</strong>
                                <ul class="mb-0">
                                    <li>✓ No es rol del sistema</li>
                                    <li>✓ No tiene usuarios asignados</li>
                                    <li>✓ Eres super administrador</li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Sí, eliminar rol</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endcan

    @push('styles')
        <style>
            .avatar {
                font-weight: bold;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .breadcrumb {
                background: transparent;
                padding: 0;
                margin-bottom: 0.5rem;
            }

            @media print {

                .btn,
                .modal,
                .breadcrumb {
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

                h1 {
                    font-size: 18px !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Tooltips
                var tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltips.map(function (tooltip) {
                    return new bootstrap.Tooltip(tooltip);
                });

                // Copiar nombre del rol al portapapeles
                document.querySelectorAll('[data-copy-role]').forEach(button => {
                    button.addEventListener('click', function () {
                        const roleName = this.getAttribute('data-copy-role');
                        navigator.clipboard.writeText(roleName).then(() => {
                            alert('Nombre del rol copiado: ' + roleName);
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection