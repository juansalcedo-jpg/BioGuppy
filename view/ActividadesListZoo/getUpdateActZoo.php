<?php
$tipo = strtoupper($actividad['nombreactividad']);
$esAlimentacion = (strpos($tipo, 'ALIMENTA') !== false);
$esRecoleccion  = (strpos($tipo, 'RECOLEC') !== false);
$esLimpieza     = (strpos($tipo, 'LIMPIEZA') !== false);
$esAjusteNivel  = (strpos($tipo, 'AJUSTE') !== false);
$esLavado       = (strpos($tipo, 'LAVADO') !== false);

$metodo = strtoupper($actividad['metodolimpieza'] ?? '');
$marcarEsponja    = ($metodo === 'ESPONJA' || $metodo === 'AMBOS');
$marcarSuccionador = ($metodo === 'SUCCIONADOR' || $metodo === 'AMBOS');
?>
<div id="actividadZooFormEdicion">
<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-9">

      <div class="mb-4">
        <h4 class="fw-semibold mb-1">Editar actividad — <?php echo htmlspecialchars($actividad['nombreactividad']); ?></h4>
        <p class="text-muted small mb-0">Corrige los datos de esta actividad de zoocriadero.</p>
      </div>

      <form action="<?php echo getUrl('ActividadesListZoo','ActividadesListZoo','postUpdate')?>" method="post" novalidate>
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
            </div>

            <?php if ($esAlimentacion): ?>
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <label for="tipo_pez" class="form-label fw-semibold">Tipo de pez *</label>
                <select class="form-select" id="tipo_pez" name="tipo_pez" required>
                  <?php $tipoPez = strtoupper($actividad['tipopez'] ?? ''); ?>
                  <option value="REPRODUCTOR" <?php echo ($tipoPez == 'REPRODUCTOR') ? 'selected' : ''; ?>>Reproductores y adultos</option>
                  <option value="ALEVIN" <?php echo ($tipoPez == 'ALEVIN') ? 'selected' : ''; ?>>Alevines</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="tipo_alimentacion" class="form-label fw-semibold">Tipo de alimentación</label>
                <select class="form-select" id="tipo_alimentacion" name="tipo_alimentacion">
                  <?php $tipoAlim = $actividad['tipoalimento'] ?? 'Mojarra molida'; ?>
                  <option value="Mojarra molida" <?php echo ($tipoAlim == 'Mojarra molida') ? 'selected' : ''; ?>>Mojarra molida</option>
                  <option value="Tabillas" <?php echo ($tipoAlim == 'Tabillas') ? 'selected' : ''; ?>>Tabillas</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="horario" class="form-label fw-semibold">Horario</label>
                <select class="form-select" id="horario" name="horario">
                  <?php $horario = strtoupper($actividad['horadia'] ?? 'MAÑANA'); ?>
                  <option value="MAÑANA" <?php echo ($horario == 'MAÑANA') ? 'selected' : ''; ?>>Mañana</option>
                  <option value="TARDE" <?php echo ($horario == 'TARDE') ? 'selected' : ''; ?>>Tarde</option>
                </select>
              </div>
            </div>
            <?php endif; ?>

            <?php if ($esRecoleccion): ?>
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label for="peces_nacidos" class="form-label fw-semibold">Peces nacidos</label>
                <input type="number" min="0" step="1" class="form-control" id="peces_nacidos" name="peces_nacidos" value="<?php echo htmlspecialchars($actividad['pecesnacidos'] ?? 0); ?>">
              </div>
              <div class="col-md-6">
                <label for="peces_muertos" class="form-label fw-semibold">Peces muertos</label>
                <input type="number" min="0" step="1" class="form-control" id="peces_muertos" name="peces_muertos" value="<?php echo htmlspecialchars($actividad['pecesmuertos'] ?? 0); ?>">
              </div>
            </div>
            <?php endif; ?>

            <?php if ($esLimpieza): ?>
            <div class="row g-3 mb-3">
              <div class="col-12">
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" value="1" id="restregado_esponja" name="restregado_esponja" <?php echo $marcarEsponja ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="restregado_esponja">
                    Restregado con esponja
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="1" id="aspirado_manguera" name="aspirado_manguera" <?php echo $marcarSuccionador ? 'checked' : ''; ?>>
                  <label class="form-check-label" for="aspirado_manguera">
                    Aspirado con manguera / mecha
                  </label>
                </div>
                <div class="form-text">Selecciona al menos uno de los dos métodos.</div>
              </div>
            </div>
            <?php endif; ?>

            <?php if ($esAjusteNivel): ?>
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label for="ph" class="form-label fw-semibold">pH *</label>
                <input type="number" step="0.1" class="form-control" id="ph" name="ph" value="<?php echo htmlspecialchars($actividad['ph'] ?? ''); ?>" required>
              </div>
              <div class="col-md-6">
                <label for="temperatura" class="form-label fw-semibold">Temperatura (°C) *</label>
                <input type="number" step="0.1" class="form-control" id="temperatura" name="temperatura" value="<?php echo htmlspecialchars($actividad['temperatura'] ?? ''); ?>" required>
              </div>
            </div>
            <?php endif; ?>

            <?php if ($esLavado): ?>
            <div class="row g-3 mb-3">
              <div class="col-md-6">
                <label for="porcentaje_agua" class="form-label fw-semibold">Porcentaje de agua cambiada (%) *</label>
                <div class="input-group">
                  <input type="number" step="0.01" min="0" max="100" class="form-control" id="porcentaje_agua" name="porcentaje_agua" value="<?php echo htmlspecialchars($actividad['porcentajeaguacambiada'] ?? ''); ?>" required>
                  <span class="input-group-text bg-light text-secondary">%</span>
                </div>
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
