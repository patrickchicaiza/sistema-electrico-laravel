@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-shield me-2"></i> Gestión de Roles
                    </h1>
                    <p class="text-muted mb-0">
                        Administra los roles y permisos del sistema
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <!-- Botón para crear nuevo rol -->
                    @can('crear-roles')
                    <a href="{{ route('roles.create') }}" class="btn btn-success">
                        <i class="fas fa-plus-circle me-1"></i> Nuevo Rol
                    </a>
                    @endcan

                    <!-- Botón para ver todos los permisos -->
                    <a href="#" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalPermisos">
                        <i class="fas fa-key me-1"></i> Ver Permisos
                    </a>
                </div>
            </div>

            <!-- Tarjetas de estadísticas -->
            <div class="row mb-4">
                @php
                    $user = auth()->user();
                    $rolesQuery = \Spatie\Permission\Models\Role::query();
                    
                    if (!$user->hasRole('super_admin')) {
                        $rolesQuery->where('name', '!=', 'super_admin');
                    }
                    
                    $totalRoles = $rolesQuery->count();
                    $rolesProtegidos = $rolesQuery->whereIn('name', ['super_admin', 'administrador', 'tecnico', 'cliente'])->count();
                    $rolesPersonalizados = $totalRoles - $rolesProtegidos;
                    $totalPermisos = \Spatie\Permission\Models\Permission::count();
                    
                    $stats = [
                        [
                            'count' => $totalRoles,
                            'label' => 'Total Roles',
                            'color' => 'dark',
                            'icon' => 'user-shield'
                        ],
                        [
                            'count' => $rolesProtegidos,
                            'label' => 'Roles del Sistema',
                            'color' => 'warning',
                            'icon' => 'shield-alt'
                        ],
                        [
                            'count' => $rolesPersonalizados,
                            'label' => 'Roles Personalizados',
                            'color' => 'success',
                            'icon' => 'user-cog'
                        ],
                        [
                            'count' => $totalPermisos,
                            'label' => 'Permisos Totales',
                            'color' => 'info',
                            'icon' => 'key'
                        ],
                    ];
                @endphp

                @foreach($stats as $stat)
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-{{ $stat['color'] }}">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle mb-1 text-muted">{{ $stat['label'] }}</h6>
                                    <h3 class="mb-0">{{ $stat['count'] }}</h3>
                                </div>
                                <div class="display-4 text-{{ $stat['color'] }}">
                                    <i class="fas fa-{{ $stat['icon'] }}"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Tabla de roles -->
            <div class="card">
                <div class="card-body p-0">
                    @if($roles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="60">ID</th>
                                        <th>Nombre del Rol</th>
                                        <th>Permisos</th>
                                        <th>Usuarios</th>
                                        <th>Tipo</th>
                                        <th>Fecha Creación</th>
                                        <th width="120">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($roles as $role)
                                    @php
                                        $esProtegido = in_array($role->name, ['super_admin', 'administrador', 'tecnico', 'cliente']);
                                        $tieneUsuarios = $role->users()->count() > 0;
                                        $colorTipo = $esProtegido ? 'warning' : 'success';
                                        $textoTipo = $esProtegido ? 'Sistema' : 'Personalizado';
                                        $iconoTipo = $esProtegido ? 'shield-alt' : 'user-cog';
                                    @endphp
                                    <tr class="{{ $esProtegido ? 'table-warning' : '' }}">
                                        <td>
                                            <span class="badge bg-secondary">#{{ $role->id }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-{{ $colorTipo }} text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                    style="width: 36px; height: 36px; font-size: 14px;">
                                                    <i class="fas fa-{{ $iconoTipo }}"></i>
                                                </div>
                                                <div>
                                                    <strong class="{{ $role->name == 'super_admin' ? 'text-danger' : '' }}">
                                                        {{ ucfirst($role->name) }}
                                                    </strong>
                                                    @if($role->name == 'super_admin')
                                                        <i class="fas fa-crown text-danger ms-1"></i>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-1" style="max-width: 250px;">
                                                @php
                                                    $permisosMostrar = $role->permissions->take(3);
                                                    $totalPermisos = $role->permissions->count();
                                                @endphp
                                                
                                                @foreach($permisosMostrar as $permiso)
                                                <span class="badge bg-primary" title="{{ $permiso->name }}">
                                                    {{ str_replace('-', ' ', $permiso->name) }}
                                                </span>
                                                @endforeach
                                                
                                                @if($totalPermisos > 3)
                                                <span class="badge bg-secondary">
                                                    +{{ $totalPermisos - 3 }} más
                                                </span>
                                                @endif
                                                
                                                @if($totalPermisos == 0)
                                                <span class="badge bg-danger">
                                                    Sin permisos
                                                </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $totalUsuarios = $role->users()->count();
                                            @endphp
                                            
                                            @if($totalUsuarios > 0)
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-info me-2">{{ $totalUsuarios }}</span>
                                                <small class="text-muted">usuarios</small>
                                            </div>
                                            @else
                                            <span class="badge bg-secondary">Sin usuarios</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $colorTipo }}">
                                                <i class="fas fa-{{ $iconoTipo }} me-1"></i>
                                                {{ $textoTipo }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ $role->created_at->format('d/m/Y') }}</small>
                                            <br>
                                            <small class="text-muted">{{ $role->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <!-- Ver -->
                                                @can('ver-roles')
                                                <a href="{{ route('roles.show', $role->id) }}" 
                                                   class="btn btn-outline-info" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan

                                                <!-- Editar -->
                                                @can('editar-roles')
                                                <a href="{{ route('roles.edit', $role->id) }}" 
                                                   class="btn btn-outline-warning" title="Editar"
                                                   @if($esProtegido || ($role->name == 'super_admin' && !auth()->user()->hasRole('super_admin')))
                                                        disabled
                                                   @endif>
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan

                                                <!-- Eliminar -->
                                                @can('eliminar-roles')
                                                <button type="button" 
                                                        class="btn btn-outline-danger" 
                                                        title="Eliminar"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalEliminar{{ $role->id }}"
                                                        @if($esProtegido || $tieneUsuarios || !auth()->user()->hasRole('super_admin'))
                                                            disabled
                                                        @endif>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endcan
                                            </div>

                                            <!-- Modal de confirmación para eliminar -->
                                            @can('eliminar-roles')
                                            @if(!$esProtegido && !$tieneUsuarios && auth()->user()->hasRole('super_admin'))
                                            <div class="modal fade" id="modalEliminar{{ $role->id }}" tabindex="-1">
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
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                Cancelar
                                                            </button>
                                                            <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">
                                                                    Sí, eliminar rol
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            @endcan
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginación -->
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">
                                        Mostrando {{ $roles->firstItem() }} - {{ $roles->lastItem() }}
                                        de {{ $roles->total() }} roles
                                    </small>
                                </div>
                                <div>
                                    {{ $roles->links() }}
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="display-4 text-muted mb-3">
                                <i class="fas fa-user-shield"></i>
                            </div>
                            <h4 class="text-muted">No hay roles</h4>
                            <p class="text-muted mb-4">
                                No se han creado roles en el sistema
                            </p>
                            @can('crear-roles')
                            <a href="{{ route('roles.create') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-plus-circle me-2"></i> Crear Primer Rol
                            </a>
                            @endcan
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ver todos los permisos -->
<div class="modal fade" id="modalPermisos" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-key me-2"></i> Todos los Permisos del Sistema
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @php
                    $permisos = \Spatie\Permission\Models\Permission::orderBy('name')->get();
                    $permisosAgrupados = $permisos->groupBy(function ($permission) {
                        $parts = explode('-', $permission->name);
                        return $parts[0] ?? 'general';
                    });
                @endphp
                
                @foreach($permisosAgrupados as $categoria => $grupoPermisos)
                <div class="mb-4">
                    <h6 class="text-primary mb-2">
                        <i class="fas fa-folder me-1"></i>
                        {{ ucfirst($categoria) }}
                        <span class="badge bg-primary ms-2">{{ $grupoPermisos->count() }}</span>
                    </h6>
                    <div class="row">
                        @foreach($grupoPermisos as $permiso)
                        <div class="col-md-6 mb-2">
                            <div class="border rounded p-2 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-info me-2">
                                        {{ str_replace('-', ' ', $permiso->name) }}
                                    </span>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-hashtag"></i> {{ $permiso->id }}
                                </small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
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
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .badge {
        font-size: 0.75em;
    }
    
    .table td {
        vertical-align: middle;
    }
    
    .table-warning {
        background-color: rgba(255, 193, 7, 0.1);
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
        
        // Confirmación antes de eliminar (por si usamos enlaces directos)
        document.querySelectorAll('form[action*="roles"] button[type="submit"]').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('¿Estás seguro de eliminar este rol?')) {
                    e.preventDefault();
                }
            });
        });
        
        // Mostrar detalles de permisos en tooltips
        document.querySelectorAll('.badge[title]').forEach(badge => {
            badge.setAttribute('data-bs-toggle', 'tooltip');
            new bootstrap.Tooltip(badge);
        });
    });
</script>
@endpush
@endsection