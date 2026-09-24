<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <h2 class="text-center fw-bold mb-4 text-dark">Actividad Ajuste de nivel</h2>
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                    <form id="formAjusteNivel"
                        action="<?php echo getUrl('ActividadesZoo', 'AjusteNivel', 'postCreateAjusteNivel'); ?>"
                        method="POST">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="tanque_id" class="form-label fw-semibold">Tanque *</label>
                                <select class="form-select form-select-lg fs-6" id="tanque_id" name="tanque_id"
                                    required>
                                    <option value="" selected disabled>Selecciona un tanque...</option>
                                    <?php if (isset($tanques) && $tanques): ?>
                                        <?php while ($tk = $tanques->fetch(PDO::FETCH_ASSOC)): ?>
                                            <option value="<?php echo $tk['id']; ?>">T-
                                                <?php echo str_pad($tk['numerotanque'], 3, '0', STR_PAD_LEFT); ?> —
                                                <?php echo htmlspecialchars($tk['nombrezoocriadero']); ?> (
                                                <?php echo htmlspecialchars($tk['nombretipotanque']); ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="fecha_actividad" class="form-label fw-semibold">Fecha *</label>
                                <input type="date" class="form-control form-control-lg fs-6" id="fecha_actividad"
                                    name="fecha_actividad" value="<?php echo date('Y-m-d'); ?>"
                                    min="<?php echo date('Y-m-d', strtotime('-2 days')); ?>"
                                    max="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <ul class="nav nav-tabs mb-4 border-bottom">
                            <li class="nav-item">
                                <a class="nav-link text-secondary fw-semibold"
                                    href="<?php echo getUrl('ActividadesZoo', 'Alimentacion', 'Alimentacion'); ?>">Alimentación</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-secondary fw-semibold"
                                    href="<?php echo getUrl('ActividadesZoo', 'NacidosMuertos', 'NacidosMuertos'); ?>">Nacidos
                                    / Muertos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-secondary fw-semibold"
                                    href="<?php echo getUrl('ActividadesZoo', 'Limpieza', 'Limpieza'); ?>">Limpieza</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active fw-semibold text-primary border-0 border-bottom border-primary border-3"
                                    href="<?php echo getUrl('ActividadesZoo', 'AjusteNivel', 'AjusteNivel'); ?>">Ajuste
                                    de nivel</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link text-secondary fw-semibold"
                                    href="<?php echo getUrl('ActividadesZoo', 'Lavado', 'Lavado'); ?>">Lavado</a>
                            </li>
                        </ul>

                        <h5 class="fw-bold mb-4 text-dark">Ajuste de Nivel</h5>
                        <p class="text-muted small mt-n3 mb-4">Completa el nivel del tanque hasta la mitad y mide los
                            parámetros fisicoquímicos (pH y temperatura). Realizar una vez a la semana.</p>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="ph" class="form-label fw-semibold">pH *</label>
                                <input type="number" step="0.1" class="form-control form-control-lg fs-6" id="ph"
                                    name="ph" placeholder="Ej. 7.2" required>
                            </div>
                            <div class="col-md-6">
                                <label for="temperatura" class="form-label fw-semibold">Temperatura (°C) *</label>
                                <input type="number" step="0.1" class="form-control form-control-lg fs-6"
                                    id="temperatura" name="temperatura" placeholder="Ej. 24" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end pt-3">
                            <button type="submit"
                                class="btn btn-primary px-4 py-2 fs-6 fw-semibold d-inline-flex align-items-center rounded-3">
                                <i class="bi bi-floppy me-2"></i> Guardar actividad
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if (isset($_SESSION['error'])) {
    ?>
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="alert alert-danger d-flex align-items-center mt-3 mb-0" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div>
                    <?php echo $_SESSION['error']; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    unset($_SESSION['error']);
}
?>