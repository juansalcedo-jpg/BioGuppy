<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-10">

      <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-semibold mb-1">Roles</h4>
          <p class="text-muted small mb-0">Consulta y administra los roles registrados en el sistema.</p>
        </div>
        <button type="button" class="btn btn-primary px-3"
          onclick="cargarFormularioModal('<?php echo getUrl('Roles', 'Roles', 'createRol') ?>', 'Registrar rol', 'rolFormRegistro', '<?php echo getUrl('Roles', 'Roles', 'listRol') ?>')">
          <i class="bi bi-plus-lg me-1"></i>Nuevo rol
        </button>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <span class="fw-semibold">
            <i class="bi bi-shield-lock-fill me-2 text-primary"></i>Roles registrados
          </span>
          <div class="input-group input-group-sm" style="max-width: 260px;">
            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
            <input type="text" id="buscadorRoles" class="form-control" placeholder="Buscar rol..."
              data-url="<?php echo getUrl('Roles', 'Roles', 'filtro', false, 'ajax'); ?>">
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-striped align-middle mb-0" id="tablaRoles">
            <thead class="table-dark">
              <tr>
                <th class="ps-4">Nombre del rol</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Editar</th>
                <th class="text-center">Eliminar</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $hayRoles = isset($resultrol) && $resultrol && $resultrol->rowCount() > 0;
              if ($hayRoles):
                while ($rol = $resultrol->fetch(PDO::FETCH_ASSOC)):
              ?>
                  <tr>
                    <td class="ps-4"><?php echo htmlspecialchars($rol['nombrerol']); ?></td>
                    <td class="text-center">
                      <?php if ($rol['estado'] === 'A'): ?>
                        <span class="badge bg-success-subtle text-success-emphasis">Activo</span>
                      <?php else: ?>
                        <span class="badge bg-secondary-subtle text-secondary-emphasis">Inactivo</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center">
                      <a href="<?php echo getUrl('Roles', 'Roles', 'editRol', ['id' => $rol['codrol']]) ?>"
                        class="btn btn-outline-primary btn-icon rounded-circle" title="Editar">
                        <i class="bi bi-pencil-fill"></i>
                      </a>
                    </td>
                    <td class="text-center">
                      <?php if ($rol['estado'] === 'A'): ?>
                        <a href="<?php echo getUrl('Roles', 'Roles', 'activacion', array('id' => $rol['codrol'], 'estado' => $rol['estado'])) ?>"
                          class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
                          onclick="return confirm('¿Seguro que deseas inhabilitar este tipo de tanque?')">
                          <i class="bi bi-slash-circle"></i>
                        </a>
                      <?php else: ?>
                        <a href="<?php echo getUrl('Roles', 'Roles', 'activacion', array('id' => $rol['codrol'], 'estado' => $rol['estado'])) ?>"
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
                    No hay roles registrados todavía.
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

<?php
if (isset($_SESSION['error'])) {
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
if (isset($_SESSION['exito'])) {
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

<script>
  document.getElementById('buscadorRoles').addEventListener('keyup', function() {
    var filtro = this.value.toLowerCase();
    document.querySelectorAll('#tablaRoles tbody tr').forEach(function(fila) {
      fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
    });
  });

  document.querySelectorAll('.btn-eliminar-rol').forEach(function(btn) {
    btn.addEventListener('click', function() {
      var id = this.dataset.id;
      if (confirm('¿Seguro que deseas eliminar este rol?')) {
        window.location.href = '<?php echo getUrl("Roles", "Roles", "deleteRol") ?>&id=' + id;
      }
    });
  });
</script>

<?php include_once __DIR__ . '/../partials/modalFormulario.php'; ?>