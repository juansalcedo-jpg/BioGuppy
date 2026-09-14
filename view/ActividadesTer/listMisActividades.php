<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-11">

      <!-- ENCABEZADO DE LA VISTA -->
      <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-semibold mb-1">Mis actividades — Terreno</h4>
          <p class="text-muted small mb-0">Consulta y filtra las actividades de terreno que has registrado.</p>
        </div>
      </div>

      <!-- TARJETA CONTENEDORA -->
      <div class="card border-0 shadow-sm">

        <!-- ENCABEZADO DE TARJETA CON FILTROS -->
        <div class="card-header bg-white border-bottom py-3">
          <div class="d-flex align-items-center mb-3">
            <i class="bi bi-file-earmark-text text-primary me-2 fs-5"></i>
            <span class="fw-semibold">Actividades registradas por mí</span>
          </div>

          <!-- FORMULARIO DE FILTROS -->
          <form id="formFiltroMisActividadesTer" action="<?php echo getUrl('ActividadesTer','ActividadesTer','filtro', false, 'ajax'); ?>" method="POST">
            <div class="row g-2 align-items-end">

              <div class="col-6 col-md-2">
                <label for="fechaDesde" class="form-label small text-muted mb-1">Desde</label>
                <input type="date" id="fechaDesde" name="fechaDesde" class="form-control form-control-sm">
              </div>

              <div class="col-6 col-md-2">
                <label for="fechaHasta" class="form-label small text-muted mb-1">Hasta</label>
                <input type="date" id="fechaHasta" name="fechaHasta" class="form-control form-control-sm">
              </div>

              <div class="col-12 col-md-3">
                <label for="selectDeposito" class="form-label small text-muted mb-1">Depósito</label>
                <select id="selectDeposito" name="coddeposito" class="form-select form-select-sm">
                  <option value="">Todos</option>
                  <?php if (isset($depositos) && $depositos): ?>
                    <?php while($dep = $depositos->fetch(PDO::FETCH_ASSOC)): ?>
                      <option value="<?php echo $dep['id']; ?>"><?php echo htmlspecialchars($dep['tipo_deposito'] . ' — ' . $dep['sitio']); ?></option>
                    <?php endwhile; ?>
                  <?php endif; ?>
                </select>
              </div>

              <div class="col-12 col-md-3">
                <label for="selectTipoActividad" class="form-label small text-muted mb-1">Tipo de actividad</label>
                <select id="selectTipoActividad" name="tipoactividad" class="form-select form-select-sm">
                  <option value="">Todos</option>
                  <option value="Inspeccion">Inspección</option>
                  <option value="Siembra">Siembra</option>
                  <option value="Seguimiento">Seguimiento</option>
                  <option value="Resiembra">Resiembra</option>
                </select>
              </div>

              <div class="col-12 col-md-2 d-grid">
                <button type="submit" class="btn btn-primary btn-sm">
                  <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
              </div>

            </div>
          </form>
        </div>

        <!-- TABLA DE RESULTADOS -->
        <div class="table-responsive">
          <table class="table table-striped align-middle mb-0" id="tablaMisActividadesTer">
            <thead class="table-dark">
              <tr>
                <th class="ps-4">Fecha</th>
                <th>Tipo de actividad</th>
                <th>Depósito</th>
                <th>Sitio</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Editar</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $hayActividades = isset($actividades) && $actividades && $actividades->rowCount() > 0;
                if ($hayActividades):
                    while($act = $actividades->fetch(PDO::FETCH_ASSOC)):
              ?>
              <tr>
                <td class="ps-4"><?php echo htmlspecialchars($act['fecha']); ?></td>
                <td class="fw-semibold"><?php echo htmlspecialchars($act['tipo_actividad']); ?></td>
                <td><?php echo htmlspecialchars($act['deposito']); ?></td>
                <td><span class="text-muted small"><?php echo htmlspecialchars($act['sitio']); ?></span></td>
                <td class="text-center">
                  <?php if ($act['estado'] === 'A'): ?>
                    <span class="badge bg-success">Activo</span>
                  <?php else: ?>
                    <span class="badge bg-danger">Inactivo</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
                          onclick="cargarFormularioModal('<?php echo getUrl('ActividadesTer','ActividadesTer','getUpdate',array('id'=>$act['id'])) ?>', 'Editar actividad', 'actividadTerFormEdicion', '<?php echo getUrl('ActividadesTer','ActividadesTer','listMisActividades') ?>')">
                    <i class="bi bi-pencil-fill"></i>
                  </button>
                </td>
              </tr>
              <?php
                    endwhile;
                else:
              ?>
              <tr>
                <td colspan="6" class="text-center text-muted py-5">
                  <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                  No has registrado actividades en este rango.
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
