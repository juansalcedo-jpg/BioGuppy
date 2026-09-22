<div class="container-fluid py-3">
  <div class="row justify-content-center">
    <div class="col-xl-11">

      <!-- Alertas de sesión (Éxito / Error) -->
      <?php if (isset($_SESSION['exito'])): ?>
          <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-4 border-0 shadow-sm rounded-3 bg-success-subtle text-success-emphasis" role="alert">
              <div class="d-flex align-items-center">
                  <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                  <div><?php echo $_SESSION['exito']; ?></div>
              </div>
              <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <?php unset($_SESSION['exito']); ?>
      <?php endif; ?>

      <?php if (isset($_SESSION['error'])): ?>
          <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-4 border-0 shadow-sm rounded-3 bg-danger-subtle text-danger-emphasis" role="alert">
              <div class="d-flex align-items-center">
                  <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                  <div><?php echo $_SESSION['error']; ?></div>
              </div>
              <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
          <?php unset($_SESSION['error']); ?>
      <?php endif; ?>

      <!-- Cabecera de la sección -->
      <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-bold text-dark mb-1">Sitios de Terreno</h4>
          <p class="text-muted small mb-0">Sitios registrados donde se realizan actividades de inspección, siembra, seguimiento y resiembra.</p>
        </div>
        <button type="button" class="btn btn-primary px-3 py-2 rounded-3 shadow-sm fw-semibold d-flex align-items-center"
                onclick="cargarFormularioModal('<?php echo getUrl('Sitios','Sitios','createSit') ?>', 'Registrar Sitio de Terreno', 'sitioFormRegistro', '<?php echo getUrl('Sitios','Sitios','listSit') ?>', 'tablaSitios')">
          <i class="bi bi-plus-lg me-1"></i> Nuevo sitio
        </button>
      </div>

      <!-- Tarjeta contenedora de la tabla -->
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
          <span class="fw-semibold text-secondary d-flex align-items-center">
            <i class="bi bi-house-door me-2 text-primary fs-5"></i>Sitios registrados
          </span>
          <div class="input-group input-group-sm shadow-sm rounded-3 overflow-hidden border bg-white" style="max-width: 260px;">
            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-search"></i></span>
            <input type="text" id="buscadorSitios" class="form-control border-0 bg-white py-2 shadow-none" placeholder="Buscar sitio..."
                  data-url="<?php echo getUrl('Sitios','Sitios','filtro', false, 'ajax'); ?>">
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="tablaSitios">
            <thead class="table-light text-uppercase fs-7 text-secondary">
              <tr>
                <th class="ps-4 py-3">Nombre</th>
                <th class="py-3">Comuna</th>
                <th class="py-3">Barrio</th>
                <th class="py-3">Tipo de depósito</th>
                <th class="py-3">Dirección</th>
                <th class="text-center py-3">Estado</th>
                <th class="text-center py-3">Editar</th>
                <th class="text-center py-3">Acción</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $haySitios = isset($sitios) && count($sitios) > 0;
                if ($haySitios):
                    foreach ($sitios as $sitio):
              ?>
              <tr>
                <td class="ps-4 fw-semibold text-dark py-3"><?php echo htmlspecialchars($sitio['nombresitio']); ?></td>
                <td class="py-3 text-muted"><?php echo htmlspecialchars($sitio['comuna']); ?></td>
                <td class="py-3 text-muted"><?php echo htmlspecialchars($sitio['barrio']); ?></td>
                <td class="py-3 text-muted"><?php echo htmlspecialchars($sitio['tipodeposito']); ?></td>
                <td class="py-3 text-muted"><?php echo htmlspecialchars($sitio['direccion']); ?></td>
                <td class="text-center py-3">
                  <?php if ($sitio['estado'] === 'A'): ?>
                    <span class="badge bg-success-subtle text-success-emphasis px-3 py-1 rounded-pill fw-semibold">Activo</span>
                  <?php else: ?>
                    <span class="badge bg-danger-subtle text-danger-emphasis px-3 py-1 rounded-pill fw-semibold">Inactivo</span>
                  <?php endif; ?>
                </td>
                <td class="text-center py-3">
                  <button type="button" class="btn btn-light btn-sm text-primary rounded-circle shadow-sm p-2" title="Editar"
                          onclick="cargarFormularioModal('<?php echo getUrl('Sitios','Sitios','editSit',array('id'=>$sitio['id'])) ?>', 'Editar Sitio de Terreno', 'sitioFormEdicion', '<?php echo getUrl('Sitios','Sitios','listSit') ?>', 'tablaSitios')">
                    <i class="bi bi-pencil-fill"></i>
                  </button>
                </td>
                <td class="text-center py-3">
                  <?php if ($sitio['estado'] === 'A'): ?>
                    <a href="<?php echo getUrl('Sitios','Sitios','delete',array('id'=>$sitio['id'])) ?>"
                       class="btn btn-light btn-sm text-danger rounded-circle shadow-sm p-2" title="Inhabilitar"
                       onclick="return confirm('¿Seguro que deseas inhabilitar el sitio <?php echo htmlspecialchars($sitio['nombresitio']); ?>?')">
                      <i class="bi bi-slash-circle"></i>
                    </a>
                  <?php else: ?>
                    <a href="<?php echo getUrl('Sitios','Sitios','delete',array('id'=>$sitio['id'])) ?>"
                       class="btn btn-light btn-sm text-success rounded-circle shadow-sm p-2" title="Activar">
                      <i class="bi bi-check-lg"></i>
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
              <?php
                    endforeach;
                else:
              ?>
              <tr>
                <td colspan="8" class="text-center text-muted py-5">
                  <div class="my-3">
                      <i class="bi bi-inbox fs-1 text-muted opacity-50 d-block mb-2"></i>
                      <span class="fs-6">No hay sitios registrados todavía.</span>
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
  var buscadorSit = document.getElementById('buscadorSitios');
  if (buscadorSit) {
    buscadorSit.addEventListener('keyup', function () {
      var filtro = this.value.toLowerCase();
      document.querySelectorAll('#tablaSitios tbody tr').forEach(function (fila) {
        if (fila.querySelector('td').getAttribute('colspan')) return;
        fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
      });
    });
  }
</script>

<style>
  .fs-7 {
    font-size: 0.75rem;
    letter-spacing: 0.05em;
  }
</style>

<?php include_once __DIR__ . '/../partials/modalFormulario.php'; ?>