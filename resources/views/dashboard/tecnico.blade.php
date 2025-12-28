@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">🔧 Dashboard - Técnico</h1>
                    <p class="text-muted mb-0">Bienvenido, {{ auth()->user()->name }}</p>
                    <small class="text-muted">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        {{ auth()->user()->direccion ?? 'Sin zona asignada' }}
                    </small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reportes.index') }}" class="btn btn-primary">
                        <i class="fas fa-tasks me-1"></i> Mis Reportes
                    </a>
                    <button class="btn btn-outline-secondary" id="btnDisponibilidad"
                            data-bs-toggle="modal" data-bs-target="#modalDisponibilidad">
                        <i class="fas fa-toggle-on me-1"></i> Disponible
                    </button>
                </div>
            </div>

            <!-- Tarjetas de estadísticas -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle mb-2 text-muted">Asignados</h6>
                                    <h2 class="mb-0">
                                        {{ auth()->user()->reportesComoTecnico()->where('estado', 'asignado')->count() }}
                                    </h2>
                                </div>
                                <div class="display-4 text-danger">
                                    <i class="fas fa-inbox"></i>
                                </div>
                            </div>
                            <p class="card-text mt-2">
                                <small>Por atender</small>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle mb-2 text-muted">En Proceso</h6>
                                    <h2 class="mb-0">
                                        {{ auth()->user()->reportesComoTecnico()->where('estado', 'en_proceso')->count() }}
                                    </h2>
                                </div>
                                <div class="display-4 text-primary">
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
                    <div class="card border-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle mb-2 text-muted">Resueltos (Hoy)</h6>
                                    <h2 class="mb-0">
                                        {{ auth()->user()->reportesComoTecnico()
                                            ->where('estado', 'resuelto')
                                            ->whereDate('updated_at', today())
                                            ->count() }}
                                    </h2>
                                </div>
                                <div class="display-4 text-success">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <p class="card-text mt-2">
                                <small>Completados hoy</small>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-subtitle mb-2 text-muted">Total Resueltos</h6>
                                    <h2 class="mb-0">
                                        {{ auth()->user()->reportesComoTecnico()->where('estado', 'resuelto')->count() }}
                                    </h2>
                                </div>
                                <div class="display-4 text-info">
                                    <i class="fas fa-trophy"></i>
                                </div>
                            </div>
                            <p class="card-text mt-2">
                                <small>Historial completo</small>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dos columnas: Reportes urgentes y Estadísticas -->
            <div class="row">
                <!-- Columna izquierda: Reportes urgentes -->
                <div class="col-lg-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                                Reportes Urgentes Pendientes
                            </h5>
                            <span class="badge bg-danger">
                                Prioridad Alta
                            </span>
                        </div>
                        <div class="card-body p-0">
                            @php
                                $reportesUrgentes = auth()->user()->reportesComoTecnico()
                                    ->with('cliente')
                                    ->where('estado', 'asignado')
                                    ->where('prioridad', 'alta')
                                    ->orderBy('created_at')
                                    ->limit(5)
                                    ->get();
                            @endphp

                            @if($reportesUrgentes->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Código</th>
                                            <th>Cliente</th>
                                            <th>Dirección</th>
                                            <th>Asignado hace</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($reportesUrgentes as $reporte)
                                        <tr class="table-danger">
                                            <td>
                                                <strong>{{ $reporte->codigo }}</strong>
                                            </td>
                                            <td>
                                                {{ $reporte->cliente->name }}
                                                <br>
                                                <small class="text-muted">{{ $reporte->cliente->telefono }}</small>
                                            </td>
                                            <td class="text-truncate" style="max-width: 150px;">
                                                <small>{{ $reporte->direccion }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-danger">
                                                    {{ $reporte->created_at->diffForHumans() }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('reportes.show', $reporte->id) }}" 
                                                       class="btn btn-outline-danger">
                                                        <i class="fas fa-play-circle"></i> Atender
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-5">
                                <div class="display-4 text-success mb-3">
                                    <i class="fas fa-check-double"></i>
                                </div>
                                <h5 class="text-success">¡Sin emergencias pendientes!</h5>
                                <p class="text-muted">No tienes reportes de prioridad alta asignados</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Columna derecha: Estadísticas y acciones -->
                <div class="col-lg-4 mb-4">
                    <!-- Acciones rápidas -->
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('reportes.index') }}?estado=asignado" 
                                   class="btn btn-outline-primary text-start">
                                    <i class="fas fa-inbox me-2"></i> Ver Asignados
                                    <span class="badge bg-primary float-end">
                                        {{ auth()->user()->reportesComoTecnico()->where('estado', 'asignado')->count() }}
                                    </span>
                                </a>
                                <a href="{{ route('reportes.index') }}?estado=en_proceso" 
                                   class="btn btn-outline-warning text-start">
                                    <i class="fas fa-tools me-2"></i> En Proceso
                                    <span class="badge bg-warning float-end">
                                        {{ auth()->user()->reportesComoTecnico()->where('estado', 'en_proceso')->count() }}
                                    </span>
                                </a>
                                <a href="{{ route('reportes.index') }}?estado=resuelto" 
                                   class="btn btn-outline-success text-start">
                                    <i class="fas fa-check-circle me-2"></i> Resueltos
                                    <span class="badge bg-success float-end">
                                        {{ auth()->user()->reportesComoTecnico()->where('estado', 'resuelto')->count() }}
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Estadísticas de eficiencia -->
                    <div class="card">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-chart-line me-2"></i>Mi Eficiencia</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small>Tasa de resolución</small>
                                    <small>
                                        @php
                                            $totalAsignados = auth()->user()->reportesComoTecnico()->whereIn('estado', ['asignado', 'en_proceso'])->count();
                                            $totalResueltos = auth()->user()->reportesComoTecnico()->where('estado', 'resuelto')->count();
                                            $totalGeneral = $totalAsignados + $totalResueltos;
                                            $porcentaje = $totalGeneral > 0 ? round(($totalResueltos / $totalGeneral) * 100) : 0;
                                        @endphp
                                        {{ $porcentaje }}%
                                    </small>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar bg-success" 
                                         style="width: {{ $porcentaje }}%"></div>
                                </div>
                            </div>

                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Tiempo promedio
                                    <span class="badge bg-info rounded-pill">
                                        3.2 horas
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Clientes satisfechos
                                    <span class="badge bg-success rounded-pill">
                                        94%
                                    </span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Reportes/mes
                                    <span class="badge bg-primary rounded-pill">
                                        {{ auth()->user()->reportesComoTecnico()
                                            ->whereMonth('created_at', now()->month)
                                            ->count() }}
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reportes recientes -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Reportes Recientes
                    </h5>
                </div>
                <div class="card-body p-0">
                    @php
                        $reportesRecientes = auth()->user()->reportesComoTecnico()
                            ->with(['cliente', 'evidencias'])
                            ->latest()
                            ->limit(8)
                            ->get();
                    @endphp

                    @if($reportesRecientes->count() > 0)
                    <div class="row g-0">
                        @foreach($reportesRecientes as $reporte)
                        <div class="col-md-6 col-lg-3 p-3 border-end border-bottom">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 me-3">
                                    @php
                                        $iconos = [
                                            'pendiente' => 'fas fa-clock text-warning',
                                            'asignado' => 'fas fa-user-check text-info',
                                            'en_proceso' => 'fas fa-tools text-primary',
                                            'resuelto' => 'fas fa-check-circle text-success',
                                            'cancelado' => 'fas fa-times-circle text-danger'
                                        ];
                                    @endphp
                                    <i class="{{ $iconos[$reporte->estado] }} fa-2x"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        <a href="{{ route('reportes.show', $reporte->id) }}" 
                                           class="text-decoration-none">
                                            {{ $reporte->codigo }}
                                        </a>
                                    </h6>
                                    <p class="small text-muted mb-1">
                                        {{ Str::limit($reporte->descripcion, 50) }}
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            {{ $reporte->created_at->format('d/m') }}
                                        </small>
                                        <span class="badge bg-{{ $reporte->prioridad == 'alta' ? 'danger' : ($reporte->prioridad == 'media' ? 'warning' : 'secondary') }}">
                                            {{ $reporte->prioridad }}
                                        </span>
                                    </div>
                                    @if($reporte->evidencias->count() > 0)
                                    <div class="mt-2">
                                        <small>
                                            <i class="fas fa-camera text-muted"></i>
                                            {{ $reporte->evidencias->count() }} fotos
                                        </small>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="display-4 text-muted mb-3">
                            <i class="fas fa-clipboard"></i>
                        </div>
                        <h5 class="text-muted">No tienes reportes asignados</h5>
                        <p class="text-muted">Espera a que te asignen reportes</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Disponibilidad -->
