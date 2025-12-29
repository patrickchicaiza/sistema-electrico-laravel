@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Header dinámico según rol -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0">
                            @if(auth()->user()->es_cliente)
                                <i class="fas fa-clipboard-list me-2"></i> Mis Reportes
                            @elseif(auth()->user()->es_tecnico)
                                <i class="fas fa-tasks me-2"></i> Reportes Asignados
                            @else
                                <i class="fas fa-clipboard-check me-2"></i> Todos los Reportes
                            @endif
                        </h1>
                        <p class="text-muted mb-0">
                            @if(auth()->user()->es_cliente)
                                Gestión de tus reportes de fallas eléctricas
                            @elseif(auth()->user()->es_tecnico)
                                Reportes asignados para atención
                            @else
                                Administración completa de reportes
                            @endif
                        </p>
                    </div>

                    <div class="d-flex gap-2">
                        <!-- Botón crear solo para clientes -->
                        @if(auth()->user()->es_cliente && auth()->user()->puede_crear_reporte)
                            <a href="{{ route('reportes.create') }}" class="btn btn-success">
                                <i class="fas fa-plus-circle me-1"></i> Nuevo Reporte
                            </a>
                        @endif

                        <!-- Filtro por estado -->
                        <div class="dropdown">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-filter me-1"></i>
                                {{ request('estado', 'todos') == 'todos' ? 'Todos los estados' : ucfirst(request('estado')) }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item"
                                        href="{{ request()->fullUrlWithQuery(['estado' => 'todos']) }}">Todos</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item"
                                        href="{{ request()->fullUrlWithQuery(['estado' => 'pendiente']) }}">Pendientes</a>
                                </li>
                                <li><a class="dropdown-item"
                                        href="{{ request()->fullUrlWithQuery(['estado' => 'asignado']) }}">Asignados</a>
                                </li>
                                <li><a class="dropdown-item"
                                        href="{{ request()->fullUrlWithQuery(['estado' => 'en_proceso']) }}">En Proceso</a>
                                </li>
                                <li><a class="dropdown-item"
                                        href="{{ request()->fullUrlWithQuery(['estado' => 'resuelto']) }}">Resueltos</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Filtros avanzados para admin -->
                @if(auth()->user()->es_administrador)
                    <div class="card mb-4">
                        <div class="card-body">
                            <form action="{{ route('reportes.index') }}" method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Prioridad</label>
                                    <select name="prioridad" class="form-select">
                                        <option value="todos" {{ request('prioridad') == 'todos' ? 'selected' : '' }}>Todas
                                        </option>
                                        <option value="alta" {{ request('prioridad') == 'alta' ? 'selected' : '' }}>Alta</option>
                                        <option value="media" {{ request('prioridad') == 'media' ? 'selected' : '' }}>Media
                                        </option>
                                        <option value="baja" {{ request('prioridad') == 'baja' ? 'selected' : '' }}>Baja</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Fecha desde</label>
                                    <input type="date" name="fecha_desde" class="form-control"
                                        value="{{ request('fecha_desde') }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Fecha hasta</label>
                                    <input type="date" name="fecha_hasta" class="form-control"
                                        value="{{ request('fecha_hasta') }}">
                                </div>

                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-search me-1"></i> Filtrar
                                    </button>
                                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary ms-2">
                                        <i class="fas fa-times"></i>
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif

                <!-- Tarjetas de estadísticas rápidas -->
                <div class="row mb-4">
                    @php
                        // Estadísticas según rol
                        if (auth()->user()->es_cliente) {
                            $stats = [
                                [
                                    'count' => auth()->user()->reportesComoCliente()->where('estado', 'pendiente')->count(),
                                    'label' => 'Pendientes',
                                    'color' => 'warning',
                                    'icon' => 'clock'
                                ],
                                [
                                    'count' => auth()->user()->reportesComoCliente()->where('estado', 'asignado')->count(),
                                    'label' => 'Asignados',
                                    'color' => 'info',
                                    'icon' => 'user-check'
                                ],
                                [
                                    'count' => auth()->user()->reportesComoCliente()->where('estado', 'en_proceso')->count(),
                                    'label' => 'En Proceso',
                                    'color' => 'primary',
                                    'icon' => 'tools'
                                ],
                                [
                                    'count' => auth()->user()->reportesComoCliente()->where('estado', 'resuelto')->count(),
                                    'label' => 'Resueltos',
                                    'color' => 'success',
                                    'icon' => 'check-circle'
                                ],
                            ];
                        } elseif (auth()->user()->es_tecnico) {
                            $stats = [
                                [
                                    'count' => auth()->user()->reportesComoTecnico()->where('estado', 'asignado')->count(),
                                    'label' => 'Por Atender',
                                    'color' => 'info',
                                    'icon' => 'inbox'
                                ],
                                [
                                    'count' => auth()->user()->reportesComoTecnico()->where('estado', 'en_proceso')->count(),
                                    'label' => 'En Reparación',
                                    'color' => 'primary',
                                    'icon' => 'tools'
                                ],
                                [
                                    'count' => auth()->user()->reportesComoTecnico()->where('estado', 'resuelto')->count(),
                                    'label' => 'Completados',
                                    'color' => 'success',
                                    'icon' => 'check-circle'
                                ],
                                [
                                    'count' => auth()->user()->reportesComoTecnico()->where('prioridad', 'alta')->whereIn('estado', ['asignado', 'en_proceso'])->count(),
                                    'label' => 'Urgentes',
                                    'color' => 'danger',
                                    'icon' => 'exclamation-triangle'
                                ],
                            ];
                        } else {
                            $stats = [
                                [
                                    'count' => \App\Models\Reporte::pendientes()->count(),
                                    'label' => 'Pendientes',
                                    'color' => 'warning',
                                    'icon' => 'clock'
                                ],
                                [
                                    'count' => \App\Models\Reporte::where('estado', 'asignado')->count(),
                                    'label' => 'Asignados',
                                    'color' => 'info',
                                    'icon' => 'user-check'
                                ],
                                [
                                    'count' => \App\Models\Reporte::where('estado', 'en_proceso')->count(),
                                    'label' => 'En Proceso',
                                    'color' => 'primary',
                                    'icon' => 'tools'
                                ],
                                [
                                    'count' => \App\Models\Reporte::where('estado', 'resuelto')->count(),
                                    'label' => 'Resueltos',
                                    'color' => 'success',
                                    'icon' => 'check-circle'
                                ],
                            ];
                        }
                    @endphp

                    @foreach($stats as $stat)
                        <div class="col-md-3">
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

                <!-- Tabla de reportes -->
                <div class="card">
                    <div class="card-body p-0">
                        @if($reportes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="120">Código</th>
                                            <th>Descripción</th>
                                            @if(auth()->user()->es_administrador)
                                                <th>Cliente</th>
                                            @endif
                                            <th>Estado</th>
                                            <th>Prioridad</th>
                                            <th>Fecha</th>
                                            @if(auth()->user()->es_administrador)
                                                <th>Técnico</th>
                                            @endif
                                            <th width="150">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportes as $reporte)
                                            <tr
                                                class="{{ $reporte->prioridad == 'alta' && $reporte->estado != 'resuelto' ? 'table-danger' : '' }}">
                                                <td>
                                                    <span class="badge bg-dark">{{ $reporte->codigo }}</span>
                                                </td>
                                                <td class="text-truncate" style="max-width: 250px;">
                                                    {{ $reporte->descripcion }}
                                                    @if($reporte->evidencias_count > 0)
                                                        <small class="text-muted ms-2">
                                                            <i class="fas fa-camera"></i> {{ $reporte->evidencias_count }}
                                                        </small>
                                                    @endif
                                                </td>

                                                @if(auth()->user()->es_administrador)
                                                    <td>
                                                        <small>{{ $reporte->cliente->name }}</small>                                                        
                                                    </td>
                                                @endif

                                                <td>
                                                    @php
                                                        $estados = [
                                                            'pendiente' => ['warning', '⏳'],
                                                            'asignado' => ['info', '👷'],
                                                            'en_proceso' => ['primary', '🔧'],
                                                            'resuelto' => ['success', '✅'],
                                                            'cancelado' => ['danger', '❌']
                                                        ];
                                                        [$color, $icon] = $estados[$reporte->estado] ?? ['secondary', ''];
                                                    @endphp
                                                    <span class="badge bg-{{ $color }}">
                                                        {{ $icon }} {{ ucfirst($reporte->estado) }}
                                                    </span>
                                                </td>

                                                <td>
                                                    @php
                                                        $prioridades = [
                                                            'alta' => 'danger',
                                                            'media' => 'warning',
                                                            'baja' => 'secondary'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $prioridades[$reporte->prioridad] }}">
                                                        {{ ucfirst($reporte->prioridad) }}
                                                    </span>
                                                </td>

                                                <td>
                                                    <small>{{ $reporte->created_at->format('d/m/Y') }}</small> - <small class="text-muted">{{ $reporte->created_at->format('H:i') }}</small>
                                                </td>

                                                @if(auth()->user()->es_administrador)
                                                    <td>
                                                        @if($reporte->tecnico)
                                                            <small>{{ $reporte->tecnico->name }}</small>                                                            
                                                        @else
                                                            <!-- Diferentes mensajes según estado -->
                                                            @php
                                                                $mensajes = [
                                                                    'pendiente' => ['warning', 'Por asignar'],
                                                                    'asignado' => ['info', 'Asignado'],
                                                                    'en_proceso' => ['primary', 'En proceso'],
                                                                    'resuelto' => ['success', 'Resuelto'],
                                                                    'cancelado' => ['danger', 'Cancelado']
                                                                ];
                                                                [$color_tecnico, $texto_tecnico] = $mensajes[$reporte->estado] ?? ['secondary', 'Sin asignar'];
                                                            @endphp
                                                            
                                                            <span class="badge bg-{{ $color_tecnico }}">
                                                                {{ $texto_tecnico }}
                                                            </span>
                                                            
                                                            @if($reporte->estado == 'pendiente')
                                                                
                                                            @endif
                                                        @endif
                                                    </td>
                                                @endif

                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('reportes.show', $reporte->id) }}"
                                                            class="btn btn-outline-info" title="Ver">
                                                            <i class="fas fa-eye"></i>
                                                        </a>

                                                        @if(auth()->user()->es_cliente && $reporte->estado == 'pendiente')
                                                            <a href="{{ route('reportes.edit', $reporte->id) }}"
                                                                class="btn btn-outline-warning" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endif

                                                        @if(auth()->user()->es_tecnico && $reporte->tecnico_asignado_id == auth()->id())
                                                            @if($reporte->estado == 'asignado')
                                                                <form action="{{ route('reportes.cambiar-estado', $reporte->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    <input type="hidden" name="estado" value="en_proceso">
                                                                    <button type="submit" class="btn btn-outline-primary"
                                                                        title="Comenzar trabajo">
                                                                        <i class="fas fa-play"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @endif

                                                        @if(auth()->user()->es_administrador && $reporte->estado == 'pendiente' && !$reporte->tecnico)
                                                            <div class="dropdown d-inline">
                                                                <button class="btn btn-outline-success dropdown-toggle" type="button"
                                                                    data-bs-toggle="dropdown" title="Asignar técnico"
                                                                    onclick="event.stopPropagation();">
                                                                    <i class="fas fa-user-plus"></i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    @foreach(\App\Models\User::tecnicosDisponibles()->get() as $tecnico)
                                                                        <li>
                                                                            <form action="{{ route('reportes.asignar', $reporte->id) }}"
                                                                                method="POST" onclick="event.stopPropagation();">
                                                                                @csrf
                                                                                <input type="hidden" name="tecnico_id"
                                                                                    value="{{ $tecnico->id }}">
                                                                                <button type="submit" class="dropdown-item" 
                                                                                        onclick="return confirm('¿Asignar este reporte a {{ $tecnico->name }}?');">
                                                                                    {{ $tecnico->name }}
                                                                                </button>
                                                                            </form>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @endif
                                                    </div>
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
                                            Mostrando {{ $reportes->firstItem() }} - {{ $reportes->lastItem() }}
                                            de {{ $reportes->total() }} reportes
                                        </small>
                                    </div>
                                    <div>
                                        {{ $reportes->links() }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="display-4 text-muted mb-3">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <h4 class="text-muted">No hay reportes</h4>
                                <p class="text-muted mb-4">
                                    @if(auth()->user()->es_cliente)
                                        No has creado reportes aún
                                    @elseif(auth()->user()->es_tecnico)
                                        No tienes reportes asignados
                                    @else
                                        No hay reportes en el sistema
                                    @endif
                                </p>
                                @if(auth()->user()->es_cliente && auth()->user()->puede_crear_reporte)
                                    <a href="{{ route('reportes.create') }}" class="btn btn-success btn-lg">
                                        <i class="fas fa-plus-circle me-2"></i> Crear Primer Reporte
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Tooltips
                var tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltips.map(function (tooltip) {
                    return new bootstrap.Tooltip(tooltip);
                });

                // Confirmación para cambiar estado
                document.querySelectorAll('form[action*="cambiar-estado"]').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        if (!confirm('¿Comenzar a trabajar en este reporte?')) {
                            e.preventDefault();
                        }
                    });
                });

                // Auto-submit filtros para admin
                document.querySelectorAll('.filtro-auto').forEach(select => {
                    select.addEventListener('change', function () {
                        this.form.submit();
                    });
                });
            });
        </script>
    @endpush
@endsection