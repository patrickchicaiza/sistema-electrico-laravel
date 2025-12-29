@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <!-- Encabezado -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item">
                                    <a href="{{ route('reportes.index') }}">
                                        <i class="fas fa-arrow-left me-1"></i> Reportes
                                    </a>
                                </li>
                                <li class="breadcrumb-item">
                                    <a href="{{ route('reportes.show', $reporte->id) }}">
                                        {{ $reporte->codigo }}
                                    </a>
                                </li>
                                <li class="breadcrumb-item active">Editar</li>
                            </ol>
                        </nav>
                        <h1 class="h3 mb-0">
                            <i class="fas fa-edit me-2"></i> Editar Reporte {{ $reporte->codigo }}
                        </h1>
                        <p class="text-muted mb-0">
                            Actualiza la información de tu reporte
                        </p>
                    </div>

                    <!-- Estado actual -->
                    <div>
                        @php
                            $color = match ($reporte->estado) {
                                'pendiente' => 'warning',
                                'asignado' => 'info',
                                'en_proceso' => 'primary',
                                'resuelto' => 'success',
                                'cancelado' => 'danger',
                                default => 'secondary'
                            };
                        @endphp
                        <span class="badge bg-{{ $color }} px-3 py-2">
                            {{ ucfirst($reporte->estado) }}
                        </span>
                    </div>
                </div>

                <!-- Validación de estado -->
                @if($reporte->estado != 'pendiente')
                    <div class="alert alert-danger">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h5 class="alert-heading">No se puede editar este reporte</h5>
                                <p class="mb-0">
                                    Solo se pueden editar reportes en estado <strong>"pendiente"</strong>.
                                    Este reporte ya está <strong>{{ $reporte->estado }}</strong>.
                                </p>
                                <div class="mt-2">
                                    <a href="{{ route('reportes.show', $reporte->id) }}" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-eye me-1"></i> Ver reporte
                                    </a>
                                    <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary btn-sm ms-2">
                                        <i class="fas fa-list me-1"></i> Volver a listado
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Formulario de edición -->
                    <div class="card">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-pencil-alt me-2"></i> Editar Información
                            </h5>
                            <small class="text-muted">
                                Última actualización: {{ $reporte->updated_at->diffForHumans() }}
                            </small>
                        </div>

                        <form action="{{ route('reportes.update', $reporte->id) }}" method="POST" enctype="multipart/form-data"
                            id="formEditar">
                            @csrf
                            @method('PUT')

                            <div class="card-body">
                                <!-- Información básica -->
                                <div class="mb-4">
                                    <h6 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-info-circle me-2"></i> Información del Reporte
                                    </h6>

                                    <!-- Descripción -->
                                    <div class="mb-3">
                                        <label for="descripcion" class="form-label required">
                                            <i class="fas fa-align-left me-1"></i> Descripción detallada
                                        </label>
                                        <textarea name="descripcion" id="descripcion"
                                            class="form-control @error('descripcion') is-invalid @enderror" rows="5"
                                            placeholder="Describe con detalle la falla eléctrica..."
                                            required>{{ old('descripcion', $reporte->descripcion) }}</textarea>
                                        <div class="form-text">
                                            Sé específico en la descripción del problema.
                                        </div>
                                        @error('descripcion')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="text-end mt-1">
                                            <small class="text-muted">
                                                <span id="charCount">{{ strlen($reporte->descripcion) }}</span>/1000 caracteres
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Dirección y Prioridad -->
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="direccion" class="form-label required">
                                                    <i class="fas fa-map-marker-alt me-1"></i> Dirección exacta
                                                </label>
                                                <input type="text" name="direccion" id="direccion"
                                                    class="form-control @error('direccion') is-invalid @enderror"
                                                    value="{{ old('direccion', $reporte->direccion) }}"
                                                    placeholder="Ej: Av. Amazonas N23-45 y Shyris, Quito" required>
                                                <div class="form-text">
                                                    Dirección donde ocurre la falla.
                                                </div>
                                                @error('direccion')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="prioridad" class="form-label required">
                                                    <i class="fas fa-exclamation-circle me-1"></i> Prioridad
                                                </label>
                                                <select name="prioridad" id="prioridad"
                                                    class="form-select @error('prioridad') is-invalid @enderror" required>
                                                    <option value="">Seleccionar prioridad</option>
                                                    <option value="alta" {{ old('prioridad', $reporte->prioridad) == 'alta' ? 'selected' : '' }}>
                                                        🔴 Alta (Peligro inminente)
                                                    </option>
                                                    <option value="media" {{ old('prioridad', $reporte->prioridad) == 'media' ? 'selected' : '' }}>
                                                        🟡 Media (Corte de luz general)
                                                    </option>
                                                    <option value="baja" {{ old('prioridad', $reporte->prioridad) == 'baja' ? 'selected' : '' }}>
                                                        ⚪ Baja (Problema menor)
                                                    </option>
                                                </select>
                                                <div class="form-text">
                                                    <small>
                                                        <span class="text-danger">Alta:</span> Emergencia<br>
                                                        <span class="text-warning">Media:</span> Normal<br>
                                                        <span class="text-muted">Baja:</span> Menor
                                                    </small>
                                                </div>
                                                @error('prioridad')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Evidencias actuales -->
                                <div class="mb-4">
                                    <h6 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-images me-2"></i> Evidencias Actuales
                                        <span class="badge bg-secondary">{{ $reporte->evidencias->count() }}</span>
                                    </h6>

                                    @if($reporte->evidencias->count() > 0)
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            Las fotos ya subidas no se pueden eliminar desde aquí.
                                            Contacta a un administrador si necesitas eliminarlas.
                                        </div>

                                        <div class="row g-2 mb-3">
                                            @foreach($reporte->evidencias as $evidencia)
                                                <div class="col-md-3">
                                                    <div class="card border-0 shadow-sm">
                                                        <img src="{{ $evidencia->url_imagen }}" class="card-img-top rounded"
                                                            alt="Evidencia {{ $loop->iteration }}"
                                                            style="height: 100px; object-fit: cover;">
                                                        <div class="card-body p-2">
                                                            <small class="text-muted d-block">
                                                                {{ ucfirst($evidencia->tipo) }}
                                                            </small>
                                                            @if($evidencia->descripcion)
                                                                <small class="text-truncate d-block" style="max-width: 150px;">
                                                                    {{ $evidencia->descripcion }}
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-3 border rounded bg-light">
                                            <i class="fas fa-image fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No hay evidencias fotográficas</p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Agregar nuevas evidencias -->
                                <div class="mb-4">
                                    <h6 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-plus-circle me-2"></i> Agregar Nuevas Evidencias (Opcional)
                                    </h6>

                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-camera me-1"></i> Subir fotos adicionales
                                        </label>

                                        <div class="dropzone border rounded p-4 text-center" id="dropzone"
                                            style="background: #f8f9fa; cursor: pointer;">
                                            <div class="dz-message">
                                                <div class="text-muted mb-3">
                                                    <i class="fas fa-cloud-upload-alt fa-2x"></i>
                                                </div>
                                                <h6>Arrastra y suelta fotos aquí</h6>
                                                <p class="text-muted small">o haz click para seleccionar</p>
                                                <small class="text-muted">Formatos: JPG, PNG, GIF (máx. 5MB cada una)</small>
                                            </div>
                                        </div>

                                        <input type="file" name="evidencias[]" id="evidencias" class="form-control d-none"
                                            multiple accept="image/*">

                                        <div id="preview-container" class="row mt-3 g-2 d-none">
                                            <div class="col-12">
                                                <h6 class="mb-2">Nuevas fotos seleccionadas:</h6>
                                            </div>
                                        </div>

                                        <div class="form-text">
                                            <small>
                                                <i class="fas fa-lightbulb"></i> Puedes agregar hasta 5 fotos adicionales.
                                            </small>
                                        </div>
                                    </div>

                                    <div id="descripciones-container" class="d-none">
                                        <h6 class="mb-3">Descripciones de las nuevas fotos:</h6>
                                    </div>
                                </div>

                                <!-- Confirmación -->
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="confirmacion" name="confirmacion"
                                            required>
                                        <label class="form-check-label" for="confirmacion">
                                            <strong>Confirmo que la información actualizada es correcta</strong>
                                        </label>
                                        <div class="form-text">
                                            Al actualizar este reporte, confirmas que la información es verídica.
                                        </div>
                                        @error('confirmacion')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer bg-white">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <!-- Botón cancelar (modal de confirmación) -->
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal"
                                            data-bs-target="#modalCancelarEdicion">
                                            <i class="fas fa-times me-1"></i> Cancelar Edición
                                        </button>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <!-- Botón ver original -->
                                        <a href="{{ route('reportes.show', $reporte->id) }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-eye me-1"></i> Ver Original
                                        </a>
                                        <!-- Botón guardar -->
                                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                                            <i class="fas fa-save me-1"></i> Guardar Cambios
                                            <span class="spinner-border spinner-border-sm d-none" id="spinner"
                                                role="status"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- Información de ayuda -->
                <div class="card mt-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-question-circle me-2"></i> ¿Necesitas ayuda?
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-clock text-warning fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">Tiempo de respuesta</h6>
                                        <small class="text-muted">Basado en prioridad</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-camera text-primary fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">Fotos claras</h6>
                                        <small class="text-muted">Ayudan a evaluar mejor</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-map-marker-alt text-danger fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-0">Dirección precisa</h6>
                                        <small class="text-muted">Ubicación exacta</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para cancelar edición -->
    <div class="modal fade" id="modalCancelarEdicion" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancelar Edición</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de cancelar la edición?</p>
                    <p class="text-muted small mb-0">
                        Los cambios no guardados se perderán.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Continuar Editando
                    </button>
                    <a href="{{ route('reportes.show', $reporte->id) }}" class="btn btn-danger">
                        Sí, Cancelar
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .required::after {
                content: " *";
                color: #dc3545;
            }

            .dropzone {
                border: 2px dashed #dee2e6;
                transition: all 0.3s;
            }

            .dropzone:hover {
                border-color: #0d6efd;
                background: #e7f1ff;
            }

            .image-preview {
                position: relative;
                border: 1px solid #dee2e6;
                border-radius: 0.375rem;
                overflow: hidden;
            }

            .image-preview img {
                width: 100%;
                height: 100px;
                object-fit: cover;
            }

            .image-preview .remove-btn {
                position: absolute;
                top: 5px;
                right: 5px;
                background: rgba(220, 53, 69, 0.9);
                color: white;
                border: none;
                border-radius: 50%;
                width: 24px;
                height: 24px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            }

            .breadcrumb {
                background: transparent;
                padding: 0;
                margin-bottom: 0.5rem;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const MAX_FILES = 5;
                const MAX_SIZE = 5 * 1024 * 1024; // 5MB
                let selectedFiles = [];

                // Elementos DOM
                const dropzone = document.getElementById('dropzone');
                const fileInput = document.getElementById('evidencias');
                const previewContainer = document.getElementById('preview-container');
                const descripcionesContainer = document.getElementById('descripciones-container');
                const form = document.getElementById('formEditar');
                const btnSubmit = document.getElementById('btnSubmit');
                const spinner = document.getElementById('spinner');
                const textarea = document.getElementById('descripcion');
                const charCount = document.getElementById('charCount');

                // 1. Contador de caracteres
                textarea.addEventListener('input', function () {
                    const length = this.value.length;
                    charCount.textContent = length;

                    if (length > 1000) {
                        this.value = this.value.substring(0, 1000);
                        charCount.textContent = 1000;
                        showAlert('warning', 'Límite de 1000 caracteres alcanzado');
                    }
                });

                // 2. Dropzone functionality (similar a create.blade.php pero simplificado)
                dropzone.addEventListener('click', function () {
                    fileInput.click();
                });

                fileInput.addEventListener('change', function () {
                    handleFiles(this.files);
                    this.value = '';
                });

                function handleFiles(files) {
                    const validFiles = Array.from(files).filter(file => {
                        if (!file.type.startsWith('image/')) {
                            showAlert('error', `"${file.name}" no es una imagen válida`);
                            return false;
                        }

                        if (file.size > MAX_SIZE) {
                            showAlert('error', `"${file.name}" supera el límite de 5MB`);
                            return false;
                        }

                        if (selectedFiles.length >= MAX_FILES) {
                            showAlert('error', `Máximo ${MAX_FILES} imágenes adicionales`);
                            return false;
                        }

                        return true;
                    });

                    validFiles.forEach(file => {
                        selectedFiles.push(file);
                        addImagePreview(file);
                        addDescriptionField(file.name);
                    });

                    updateUI();
                }

                function addImagePreview(file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-4 col-lg-3';
                        col.innerHTML = `
                                            <div class="image-preview">
                                                <img src="${e.target.result}" alt="${file.name}">
                                                <button type="button" class="remove-btn" data-name="${file.name}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        `;
                        previewContainer.appendChild(col);

                        col.querySelector('.remove-btn').addEventListener('click', function () {
                            removeFile(file.name);
                        });
                    };
                    reader.readAsDataURL(file);
                }

                function addDescriptionField(fileName) {
                    const descDiv = document.createElement('div');
                    descDiv.className = 'mb-2';
                    descDiv.innerHTML = `
                                        <label class="form-label small">Descripción para: <strong>${fileName}</strong></label>
                                        <input type="text" 
                                               name="descripcion_evidencia[]" 
                                               class="form-control form-control-sm" 
                                               placeholder="Ej: Nueva foto del problema..."
                                               data-file="${fileName}">
                                    `;
                    descripcionesContainer.appendChild(descDiv);
                }

                function removeFile(fileName) {
                    selectedFiles = selectedFiles.filter(file => file.name !== fileName);

                    const preview = document.querySelector(`.remove-btn[data-name="${fileName}"]`);
                    if (preview) {
                        preview.closest('.col-md-4').remove();
                    }

                    const descInput = document.querySelector(`input[data-file="${fileName}"]`);
                    if (descInput) {
                        descInput.closest('div').remove();
                    }

                    updateUI();
                }

                function updateUI() {
                    if (selectedFiles.length > 0) {
                        previewContainer.classList.remove('d-none');
                        descripcionesContainer.classList.remove('d-none');
                        dropzone.querySelector('.dz-message').innerHTML = `
                                            <div class="text-success">
                                                <i class="fas fa-check-circle mb-2"></i>
                                                <div>${selectedFiles.length} de ${MAX_FILES} imágenes adicionales</div>
                                                <small class="text-muted">Haz click para agregar más</small>
                                            </div>
                                        `;
                    } else {
                        previewContainer.classList.add('d-none');
                        descripcionesContainer.classList.add('d-none');
                        dropzone.querySelector('.dz-message').innerHTML = `
                                            <div class="text-muted mb-3">
                                                <i class="fas fa-cloud-upload-alt fa-2x"></i>
                                            </div>
                                            <h6>Arrastra y suelta fotos aquí</h6>
                                            <p class="text-muted small">o haz click para seleccionar</p>
                                            <small class="text-muted">Formatos: JPG, PNG, GIF (máx. 5MB cada una)</small>
                                        `;
                    }
                }

                // 3. Form submission
                form.addEventListener('submit', function (e) {
                    // Validar cambios mínimos
                    const descripcion = document.getElementById('descripcion').value;
                    const direccion = document.getElementById('direccion').value;
                    const prioridad = document.getElementById('prioridad').value;

                    const originalDescripcion = "{{ $reporte->descripcion }}";
                    const originalDireccion = "{{ $reporte->direccion }}";
                    const originalPrioridad = "{{ $reporte->prioridad }}";

                    // Validar si hay ALGÚN cambio
                    const hayCambios = descripcion !== originalDescripcion ||
                        direccion !== originalDireccion ||
                        prioridad !== originalPrioridad ||
                        selectedFiles.length > 0;

                    if (!hayCambios) {
                        e.preventDefault();
                        showAlert('info', 'No hay cambios para guardar');
                        return;
                    }

                    // Agregar archivos al form
                    const dataTransfer = new DataTransfer();
                    selectedFiles.forEach(file => dataTransfer.items.add(file));
                    fileInput.files = dataTransfer.files;

                    // Mostrar spinner
                    btnSubmit.disabled = true;
                    spinner.classList.remove('d-none');

                    showAlert('success', 'Guardando cambios...');
                });
                // 4. Alertas
                function showAlert(type, message) {
                    const alert = document.createElement('div');
                    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
                    alert.style.cssText = 'top: 20px; right: 20px; z-index: 1050; max-width: 400px;';
                    alert.innerHTML = `
                                        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                                        ${message}
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    `;
                    document.body.appendChild(alert);

                    setTimeout(() => {
                        if (alert.parentNode) {
                            alert.parentNode.removeChild(alert);
                        }
                    }, 5000);
                }

                // 5. Tooltips
                var tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                tooltips.map(function (tooltip) {
                    return new bootstrap.Tooltip(tooltip);
                });

                // 6. Prevenir salir sin guardar
                let formChanged = false;

                const formInputs = form.querySelectorAll('input, textarea, select');
                formInputs.forEach(input => {
                    input.addEventListener('change', function () {
                        formChanged = true;
                    });
                });

                textarea.addEventListener('input', function () {
                    formChanged = true;
                });

                window.addEventListener('beforeunload', function (e) {
                    if (formChanged) {
                        e.preventDefault();
                        e.returnValue = 'Tienes cambios sin guardar. ¿Estás seguro de salir?';
                    }
                });

                // Resetear cuando se envía el formulario
                form.addEventListener('submit', function () {
                    formChanged = false;
                });
            });
        </script>
    @endpush
@endsection