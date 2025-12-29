@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-users me-2"></i> Gestión de Usuarios
                    </h1>
                    <p class="text-muted mb-0">
                        Administra los usuarios del sistema según tus permisos
                    </p>
                </div>

                <div class="d-flex gap-2">
                    <!-- Botón para crear nuevo usuario (solo con permiso) -->
                    @can('crear-usuarios')
                    <a href="{{ route('users.create') }}" class="btn btn-success">
                        <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
                    </a>
                    @endcan

                    <!-- Filtro por rol -->                    
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-filter me-1"></i>
                            @if(request('rol'))
                                {{ ucfirst(request('rol')) }}
                            @else
                                Todos los roles
                            @endif
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('users.index', array_diff_key(request()->query(), ['rol' => ''])) }}">
                                    Todos los roles
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="{{ route('users.index', array_merge(request()->query(), ['rol' => 'cliente'])) }}">
                                    Clientes
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('users.index', array_merge(request()->query(), ['rol' => 'tecnico'])) }}">
                                    Técnicos
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('users.index', array_merge(request()->query(), ['rol' => 'administrador'])) }}">
                                    Administradores
                                </a>
                            </li>
                            @if(auth()->user()->hasRole('super_admin'))
                            <li>
                                <a class="dropdown-item" href="{{ route('users.index', array_merge(request()->query(), ['rol' => 'super_admin'])) }}">
                                    Super Admins
                                </a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Tarjetas de estadísticas -->
            <div class="row mb-4">
                @php
                    // Usuarios totales sin contar super_admins (si no es super_admin)
                    $userActual = auth()->user();
                    $totalUsuarios = \App\Models\User::query();
                    
                    if (!$userActual->hasRole('super_admin')) {
                        $totalUsuarios->whereDoesntHave('roles', function($q) {
                            $q->where('name', 'super_admin');
                        });
                    }
                    
                    $stats = [
                        [
                            'count' => $totalUsuarios->count(),
                            'label' => 'Total Usuarios',
                            'color' => 'dark',
                            'icon' => 'users'
                        ],
                        [
                            'count' => \App\Models\User::whereHas('roles', function($q) {
                                $q->where('name', 'cliente');
                            })->count(),
                            'label' => 'Clientes',
                            'color' => 'primary',
                            'icon' => 'user'
                        ],
                        [
                            'count' => \App\Models\User::whereHas('roles', function($q) {
                                $q->where('name', 'tecnico');
                            })->count(),
                            'label' => 'Técnicos',
                            'color' => 'info',
                            'icon' => 'user-hard-hat'
                        ],
                        [
                            'count' => \App\Models\User::whereHas('roles', function($q) {
                                $q->where('name', 'administrador');
                            })->count(),
                            'label' => 'Administradores',
                            'color' => 'warning',
                            'icon' => 'user-shield'
                        ],
                    ];
                    
                    // Solo super_admin ve la tarjeta de super_admins
                    if ($userActual->hasRole('super_admin')) {
                        $stats[] = [
                            'count' => \App\Models\User::whereHas('roles', function($q) {
                                $q->where('name', 'super_admin');
                            })->count(),
                            'label' => 'Super Admins',
                            'color' => 'danger',
                            'icon' => 'crown'
                        ];
                    }
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

            <!-- Tabla de usuarios -->
            <div class="card">
                <div class="card-body p-0">
                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="50">ID</th>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Teléfono</th>
                                        <th>Rol</th>
                                        <th>Fecha Registro</th>
                                        <th width="150">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary">#{{ $user->id }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2"
                                                    style="width: 36px; height: 36px; font-size: 14px;">
                                                    {{ substr($user->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <strong>{{ $user->name }}</strong>
                                                    @if(auth()->id() == $user->id)
                                                        <span class="badge bg-info ms-1">Tú</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small>{{ $user->email }}</small>
                                            @if(!$user->email_verified_at)
                                                <br>
                                                <small class="text-warning">
                                                    <i class="fas fa-exclamation-circle"></i> No verificado
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <small>{{ $user->telefono ?? 'No registrado' }}</small>
                                        </td>
                                        <td>
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
                                            <span class="badge bg-{{ $colores[$role] ?? 'secondary' }} mb-1">
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
                                        </td>
                                        <td>
                                            <small>{{ $user->created_at->format('d/m/Y') }}</small>
                                            <br>
                                            <small class="text-muted">{{ $user->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <!-- Ver -->
                                                @can('ver-usuarios')
                                                <a href="{{ route('users.show', $user->id) }}" 
                                                   class="btn btn-outline-info" title="Ver detalles">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan

                                                <!-- Editar -->
                                                @can('editar-usuarios')
                                                <a href="{{ route('users.edit', $user->id) }}" 
                                                   class="btn btn-outline-warning" title="Editar"
                                                   @if(!auth()->user()->can('editar-usuarios') || 
                                                       (auth()->id() == $user->id && $user->hasRole('super_admin') && !auth()->user()->hasRole('super_admin')))
                                                        disabled
                                                   @endif>
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan

                                                <!-- Eliminar -->
                                                @can('eliminar-usuarios')
                                                <button type="button" 
                                                        class="btn btn-outline-danger" 
                                                        title="Eliminar"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalEliminar{{ $user->id }}"
                                                        @if(auth()->id() == $user->id || 
                                                            ($user->hasRole('super_admin') && !auth()->user()->hasRole('super_admin')))
                                                            disabled
                                                        @endif>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endcan
                                            </div>

                                            <!-- Modal de confirmación para eliminar -->
                                            @can('eliminar-usuarios')
                                            @if(auth()->id() != $user->id && 
                                                !($user->hasRole('super_admin') && !auth()->user()->hasRole('super_admin')))
                                            <div class="modal fade" id="modalEliminar{{ $user->id }}" tabindex="-1">
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
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-circle me-2"></i>
                                                                <strong>¡Atención!</strong> Esta acción no se puede deshacer.
                                                            </div>
                                                            <ul class="text-muted">
                                                                <li>Email: {{ $user->email }}</li>
                                                                <li>Rol: {{ $user->getRoleNames()->first() }}</li>
                                                                <li>Registrado: {{ $user->created_at->format('d/m/Y') }}</li>
                                                            </ul>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                Cancelar
                                                            </button>
                                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger">
                                                                    Sí, eliminar usuario
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
                        @if(method_exists($users, 'links'))
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted">
                                        Mostrando {{ $users->firstItem() }} - {{ $users->lastItem() }}
                                        de {{ $users->total() }} usuarios
                                    </small>
                                </div>
                                <div>
                                    {{ $users->links() }}
                                </div>
                            </div>
                        </div>
                        @endif
                    @else
                        <div class="text-center py-5">
                            <div class="display-4 text-muted mb-3">
                                <i class="fas fa-user-slash"></i>
                            </div>
                            <h4 class="text-muted">No hay usuarios</h4>
                            <p class="text-muted mb-4">
                                @if(request('rol'))
                                    No hay usuarios con el rol "{{ request('rol') }}"
                                @else
                                    No hay usuarios en el sistema
                                @endif
                            </p>
                            @can('crear-usuarios')
                            <a href="{{ route('users.create') }}" class="btn btn-success btn-lg">
                                <i class="fas fa-user-plus me-2"></i> Crear Primer Usuario
                            </a>
                            @endcan
                        </div>
                    @endif
                </div>
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
        
        // Auto-submit filtros
        document.querySelectorAll('.filtro-auto').forEach(select => {
            select.addEventListener('change', function() {
                this.form.submit();
            });
        });
        
        // Confirmación antes de eliminar (por si usamos enlaces directos)
        document.querySelectorAll('form[action*="users"] button[type="submit"]').forEach(button => {
            button.addEventListener('click', function(e) {
                if (!confirm('¿Estás seguro de eliminar este usuario?')) {
                    e.preventDefault();
                }
            });
        });
    });
</script>
@endpush
@endsection