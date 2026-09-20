
<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-11">

      <!-- ENCABEZADO DE LA VISTA -->
      <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-semibold mb-1">Historial de Actividades — Zoocriadero</h4>
          <p class="text-muted small mb-0">Consulta y filtra las actividades registradas en los zoocriaderos.</p>
        </div>
      </div>

      <!-- TARJETA CONTENEDORA -->
      <div class="card border-0 shadow-sm">

        <!-- ENCABEZADO DE TARJETA CON FILTROS -->
        <div class="card-header bg-white border-bottom py-3">
          <div class="d-flex align-items-center mb-3">
            <i class="bi bi-file-earmark-text text-primary me-2 fs-5"></i>
            <span class="fw-semibold">Actividades registradas</span>
          </div>

          <!-- FORMULARIO DE FILTROS -->
          <!-- OJO: en un form method="GET" el navegador ignora el ?... del action
               y arma la query solo con los campos del form. Por eso modulo/controlador/funcion
               van como inputs ocultos y no como parte de la URL. -->
          <form id="formFiltroHistorial" action="index.php" method="GET">
            <input type="hidden" name="modulo" value="HistorialZoo">
            <input type="hidden" name="controlador" value="HistorialZoo">
            <input type="hidden" name="funcion" value="listHistZoo">
            <div class="row g-2 align-items-end">

              <div class="col-6 col-md-2">
                <label for="fechaDesde" class="form-label small text-muted mb-1">Desde</label>
                <input type="date" id="fechaDesde" name="fechaDesde" class="form-control form-control-sm"
                       value="<?php echo htmlspecialchars($_GET['fechaDesde'] ?? ''); ?>">
              </div>

              <div class="col-6 col-md-2">
                <label for="fechaHasta" class="form-label small text-muted mb-1">Hasta</label>
                <input type="date" id="fechaHasta" name="fechaHasta" class="form-control form-control-sm"
                       value="<?php echo htmlspecialchars($_GET['fechaHasta'] ?? ''); ?>">
              </div>

              <div class="col-12 col-md-3">
                <label for="selectZoocriadero" class="form-label small text-muted mb-1">Zoocriadero</label>
                <select id="selectZoocriadero" name="codzoocriadero" class="form-select form-select-sm">
                  <option value="">Todos</option>
                  <?php if (isset($zoocriaderos) && $zoocriaderos): ?>
                    <?php while ($z = $zoocriaderos->fetch(PDO::FETCH_ASSOC)): ?>
                      <option value="<?php echo $z['codzoocriadero']; ?>"
                        <?php echo (($_GET['codzoocriadero'] ?? '') == $z['codzoocriadero']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($z['nombrezoocriadero']); ?>
                      </option>
                    <?php endwhile; ?>
                  <?php endif; ?>
                </select>
              </div>

              <div class="col-12 col-md-3">
                <label for="selectTipoActividad" class="form-label small text-muted mb-1">Tipo de actividad</label>
                <select id="selectTipoActividad" name="codtipoactividad" class="form-select form-select-sm">
                  <option value="">Todos</option>
                  <?php if (isset($tiposActividad) && $tiposActividad): ?>
                    <?php while ($tipo = $tiposActividad->fetch(PDO::FETCH_ASSOC)): ?>
                      <option value="<?php echo $tipo['codtipoactividad']; ?>"
                        <?php echo (($_GET['codtipoactividad'] ?? '') == $tipo['codtipoactividad']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($tipo['nombreactividad']); ?>
                      </option>
                    <?php endwhile; ?>
                  <?php endif; ?>
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
          <table class="table table-striped align-middle mb-0" id="tablaHistorialZoo">
            <thead class="table-dark">
              <tr>
                <th class="ps-4">Fecha</th>
                <th>Tipo de actividad</th>
                <th>Referencia</th>
                <th>Responsable</th>
                <th>Observaciones</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Inhabilitar</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $hayActividades = isset($actividades) && $actividades && $actividades->rowCount() > 0;
              if ($hayActividades):
                while ($act = $actividades->fetch(PDO::FETCH_ASSOC)):
              ?>
                  <tr>
                    <td class="ps-4"><?php echo htmlspecialchars($act['fecha']); ?></td>
                    <td class="fw-semibold"><?php echo htmlspecialchars($act['tipo_actividad']); ?></td>
                    <td>
                      <span class="text-muted small"><?php echo htmlspecialchars($act['tanque']); ?> · </span>
                      <?php echo htmlspecialchars($act['zoocriadero']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($act['responsable']); ?></td>
                    <td><small class="text-muted"><?php echo htmlspecialchars($act['observaciones']); ?></small></td>
                    <td class="text-center">
                      <?php if ($act['estado'] === 'A'): ?>
                        <span class="badge bg-success">Activo</span>
                      <?php else: ?>
                        <span class="badge bg-danger">Inactivo</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center">
                      <?php if ($act['estado'] === 'A'): ?>
                        <a href="<?php echo getUrl('ActividadesZoo', 'ActividadesZoo', 'delete', array('id' => $act['codactividad'])) ?>"
                          class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
                          onclick="return confirm('¿Seguro que deseas inhabilitar esta actividad?')">
                          <i class="bi bi-slash-circle"></i>
                        </a>
                      <?php else: ?>
                        <a href="<?php echo getUrl('ActividadesZoo', 'ActividadesZoo', 'delete', array('id' => $act['codactividad'])) ?>"
                          class="btn btn-outline-success btn-icon rounded-circle" title="Activar">
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
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    No se encontraron actividades registradas en este rango.
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