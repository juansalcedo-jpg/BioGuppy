<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-10">

      <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-semibold mb-1">Parámetros Generales del Sistema</h4>
          <p class="text-muted small mb-0">Administra los valores base usados en el sistema (comunas, barrios, cargos).</p>
        </div>
      </div>

      <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-bottom pt-3 pb-0">
          <ul class="nav nav-tabs card-header-tabs" id="tabsParametros" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="tab-comunas-btn" data-bs-toggle="tab" data-bs-target="#tab-comunas" type="button" role="tab">
                Comunas
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-barrios-btn" data-bs-toggle="tab" data-bs-target="#tab-barrios" type="button" role="tab">
                Barrios
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="tab-cargos-btn" data-bs-toggle="tab" data-bs-target="#tab-cargos" type="button" role="tab">
                Cargos
              </button>
            </li>
          </ul>
        </div>

        <div class="card-body">
          <div class="tab-content" id="tabsParametrosContent">

            <!-- Comunas -->
            <div class="tab-pane fade show active" id="tab-comunas" role="tabpanel">
              <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <span class="fw-semibold">
                  <i class="bi bi-gear-fill me-2 text-primary"></i>Comunas
                </span>
                <button type="button" class="btn btn-primary btn-sm">
                  <i class="bi bi-plus-lg me-1"></i>Nuevo
                </button>
              </div>
              <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                  <thead class="table-dark">
                    <tr>
                      <th class="ps-4">Nombre</th>
                      <th>Estado</th>
                      <th class="text-center">Editar</th>
                      <th class="text-center">Inhabilitar</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $hayComunas = isset($resultComunas) && $resultComunas && $resultComunas->rowCount() > 0;
                      if ($hayComunas):
                          while ($comuna = $resultComunas->fetch(PDO::FETCH_ASSOC)):
                    ?>
                    <tr>
                      <td class="ps-4"><?php echo htmlspecialchars($comuna['nombrecomuna']); ?></td>
                      <td>
                        <span class="badge <?php echo $comuna['estado'] === 'A' ? 'bg-success' : 'bg-secondary'; ?>">
                          <?php echo $comuna['estado'] === 'A' ? 'Activo' : 'Inactivo'; ?>
                        </span>
                      </td>
                      <td class="text-center">
                        <button type="button" class="btn btn-primary btn-sm">
                          <i class="bi bi-pencil-fill"></i>
                        </button>
                      </td>
                      <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm">
                          <i class="bi bi-toggle-off"></i>
                        </button>
                      </td>
                    </tr>
                    <?php
                          endwhile;
                      else:
                    ?>
                    <tr>
                      <td colspan="4" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        No hay comunas registradas todavía.
                      </td>
                    </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Barrios -->
            <div class="tab-pane fade" id="tab-barrios" role="tabpanel">
              <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <span class="fw-semibold">
                  <i class="bi bi-gear-fill me-2 text-primary"></i>Barrios
                </span>
                <button type="button" class="btn btn-primary btn-sm">
                  <i class="bi bi-plus-lg me-1"></i>Nuevo
                </button>
              </div>
              <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                  <thead class="table-dark">
                    <tr>
                      <th class="ps-4">Nombre</th>
                      <th>Estado</th>
                      <th class="text-center">Editar</th>
                      <th class="text-center">Inhabilitar</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $hayBarrios = isset($resultBarrios) && $resultBarrios && $resultBarrios->rowCount() > 0;
                      if ($hayBarrios):
                          while ($barrio = $resultBarrios->fetch(PDO::FETCH_ASSOC)):
                    ?>
                    <tr>
                      <td class="ps-4"><?php echo htmlspecialchars($barrio['nombrebarrio']); ?></td>
                      <td>
                        <span class="badge <?php echo $barrio['estado'] === 'A' ? 'bg-success' : 'bg-secondary'; ?>">
                          <?php echo $barrio['estado'] === 'A' ? 'Activo' : 'Inactivo'; ?>
                        </span>
                      </td>
                      <td class="text-center">
                        <button type="button" class="btn btn-primary btn-sm">
                          <i class="bi bi-pencil-fill"></i>
                        </button>
                      </td>
                      <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm">
                          <i class="bi bi-toggle-off"></i>
                        </button>
                      </td>
                    </tr>
                    <?php
                          endwhile;
                      else:
                    ?>
                    <tr>
                      <td colspan="4" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        No hay barrios registrados todavía.
                      </td>
                    </tr>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Cargos -->
            <div class="tab-pane fade" id="tab-cargos" role="tabpanel">
              <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <span class="fw-semibold">
                  <i class="bi bi-gear-fill me-2 text-primary"></i>Cargos
                </span>
                <button type="button" class="btn btn-primary btn-sm">
                  <i class="bi bi-plus-lg me-1"></i>Nuevo
                </button>
              </div>
              <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                  <thead class="table-dark">
                    <tr>
                      <th class="ps-4">Nombre</th>
                      <th>Estado</th>
                      <th class="text-center">Editar</th>
                      <th class="text-center">Inhabilitar</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colspan="4" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                        No hay cargos registrados todavía.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>
</div>