@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Encabezado con navegación -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('reportes.index') }}">
                                        <i class="fas fa-arrow-left me-1"></i> Volver a reportes
                                    </a>
                                </li>
                            </ol>
                        </nav>
                        <h1 class="h3 mb-0">
                            <i class="fas fa-file-alt me-2"></i> Reporte {{ $reporte->codigo }}
                            <span
                                class="badge bg-{{ $reporte->prioridad == 'alta' ? 'danger' : ($reporte->prioridad == 'media' ? 'warning' : 'secondary') }}">
                                {{ strtoupper($reporte->prioridad) }}
                            </span>
                        </h1>
                    </div>

                    <div class="d-flex gap-2">
                        <!-- Botones según permisos -->
                        @if(auth()->user()->es_administrador)
                            <a href="{{ route('reportes.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list me-1"></i> Todos los reportes
                            </a>
                        @endif

                        <!-- Botón imprimir -->
                        <button class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Imprimir
                        </button>
                    </div>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Grid principal: 2 columnas -->
                <div class="row">
                    <!-- Columna izquierda: Información principal -->
                    <div class="col-lg-8">
                        <!-- Card de información básica -->
                        <div class="card mb-4">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-info-circle me-2"></i> Información del Reporte
                                </h5>
                                <div>
                                    @php
                                        $estados = [
                                            'pendiente' => ['warning', '⏳ Pendiente'],
                                            'asignado' => ['info', '👷 Asignado'],
                                            'en_proceso' => ['primary', '🔧 En Proceso'],
                                            'resuelto' => ['success', '✅ Resuelto'],
                                            'cancelado' => ['danger', '❌ Cancelado']
                                        ];
                                        [$color, $texto] = $estados[$reporte->estado] ?? ['secondary', ''];
                                    @endphp
                                    <span class="badge bg-{{ $color }} px-3 py-2">
                                        {{ $texto }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small mb-1">Código</label>
                                        <div class="fw-bold">{{ $reporte->codigo }}</div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small mb-1">Fecha de creación</label>
                                        <div class="fw-bold">
                                            {{ $reporte->created_at->format('d/m/Y H:i') }}
                                            <small class="text-muted">({{ $reporte->tiempo_transcurrido }})</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small mb-1">Prioridad</label>
                                        <div>
                                            <span
                                                class="badge bg-{{ $reporte->prioridad == 'alta' ? 'danger' : ($reporte->prioridad == 'media' ? 'warning' : 'secondary') }} px-3">
                                                {{ ucfirst($reporte->prioridad) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted small mb-1">Estado</label>
                                        <div>
                                            <span class="badge bg-{{ $color }} px-3">
                                                {{ ucfirst($reporte->estado) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label text-muted small mb-1">Descripción</label>
                                        <div class="border rounded p-3 bg-light">
                                            {{ $reporte->descripcion }}
                                        </div>
                                    </div>

                                    <div class="col-12 mb-3">
                                        <label class="form-label text-muted small mb-1">Dirección</label>
                                        <div class="fw-bold">
                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                            {{ $reporte->direccion }}
                                        </div>
                                    </div>

                                    @if($reporte->solucion)
                                        <div class="col-12 mb-3">
                                            <label class="form-label text-muted small mb-1">Solución aplicada</label>
                                            <div class="border rounded p-3 bg-success bg-opacity-10 border-success">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                {{ $reporte->solucion }}
                                            </div>
                                        </div>
                                    @endif

                                    @if($reporte->fecha_cierre)
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-muted small mb-1">Fecha de resolución</label>
                                            <div class="fw-bold">
                                                {{ $reporte->fecha_cierre->format('d/m/Y H:i') }}
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Card de evidencias -->
                        <div class="card mb-4">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-images me-2"></i> Evidencias Fotográficas
                                    <span class="badge bg-secondary">{{ $reporte->evidencias->count() }}</span>
                                </h5>
                                <!-- Botón para subir más fotos (solo técnico asignado) -->
                                @if(auth()->user()->es_tecnico && $reporte->tecnico_asignado_id == auth()->id() && $reporte->estado != 'resuelto')
                                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                        data-bs-target="#modalSubirFotos">
                                        <i class="fas fa-upload me-1"></i> Subir más fotos
                                    </button>
                                @endif
                            </div>
                            <div class="card-body">
                                @if($reporte->evidencias->count() > 0)
                                    <div class="row g-3">
                                        @foreach($reporte->evidencias as $evidencia)
                                            <div class="col-md-6 col-lg-4">
                                                <div class="card border-0 shadow-sm">
                                                    <div class="card-img-top position-relative">
                                                        @php
                                                            // Intentar obtener la URL de diferentes formas
                                                            $urlImagen = null;

                                                            // Método 1: Usando Storage
                                                            if (Storage::exists($evidencia->imagen_path)) {
                                                                $urlImagen = Storage::url($evidencia->imagen_path);
                                                            }
                                                            // Método 2: Usando asset (fallback)
                                                            elseif (file_exists(public_path('storage/' . $evidencia->imagen_path))) {
                                                                $urlImagen = asset('storage/' . $evidencia->imagen_path);
                                                            }
                                                            // Método 3: Si es URL completa
                                                            elseif (filter_var($evidencia->imagen_path, FILTER_VALIDATE_URL)) {
                                                                $urlImagen = $evidencia->imagen_path;
                                                            }
                                                        @endphp

                                                        @if($urlImagen)
                                                            <img src="{{ $urlImagen }}" class="img-fluid rounded-top"
                                                                alt="Evidencia {{ $loop->iteration }}"
                                                                style="height: 200px; width: 100%; object-fit: cover;"
                                                                data-bs-toggle="modal" data-bs-target="#modalImagen{{ $evidencia->id }}"
                                                                role="button">
                                                        @else
                                                            <!-- Imagen por defecto si no se encuentra -->
                                                            <div class="bg-light d-flex align-items-center justify-content-center rounded-top"
                                                                style="height: 200px; width: 100%;">
                                                                <div class="text-center text-muted">
                                                                    <i class="fas fa-image fa-3x mb-2"></i>
                                                                    <p class="small mb-0">Imagen no disponible</p>
                                                                    <small class="x-small">Ruta: {{ $evidencia->imagen_path }}</small>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        <!-- Badge del tipo de evidencia -->
                                                        <span
                                                            class="position-absolute top-0 start-0 m-2 badge bg-{{ $evidencia->tipo == 'antes' ? 'warning' : ($evidencia->tipo == 'durante' ? 'primary' : 'success') }}">
                                                            {{ ucfirst($evidencia->tipo) }}
                                                        </span>
                                                    </div>
                                                    <div class="card-body p-3">
                                                        @if($evidencia->descripcion)
                                                            <p class="card-text small mb-2">{{ $evidencia->descripcion }}</p>
                                                        @endif
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small class="text-muted">
                                                                {{ $evidencia->created_at->format('d/m/Y H:i') }}
                                                            </small>
                                                            @if($urlImagen)
                                                                <a href="{{ $urlImagen }}" target="_blank"
                                                                    class="btn btn-sm btn-outline-secondary">
                                                                    <i class="fas fa-download"></i>
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-center py-5">
                                        <div class="display-4 text-muted mb-3">
                                            <i class="fas fa-image"></i>
                                        </div>
                                        <h5 class="text-muted">No hay evidencias fotográficas</h5>
                                        <p class="text-muted">No se han subido fotos para este reporte</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Columna derecha: Información de contacto y acciones -->
                    <div class="col-lg-4">
                        <!-- Card de información del cliente -->
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-user me-2"></i> Información del Cliente
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                            style="width: 50px; height: 50px; font-size: 20px;">
                                            {{ substr($reporte->cliente->name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ $reporte->cliente->name }}</h6>
                                        <p class="text-muted small mb-1">
                                            <i class="fas fa-envelope me-1"></i> {{ $reporte->cliente->email }}
                                        </p>
                                        <p class="text-muted small mb-0">
                                            <i class="fas fa-phone me-1"></i>
                                            {{ $reporte->cliente->telefono ?? 'No registrado' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="border-top pt-3">
                                    <h6 class="small text-muted mb-2">Dirección registrada</h6>
                                    <p class="mb-0">
                                        <i class="fas fa-home me-1"></i>
                                        {{ $reporte->cliente->direccion ?? 'No registrada' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Card de información del técnico (si está asignado) -->
                        @if($reporte->tecnico)
                            <div class="card mb-4 border-primary">
                                <div class="card-header bg-primary bg-opacity-10 border-primary">
                                    <h6 class="mb-0 text-primary">
                                        <i class="fas fa-user-hard-hat me-2"></i> Técnico Asignado
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="flex-shrink-0">
                                            <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px; font-size: 20px;">
                                                {{ substr($reporte->tecnico->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1">{{ $reporte->tecnico->name }}</h6>
                                            <p class="text-muted small mb-1">
                                                <i class="fas fa-envelope me-1"></i> {{ $reporte->tecnico->email }}
                                            </p>
                                            <p class="text-muted small mb-0">
                                                <i class="fas fa-phone me-1"></i>
                                                {{ $reporte->tecnico->telefono ?? 'No registrado' }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Estadísticas del técnico -->
                                    <div class="border-top pt-3">
                                        <h6 class="small text-muted mb-2">Estadísticas del técnico</h6>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="text-center border rounded p-2">
                                                    <div class="h5 mb-0 text-primary">
                                                        {{ $reporte->tecnico->reportesComoTecnico()->where('estado', 'resuelto')->count() }}
                                                    </div>
                                                    <small class="text-muted">Resueltos</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center border rounded p-2">
                                                    <div class="h5 mb-0 text-warning">
                                                        {{ $reporte->tecnico->reportesComoTecnico()->whereIn('estado', ['asignado', 'en_proceso'])->count() }}
                                                    </div>
                                                    <small class="text-muted">Activos</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Card de acciones según rol -->
                        <div class="card mb-4">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-bolt me-2"></i> Acciones Disponibles
                                </h6>
                            </div>
                            <div class="card-body">
                                <!-- Acciones para CLIENTE -->
                                @if(auth()->user()->es_cliente && $reporte->user_id == auth()->id())
                                    @if($reporte->estado == 'pendiente')
                                        <div class="d-grid gap-2 mb-3">
                                            <a href="{{ route('reportes.edit', $reporte->id) }}" class="btn btn-warning">
                                                <i class="fas fa-edit me-2"></i> Editar Reporte
                                            </a>

                                            <button class="btn btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#modalCancelar">
                                                <i class="fas fa-times me-2"></i> Cancelar Reporte
                                            </button>
                                        </div>
                                    @endif

                                    @if($reporte->estado == 'resuelto')
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <strong>¡Reporte resuelto!</strong>
                                            <div class="small mt-1">
                                                Si el problema persiste, puedes crear un nuevo reporte.
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <!-- Acciones para TÉCNICO -->
                                @if(auth()->user()->es_tecnico && $reporte->tecnico_asignado_id == auth()->id())
                                    @if($reporte->estado == 'asignado')
                                        <div class="d-grid gap-2 mb-3">
                                            <form action="{{ route('reportes.cambiar-estado', $reporte->id) }}" method="POST"
                                                class="d-grid">
                                                @csrf
                                                <input type="hidden" name="estado" value="en_proceso">
                                                <button type="submit" class="btn btn-primary mb-2"
                                                    onclick="return confirm('¿Comenzar a trabajar en este reporte?')">
                                                    <i class="fas fa-play-circle me-2"></i> Comenzar Trabajo
                                                </button>
                                            </form>
                                        </div>
                                    @endif

                                    @if(auth()->user()->es_tecnico && $reporte->tecnico_asignado_id == auth()->id() && $reporte->estado == 'en_proceso')
                                        <div class="mb-3">
                                            <h6 class="small text-muted mb-2">Marcar como Resuelto</h6>
                                            <form action="{{ route('reportes.update', $reporte->id) }}" method="POST"
                                                id="formResolver" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')

                                                <div class="mb-3">
                                                    <label class="form-label small">Solución aplicada *</label>
                                                    <textarea name="solucion" class="form-control" rows="3"
                                                        placeholder="Describe la solución aplicada..." required></textarea>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small">Subir fotos del trabajo terminado
                                                        (opcional)</label>
                                                    <input type="file" name="evidencias_tecnico[]" class="form-control" multiple
                                                        accept="image/*">
                                                    <div class="form-text">
                                                        Puedes subir imágenes del resultado final (máx. 5MB por imagen)
                                                    </div>
                                                </div>

                                                <input type="hidden" name="estado" value="resuelto">

                                                <div class="d-grid gap-2">
                                                    <button type="submit" class="btn btn-success"
                                                        onclick="return confirm('¿Marcar este reporte como resuelto?')">
                                                        <i class="fas fa-check-circle me-2"></i> Marcar como Resuelto
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif
                                @endif
                                <!-- Acciones para ADMINISTRADOR -->
                                @if(auth()->user()->es_administrador)
                                    @if($reporte->estado == 'pendiente')
                                        <div class="mb-3">
                                            <h6 class="small text-muted mb-2">Asignar Técnico</h6>
                                            <form action="{{ route('reportes.asignar', $reporte->id) }}" method="POST">
                                                @csrf
                                                <div class="input-group">
                                                    <select name="tecnico_id" class="form-select" required>
                                                        <option value="">Seleccionar técnico</option>
                                                        @foreach($tecnicosDisponibles as $tecnico)
                                                            <option value="{{ $tecnico->id }}">
                                                                {{ $tecnico->name }}
                                                                ({{ $tecnico->reportesComoTecnico()->whereIn('estado', ['asignado', 'en_proceso'])->count() }}
                                                                activos)
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <button type="submit" class="btn btn-primary"
                                                        onclick="return confirm('¿Asignar este reporte al técnico seleccionado?')">
                                                        Asignar
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    @endif

                                    <div class="d-grid gap-2">
                                        @if($reporte->estado == 'pendiente' || $reporte->estado == 'cancelado')
                                            <button class="btn btn-outline-danger" data-bs-toggle="modal"
                                                data-bs-target="#modalEliminar">
                                                <i class="fas fa-trash me-2"></i> Eliminar Reporte
                                            </button>
                                        @endif

                                        <a href="{{ route('reportes.edit', $reporte->id) }}" class="btn btn-outline-warning">
                                            <i class="fas fa-edit me-2"></i> Editar como Admin
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Card de historial de estados -->
                        <div class="card">
                            <div class="card-header bg-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-history me-2"></i> Historial de Estados
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="timeline">
                                    <div class="timeline-item">
                                        <div class="timeline-marker bg-success"></div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">Creado</h6>
                                            <small class="text-muted">
                                                {{ $reporte->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                    </div>

                                    @if($reporte->tecnico_asignado_id)
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-primary"></div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1">Asignado a técnico</h6>
                                                <small class="text-muted">
                                                    {{ $reporte->updated_at->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                        </div>
                                    @endif

                                    @if($reporte->estado == 'resuelto' && $reporte->fecha_cierre)
                                        <div class="timeline-item">
                                            <div class="timeline-marker bg-success"></div>
                                            <div class="timeline-content">
                                                <h6 class="mb-1">Resuelto</h6>
                                                <small class="text-muted">
                                                    {{ $reporte->fecha_cierre->format('d/m/Y H:i') }}
                                                </small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para cancelar reporte (cliente) -->
    @if(auth()->user()->es_cliente && $reporte->user_id == auth()->id() && $reporte->estado == 'pendiente')
        <div class="modal fade" id="modalCancelar" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cancelar Reporte</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('reportes.update', $reporte->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <p>¿Estás seguro de cancelar este reporte?</p>
                            <div class="mb-3">
                                <label class="form-label">Motivo de cancelación (opcional)</label>
                                <textarea name="solucion" class="form-control" rows="3"
                                    placeholder="Ej: Ya se solucionó por otra vía..."></textarea>
                                <input type="hidden" name="estado" value="cancelado">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No cancelar</button>
                            <button type="submit" class="btn btn-danger">Sí, cancelar reporte</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal para eliminar reporte (admin) -->
    @if(auth()->user()->es_administrador && ($reporte->estado == 'pendiente' || $reporte->estado == 'cancelado'))
        <div class="modal fade" id="modalEliminar" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Eliminar Reporte</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('reportes.destroy', $reporte->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="modal-body">
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>¡Advertencia!</strong> Esta acción no se puede deshacer.
                            </div>
                            <p>¿Estás seguro de eliminar permanentemente el reporte <strong>{{ $reporte->codigo }}</strong>?</p>
                            <p class="text-muted small">
                                Se eliminarán todas las evidencias fotográficas asociadas.
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger">Sí, eliminar permanentemente</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal para subir fotos (técnico) -->
    @if(auth()->user()->es_tecnico && $reporte->tecnico_asignado_id == auth()->id() && $reporte->estado != 'resuelto')
        <div class="modal fade" id="modalSubirFotos" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Subir Fotos Adicionales</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('reportes.update', $reporte->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Seleccionar fotos</label>
                                <input type="file" name="evidencias_tecnico[]" class="form-control" multiple accept="image/*">
                                <div class="form-text">
                                    Puedes subir fotos del proceso de reparación o resultado final.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tipo de evidencia</label>
                                <select name="tipo_fotos" class="form-select">
                                    <option value="durante">Durante la reparación</option>
                                    <option value="despues">Resultado final</option>
                                </select>
                            </div>
                            <input type="hidden" name="estado" value="{{ $reporte->estado }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Subir Fotos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @push('styles')
        <style>
            .avatar {
                font-weight: bold;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .timeline {
                position: relative;
                padding-left: 30px;
            }

            .timeline-item {
                position: relative;
                margin-bottom: 20px;
            }

            .timeline-marker {
                position: absolute;
                left: -30px;
                top: 5px;
                width: 12px;
                height: 12px;
                border-radius: 50%;
            }

            .timeline-content {
                padding-left: 0;
            }

            .breadcrumb {
                background: transparent;
                padding: 0;
                margin-bottom: 0.5rem;
            }

            @media print {

                .btn,
                .modal,
                .breadcrumb,
                .card-header .badge {
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

                // Galería de imágenes con navegación
                const modales = document.querySelectorAll('[id^="modalImagen"]');
                if (modales.length > 0) {
                    // Aquí podrías agregar navegación entre modales si quisieras
                }

                // Validación del formulario de resolución
                const formResolver = document.getElementById('formResolver');
                if (formResolver) {
                    formResolver.addEventListener('submit', function (e) {
                        const solucion = this.querySelector('textarea[name="solucion"]').value;
                        if (!solucion.trim()) {
                            e.preventDefault();
                            alert('Por favor, describe la solución aplicada.');
                            return;
                        }

                        if (!confirm('¿Estás seguro de marcar este reporte como resuelto?')) {
                            e.preventDefault();
                        }
                    });
                }

                // Auto-focus en textareas de modales
                const modals = document.querySelectorAll('.modal');
                modals.forEach(modal => {
                    modal.addEventListener('shown.bs.modal', function () {
                        const textarea = this.querySelector('textarea');
                        if (textarea) {
                            textarea.focus();
                        }
                    });
                });

                // Mostrar/ocultar detalles adicionales
                const toggleDetails = document.getElementById('toggleDetails');
                if (toggleDetails) {
                    toggleDetails.addEventListener('click', function () {
                        const details = document.getElementById('additionalDetails');
                        details.classList.toggle('d-none');
                        this.innerHTML = details.classList.contains('d-none')
                            ? '<i class="fas fa-chevron-down me-1"></i> Mostrar más detalles'
                            : '<i class="fas fa-chevron-up me-1"></i> Ocultar detalles';
                    });
                }
            });
        </script>
    @endpush
@endsection