<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-11">

      <!-- ENCABEZADO DE LA VISTA -->
      <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-semibold mb-1">Reportes — Zoocriadero</h4>
          <p class="text-muted small mb-0">Genera y exporta reportes consolidados sobre las actividades y registros de zoocriaderos.</p>
        </div>
      </div>

      <!-- SECCIÓN SUPERIOR: FILTROS / GENERADOR -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
          <form id="formGenerarReporte" action="<?php echo getUrl('ReportesZoo','ReportesZoo','generar', false, 'ajax'); ?>" method="POST">
            <div class="row g-2 align-items-end">
              
              <div class="col-12 col-md-5">
                <label for="tipoReporte" class="form-label small text-muted mb-1">Tipo de reporte</label>
                <select id="tipoReporte" name="tipoReporte" class="form-select form-select-sm" required>
                  <option value="seguimiento">Seguimiento de actividades</option>
                  <option value="tanques">Estado de tanques</option>
                  <option value="mortalidad">Registro de mortalidad</option>
                </select>
              </div>

              <div class="col-6 col-md-3">
                <label for="fechaDesde" class="form-label small text-muted mb-1">Desde</label>
                <input type="date" id="fechaDesde" name="fechaDesde" class="form-control form-control-sm" required>
              </div>

              <div class="col-6 col-md-3">
                <label for="fechaHasta" class="form-label small text-muted mb-1">Hasta</label>
                <input type="date" id="fechaHasta" name="fechaHasta" class="form-control form-control-sm" required>
              </div>

              <div class="col-12 col-md-1 d-grid">
                <button type="submit" class="btn btn-primary btn-sm">
                  <i class="bi bi-funnel me-1"></i>Generar
                </button>
              </div>

            </div>
          </form>
        </div>
      </div>

      <!-- SECCIÓN INFERIOR: RESULTADOS DEL REPORTE -->
      <div class="card border-0 shadow-sm">
        
        <!-- ENCABEZADO CON TITULO Y BOTÓN DE EXPORTACIÓN -->
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <div class="d-flex align-items-center">
            <i class="bi bi-file-earmark-text text-primary me-2 fs-5"></i>
            <span class="fw-semibold">
              Resultados: <span id="tituloReporteGen"><?php echo htmlspecialchars($nombreReporte ?? 'Seguimiento de actividades'); ?></span>
            </span>
          </div>

          <?php if (isset($reportes) && $reportes && $reportes->rowCount() > 0): ?>
            <a href="<?php echo getUrl('ReportesZoo','ReportesZoo','exportarPDF', array('tipo' => $tipoSel ?? 'seguimiento')); ?>" 
               target="_blank" 
               class="btn btn-outline-primary btn-sm px-3">
              <i class="bi bi-download me-1"></i>Exportar a PDF
            </a>
          <?php endif; ?>
        </div>

        <!-- TABLA DE RESULTADOS -->
        <div class="table-responsive">
          <table class="table table-striped align-middle mb-0" id="tablaResultadosReporte">
            <thead class="table-dark">
              <tr>
                <th class="ps-4">Fecha</th>
                <th>Tipo</th>
                <th>Tanque</th>
                <th>Responsable</th>
                <th>Observaciones</th>
                <th class="text-center">Estado</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $hayResultados = isset($reportes) && $reportes && $reportes->rowCount() > 0;
                if ($hayResultados):
                    while($row = $reportes->fetch(PDO::FETCH_ASSOC)):
              ?>
              <tr>
                <td class="ps-4"><?php echo htmlspecialchars($row['fecha']); ?></td>
                <td class="fw-semibold"><?php echo htmlspecialchars($row['tipo']); ?></td>
                <td>
                  <span class="text-muted small"><?php echo htmlspecialchars($row['tanque']); ?> · </span>
                  <?php echo htmlspecialchars($row['zoocriadero']); ?>
                </td>
                <td><?php echo htmlspecialchars($row['responsable']); ?></td>
                <td><small class="text-muted"><?php echo htmlspecialchars($row['observaciones']); ?></small></td>
                <td class="text-center">
                  <?php if ($row['estado'] === 'A' || $row['estado'] === 'Activo'): ?>
                    <span class="badge bg-success text-white">Activo</span>
                  <?php else: ?>
                    <span class="badge bg-danger text-white">Inactivo</span>
                  <?php endif; ?>
                </td>
              </tr>
              <?php
                    endwhile;
                else:
              ?>
              <tr>
                <td colspan="6" class="text-center text-muted py-5">
                  <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                  No se encontraron registros para la consulta seleccionada.
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