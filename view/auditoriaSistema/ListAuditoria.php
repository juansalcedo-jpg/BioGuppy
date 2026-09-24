<div class="container-fluid py-3">
  <!-- Cabecera compacta -->
  <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
    <div>
      <h4 class="fw-bold text-dark mb-1">Auditoría del Sistema</h4>
      <p class="text-muted small mb-0">Trazabilidad y registro de actividad de los usuarios.</p>
    </div>
  </div>

  <div class="card border-0 shadow-sm">
    <div class="card-body bg-light bg-opacity-50 border-bottom py-3 px-4">
      <div class="row g-2 align-items-end">

        <!-- Formulario de fechas: solo "Desde" y "Hasta" -->
        <div class="col-md-8">
          <form method="GET" action="index.php" id="formFechasAuditoria" class="row g-2 align-items-end">
            <input type="hidden" name="modulo" value="Auditoria">
            <input type="hidden" name="controlador" value="Auditoria">
            <input type="hidden" name="funcion" value="listAuditoria">

            <div class="col-md-4">
              <label class="form-label text-muted small mb-1">Desde</label>
              <input type="date" class="form-control form-control-sm bg-white" name="desde" id="audDesde"
                value="<?php echo htmlspecialchars($_GET['desde'] ?? ''); ?>">
            </div>

            <div class="col-md-4">
              <label class="form-label text-muted small mb-1">Hasta</label>
              <input type="date" class="form-control form-control-sm bg-white" name="hasta" id="audHasta"
                value="<?php echo htmlspecialchars($_GET['hasta'] ?? ''); ?>">
            </div>

            <div class="col-auto">
              <button type="submit" class="btn btn-primary btn-sm px-4">
                <i class="bi bi-funnel me-1"></i>Filtrar
              </button>
            </div>

            <div class="col-12">
              <small class="text-danger d-none" id="audErrorFechas">
                La fecha "Desde" no puede ser mayor que la fecha "Hasta".
              </small>
            </div>
          </form>
        </div>

        <!-- Buscador de usuario por AJAX (fuera del formulario) -->
        <div class="col-md-3 ms-auto">
          <label class="form-label text-muted small mb-1">Usuario</label>
          <input type="text" class="form-control form-control-sm bg-white" id="buscadorAuditoria"
            placeholder="Buscar usuario..." autocomplete="off"
            data-url="<?php echo getUrl('Auditoria', 'Auditoria', 'filtro', false, 'ajax'); ?>">
        </div>

      </div>
    </div>

    <div class="card-body px-0 pb-0">
      <div class="table-responsive" style="max-height: 750px; overflow-y: auto;">
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
            <?php include __DIR__ . '/filaAuditoria.php'; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>


