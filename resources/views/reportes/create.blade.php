@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <!-- Encabezado con validación de límite -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-plus-circle me-2"></i> Nuevo Reporte de Falla
                    </h1>
                    <p class="text-muted mb-0">
                        Reporta fallas eléctricas en tu domicilio o zona
                    </p>
                </div>
                <div class="text-end">
                    <div class="badge bg-{{ auth()->user()->puede_crear_reporte ? 'success' : 'danger' }} p-2">
                        <i class="fas fa-{{ auth()->user()->puede_crear_reporte ? 'check' : 'exclamation-triangle' }} me-1"></i>
                        {{ auth()->user()->reportesComoCliente()->where('estado', 'pendiente')->count() }}/3 reportes activos
                    </div>
                </div>
            </div>

            <!-- Alertas de validación -->
            @if(!auth()->user()->puede_crear_reporte)
            <div class="alert alert-danger">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="alert-heading">Límite de reportes alcanzado</h5>
                        <p class="mb-0">
                            Has alcanzado el límite de <strong>3 reportes activos</strong>. 
                            Espera a que alguno de tus reportes sea resuelto o cancelado para crear uno nuevo.
                        </p>
                        <div class="mt-2">
                            <a href="{{ route('reportes.index') }}" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-list me-1"></i> Ver mis reportes activos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Formulario (solo si puede crear) -->
            @if(auth()->user()->puede_crear_reporte)
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i> Información del Reporte
                    </h5>
                </div>
                
                <form action="{{ route('reportes.store') }}" method="POST" enctype="multipart/form-data" id="formReporte">
                    @csrf
                    
                    <div class="card-body">
                        <!-- Paso 1: Información básica -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3">
                                <span class="badge bg-primary me-2">1</span> Descripción de la falla
                            </h6>
                            
                            <div class="mb-3">
                                <label for="descripcion" class="form-label required">
                                    <i class="fas fa-align-left me-1"></i> Descripción detallada
                                </label>
                                <textarea name="descripcion" id="descripcion" 
                                          class="form-control @error('descripcion') is-invalid @enderror" 
                                          rows="4" 
                                          placeholder="Describe con detalle la falla eléctrica..."
                                          required>{{ old('descripcion') }}</textarea>
                                <div class="form-text">
                                    Sé específico: ej. "Corte de luz en toda la manzana", "Poste inclinado en la esquina", "Olor a quemado en el cuadro eléctrico"
                                </div>
                                @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="direccion" class="form-label required">
                                            <i class="fas fa-map-marker-alt me-1"></i> Dirección exacta
                                        </label>
                                        <input type="text" name="direccion" id="direccion" 
                                               class="form-control @error('direccion') is-invalid @enderror" 
                                               value="{{ old('direccion', auth()->user()->direccion ?? '') }}"
                                               placeholder="Ej: Av. Amazonas N23-45 y Shyris, Quito"
                                               required>
                                        <div class="form-text">
                                            Dirección donde ocurre la falla. Si es tu domicilio, usa el registrado.
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
                                            <option value="alta" {{ old('prioridad') == 'alta' ? 'selected' : '' }}>
                                                🔴 Alta (Peligro inminente)
                                            </option>
                                            <option value="media" {{ old('prioridad') == 'media' || !old('prioridad') ? 'selected' : '' }}>
                                                🟡 Media (Corte de luz general)
                                            </option>
                                            <option value="baja" {{ old('prioridad') == 'baja' ? 'selected' : '' }}>
                                                ⚪ Baja (Problema menor)
                                            </option>
                                        </select>
                                        <div class="form-text">
                                            <small>
                                                <span class="text-danger">Alta:</span> Incendio, cables sueltos<br>
                                                <span class="text-warning">Media:</span> Corte de luz<br>
                                                <span class="text-muted">Baja:</span> Foco fundido, poste inclinado
                                            </small>
                                        </div>
                                        @error('prioridad')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Paso 2: Evidencias -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3">
                                <span class="badge bg-primary me-2">2</span> Evidencias fotográficas
                            </h6>
                            
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <p class="mb-0">
                                            <strong>Importante:</strong> Subir fotos ayuda a los técnicos a evaluar mejor la situación.
                                            Puedes subir hasta 5 fotos (máximo 5MB cada una).
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Dropzone para subir imágenes -->
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-camera me-1"></i> Subir fotos de la falla
                                </label>
                                
                                <div class="dropzone border rounded p-4 text-center" 
                                     id="dropzone" 
                                     style="background: #f8f9fa; cursor: pointer;">
                                    <div class="dz-message">
                                        <div class="display-4 text-muted mb-3">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <h5>Arrastra y suelta tus fotos aquí</h5>
                                        <p class="text-muted">o haz click para seleccionar</p>
                                        <small class="text-muted">Formatos: JPG, PNG, GIF (máx. 5MB cada una)</small>
                                    </div>
                                </div>
                                
                                <!-- Input oculto para Laravel -->
                                <input type="file" name="evidencias[]" id="evidencias" 
                                       class="form-control d-none" multiple 
                                       accept="image/*">
                                
                                <!-- Previsualización de imágenes -->
                                <div id="preview-container" class="row mt-3 g-2 d-none">
                                    <div class="col-12">
                                        <h6 class="mb-2">Fotos seleccionadas:</h6>
                                    </div>
                                    <!-- Las imágenes se agregarán aquí dinámicamente -->
                                </div>
                                
                                <div class="form-text">
                                    <small>
                                        <i class="fas fa-lightbulb"></i> Toma fotos claras desde diferentes ángulos.
                                        Muestra el problema, postes, cables, medidores, etc.
                                    </small>
                                </div>
                                @error('evidencias.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Descripciones de las fotos -->
                            <div id="descripciones-container" class="d-none">
                                <h6 class="mb-3">Descripciones de las fotos:</h6>
                                <!-- Las descripciones se agregarán aquí dinámicamente -->
                            </div>
                        </div>

                        <!-- Paso 3: Confirmación -->
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2 mb-3">
                                <span class="badge bg-primary me-2">3</span> Confirmación
                            </h6>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" 
                                       id="confirmacion" name="confirmacion" required>
                                <label class="form-check-label" for="confirmacion">
                                    <strong>Confirmo que la información proporcionada es verídica</strong>
                                </label>
                                <div class="form-text">
                                    Al enviar este reporte, aceptas que la información es correcta y autorizas 
                                    a la empresa eléctrica a acceder a la ubicación para la reparación.
                                </div>
                                @error('confirmacion')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="alert alert-warning">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-clock fa-2x"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="alert-heading">Tiempo estimado de respuesta</h6>
                                        <p class="mb-0">
                                            Reportes de <strong>prioridad alta</strong>: 1-2 horas<br>
                                            Reportes de <strong>prioridad media</strong>: 4-6 horas<br>
                                            Reportes de <strong>prioridad baja</strong>: 24-48 horas
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success" id="btnSubmit">
                                <i class="fas fa-paper-plane me-1"></i> Enviar Reporte
                                <span class="spinner-border spinner-border-sm d-none" 
                                      id="spinner" role="status"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            @endif

            <!-- Información adicional -->
            <div class="card mt-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-question-circle me-2"></i> ¿Necesitas ayuda?
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center mb-3">
                            <div class="display-4 text-primary mb-2">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <h6>Línea de emergencias</h6>
                            <p class="text-muted mb-0">1800-ELECTRICA</p>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="display-4 text-warning mb-2">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h6>Emergencias 24/7</h6>
                            <p class="text-muted mb-0">Incendios, cables sueltos</p>
                        </div>
                        <div class="col-md-4 text-center mb-3">
                            <div class="display-4 text-success mb-2">
                                <i class="fas fa-headset"></i>
                            </div>
                            <h6>Atención al cliente</h6>
                            <p class="text-muted mb-0">Lunes a Viernes 8:00-17:00</p>
                        </div>
                    </div>
                </div>
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
    
    .dropzone.dz-drag-hover {
        border-color: #198754;
        background: #d1e7dd;
    }
    
    .image-preview {
        position: relative;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        overflow: hidden;
    }
    
    .image-preview img {
        width: 100%;
        height: 120px;
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
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const MAX_FILES = 5;
        const MAX_SIZE = 5 * 1024 * 1024; // 5MB
        let selectedFiles = [];
        
        // Elementos DOM
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('evidencias');
        const previewContainer = document.getElementById('preview-container');
        const descripcionesContainer = document.getElementById('descripciones-container');
        const form = document.getElementById('formReporte');
        const btnSubmit = document.getElementById('btnSubmit');
        const spinner = document.getElementById('spinner');
        
        // 1. Click en dropzone abre el file input
        dropzone.addEventListener('click', function() {
            fileInput.click();
        });
        
        // 2. Drag and drop
        dropzone.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dz-drag-hover');
        });
        
        dropzone.addEventListener('dragleave', function() {
            this.classList.remove('dz-drag-hover');
        });
        
        dropzone.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dz-drag-hover');
            handleFiles(e.dataTransfer.files);
        });
        
        // 3. Selección manual de archivos
        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
            this.value = ''; // Reset para poder seleccionar los mismos archivos otra vez
        });
        
        // 4. Manejo de archivos
        function handleFiles(files) {
            const validFiles = Array.from(files).filter(file => {
                // Validar tipo
                if (!file.type.startsWith('image/')) {
                    showAlert('error', `"${file.name}" no es una imagen válida`);
                    return false;
                }
                
                // Validar tamaño
                if (file.size > MAX_SIZE) {
                    showAlert('error', `"${file.name}" supera el límite de 5MB`);
                    return false;
                }
                
                // Validar número máximo
                if (selectedFiles.length >= MAX_FILES) {
                    showAlert('error', `Máximo ${MAX_FILES} imágenes permitidas`);
                    return false;
                }
                
                return true;
            });
            
            // Agregar archivos válidos
            validFiles.forEach(file => {
                selectedFiles.push(file);
                addImagePreview(file);
                addDescriptionField(file.name);
            });
            
            // Actualizar UI
            updateUI();
        }
        
        // 5. Agregar previsualización de imagen
        function addImagePreview(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
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
                
                // Evento para eliminar imagen
                col.querySelector('.remove-btn').addEventListener('click', function() {
                    removeFile(file.name);
                });
            };
            reader.readAsDataURL(file);
        }
        
        // 6. Agregar campo de descripción
        function addDescriptionField(fileName) {
            const descDiv = document.createElement('div');
            descDiv.className = 'mb-2';
            descDiv.innerHTML = `
                <label class="form-label small">Descripción para: <strong>${fileName}</strong></label>
                <input type="text" 
                       name="descripcion_evidencia[]" 
                       class="form-control form-control-sm" 
                       placeholder="Ej: Poste eléctrico dañado, Cable suelto..."
                       data-file="${fileName}">
            `;
            descripcionesContainer.appendChild(descDiv);
        }
        
        // 7. Eliminar archivo
        function removeFile(fileName) {
            // Eliminar de array
            selectedFiles = selectedFiles.filter(file => file.name !== fileName);
            
            // Eliminar previsualización
            const preview = document.querySelector(`.remove-btn[data-name="${fileName}"]`);
            if (preview) {
                preview.closest('.col-md-4').remove();
            }
            
            // Eliminar campo de descripción
            const descInput = document.querySelector(`input[data-file="${fileName}"]`);
            if (descInput) {
                descInput.closest('div').remove();
            }
            
            // Actualizar UI
            updateUI();
        }
        
        // 8. Actualizar UI
        function updateUI() {
            // Mostrar/ocultar contenedores
            if (selectedFiles.length > 0) {
                previewContainer.classList.remove('d-none');
                descripcionesContainer.classList.remove('d-none');
                dropzone.querySelector('.dz-message').innerHTML = `
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <div>${selectedFiles.length} de ${MAX_FILES} imágenes seleccionadas</div>
                        <small class="text-muted">Haz click para agregar más</small>
                    </div>
                `;
            } else {
                previewContainer.classList.add('d-none');
                descripcionesContainer.classList.add('d-none');
                dropzone.querySelector('.dz-message').innerHTML = `
                    <div class="display-4 text-muted mb-3">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <h5>Arrastra y suelta tus fotos aquí</h5>
                    <p class="text-muted">o haz click para seleccionar</p>
                    <small class="text-muted">Formatos: JPG, PNG, GIF (máx. 5MB cada una)</small>
                `;
            }
            
            // Actualizar contador en el botón
            const counter = selectedFiles.length > 0 ? ` (${selectedFiles.length})` : '';
            document.querySelector('button[type="submit"]').innerHTML = 
                `<i class="fas fa-paper-plane me-1"></i> Enviar Reporte${counter}`;
        }
        
        // 9. Validación antes de enviar
        form.addEventListener('submit', function(e) {
            // Validar que haya al menos una imagen
            if (selectedFiles.length === 0) {
                e.preventDefault();
                showAlert('warning', 'Recomendamos subir al menos una foto como evidencia');
                return;
            }
            
            // Crear DataTransfer para agregar archivos al form
            const dataTransfer = new DataTransfer();
            selectedFiles.forEach(file => dataTransfer.items.add(file));
            fileInput.files = dataTransfer.files;
            
            // Mostrar spinner
            btnSubmit.disabled = true;
            spinner.classList.remove('d-none');
            
            // Mostrar mensaje de éxito
            showAlert('success', 'Enviando reporte...');
        });
        
        // 10. Mostrar alertas
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
            
            // Auto-remover después de 5 segundos
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 5000);
        }
        
        // 11. Validación en tiempo real del textarea
        const textarea = document.getElementById('descripcion');
        const charCount = document.createElement('div');
        charCount.className = 'form-text text-end small';
        textarea.parentNode.appendChild(charCount);
        
        textarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = `${length}/1000 caracteres`;
            
            if (length < 10) {
                charCount.className = 'form-text text-end small text-danger';
            } else if (length < 50) {
                charCount.className = 'form-text text-end small text-warning';
            } else {
                charCount.className = 'form-text text-end small text-success';
            }
        });
        
        // Inicializar contador
        textarea.dispatchEvent(new Event('input'));
        
        // 12. Tooltips
        var tooltips = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltips.map(function(tooltip) {
            return new bootstrap.Tooltip(tooltip);
        });
    });
</script>
@endpush
@endsection