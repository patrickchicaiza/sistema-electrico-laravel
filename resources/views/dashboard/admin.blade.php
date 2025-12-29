@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0">👑 Dashboard - Administrador</h1>
                        <p class="text-muted mb-0">Panel de control del sistema</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalReportes">
                            <i class="fas fa-chart-bar me-1"></i> Reportes
                        </button>
                        <a href="{{ route('users.index') }}" class="btn btn-outline-success">
                            <i class="fas fa-users me-1"></i> Usuarios
                        </a>                        
                    </div>
                </div>

                <!-- KPI Cards -->
                <div class="row mb-4">
                    <div class="col-xl-2 col-md-4 mb-4">
                        <div class="card border-start border-primary border-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-primary mb-1">
                                            Total Reportes
                                        </div>
                                        <div class="h5 mb-0 fw-bold">
                                            {{ \App\Models\Reporte::count() }}
                                        </div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-success mr-2">
                                                <i class="fas fa-arrow-up"></i> 5.2%
                                            </span>
                                            <span>vs mes anterior</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clipboard-list fa-2x text-primary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 mb-4">
                        <div class="card border-start border-warning border-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-warning mb-1">
                                            Pendientes
                                        </div>
                                        <div class="h5 mb-0 fw-bold">
                                            {{ \App\Models\Reporte::pendientes()->count() }}
                                        </div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-danger mr-2">
                                                <i class="fas fa-arrow-down"></i> 2.3%
                                            </span>
                                            <span>menos que ayer</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-clock fa-2x text-warning"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 mb-4">
                        <div class="card border-start border-danger border-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-danger mb-1">
                                            Urgentes (Alta)
                                        </div>
                                        <div class="h5 mb-0 fw-bold">
                                            {{ \App\Models\Reporte::where('prioridad', 'alta')->where('estado', 'pendiente')->count() }}
                                        </div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-success mr-2">
                                                <i class="fas fa-arrow-up"></i> 12%
                                            </span>
                                            <span>aumento</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 mb-4">
                        <div class="card border-start border-success border-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-success mb-1">
                                            Resueltos (Hoy)
                                        </div>
                                        <div class="h5 mb-0 fw-bold">
                                            {{ \App\Models\Reporte::where('estado', 'resuelto')->whereDate('updated_at', today())->count() }}
                                        </div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-success mr-2">
                                                <i class="fas fa-arrow-up"></i> 8.4%
                                            </span>
                                            <span>eficiencia</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-check-circle fa-2x text-success"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 mb-4">
                        <div class="card border-start border-info border-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-info mb-1">
                                            Técnicos Activos
                                        </div>
                                        <div class="h5 mb-0 fw-bold">
                                            {{ \App\Models\User::tecnicosDisponibles()->count() }}
                                        </div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-success mr-2">
                                                <i class="fas fa-user-check"></i>
                                            </span>
                                            <span>disponibles</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-user-hard-hat fa-2x text-info"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-2 col-md-4 mb-4">
                        <div class="card border-start border-secondary border-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs fw-bold text-secondary mb-1">
                                            Clientes Activos
                                        </div>
                                        <div class="h5 mb-0 fw-bold">
                                            {{ \App\Models\User::conRol('cliente')->count() }}
                                        </div>
                                        <div class="mt-2 mb-0 text-muted text-xs">
                                            <span class="text-success mr-2">
                                                <i class="fas fa-arrow-up"></i> 3.1%
                                            </span>
                                            <span>crecimiento</span>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-users fa-2x text-secondary"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dos columnas principales -->
                <div class="row">
                    <!-- Columna izquierda: Reportes recientes y asignación -->
                    <div class="col-lg-8">
                        <!-- Reportes pendientes de asignar -->
                        <div class="card mb-4">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-tasks me-2"></i>
                                    Reportes Pendientes de Asignar
                                </h5>
                                <span class="badge bg-warning">
                                    {{ \App\Models\Reporte::pendientes()->count() }} pendientes
                                </span>
                            </div>
                            <div class="card-body p-0">
                                @php
                                    $reportesPendientes = \App\Models\Reporte::pendientes()
                                        ->with('cliente')
                                        ->orderBy('prioridad', 'desc')
                                        ->orderBy('created_at')
                                        ->limit(6)
                                        ->get();
                                @endphp

                                @if($reportesPendientes->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Código</th>
                                                    <th>Cliente</th>
                                                    <th>Prioridad</th>
                                                    <th>Dirección</th>
                                                    <th>Tiempo</th>
                                                    <th>Asignar</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($reportesPendientes as $reporte)
                                                    <tr class="{{ $reporte->prioridad == 'alta' ? 'table-danger' : '' }}">
                                                        <td>
                                                            <strong>{{ $reporte->codigo }}</strong>
                                                        </td>
                                                        <td>
                                                            {{ $reporte->cliente->name }}
                                                            <br>
                                                            <small class="text-muted">{{ $reporte->cliente->telefono }}</small>
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ $reporte->prioridad == 'alta' ? 'danger' : ($reporte->prioridad == 'media' ? 'warning' : 'secondary') }}">
                                                                {{ $reporte->prioridad }}
                                                            </span>
                                                        </td>
                                                        <td class="text-truncate" style="max-width: 150px;">
                                                            <small>{{ $reporte->direccion }}</small>
                                                        </td>
                                                        <td>
                                                            <small>{{ $reporte->created_at->diffForHumans() }}</small>
                                                        </td>
                                                        <td>
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle"
                                                                    type="button" data-bs-toggle="dropdown"
                                                                    onclick="event.stopPropagation();">
                                                                    <i class="fas fa-user-plus"></i>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    @foreach(\App\Models\User::tecnicosDisponibles()->get() as $tecnico)
                                                                        <li>
                                                                            <form action="{{ route('reportes.asignar', $reporte->id) }}"
                                                                                method="POST" class="d-inline w-100"
                                                                                onclick="event.stopPropagation();">
                                                                                @csrf
                                                                                <input type="hidden" name="tecnico_id"
                                                                                    value="{{ $tecnico->id }}">
                                                                                <button type="submit"
                                                                                    class="dropdown-item w-100 text-start"
                                                                                    onclick="return confirm('¿Asignar este reporte a {{ $tecnico->name }}?');">
                                                                                    <i
                                                                                        class="fas fa-user-hard-hat me-2"></i>{{ $tecnico->name }}
                                                                                </button>
                                                                            </form>
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="card-footer bg-white">
                                        <a href="{{ route('reportes.index') }}?estado=pendiente"
                                            class="btn btn-sm btn-outline-primary">
                                            Ver todos los pendientes
                                        </a>
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <div class="display-4 text-success mb-3">
                                            <i class="fas fa-check-double"></i>
                                        </div>
                                        <h5 class="text-success">¡Todo al día!</h5>
                                        <p class="text-muted">No hay reportes pendientes de asignar</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Actividad reciente del sistema -->
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">
                                    <i class="fas fa-history me-2"></i>
                                    Actividad Reciente
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    @php
                                        $actividades = \App\Models\Reporte::with(['cliente', 'tecnico'])
                                            ->latest()
                                            ->limit(8)
                                            ->get();
                                    @endphp

                                    @foreach($actividades as $actividad)
                                        <div class="timeline-item mb-3">
                                            <div class="timeline-marker 
                                                                @if($actividad->estado == 'resuelto') bg-success
                                                                @elseif($actividad->estado == 'en_proceso') bg-primary
                                                                @elseif($actividad->estado == 'asignado') bg-info
                                                                @else bg-warning @endif">
                                            </div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="mb-1">
                                                        Reporte {{ $actividad->codigo }}
                                                        <span
                                                            class="badge bg-{{ $actividad->prioridad == 'alta' ? 'danger' : 'secondary' }}">
                                                            {{ $actividad->prioridad }}
                                                        </span>
                                                    </h6>
                                                    <small class="text-muted">
                                                        {{ $actividad->updated_at->diffForHumans() }}
                                                    </small>
                                                </div>
                                                <p class="mb-1 small">
                                                    @if($actividad->tecnico)
                                                        <i class="fas fa-user-hard-hat text-info me-1"></i>
                                                        {{ $actividad->tecnico->name }}
                                                        @if($actividad->estado == 'resuelto')
                                                            resolvió el reporte
                                                        @else
                                                            está trabajando en ello
                                                        @endif
                                                    @else
                                                        <i class="fas fa-user text-secondary me-1"></i>
                                                        {{ $actividad->cliente->name }} reportó:
                                                        {{ Str::limit($actividad->descripcion, 80) }}
                                                    @endif
                                                </p>
                                                @if($actividad->estado == 'resuelto' && $actividad->solucion)
                                                    <div class="alert alert-success py-2 px-3 small mb-0">
                                                        <strong>Solución:</strong> {{ Str::limit($actividad->solucion, 100) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha: Estadísticas y acciones -->
                    <div class="col-lg-4">
                        <!-- Distribución por estado -->
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-chart-pie me-2"></i>
                                    Distribución de Reportes
                                </h6>
                            </div>
                            <div class="card-body">
                                @php
                                    $estados = [
                                        'pendiente' => \App\Models\Reporte::where('estado', 'pendiente')->count(),
                                        'asignado' => \App\Models\Reporte::where('estado', 'asignado')->count(),
                                        'en_proceso' => \App\Models\Reporte::where('estado', 'en_proceso')->count(),
                                        'resuelto' => \App\Models\Reporte::where('estado', 'resuelto')->count(),
                                        'cancelado' => \App\Models\Reporte::where('estado', 'cancelado')->count(),
                                    ];
                                    $total = array_sum($estados);
                                @endphp

                                <div class="mb-3">
                                    @foreach($estados as $estado => $cantidad)
                                        @if($cantidad > 0)
                                            <div class="mb-2">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <span class="text-capitalize">{{ $estado }}</span>
                                                    <span class="fw-bold">{{ $cantidad }}</span>
                                                </div>
                                                <div class="progress" style="height: 8px;">
                                                    @php
                                                        $porcentaje = $total > 0 ? round(($cantidad / $total) * 100) : 0;
                                                        $colores = [
                                                            'pendiente' => 'warning',
                                                            'asignado' => 'info',
                                                            'en_proceso' => 'primary',
                                                            'resuelto' => 'success',
                                                            'cancelado' => 'danger'
                                                        ];
                                                    @endphp
                                                    <div class="progress-bar bg-{{ $colores[$estado] }}"
                                                        style="width: {{ $porcentaje }}%"></div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                <div class="row text-center">
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <div class="h5 mb-1">{{ round(($estados['resuelto'] / max(1, $total)) * 100) }}%
                                            </div>
                                            <small class="text-muted">Tasa de resolución</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="border rounded p-2">
                                            <div class="h5 mb-1">
                                                @php
                                                    $tiempoPromedio = \App\Models\Reporte::where('estado', 'resuelto')
                                                        ->whereNotNull('fecha_cierre')
                                                        ->avg(\DB::raw('EXTRACT(EPOCH FROM (fecha_cierre - created_at)) / 3600'));
                                                @endphp
                                                {{ $tiempoPromedio ? round($tiempoPromedio, 1) : 'N/A' }}h
                                            </div>
                                            <small class="text-muted">Promedio resolución</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Técnicos con mejor desempeño -->
                        <div class="card">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-trophy me-2"></i>
                                    Técnicos Destacados
                                </h6>
                            </div>
                            <div class="card-body">
                                @php
                                    $tecnicos = \App\Models\User::conRol('tecnico')
                                        ->withCount([
                                            'reportesComoTecnico as resueltos' => function ($query) {
                                                $query->where('estado', 'resuelto');
                                            }
                                        ])
                                        ->orderBy('resueltos', 'desc')
                                        ->limit(5)
                                        ->get();
                                @endphp

                                <div class="list-group list-group-flush">
                                    @foreach($tecnicos as $tecnico)
                                        <div class="list-group-item border-0 px-0 py-2">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar bg-{{ $loop->first ? 'warning' : 'light' }} 
                                                                        {{ $loop->first ? 'text-dark' : 'text-muted' }} 
                                                                        rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width: 40px; height: 40px;">
                                                        {{ substr($tecnico->name, 0, 1) }}
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-0">{{ $tecnico->name }}</h6>
                                                    <small class="text-muted">
                                                        {{ $tecnico->reportesComoTecnico()->whereIn('estado', ['asignado', 'en_proceso'])->count() }}
                                                        activos
                                                    </small>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <span class="badge bg-success">
                                                        {{ $tecnico->resueltos }} resueltos
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3">
                                    <a href="{{ route('users.index') }}?rol=tecnico"
                                        class="btn btn-sm btn-outline-primary w-100">
                                        <i class="fas fa-list me-1"></i> Ver todos los técnicos
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Acciones administrativas -->
                        <div class="card mt-4">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-cogs me-2"></i>
                                    Acciones Rápidas
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('users.create') }}" class="btn btn-outline-success text-start">
                                        <i class="fas fa-user-plus me-2"></i> Nuevo Usuario
                                    </a>
                                    <a href="{{ route('roles.create') }}" class="btn btn-outline-warning text-start">
                                        <i class="fas fa-user-shield me-2"></i> Nuevo Rol
                                    </a>
                                    <button class="btn btn-outline-info text-start" data-bs-toggle="modal"
                                        data-bs-target="#modalReportes">
                                        <i class="fas fa-file-export me-2"></i> Generar Reporte
                                    </button>
                                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-primary text-start">
                                        <i class="fas fa-search me-2"></i> Buscar Reportes
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Generar Reporte -->
    <div class="modal fade" id="modalReportes" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Generar Reporte</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Reporte</label>
                            <select class="form-select">
                                <option selected>Reporte de actividad</option>
                                <option>Reporte de técnicos</option>
                                <option>Reporte de clientes</option>
                                <option>Reporte financiero</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Periodo</label>
                            <select class="form-select">
                                <option selected>Últimos 7 días</option>
                                <option>Este mes</option>
                                <option>Mes anterior</option>
                                <option>Personalizado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Formato</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="formato" id="pdf" checked>
                                <label class="form-check-label" for="pdf">PDF</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="formato" id="excel">
                                <label class="form-check-label" for="excel">Excel</label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i> Generar
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .timeline {
                position: relative;
                padding-left: 30px;
            }

            .timeline-item {
                position: relative;
            }

            .timeline-marker {
                position: absolute;
                left: -30px;
                top: 0;
                width: 12px;
                height: 12px;
                border-radius: 50%;
                background: #6c757d;
            }

            .timeline-content {
                padding-bottom: 10px;
            }

            .avatar {
                font-weight: bold;
                font-size: 16px;
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

                // Asignación rápida
                document.querySelectorAll('.dropdown-item[type="submit"]').forEach(button => {
                    button.addEventListener('click', function (e) {
                        if (!confirm('¿Asignar este reporte al técnico seleccionado?')) {
                            e.preventDefault();
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection