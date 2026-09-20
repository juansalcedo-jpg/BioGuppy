<?php
?>
<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-11">

      <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-semibold mb-1">Sitios de Terreno</h4>
          <p class="text-muted small mb-0">Sitios registrados donde se realizan actividades de inspección, siembra, seguimiento y resiembra.</p>
        </div>
        <button type="button" class="btn btn-primary px-3"
                onclick="cargarFormularioModal('<?php echo getUrl('Sitios','Sitios','createSit') ?>',
                'Registrar sitio de terreno', 'sitioFormRegistro', '<?php echo getUrl('Sitios','Sitios','listSit') ?>', 'tablaSitios')">
          <i class="bi bi-plus-lg me-1"></i>Nuevo sitio
        </button>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <span class="fw-semibold">
            <i class="bi bi-house-door me-2 text-primary"></i>Sitios registrados
          </span>
          <div class="input-group input-group-sm w-50">
            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
            <input type="text" id="buscadorSitios" class="form-control" placeholder="Buscar sitio..."
                  data-url="<?php echo getUrl('Sitios','Sitios','filtro', false, 'ajax'); ?>">
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-striped align-middle mb-0" id="tablaSitios">
            <thead class="table-dark">
              <tr>
                <th class="ps-4">Nombre</th>
                <th>Comuna</th>
                <th>Barrio</th>
                <th>Tipo de depósito</th>
                <th>Dirección</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Editar</th>
                <th class="text-center">Inhabilitar</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $haySitios = isset($sitios) && count($sitios) > 0;
                if ($haySitios):
                    foreach ($sitios as $sitio):
              ?>
              <tr>
                <td class="ps-4"><?php echo htmlspecialchars($sitio['nombresitio']); ?></td>
                <td><?php echo htmlspecialchars($sitio['comuna']); ?></td>
                <td><?php echo htmlspecialchars($sitio['barrio']); ?></td>
                <td><?php echo htmlspecialchars($sitio['tipodeposito']); ?></td>
                <td><?php echo htmlspecialchars($sitio['direccion']); ?></td>
                <td class="text-center">
                  <?php if ($sitio['estado'] === 'A'): ?>
                    <span class="badge bg-success">Activo</span>
                  <?php else: ?>
                    <span class="badge bg-danger">Inactivo</span>
                  <?php endif; ?>
                </td>
                                <td class="text-center">
                  <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
                          onclick="cargarFormularioModal('<?php echo getUrl('Sitios','Sitios','editSit',array('id'=>$sitio['id'])) ?>', 'Editar sitio de terreno', 'sitioFormEdicion', '<?php echo getUrl('Sitios','Sitios','listSit') ?>', 'tablaSitios')">
                    <i class="bi bi-pencil-fill"></i>
                  </button>
                </td>
                <td class="text-center">
                  <?php if ($sitio['estado'] === 'A'): ?>
                    <a href="<?php echo getUrl('Sitios','Sitios','delete',array('id'=>$sitio['id'])) ?>"
                       class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar">
                      <i class="bi bi-slash-circle"></i>
                    </a>
                  <?php else: ?>
                    <a href="<?php echo getUrl('Sitios','Sitios','delete',array('id'=>$sitio['id'])) ?>"
                       class="btn btn-outline-success btn-icon rounded-circle" title="Activar">
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
                  <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                  No hay sitios registrados todavía.
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
        fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
      });
    });
  }
</script>
<?php
  if(isset($_SESSION['error'])){
?>
<div class="row justify-content-center">
  <div class="col-xl-11">
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
  <div class="col-xl-11">
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