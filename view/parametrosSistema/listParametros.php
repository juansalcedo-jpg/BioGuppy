<?php
?>
<div class="container-fluid py-3">
  <!-- Cabecera compacta -->
  <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
    <div>
      <h4 class="fw-bold text-dark mb-1">Parámetros Generales del Sistema</h4>
      <p class="text-muted small mb-0">Administra los valores base usados en el sistema (comunas, barrios).</p>
    </div>
  </div>

  <!-- Alertas de sesión integradas -->
  <?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2 px-3 small mb-3" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $_SESSION['error']; ?>
      <button type="button" class="btn-close btn-sm py-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <?php if(isset($_SESSION['exito'])): ?>
    <div class="alert alert-success alert-dismissible fade show py-2 px-3 small mb-3" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i><?php echo $_SESSION['exito']; ?>
      <button type="button" class="btn-close btn-sm py-2" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['exito']); ?>
  <?php endif; ?>

  <!-- Contenedor principal idéntico al de auditoría -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom pt-3 pb-0 px-4">
      <ul class="nav nav-tabs card-header-tabs border-bottom-0" id="tabsParametros" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active fw-medium px-3" id="tab-comunas-btn" data-bs-toggle="tab" data-bs-target="#tab-comunas" type="button" role="tab">
            <i class="bi bi-geo-alt-fill me-1 text-primary"></i> Comunas
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link fw-medium px-3" id="tab-barrios-btn" data-bs-toggle="tab" data-bs-target="#tab-barrios" type="button" role="tab">
            <i class="bi bi-signpost-split-fill me-1 text-primary"></i> Barrios
          </button>
        </li>
      </ul>
    </div>

    <div class="card-body p-4">
      <div class="tab-content" id="tabsParametrosContent">

        <!-- Comunas -->
        <div class="tab-pane fade show active" id="tab-comunas" role="tabpanel">
          <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
            <span class="text-secondary small text-uppercase fw-bold tracking-wide">Listado de Comunas</span>
            <div class="d-flex align-items-center gap-2">
              <div class="input-group input-group-sm" style="width: 220px;">
                <span class="input-group-text bg-light border-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="buscadorComunas" class="form-control bg-light border-0" placeholder="Buscar comuna..."
                       data-url="<?php echo getUrl('Parametros','Parametros','filtroComuna', false, 'ajax'); ?>">
              </div>
              <button type="button" class="btn btn-primary btn-sm"
                      onclick="cargarFormularioModal('<?php echo getUrl('Parametros','Parametros','createComuna') ?>',
                      'Registrar comuna', 'comunaFormRegistro', '<?php echo getUrl('Parametros','Parametros','listParametros') ?>', 'tablaComunas')">
                <i class="bi bi-plus-lg me-1"></i>Nueva comuna
              </button>
            </div>
          </div>
          <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0" id="tablaComunas">
              <thead class="bg-white text-uppercase fs-7 text-secondary border-bottom sticky-top">
                <tr>
                  <th class="py-3 ps-3">Nombre</th>
                  <th class="py-3 text-center">Estado</th>
                  <th class="py-3 text-center" style="width: 80px;">Editar</th>
                  <th class="py-3 text-center pe-3" style="width: 80px;">Acción</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $hayComunas = isset($resultComunas) && $resultComunas && $resultComunas->rowCount() > 0;
                  if ($hayComunas):
                      while ($comuna = $resultComunas->fetch(PDO::FETCH_ASSOC)):
                ?>
                <tr class="border-bottom">
                  <td class="ps-3 py-2 fw-medium text-dark"><?php echo htmlspecialchars($comuna['nombrecomuna']); ?></td>
                  <td class="text-center py-2">
                    <?php if ($comuna['estado'] === 'A'): ?>
                      <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 fw-normal">Activo</span>
                    <?php else: ?>
                      <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 fw-normal">Inactivo</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center py-2">
                    <button type="button" class="btn btn-sm btn-light text-primary border-0" title="Editar"
                            onclick="cargarFormularioModal('<?php echo getUrl('Parametros','Parametros','getUpdateComuna',array('id'=>$comuna['id'])) ?>', 'Editar comuna', 'comunaFormEdicion', '<?php echo getUrl('Parametros','Parametros','listParametros') ?>', 'tablaComunas')">
                      <i class="bi bi-pencil-fill"></i>
                    </button>
                  </td>
                  <td class="text-center py-2 pe-3">
                    <?php if ($comuna['estado'] === 'A'): ?>
                      <a href="<?php echo getUrl('Parametros','Parametros','deleteComuna',array('id'=>$comuna['id'])) ?>"
                         class="btn btn-sm btn-light text-danger border-0" title="Inhabilitar"
                         onclick="return confirm('¿Seguro que deseas inhabilitar esta comuna?')">
                        <i class="bi bi-slash-circle"></i>
                      </a>
                    <?php else: ?>
                      <a href="<?php echo getUrl('Parametros','Parametros','deleteComuna',array('id'=>$comuna['id'])) ?>"
                         class="btn btn-sm btn-light text-success border-0" title="Activar">
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
                  <td colspan="4" class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-3 d-block mb-1 text-secondary opacity-50"></i>
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
            <span class="text-secondary small text-uppercase fw-bold tracking-wide">Listado de Barrios</span>
            <div class="d-flex align-items-center gap-2">
              <div class="input-group input-group-sm" style="width: 220px;">
                <span class="input-group-text bg-light border-0 text-muted"><i class="bi bi-search"></i></span>
                <input type="text" id="buscadorBarrios" class="form-control bg-light border-0" placeholder="Buscar barrio..."
                       data-url="<?php echo getUrl('Parametros','Parametros','filtroBarrio', false, 'ajax'); ?>">
              </div>
              <button type="button" class="btn btn-primary btn-sm"
                      onclick="cargarFormularioModal('<?php echo getUrl('Parametros','Parametros','createBarrio') ?>',
                      'Registrar barrio', 'barrioFormRegistro', '<?php echo getUrl('Parametros','Parametros','listParametros') ?>', 'tablaBarrios')">
                <i class="bi bi-plus-lg me-1"></i>Nuevo barrio
              </button>
            </div>
          </div>
          <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0" id="tablaBarrios">
              <thead class="bg-white text-uppercase fs-7 text-secondary border-bottom sticky-top">
                <tr>
                  <th class="py-3 ps-3">Nombre</th>
                  <th class="py-3">Comuna</th>
                  <th class="py-3 text-center">Estado</th>
                  <th class="py-3 text-center" style="width: 80px;">Editar</th>
                  <th class="py-3 text-center pe-3" style="width: 80px;">Acción</th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $hayBarrios = isset($resultBarrios) && $resultBarrios && $resultBarrios->rowCount() > 0;
                  if ($hayBarrios):
                      while ($barrio = $resultBarrios->fetch(PDO::FETCH_ASSOC)):
                ?>
                <tr class="border-bottom">
                  <td class="ps-3 py-2 fw-medium text-dark"><?php echo htmlspecialchars($barrio['nombrebarrio']); ?></td>
                  <td class="py-2 text-secondary"><?php echo htmlspecialchars($barrio['nombrecomuna']); ?></td>
                  <td class="text-center py-2">
                    <?php if ($barrio['estado'] === 'A'): ?>
                      <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 fw-normal">Activo</span>
                    <?php else: ?>
                      <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 fw-normal">Inactivo</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-center py-2">
                    <button type="button" class="btn btn-sm btn-light text-primary border-0" title="Editar"
                            onclick="cargarFormularioModal('<?php echo getUrl('Parametros','Parametros','getUpdateBarrio',array('id'=>$barrio['id'])) ?>', 'Editar barrio', 'barrioFormEdicion', '<?php echo getUrl('Parametros','Parametros','listParametros') ?>', 'tablaBarrios')">
                      <i class="bi bi-pencil-fill"></i>
                    </button>
                  </td>
                  <td class="text-center py-2 pe-3">
                    <?php if ($barrio['estado'] === 'A'): ?>
                      <a href="<?php echo getUrl('Parametros','Parametros','deleteBarrio',array('id'=>$barrio['id'])) ?>"
                         class="btn btn-sm btn-light text-danger border-0" title="Inhabilitar"
                         onclick="return confirm('¿Seguro que deseas inhabilitar este barrio?')">
                        <i class="bi bi-slash-circle"></i>
                      </a>
                    <?php else: ?>
                      <a href="<?php echo getUrl('Parametros','Parametros','deleteBarrio',array('id'=>$barrio['id'])) ?>"
                         class="btn btn-sm btn-light text-success border-0" title="Activar">
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
                  <td colspan="5" class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-3 d-block mb-1 text-secondary opacity-50"></i>
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

<?php include_once __DIR__ . '/../partials/modalFormulario.php'; ?>