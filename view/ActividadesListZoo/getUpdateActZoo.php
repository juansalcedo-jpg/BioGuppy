<?php
$tipo = strtoupper($actividad['nombreactividad'] ?? '');
$esAlimentacion = (strpos($tipo, 'ALIMENTA') !== false);
$esRecoleccion = (strpos($tipo, 'RECOLEC') !== false);
$esLimpieza = (strpos($tipo, 'LIMPIEZA') !== false);
$esAjusteNivel = (strpos($tipo, 'AJUSTE') !== false);
$esLavado = (strpos($tipo, 'LAVADO') !== false);

$metodo = strtoupper($actividad['metodolimpieza'] ?? '');
$marcarEsponja = ($metodo === 'ESPONJA' || $metodo === 'AMBOS');
$marcarSuccionador = ($metodo === 'SUCCIONADOR' || $metodo === 'AMBOS');
?>
<div id="actividadZooFormEdicion">
  <div class="container-fluid px-0 py-2">

    <!-- Alertas de Sesión -->
    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm border-0 rounded-4">
        <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-danger"></i>
        <div><?php echo htmlspecialchars($_SESSION['error']); ?></div>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="<?php echo getUrl('ActividadesListZoo', 'ActividadesListZoo', 'postUpdate') ?>" method="post"
      novalidate>
      <input type="hidden" name="codactividad" value="<?php echo $actividad['codactividad']; ?>">

      <!-- Fecha -->
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <label for="fecha_actividad" class="form-label small fw-semibold text-secondary">Fecha *</label>
          <div class="input-group">
            <span class="input-group-text bg-light border-0 rounded-start-3 text-muted"><i
                class="bi bi-calendar-date"></i></span>
            <input type="date" class="form-control form-control-lg fs-6" id="fecha_actividad" name="fecha_actividad"
              value="<?php echo date('Y-m-d'); ?>" min="<?php echo date('Y-m-d', strtotime('-2 days')); ?>"
              max="<?php echo date('Y-m-d'); ?>" required>
          </div>
        </div>
      </div>

      <!-- Campos específicos para ALIMENTACIÓN -->
      <?php if ($esAlimentacion): ?>
        <div class="p-3 bg-light rounded-4 mb-4 border-0">
          <h6 class="fw-bold text-dark small text-uppercase mb-3"><i class="bi bi-box2-heart text-primary me-1"></i>
            Parámetros de Alimentación</h6>
          <div class="row g-3">
            <div class="col-md-4">
              <label for="tipo_pez" class="form-label small fw-semibold text-secondary">Tipo de pez *</label>
              <select class="form-select bg-white border-0 rounded-3 py-2" id="tipo_pez" name="tipo_pez" required>
                <?php $tipoPez = strtoupper($actividad['tipopez'] ?? ''); ?>
                <option value="REPRODUCTOR" <?php echo ($tipoPez == 'REPRODUCTOR') ? 'selected' : ''; ?>>Reproductores y
                  adultos</option>
                <option value="ALEVIN" <?php echo ($tipoPez == 'ALEVIN') ? 'selected' : ''; ?>>Alevines</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="tipo_alimentacion" class="form-label small fw-semibold text-secondary">Tipo de
                alimentación</label>
              <select class="form-select bg-white border-0 rounded-3 py-2" id="tipo_alimentacion"
                name="tipo_alimentacion">
                <?php $tipoAlim = $actividad['tipoalimento'] ?? 'Mojarra molida'; ?>
                <option value="Mojarra molida" <?php echo ($tipoAlim == 'Mojarra molida') ? 'selected' : ''; ?>>Mojarra
                  molida</option>
                <option value="Tabillas" <?php echo ($tipoAlim == 'Tabillas') ? 'selected' : ''; ?>>Tabillas</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="horario" class="form-label small fw-semibold text-secondary">Horario</label>
              <select class="form-select bg-white border-0 rounded-3 py-2" id="horario" name="horario">
                <?php $horario = strtoupper($actividad['horadia'] ?? 'MAÑANA'); ?>
                <option value="MAÑANA" <?php echo ($horario == 'MAÑANA') ? 'selected' : ''; ?>>Mañana</option>
                <option value="TARDE" <?php echo ($horario == 'TARDE') ? 'selected' : ''; ?>>Tarde</option>
              </select>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Campos específicos para RECOLECCIÓN -->
      <?php if ($esRecoleccion): ?>
        <div class="p-3 bg-light rounded-4 mb-4 border-0">
          <h6 class="fw-bold text-dark small text-uppercase mb-3"><i class="bi bi-heart-pulse text-primary me-1"></i>
            Control de Población</h6>
          <div class="row g-3">
            <div class="col-md-6">
              <label for="peces_nacidos" class="form-label small fw-semibold text-secondary">Peces nacidos</label>
              <input type="number" min="0" step="1" class="form-control bg-white border-0 rounded-3 py-2"
                id="peces_nacidos" name="peces_nacidos"
                value="<?php echo htmlspecialchars($actividad['pecesnacidos'] ?? 0); ?>">
            </div>
            <div class="col-md-6">
              <label for="peces_muertos" class="form-label small fw-semibold text-secondary">Peces muertos</label>
              <input type="number" min="0" step="1" class="form-control bg-white border-0 rounded-3 py-2"
                id="peces_muertos" name="peces_muertos"
                value="<?php echo htmlspecialchars($actividad['pecesmuertos'] ?? 0); ?>">
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Campos específicos para LIMPIEZA -->
      <?php if ($esLimpieza): ?>
        <div class="p-3 bg-light rounded-4 mb-4 border-0">
          <h6 class="fw-bold text-dark small text-uppercase mb-3"><i class="bi bi-brush text-primary me-1"></i> Método de
            Limpieza</h6>
          <div class="row g-3">
            <div class="col-12">
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" value="1" id="restregado_esponja"
                  name="restregado_esponja" <?php echo $marcarEsponja ? 'checked' : ''; ?>>
                <label class="form-check-label text-dark fw-semibold" for="restregado_esponja">
                  Restregado con esponja
                </label>
              </div>
              <div class="form-check mb-2">
                <input class="form-check-input" type="checkbox" value="1" id="aspirado_manguera" name="aspirado_manguera"
                  <?php echo $marcarSuccionador ? 'checked' : ''; ?>>
                <label class="form-check-label text-dark fw-semibold" for="aspirado_manguera">
                  Aspirado con manguera / mecha
                </label>
              </div>
              <div class="form-text text-muted">Selecciona al menos uno de los dos métodos de limpieza utilizados.</div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Campos específicos para AJUSTE DE NIVEL -->
      <?php if ($esAjusteNivel): ?>
        <div class="p-3 bg-light rounded-4 mb-4 border-0">
          <h6 class="fw-bold text-dark small text-uppercase mb-3"><i class="bi bi-activity text-primary me-1"></i>
            Parámetros Físico-Químicos</h6>
          <div class="row g-3">
            <div class="col-md-6">
              <label for="ph" class="form-label small fw-semibold text-secondary">pH *</label>
              <input type="number" step="0.1" class="form-control bg-white border-0 rounded-3 py-2" id="ph" name="ph"
                value="<?php echo htmlspecialchars($actividad['ph'] ?? ''); ?>" required>
            </div>
            <div class="col-md-6">
              <label for="temperatura" class="form-label small fw-semibold text-secondary">Temperatura (°C) *</label>
              <input type="number" step="0.1" class="form-control bg-white border-0 rounded-3 py-2" id="temperatura"
                name="temperatura" value="<?php echo htmlspecialchars($actividad['temperatura'] ?? ''); ?>" required>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Campos específicos para LAVADO -->
      <?php if ($esLavado): ?>
        <div class="p-3 bg-light rounded-4 mb-4 border-0">
          <h6 class="fw-bold text-dark small text-uppercase mb-3"><i class="bi bi-droplet-half text-primary me-1"></i>
            Parámetro de Lavado</h6>
          <div class="row g-3">
            <div class="col-md-6">
              <label for="porcentaje_agua" class="form-label small fw-semibold text-secondary">Porcentaje de agua cambiada
                (%) *</label>
              <div class="input-group">
                <input type="number" step="0.01" min="0" max="100"
                  class="form-control bg-white border-0 rounded-start-3 py-2" id="porcentaje_agua" name="porcentaje_agua"
                  value="<?php echo htmlspecialchars($actividad['porcentajeaguacambiada'] ?? ''); ?>" required>
                <span class="input-group-text bg-white border-0 rounded-end-3 text-secondary fw-bold">%</span>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Observaciones Generales -->
      <div class="row mb-4">
        <div class="col-12">
          <label for="observaciones" class="form-label small fw-semibold text-secondary">Observaciones</label>
          <textarea class="form-control bg-light border-0 rounded-3 p-3" id="observaciones" name="observaciones"
            rows="3"
            placeholder="Escribe notas u observaciones relevantes..."><?php echo htmlspecialchars($actividad['observaciones'] ?? ''); ?></textarea>
        </div>
      </div>

      <!-- Botones de Acción -->
      <div class="d-flex justify-content-end gap-2 pt-3 border-top">
        <button type="button" class="btn btn-light rounded-pill px-4 py-2 fw-semibold text-secondary border-0"
          data-bs-dismiss="modal">
          Cancelar
        </button>
        <button type="submit" class="btn btn-dark rounded-pill px-4 py-2 fw-semibold shadow-sm">
          <i class="bi bi-check-lg me-1"></i> Guardar cambios
        </button>
      </div>

    </form>

  </div>
</div>