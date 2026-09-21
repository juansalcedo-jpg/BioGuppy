<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <h2 class="text-center fw-bold mb-4 text-dark">Actividad de Terreno</h2>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <form action="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'postCreateInspeccion'); ?>" method="POST">

                        <div class="row g-3 mb-4">
                            <div class="col-md-5">
                                <label for="deposito_id" class="form-label fw-semibold">Depósito *</label>
                                <select class="form-select form-select-lg fs-6" id="deposito_id" name="deposito_id" required>
                                    <option selected disabled>Seleccione...</option>
                                    <?php
                                        if (isset($depositos) && $depositos):
                                            while($dep = $depositos->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                    <option value="<?php echo $dep['id']; ?>"><?php echo htmlspecialchars($dep['nombresitio'] . ' — ' . $dep['tipodeposito']); ?></option>
                                    <?php
                                            endwhile;
                                        endif;
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="fecha_actividad" class="form-label fw-semibold">Fecha *</label>
                                <input type="date" class="form-control form-control-lg fs-6" id="fecha_actividad" name="fecha_actividad" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="col-md-3">
                                <label for="hora_actividad" class="form-label fw-semibold">Hora</label>
                                <input type="time" class="form-control form-control-lg fs-6" id="hora_actividad" name="hora_actividad">
                            </div>
                        </div>

                        <ul class="nav nav-tabs mb-4 border-bottom">
                            <li class="nav-item">
                                <a class="nav-link active fw-semibold text-primary border-0 border-bottom border-primary border-3" href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'Inspeccion'); ?>">Inspección</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-secondary fw-semibold" href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'Siembra'); ?>">Siembra</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-secondary fw-semibold" href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'Seguimiento'); ?>">Seguimiento</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-secondary fw-semibold" href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'Resiembra'); ?>">Resiembra</a>
                            </li>
                        </ul>

                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="ph" class="form-label fw-semibold">pH del agua</label>
                                <input type="number" min="0" max="14" step="0.01" class="form-control form-control-lg fs-6" id="ph" name="ph">
                            </div>

                            <div class="col-md-3">
                                <label for="temperatura" class="form-label fw-semibold">Temperatura (°C)</label>
                                <input type="number" min="0" step="0.01" class="form-control form-control-lg fs-6" id="temperatura" name="temperatura">
                            </div>

                            <div class="col-md-2">
                                <label for="larvas_aedes" class="form-label fw-semibold">Larvas Aedes</label>
                                <input type="number" min="0" class="form-control form-control-lg fs-6" id="larvas_aedes" name="larvas_aedes" value="0">
                            </div>

                            <div class="col-md-2">
                                <label for="pupas" class="form-label fw-semibold">Pupas</label>
                                <input type="number" min="0" class="form-control form-control-lg fs-6" id="pupas" name="pupas" value="0">
                            </div>

                            <div class="col-md-2">
                                <label for="larvas_culex" class="form-label fw-semibold">Larvas Culex</label>
                                <input type="number" min="0" class="form-control form-control-lg fs-6" id="larvas_culex" name="larvas_culex" value="0">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label for="observaciones" class="form-label fw-semibold">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" rows="2"></textarea>
                            </div>
                        </div>

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
