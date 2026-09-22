<div class="container-fluid py-3">
  <div class="row justify-content-center">
    <div class="col-xl-11">

      <!-- Alertas de error en fechas / sesión -->
      <div id="alertaFiltroHistorialTer">
        <?php if (!empty($errorFechas)): ?>
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-4 border-0 shadow-sm rounded-3 bg-danger-subtle text-danger-emphasis" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                    <div><?php echo htmlspecialchars($errorFechas); ?></div>
                </div>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
      </div>

      <!-- Cabecera de la sección -->
      <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-bold text-dark mb-1">Historial de Actividades — Terreno</h4>
          <p class="text-muted small mb-0">Consulta y filtra las actividades registradas en los sitios de terreno.</p>
        </div>
      </div>

      <!-- Tarjeta contenedora con filtros y tabla -->
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4">
          <div class="d-flex align-items-center mb-3">
            <i class="bi bi-file-earmark-text text-primary me-2 fs-5"></i>
            <span class="fw-semibold text-secondary">Filtros de búsqueda</span>
          </div>

          <!-- Formulario de Filtros -->
          <form id="formFiltroHistorialTer" action="index.php" method="GET">
            <input type="hidden" name="modulo" value="HistorialTer">
            <input type="hidden" name="controlador" value="HistorialTer">
            <input type="hidden" name="funcion" value="listHistTer">
            
            <div class="row g-3 align-items-end">

              <div class="col-6 col-md-2">
                <label for="fechaDesde" class="form-label text-dark fw-bold small mb-1">Desde</label>
                <div class="input-group input-group-sm rounded-3 overflow-hidden border bg-white shadow-xs">
                  <input type="date" id="fechaDesde" name="fechaDesde" class="form-control border-0 bg-transparent py-2 shadow-none"
                         min="2026-09-10" max="<?php echo date('Y-m-d'); ?>"
                         value="<?php echo htmlspecialchars($_GET['fechaDesde'] ?? ''); ?>" required>
                </div>
              </div>

              <div class="col-6 col-md-2">
                <label for="fechaHasta" class="form-label text-dark fw-bold small mb-1">Hasta</label>
                <div class="input-group input-group-sm rounded-3 overflow-hidden border bg-white shadow-xs">
                  <input type="date" id="fechaHasta" name="fechaHasta" class="form-control border-0 bg-transparent py-2 shadow-none"
                         min="2026-09-10" max="<?php echo date('Y-m-d'); ?>"
                         value="<?php echo htmlspecialchars($_GET['fechaHasta'] ?? ''); ?>" required>
                </div>
              </div>

              <div class="col-12 col-md-3">
                <label for="selectSitio" class="form-label text-dark fw-bold small mb-1">Sitio</label>
                <div class="input-group input-group-sm rounded-3 overflow-hidden border bg-white shadow-xs">
                  <select id="selectSitio" name="codsitio" class="form-select border-0 bg-transparent py-2 shadow-none">
                    <option value="">Todos los sitios</option>
                    <?php if (isset($sitios) && $sitios): ?>
                      <?php while ($s = $sitios->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $s['codsitio']; ?>"
                          <?php echo (($_GET['codsitio'] ?? '') == $s['codsitio']) ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($s['nombresitio']); ?>
                        </option>
                      <?php endwhile; ?>
                    <?php endif; ?>
                  </select>
                </div>
              </div>

              <div class="col-12 col-md-3">
                <label for="selectTipoActividad" class="form-label text-dark fw-bold small mb-1">Tipo de actividad</label>
                <div class="input-group input-group-sm rounded-3 overflow-hidden border bg-white shadow-xs">
                  <select id="selectTipoActividad" name="codtipoactividad" class="form-select border-0 bg-transparent py-2 shadow-none">
                    <option value="">Todos los tipos</option>
                    <?php if (isset($tiposActividad) &&$tiposActividad): ?>
                      <?php while ($tipo =$tiposActividad->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $tipo['codtipoactividad']; ?>"
                          <?php echo (($_GET['codtipoactividad'] ?? '') ==$tipo['codtipoactividad']) ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($tipo['nombreactividad']); ?>
                        </option>
                      <?php endwhile; ?>
                    <?php endif; ?>
                  </select>
                </div>
              </div>

              <div class="col-12 col-md-2 d-grid">
                <button type="submit" class="btn btn-primary btn-sm py-2 rounded-3 shadow-sm fw-semibold d-flex align-items-center justify-content-center">
                  <i class="bi bi-funnel me-1"></i> Filtrar
                </button>
              </div>

            </div>
          </form>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="tablaHistorialTer">
            <thead class="table-light text-uppercase fs-7 text-secondary">
              <tr>
                <th class="ps-4 py-3">Fecha</th>
                <th class="py-3">Tipo de actividad</th>
                <th class="py-3">Depósito / Referencia</th>
                <th class="py-3">Responsable</th>
                <th class="py-3">Hallazgos</th>
                <th class="text-center py-3">Estado</th>
                <th class="text-center py-3">Habilitar</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $hayActividades = isset($actividades) && $actividades &&$actividades->rowCount() > 0;
              if ($hayActividades):
                while ($act =$actividades->fetch(PDO::FETCH_ASSOC)):
              ?>
              <tr>
                <td class="ps-4 py-3 text-muted"><?php echo htmlspecialchars($act['fecha']); ?></td>
                <td class="py-3 fw-semibold text-dark"><?php echo htmlspecialchars($act['nombreactividad']); ?></td>
                <td class="py-3">
                  <span class="text-dark fw-medium"><?php echo htmlspecialchars($act['nombresitio']); ?></span>
                  <span class="text-muted small"> · <?php echo htmlspecialchars($act['nombrebarrio']); ?></span>
                </td>
                <td class="py-3 text-muted"><?php echo htmlspecialchars($act['nombreusuario'] . ' ' . $act['apellidousuario']); ?></td>
                <td class="py-3 text-muted small" style="max-width: 220px;">
                  <?php
                    // Hallazgos segun el tipo de actividad
                    $tipo = $act['nombreactividad'];
                    if (stripos($tipo, 'Inspecci') !== false) {
                      echo 'Presencia larvas Aedes: ' . ($act['larvasaedes'] > 0 ? 'Sí' : 'No');
                    } elseif (stripos($tipo, 'Resiembra') !== false) {
                      echo htmlspecialchars($act['cantidadhembras'] . ' hembras, ' . $act['cantidadmachos'] . ' machos');
                    } elseif (stripos($tipo, 'Siembra') !== false) {
                      echo htmlspecialchars($act['cantidadhembras'] . ' hembras, ' . $act['cantidadmachos'] . ' machos, ' . $act['volumenagualitros'] . ' L agua');
                    } elseif (stripos($tipo, 'Seguimiento') !== false) {
                      echo 'Presencia guppies: ' . ($act['peces'] === 'S' ? 'Sí' : 'No');
                    } else {
                      echo htmlspecialchars($act['observaciones'] ?? '—');
                    }
                  ?>
                </td>
                <td class="text-center py-3">
                  <?php if ($act['estado'] === 'A'): ?>
                    <span class="badge bg-success-subtle text-success-emphasis px-3 py-1 rounded-pill fw-semibold">Activo</span>
                  <?php else: ?>
                    <span class="badge bg-danger-subtle text-danger-emphasis px-3 py-1 rounded-pill fw-semibold">Inactivo</span>
                  <?php endif; ?>
                </td>
                <td class="text-center py-3">
                  <?php if ($act['estado'] === 'A'): ?>
                    <a href="<?php echo getUrl('HistorialTer', 'HistorialTer', 'delete', array('id' => $act['codactividad'])) ?>"
                       class="btn btn-light btn-sm text-danger rounded-circle shadow-sm p-2" title="Inhabilitar"
                       onclick="return confirm('¿Seguro que deseas inhabilitar esta actividad?')">
                      <i class="bi bi-slash-circle"></i>
                    </a>
                  <?php else: ?>
                    <a href="<?php echo getUrl('HistorialTer', 'HistorialTer', 'delete', array('id' => $act['codactividad'])) ?>"
                       class="btn btn-light btn-sm text-success rounded-circle shadow-sm p-2" title="Activar">
                      <i class="bi bi-check-lg"></i>
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
              <?php
                endwhile;
              else:
              ?>
              <tr>
                <td colspan="7" class="text-center text-muted py-5">
                  <div class="my-3">
                      <i class="bi bi-inbox fs-1 text-muted opacity-50 d-block mb-2"></i>
                      <span class="fs-6">No se encontraron actividades registradas en este rango.</span>
                  </div>
                </td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
  (function() {
    var formulario = document.getElementById("formFiltroHistorialTer");
    var alerta = document.getElementById("alertaFiltroHistorialTer");

    if (!formulario) return;

    formulario.addEventListener("submit", function(event) {
      var fechaDesde = document.getElementById("fechaDesde").value;
      var fechaHasta = document.getElementById("fechaHasta").value;

      if (fechaDesde && fechaHasta && fechaDesde > fechaHasta) {
        event.preventDefault();
        alerta.innerHTML = `
          <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-4 border-0 shadow-sm rounded-3 bg-danger-subtle text-danger-emphasis" role="alert">
              <div class="d-flex align-items-center">
                  <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                  <div>La fecha desde no puede ser mayor que la fecha hasta.</div>
              </div>
              <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>`;
      }
    });
  })();
</script>

<style>
  .fs-7 {
    font-size: 0.75rem;
    letter-spacing: 0.05em;
  }
  .input-group:focus-within {
    border-color: var(--bs-primary) !important;
    box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15);
  }
</style>