<div class="modal fade" id="modalDisponibilidad" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Estado de Disponibilidad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">¿Estás disponible para nuevos reportes?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="disponibilidad" id="disponibleSi" checked>
                        <label class="form-check-label" for="disponibleSi">
                            <i class="fas fa-toggle-on text-success me-2"></i> Disponible
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="disponibilidad" id="disponibleNo">
                        <label class="form-check-label" for="disponibleNo">
                            <i class="fas fa-toggle-off text-danger me-2"></i> No Disponible
                        </label>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Motivo (opcional)</label>
                    <textarea class="form-control" rows="3" placeholder="Ej: En mantenimiento de equipo..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Guardar</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cambiar texto del botón de disponibilidad
        const btnDisponibilidad = document.getElementById('btnDisponibilidad');
        const radios = document.querySelectorAll('input[name="disponibilidad"]');
        
        radios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.id === 'disponibleSi') {
                    btnDisponibilidad.innerHTML = '<i class="fas fa-toggle-on me-1"></i> Disponible';
                    btnDisponibilidad.classList.remove('btn-outline-danger');
                    btnDisponibilidad.classList.add('btn-outline-secondary');
                } else {
                    btnDisponibilidad.innerHTML = '<i class="fas fa-toggle-off me-1"></i> No Disponible';
                    btnDisponibilidad.classList.remove('btn-outline-secondary');
                    btnDisponibilidad.classList.add('btn-outline-danger');
                }
            });
        });

        // Tooltips
        var tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltips.map(function(tooltip) {
            return new bootstrap.Tooltip(tooltip);
        });
    });
</script>
@endpush
@endsection