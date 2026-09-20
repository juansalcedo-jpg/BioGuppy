<?php
?>
<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-10">

      <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-semibold mb-1">Zoocriaderos</h4>
          <p class="text-muted small mb-0">Consulta y administra los zoocriaderos registrados en el sistema.</p>
        </div>
        <button type="button" class="btn btn-primary px-3"
                onclick="cargarFormularioModal('<?php echo getUrl('Zoocriadero','Zoocriadero','create') ?>',
                'Registrar zoocriadero', 'zoocriaderoFormRegistro', '<?php echo getUrl('Zoocriadero','Zoocriadero','listZoo') ?>', 'tablaZoocriaderos')">
          <i class="bi bi-plus-lg me-1"></i>Nuevo zoocriadero
        </button>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <span class="fw-semibold">
            Zoocriaderos registrados
          </span>
          <div class="input-group input-group-sm" style="max-width: 260px;">
            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
            <input type="text" id="buscadorZoocriaderos" class="form-control" placeholder="Buscar zoocriadero..."
                  data-url="<?php echo getUrl('Zoocriadero','Zoocriadero','filtro', false, 'ajax'); ?>">
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-striped align-middle mb-0" id="tablaZoocriaderos">
            <thead class="table-dark">
              <tr>
                <th class="ps-4">Nombre</th>
                <th>Dirección</th>
                <th>Comuna</th>
                <th>Barrio</th>
                <th> Aux Encargado</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Editar</th>
                <th class="text-center">Inhabilitar</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $hayZoocriaderos = isset($zoocriaderos) && $zoocriaderos && $zoocriaderos->rowCount() > 0;
                if ($hayZoocriaderos):
                    while($zoo = $zoocriaderos->fetch(PDO::FETCH_ASSOC)):
              ?>
              <tr>
                <td class="ps-4"><?php echo htmlspecialchars($zoo['nombre']); ?></td>
                <td><?php echo htmlspecialchars($zoo['direccion']); ?></td>
                <td><?php echo htmlspecialchars($zoo['comuna']); ?></td>
                <td><?php echo htmlspecialchars($zoo['barrio']); ?></td>
                <td><?php echo htmlspecialchars($zoo['encargado']); ?></td>
                <td class="text-center">
                  <?php if ($zoo['estado'] === 'A'): ?>
                    <span class="badge bg-success">Activo</span>
                  <?php else: ?>
                    <span class="badge bg-danger">Inactivo</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
                          onclick="cargarFormularioModal('<?php echo getUrl('Zoocriadero','Zoocriadero','getUpdate',array('id'=>$zoo['id'])) ?>', 'Editar zoocriadero', 'zoocriaderoFormEdicion', '<?php echo getUrl('Zoocriadero','Zoocriadero','listZoo') ?>', 'tablaZoocriaderos')">
                    <i class="bi bi-pencil-fill"></i>
                  </button>
                </td>
                <td class="text-center">
                  <?php if ($zoo['estado'] === 'A'): ?>
                    <a href="<?php echo getUrl('Zoocriadero','Zoocriadero','delete',array('id'=>$zoo['id'])) ?>"
                       class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
                       onclick="return confirm('¿Seguro que deseas inhabilitar el zoocriadero <?php echo htmlspecialchars($zoo['nombre']); ?>?')">
                      <i class="bi bi-slash-circle"></i>
                    </a>
                  <?php else: ?>
                    <a href="<?php echo getUrl('Zoocriadero','Zoocriadero','delete',array('id'=>$zoo['id'])) ?>"
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
                <td colspan="8" class="text-center text-muted py-5">
                  <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                  No hay zoocriaderos registrados todavía.
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

<!-- filtra las filas ya cargadas en la tabla sin recargar la pagina -->
<script>
  var buscadorZoo = document.getElementById('buscadorZoocriaderos');
  if (buscadorZoo) {
    buscadorZoo.addEventListener('keyup', function () {
      var filtro = this.value.toLowerCase();
      document.querySelectorAll('#tablaZoocriaderos tbody tr').forEach(function (fila) {
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