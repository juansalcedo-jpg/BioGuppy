<?php
$tipo = strtoupper($actividad['nombreactividad']);
  $esInspeccion  = (strpos($tipo, 'INSPEC') !== false);
  $esSiembra     = (strpos($tipo, 'SIEMBRA') !== false);
  $esSeguimiento = (strpos($tipo, 'SEGUIMIENTO') !== false);
  $esResiembra   = (strpos($tipo, 'RESIEMBRA') !== false);
?>
<div id="actividadTerFormEdicion">
<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-9">

      <div class="mb-4">
        <h4 class="fw-semibold mb-1">Editar actividad — <?php echo htmlspecialchars($actividad['nombreactividad']); ?></h4>
        <p class="text-muted small mb-0">Corrige los datos de esta actividad de terreno.</p>
      </div>

      <form action="<?php echo getUrl('ActividadesTer','ActividadesTer','postUpdate')?>" method="post" novalidate>
        <input type="hidden" name="codactividad" value="<?php echo $actividad['codactividad']; ?>">

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white border-bottom py-3">
            <span class="fw-semibold">
              <i class="bi bi-pencil-square me-2 text-primary"></i>Datos de la actividad
            </span>
          </div>
          <div class="card-body p-4">

            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label for="fecha_actividad" class="form-label fw-semibold">Fecha *</label>
                <input type="date" class="form-control" id="fecha_actividad" name="fecha_actividad" value="<?php echo $actividad['fecha']; ?>" required>
              </div>
              <div class="col-md-6">
                <label for="hora_actividad" class="form-label fw-semibold">Hora</label>
                <input type="time" class="form-control" id="hora_actividad" name="hora_actividad" value="<?php echo htmlspecialchars(substr($actividad['hora'] ?? '', 0, 5)); ?>">
              </div>
            </div>

            <?php if ($esInspeccion): ?>
            <!-- Campos de Inspección -->
            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <label for="ph" class="form-label fw-semibold">pH del agua</label>
                <input type="number" min="0" max="14" step="0.01" class="form-control" id="ph" name="ph" value="<?php echo htmlspecialchars($actividad['ph'] ?? ''); ?>">
              </div>
              <div class="col-md-3">
                <label for="temperatura" class="form-label fw-semibold">Temperatura (°C)</label>
                <input type="number" min="0" step="0.01" class="form-control" id="temperatura" name="temperatura" value="<?php echo htmlspecialchars($actividad['temperatura'] ?? ''); ?>">
              </div>
              <div class="col-md-2">
                <label for="larvas_aedes" class="form-label fw-semibold">Larvas Aedes</label>
                <input type="number" min="0" class="form-control" id="larvas_aedes" name="larvas_aedes" value="<?php echo htmlspecialchars($actividad['larvasaedes'] ?? 0); ?>">
              </div>
              <div class="col-md-2">
                <label for="pupas" class="form-label fw-semibold">Pupas</label>
                <input type="number" min="0" class="form-control" id="pupas" name="pupas" value="<?php echo htmlspecialchars($actividad['pupas'] ?? 0); ?>">
              </div>
              <div class="col-md-2">
                <label for="larvas_culex" class="form-label fw-semibold">Larvas Culex</label>
                <input type="number" min="0" class="form-control" id="larvas_culex" name="larvas_culex" value="<?php echo htmlspecialchars($actividad['larvasculex'] ?? 0); ?>">
              </div>
            </div>
            <?php endif; ?>

            <?php if ($esSeguimiento): ?>
            <!-- Campos de Seguimiento -->
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label for="peces" class="form-label fw-semibold">¿Presencia de peces?</label>
                <select class="form-select" id="peces" name="peces">
                  <option value="S" <?php echo ($actividad['peces'] == 'S') ? 'selected' : ''; ?>>Sí</option>
                  <option value="N" <?php echo ($actividad['peces'] == 'N') ? 'selected' : ''; ?>>No</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="larvas" class="form-label fw-semibold">¿Presencia de larvas?</label>
                <select class="form-select" id="larvas" name="larvas">
                  <option value="N" <?php echo ($actividad['larvas'] == 'N') ? 'selected' : ''; ?>>No</option>
                  <option value="S" <?php echo ($actividad['larvas'] == 'S') ? 'selected' : ''; ?>>Sí</option>
                </select>
              </div>
            </div>
            <?php endif; ?>

            <?php if ($esSiembra || $esResiembra): ?>
            <!-- Campos de Siembra / Resiembra -->
            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <label for="cantidad_hembras" class="form-label fw-semibold">Cantidad hembras</label>
                <input type="number" min="0" class="form-control" id="cantidad_hembras" name="cantidad_hembras" value="<?php echo htmlspecialchars($actividad['cantidadhembras'] ?? 0); ?>">
              </div>
              <div class="col-md-3">
                <label for="cantidad_machos" class="form-label fw-semibold">Cantidad machos</label>
                <input type="number" min="0" class="form-control" id="cantidad_machos" name="cantidad_machos" value="<?php echo htmlspecialchars($actividad['cantidadmachos'] ?? 0); ?>">
              </div>
              <div class="col-md-3">
                <label for="tiempo_aclimatar" class="form-label fw-semibold">Aclimatación (min)</label>
                <input type="number" min="0" class="form-control" id="tiempo_aclimatar" name="tiempo_aclimatar" value="<?php echo htmlspecialchars($actividad['tiempoaclimatacionmin'] ?? 0); ?>">
              </div>
              <div class="col-md-3">
                <label for="recolectar_empacar" class="form-label fw-semibold">¿Recolectar/empacar?</label>
                <select class="form-select" id="recolectar_empacar" name="recolectar_empacar">
                  <option value="S" <?php echo ($actividad['recolectarempacar'] == 'S') ? 'selected' : ''; ?>>Sí</option>
                  <option value="N" <?php echo ($actividad['recolectarempacar'] == 'N') ? 'selected' : ''; ?>>No</option>
                </select>
              </div>
            </div>
            <?php endif; ?>

            <div class="row">
              <div class="col-12">
                <label for="observaciones" class="form-label fw-semibold">Observaciones</label>
                <textarea class="form-control" id="observaciones" name="observaciones" rows="2"><?php echo htmlspecialchars($actividad['observaciones'] ?? ''); ?></textarea>
              </div>
            </div>

          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
          <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-1"></i>Guardar cambios
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<?php
  if(isset($_SESSION['error'])){
?>
<div class="row justify-content-center">
  <div class="col-xl-9">
    <div class="alert alert-danger d-flex align-items-center mt-3 mb-0" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <div><?php echo $_SESSION['error']; ?></div>
    </div>
  </div>
</div>
<?php
      unset($_SESSION['error']);
  }
?>
</div>