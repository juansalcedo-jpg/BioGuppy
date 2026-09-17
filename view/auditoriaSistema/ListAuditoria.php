<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-10">

      <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-semibold mb-1">Auditoría del Sistema</h4>
          <p class="text-muted small mb-0">Consulta la trazabilidad de las acciones realizadas en el sistema.</p>
        </div>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3">
          <span class="fw-semibold">
            <i class="bi bi-activity me-2 text-primary"></i>Registro de actividad
          </span>
        </div>

        <div class="card-body border-bottom">
          <form method="GET" action="<?php echo getUrl('Auditoria','Auditoria','listAuditoria') ?>" class="row g-3 align-items-end">
            <input type="hidden" name="modulo" value="Auditoria">
            <input type="hidden" name="controlador" value="Auditoria">
            <input type="hidden" name="funcion" value="listAuditoria">

            <div class="col-md-3">
              <label for="filtroDesde" class="form-label fw-semibold small mb-1">Desde</label>
              <input type="date" class="form-control" id="filtroDesde" name="desde"
                     value="<?php echo htmlspecialchars($_GET['desde'] ?? ''); ?>">
            </div>

            <div class="col-md-3">
              <label for="filtroHasta" class="form-label fw-semibold small mb-1">Hasta</label>
              <input type="date" class="form-control" id="filtroHasta" name="hasta"
                     value="<?php echo htmlspecialchars($_GET['hasta'] ?? ''); ?>">
            </div>

            <div class="col-md-4">
              <label for="filtroUsuario" class="form-label fw-semibold small mb-1">Usuario</label>
              <input type="text" class="form-control" id="filtroUsuario" name="usuario"
                     placeholder="Buscar usuario..."
                     value="<?php echo htmlspecialchars($_GET['usuario'] ?? ''); ?>">
            </div>

            <div class="col-md-2">
              <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-funnel-fill me-1"></i>Filtrar
              </button>
            </div>
          </form>
        </div>

        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
          <table class="table table-striped align-middle mb-0" id="tablaAuditoria">
            <thead class="table-dark" style="position: sticky; top: 0; z-index: 1;">
              <tr>
                <th class="ps-4">Fecha / Hora</th>
                <th>Usuario</th>
                <th>Acción</th>
                <th>Módulo afectado</th>
                <th>Detalle</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $hayRegistros = isset($resultAuditoria) && $resultAuditoria && $resultAuditoria->rowCount() > 0;
                if ($hayRegistros):
                    while ($log = $resultAuditoria->fetch(PDO::FETCH_ASSOC)):
              ?>
              <tr>
                <td class="ps-4 text-muted"><?php echo htmlspecialchars($log['fecha']); ?></td>
                <td><?php echo htmlspecialchars($log['usuario']); ?></td>
                <td><?php echo htmlspecialchars($log['accion']); ?></td>
                <td><span class="badge text-bg-info-subtle text-info-emphasis rounded-pill"><?php echo htmlspecialchars($log['modulo']); ?></span></td>
                <td class="text-muted"><?php echo htmlspecialchars($log['detalle']); ?></td>
              </tr>
              <?php
                    endwhile;
                else:
              ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-5">
                  <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                  No se encontraron registros para los filtros aplicados
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