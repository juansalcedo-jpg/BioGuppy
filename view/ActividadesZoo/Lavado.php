<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <!-- Título Principal Modificado -->
            <h2 class="text-center fw-bold mb-4 text-dark">Actividad Lavado</h2>

            <!-- Tarjeta del Formulario -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <form action="<?php echo getUrl('ActividadesZoo', 'ActividadesZoo', 'postCreateLavado'); ?>" method="POST">

                        <!-- Fila Superior: Tanque y Fecha -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="tanque_id" class="form-label fw-semibold">Tanque *</label>
                                <select class="form-select form-select-lg fs-6" id="tanque_id" name="tanque_id" required>
                                    <option value="1" selected>T-001 — Zoocriadero La Flora</option>
                                    <option value="2">T-002 — Zoocriadero La Flora</option>
                                    <option value="3">T-003 — Zoocriadero San Fernando</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="fecha_actividad" class="form-label fw-semibold">Fecha *</label>
                                <input type="text" class="form-control form-control-lg fs-6" id="fecha_actividad" name="fecha_actividad" value="13/09/2026" required>
                            </div>
                        </div>

                        <!-- Titulo -->
                        <h5 class="fw-bold mb-4 text-dark">Lavado</h5>

                        <!-- Campos Específicos: Porcentaje de agua cambiada (%) con Input Group -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="porcentaje_agua" class="form-label fw-semibold">Porcentaje de agua cambiada (%)</label>
                                <div class="input-group input-group-lg">
                                    <input type="number" step="0.01" min="0" max="100" class="form-control fs-6" id="porcentaje_agua" name="porcentaje_agua">
                                    <span class="input-group-text bg-light text-secondary fs-6">%</span>
                                </div>
                            </div>
                        </div>

                        <!-- Botón Guardar -->
                        <div class="d-flex justify-content-end pt-3">
                            <button type="submit" class="btn btn-primary px-4 py-2 fs-6 fw-semibold d-inline-flex align-items-center rounded-3">
                                <i class="bi bi-floppy me-2"></i> Guardar actividad
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>