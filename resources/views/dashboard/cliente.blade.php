@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0">📊 Dashboard - Cliente</h1>
                        <p class="text-muted mb-0">Bienvenido, {{ auth()->user()->name }}</p>
                    </div>
                    <div class="d-flex gap-2">
                        @if(auth()->user()->puede_crear_reporte)
                            <a href="{{ route('reportes.create') }}" class="btn btn-success">
                                <i class="fas fa-plus-circle me-1"></i> Nuevo Reporte
                            </a>
                        @else
                            <button class="btn btn-secondary" disabled data-bs-toggle="tooltip"
                                title="Límite de 3 reportes activos alcanzado">
                                <i class="fas fa-exclamation-triangle me-1"></i> Límite Alcanzado
                            </button>
                        @endif
                        <a href="{{ route('reportes.index') }}" class="btn btn-primary">
                            <i class="fas fa-list me-1"></i> Ver Todos
                        </a>
                    </div>
                </div>

                <!-- Tarjetas de estadísticas -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card border-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2 text-muted">Reportes Activos</h6>
                                        <h2 class="mb-0">{{ auth()->user()->reportes_activos_count }}</h2>
                                    </div>
                                    <div class="display-4 text-primary">
                                        <i class="fas fa-clipboard-list"></i>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <div class="progress" style="height: 8px;">
                                        @php
                                            $porcentaje = min(100, (auth()->user()->reportes_activos_count / 3) * 100);
                                            $color = $porcentaje >= 100 ? 'bg-danger' : ($porcentaje >= 66 ? 'bg-warning' : 'bg-success');
                                        @endphp
                                        <div class="progress-bar {{ $color }}" style="width: {{ $porcentaje }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2 text-muted">Reportes Resueltos</h6>
                                        <h2 class="mb-0">
                                            {{ auth()->user()->reportesComoCliente()->where('estado', 'resuelto')->count() }}
                                        </h2>
                                    </div>
                                    <div class="display-4 text-success">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                </div>
                                <p class="card-text mt-2">
                                    <small>Problemas solucionados</small>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2 text-muted">En Proceso</h6>
                                        <h2 class="mb-0">
                                            {{ auth()->user()->reportesComoCliente()->where('estado', 'en_proceso')->count() }}
                                        </h2>
                                    </div>
                                    <div class="display-4 text-info">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                </div>
                                <p class="card-text mt-2">
                                    <small>En reparación</small>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card border-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="card-subtitle mb-2 text-muted">Pendientes</h6>
                                        <h2 class="mb-0">
                                            {{ auth()->user()->reportesComoCliente()->where('estado', 'pendiente')->count() }}/3
                                        </h2>
                                    </div>
                                    <div class="display-4 text-warning">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                </div>
                                <p class="card-text mt-2">
                                    <small>Esperando asignación</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Últimos reportes -->
                <div class="card">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Últimos Reportes
                        </h5>
                        <a href="{{ route('reportes.index') }}" class="btn btn-sm btn-outline-primary">
                            Ver todos <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $ultimosReportes = auth()->user()->reportesComoCliente()
                                ->with('tecnico')
                                ->latest()
                                ->limit(5)
                                ->get();
                        @endphp

                        @if($ultimosReportes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Código</th>
                                            <th>Descripción</th>
                                            <th>Estado</th>
                                            <th>Prioridad</th>
                                            <th>Técnico</th>
                                            <th>Fecha</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($ultimosReportes as $reporte)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-dark">{{ $reporte->codigo }}</span>
                                                </td>
                                                <td class="text-truncate" style="max-width: 200px;">
                                                    {{ $reporte->descripcion }}
                                                </td>
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
                                                    @if($reporte->tecnico)
                                                        {{ $reporte->tecnico->name }}
                                                    @else
                                                        <span class="text-muted">Por asignar</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small>{{ $reporte->created_at->format('d/m/Y') }}</small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('reportes.show', $reporte->id) }}"
                                                            class="btn btn-outline-info" title="Ver">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($reporte->estado == 'pendiente')
                                                            <a href="{{ route('reportes.edit', $reporte->id) }}"
                                                                class="btn btn-outline-warning" title="Editar">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="display-4 text-muted mb-3">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <h5 class="text-muted">No tienes reportes aún</h5>
                                <p class="text-muted mb-4">Crea tu primer reporte de falla eléctrica</p>
                                @if(auth()->user()->puede_crear_reporte)
                                    <a href="{{ route('reportes.create') }}" class="btn btn-lg btn-success">
                                        <i class="fas fa-plus-circle me-2"></i> Crear Primer Reporte
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Acciones rápidas -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <a href="{{ route('reportes.create') }}" class="btn btn-outline-primary w-100 text-start py-3 
                                                  {{ !auth()->user()->puede_crear_reporte ? 'disabled' : '' }}">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="fas fa-plus-circle fa-2x"></i>
                                                </div>
                                                <div>
                                                    <strong>Nuevo Reporte</strong>
                                                    <div class="small text-muted">Reportar falla</div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="{{ route('reportes.index') }}"
                                            class="btn btn-outline-success w-100 text-start py-3">
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="fas fa-list fa-2x"></i>
                                                </div>
                                                <div>
                                                    <strong>Mis Reportes</strong>
                                                    <div class="small text-muted">Ver historial</div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Estadísticas</h6>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Total reportes
                                        <span class="badge bg-primary rounded-pill">
                                            {{ auth()->user()->reportesComoCliente()->count() }}
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Tiempo promedio resolución
                                        <span class="badge bg-info rounded-pill">
                                            {{-- Aquí iría cálculo real --}}
                                            2.5 días
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Prioridad más usada
                                        <span class="badge bg-warning rounded-pill">
                                            Media
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Tooltips
            document.addEventListener('DOMContentLoaded', function () {
                var tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltips.map(function (tooltip) {
                    return new bootstrap.Tooltip(tooltip);
                });
            });
        </script>
    @endpush
@endsection