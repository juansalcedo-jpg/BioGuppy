<div class="container-fluid py-3">
  <!-- Cabecera compacta -->
  <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
    <div>
      <h4 class="fw-bold text-dark mb-1">Auditoría del Sistema</h4>
      <p class="text-muted small mb-0">Trazabilidad y registro de actividad de los usuarios.</p>
    </div>
  </div>

  <!-- Contenedor principal con filtros integrados de forma ligera -->
  <div class="card border-0 shadow-sm">
    <div class="card-body bg-light bg-opacity-50 border-bottom py-3 px-4">
      <form method="GET" action="<?php echo getUrl('Auditoria','Auditoria','listAuditoria') ?>" class="row g-2 align-items-end">
        <input type="hidden" name="modulo" value="Auditoria">
        <input type="hidden" name="controlador" value="Auditoria">
        <input type="hidden" name="funcion" value="listAuditoria">

        <div class="col-md-3">
          <label class="form-label text-muted small mb-1">Desde</label>
          <input type="date" class="form-control form-control-sm bg-white" name="desde"
                 value="<?php echo htmlspecialchars($_GET['desde'] ?? ''); ?>">
        </div>

        <div class="col-md-3">
          <label class="form-label text-muted small mb-1">Hasta</label>
          <input type="date" class="form-control form-control-sm bg-white" name="hasta"
                 value="<?php echo htmlspecialchars($_GET['hasta'] ?? ''); ?>">
        </div>

        <div class="col-md-4">
          <label class="form-label text-muted small mb-1">Usuario</label>
          <input type="text" class="form-control form-control-sm bg-white" name="usuario"
                 placeholder="Buscar usuario..."
                 value="<?php echo htmlspecialchars($_GET['usuario'] ?? ''); ?>">
        </div>

        <div class="col-md-2">
          <button type="submit" class="btn btn-sm btn-primary w-100">
            <i class="bi bi-funnel me-1"></i>Filtrar
          </button>
        </div>
      </form>
    </div>

    <div class="card-body px-0 pb-0">
      <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
        <table class="table table-hover align-middle mb-0" id="tablaAuditoria">
          <thead class="bg-white text-uppercase fs-7 text-secondary border-bottom sticky-top">
            <tr>
              <th class="py-3 ps-4">Fecha / Hora</th>
              <th class="py-3">Usuario</th>
              <th class="py-3">Acción</th>
              <th class="py-3">Módulo</th>
              <th class="py-3">Antes</th>
              <th class="py-3 pe-4">Después</th>
            </tr>
          </thead>
          <tbody>
            <?php
              $hayRegistros = isset($resultAuditoria) && $resultAuditoria && $resultAuditoria->rowCount() > 0;
              if ($hayRegistros):
                  while ($log = $resultAuditoria->fetch(PDO::FETCH_ASSOC)):
            ?>
            <tr class="border-bottom">
              <td class="ps-4 py-2 text-muted small text-nowrap"><?php echo htmlspecialchars($log['fecha']); ?></td>
              <td class="py-2 fw-medium text-dark"><?php echo htmlspecialchars($log['usuario']); ?></td>
              <td class="py-2 text-secondary"><?php echo htmlspecialchars($log['accion']); ?></td>
              <td class="py-2">
                <span class="badge bg-light text-dark border px-2 py-1 fw-normal"><?php echo htmlspecialchars($log['modulo']); ?></span>
              </td>
              <td class="py-2 text-muted small font-monospace" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($log['valoranterior'] ?? ''); ?>">
                <?php echo !empty($log['valoranterior']) ? htmlspecialchars($log['valoranterior']) : '&mdash;'; ?>
              </td>
              <td class="py-2 pe-4 text-muted small font-monospace" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($log['valornuevo'] ?? ''); ?>">
                <?php echo !empty($log['valornuevo']) ? htmlspecialchars($log['valornuevo']) : '&mdash;'; ?>
              </td>
            </tr>
            <?php
                  endwhile;
              else:
            ?>
            <tr>
              <td colspan="6" class="text-center text-muted py-4">
                <i class="bi bi-inbox fs-3 d-block mb-1 text-secondary opacity-50"></i>
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