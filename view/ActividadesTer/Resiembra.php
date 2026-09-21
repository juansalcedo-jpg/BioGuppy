<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <h2 class="text-center fw-bold mb-4 text-dark">Actividad de Terreno</h2>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <form action="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'postCreateResiembra'); ?>" method="POST">

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
                                <a class="nav-link text-secondary fw-semibold" href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'Inspeccion'); ?>">Inspección</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-secondary fw-semibold" href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'Siembra'); ?>">Siembra</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-secondary fw-semibold" href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'Seguimiento'); ?>">Seguimiento</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active fw-semibold text-primary border-0 border-bottom border-primary border-3" href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'Resiembra'); ?>">Resiembra</a>
                            </li>
                        </ul>
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="cantidad_hembras" class="form-label fw-semibold">Cantidad de hembras</label>
                                <input type="number" min="0" class="form-control form-control-lg fs-6" id="cantidad_hembras" name="cantidad_hembras" value="0">
                            </div>

                            <div class="col-md-3">
                                <label for="cantidad_machos" class="form-label fw-semibold">Cantidad de machos</label>
                                <input type="number" min="0" class="form-control form-control-lg fs-6" id="cantidad_machos" name="cantidad_machos" value="0">
                            </div>

                            <div class="col-md-3">
                                <label for="tiempo_aclimatar" class="form-label fw-semibold">Tiempo de aclimatación (min)</label>
                                <input type="number" min="0" class="form-control form-control-lg fs-6" id="tiempo_aclimatar" name="tiempo_aclimatar" value="0">
                            </div>

                            <div class="col-md-3">
                                <label for="recolectar_empacar" class="form-label fw-semibold">¿Recolectar y empacar?</label>
                                <select class="form-select form-select-lg fs-6" id="recolectar_empacar" name="recolectar_empacar">
                                    <option value="S" selected>Sí</option>
                                    <option value="N">No</option>
                                </select>
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
