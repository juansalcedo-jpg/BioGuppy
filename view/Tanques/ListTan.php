<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-10">

      <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-semibold mb-1">Tanques</h4>
          <p class="text-muted small mb-0">Consulta y administra los tanques registrados en el sistema.</p>
        </div>
        <button type="button" class="btn btn-primary px-3"
                onclick="cargarFormularioModal('<?php echo getUrl('Tanques','Tanques','create') ?>', 'Registrar Tanque', 'tanqueFormRegistro', '<?php echo getUrl('Tanques','Tanques','listTan') ?>', 'tablaTanques')">
          <i class="bi bi-plus-lg me-1"></i>Nuevo Tanques
        </button>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <span class="fw-semibold">
            <i class="bi bi-droplet-fill me-2 text-primary"></i>Tanques Registrados
          </span>
          <div class="input-group input-group-sm" style="max-width: 260px;">
            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
            <input type="text" id="buscadorTanques" class="form-control" placeholder="Buscar tanque..."
                  data-url="<?php echo getUrl('Tanques','Tanques','filtro', false, 'ajax'); ?>">
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-striped align-middle mb-0" id="tablaTanques">
            <thead class="table-dark">
              <tr>
                <th class="ps-4">Tanque</th>
                <th>Tipo</th>
                <th>Capacidad (L)</th>
                <th>Zoocriadero</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Editar</th>
                <th class="text-center">Inhabilitar</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $hayTanques = isset($tanques) && $tanques && $tanques->rowCount() > 0;
                if ($hayTanques):
                    while($tanque = $tanques->fetch(PDO::FETCH_ASSOC)):
              ?>
              <tr>
                <td class="ps-4"><?php echo htmlspecialchars($tanque['numero']); ?></td>
                <td><?php echo htmlspecialchars($tanque['tipo']); ?></td>
                <td><?php echo htmlspecialchars($tanque['capacidad']); ?></td>
                <td><?php echo htmlspecialchars($tanque['zoocriadero']); ?></td>
                <td class="text-center">
                  <?php if ($tanque['estado'] === 'Activo'): ?>
                    <span class="badge bg-success">Activo</span>
                  <?php else: ?>
                    <span class="badge bg-danger">Inactivo</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
                          onclick="cargarFormularioModal('<?php echo getUrl('Tanques','Tanques','getUpdate',array('id'=>$tanque['id'])) ?>', 'Editar Tanque', 'tanqueFormEdicion', '<?php echo getUrl('Tanques','Tanques','listTan') ?>', 'tablaTanques')">
                    <i class="bi bi-pencil-fill"></i>
                  </button>
                </td>
                <td class="text-center">
                  <?php if ($tanque['estado'] === 'Activo'): ?>
                    <a href="<?php echo getUrl('Tanques','Tanques','delete',array('id'=>$tanque['id'])) ?>"
                       class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
                       onclick="return confirm('¿Seguro que deseas inhabilitar el tanque <?php echo $tanque['numero']; ?>?')">
                      <i class="bi bi-slash-circle"></i>
                    </a>
                  <?php else: ?>
                    <a href="<?php echo getUrl('Tanques','Tanques','delete',array('id'=>$tanque['id'])) ?>"
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
                  No hay tanques registrados todavía.
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
  var buscadorTan = document.getElementById('buscadorTanques');
  if (buscadorTan) {
    buscadorTan.addEventListener('keyup', function () {
      var filtro = this.value.toLowerCase();
      document.querySelectorAll('#tablaTanques tbody tr').forEach(function (fila) {
        fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
      });
    });
  }
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