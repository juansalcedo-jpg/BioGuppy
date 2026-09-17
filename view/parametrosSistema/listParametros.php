<?php
?>
<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-10">

      <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-semibold mb-1">Parámetros Generales del Sistema</h4>
          <p class="text-muted small mb-0">Administra los valores base usados en el sistema (comunas, barrios).</p>
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
          </ul>
        </div>

        <div class="card-body">
          <div class="tab-content" id="tabsParametrosContent">

            <!-- Comunas -->
            <div class="tab-pane fade show active" id="tab-comunas" role="tabpanel">
              <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <span class="fw-semibold">
                  <i class="bi bi-geo-alt-fill me-2 text-primary"></i>Comunas
                </span>
                <div class="d-flex align-items-center gap-2">
                  <div class="input-group input-group-sm" style="width: 220px;">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" id="buscadorComunas" class="form-control" placeholder="Buscar comuna...">
                  </div>
                  <button type="button" class="btn btn-primary btn-sm"
                          onclick="cargarFormularioModal('<?php echo getUrl('Parametros','Parametros','createComuna') ?>',
                          'Registrar comuna', 'comunaFormRegistro', '<?php echo getUrl('Parametros','Parametros','listParametros') ?>', 'tablaComunas')">
                    <i class="bi bi-plus-lg me-1"></i>Nueva comuna
                  </button>
                </div>
              </div>
              <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                <table class="table table-striped align-middle mb-0" id="tablaComunas">
                  <thead class="table-dark" style="position: sticky; top: 0; z-index: 1;">
                    <tr>
                      <th class="ps-4">Nombre</th>
                      <th class="text-center">Estado</th>
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
                      <td class="text-center">
                        <?php if ($comuna['estado'] === 'A'): ?>
                          <span class="badge bg-success-subtle text-success-emphasis">Activo</span>
                        <?php else: ?>
                          <span class="badge bg-secondary-subtle text-secondary-emphasis">Inactivo</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-center">
                        <?php if ($comuna['estado'] === 'A'): ?>
                          <a href="<?php echo getUrl('Parametros','Parametros','deleteComuna',array('id'=>$comuna['id'])) ?>"
                             class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
                             onclick="return confirm('¿Seguro que deseas inhabilitar esta comuna?')">
                            <i class="bi bi-slash-circle"></i>
                          </a>
                        <?php else: ?>
                          <a href="<?php echo getUrl('Parametros','Parametros','deleteComuna',array('id'=>$comuna['id'])) ?>"
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
                      <td colspan="3" class="text-center text-muted py-5">
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
                  <i class="bi bi-signpost-split-fill me-2 text-primary"></i>Barrios
                </span>
                <div class="d-flex align-items-center gap-2">
                  <div class="input-group input-group-sm" style="width: 220px;">
                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                    <input type="text" id="buscadorBarrios" class="form-control" placeholder="Buscar barrio...">
                  </div>
                  <button type="button" class="btn btn-primary btn-sm"
                          onclick="cargarFormularioModal('<?php echo getUrl('Parametros','Parametros','createBarrio') ?>',
                          'Registrar barrio', 'barrioFormRegistro', '<?php echo getUrl('Parametros','Parametros','listParametros') ?>', 'tablaBarrios')">
                    <i class="bi bi-plus-lg me-1"></i>Nuevo barrio
                  </button>
                </div>
              </div>
              <div class="table-responsive" style="max-height: 420px; overflow-y: auto;">
                <table class="table table-striped align-middle mb-0" id="tablaBarrios">
                  <thead class="table-dark" style="position: sticky; top: 0; z-index: 1;">
                    <tr>
                      <th class="ps-4">Nombre</th>
                      <th>Comuna</th>
                      <th class="text-center">Estado</th>
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
                      <td><?php echo htmlspecialchars($barrio['nombrecomuna']); ?></td>
                      <td class="text-center">
                        <?php if ($barrio['estado'] === 'A'): ?>
                          <span class="badge bg-success-subtle text-success-emphasis">Activo</span>
                        <?php else: ?>
                          <span class="badge bg-secondary-subtle text-secondary-emphasis">Inactivo</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-center">
                        <?php if ($barrio['estado'] === 'A'): ?>
                          <a href="<?php echo getUrl('Parametros','Parametros','deleteBarrio',array('id'=>$barrio['id'])) ?>"
                             class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
                             onclick="return confirm('¿Seguro que deseas inhabilitar este barrio?')">
                            <i class="bi bi-slash-circle"></i>
                          </a>
                        <?php else: ?>
                          <a href="<?php echo getUrl('Parametros','Parametros','deleteBarrio',array('id'=>$barrio['id'])) ?>"
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

          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
  document.getElementById('buscadorComunas').addEventListener('keyup', function () {
    var filtro = this.value.toLowerCase();
    document.querySelectorAll('#tablaComunas tbody tr').forEach(function (fila) {
      fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
    });
  });

  document.getElementById('buscadorBarrios').addEventListener('keyup', function () {
    var filtro = this.value.toLowerCase();
    document.querySelectorAll('#tablaBarrios tbody tr').forEach(function (fila) {
      fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
    });
  });
</script>

<?php
  if(isset($_SESSION['error'])){
?>
<div class="row justify-content-center">
  <div class="col-xl-10">
    <div class="alert alert-danger d-flex align-items-center mt-3 mb-0" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <div><?php echo $_SESSION['error']; ?></div>
    </div>
  </div>
</div>
<?php
      unset($_SESSION['error']);
  }
  if(isset($_SESSION['exito'])){
?>
<div class="row justify-content-center">
  <div class="col-xl-10">
    <div class="alert alert-success d-flex align-items-center mt-3 mb-0" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>
      <div><?php echo $_SESSION['exito']; ?></div>
    </div>
  </div>
</div>
<?php
      unset($_SESSION['exito']);
  }
?>

<?php include_once __DIR__ . '/../partials/modalFormulario.php'; ?>