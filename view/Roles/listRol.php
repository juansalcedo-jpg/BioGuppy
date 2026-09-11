<style>
  .table-roles thead th {
    background-color: #10254a;
    color: #fff;
    font-weight: 600;
    vertical-align: middle;
    white-space: nowrap;
  }
  .table-roles tbody tr:hover {
    background-color: #f4f7fb;
  }
  .badge-rol {
    background-color: rgba(34, 193, 164, 0.12);
    color: #17957f;
    font-weight: 600;
    padding: .4em .7em;
  }
  .btn-icon {
    width: 34px;
    height: 34px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
</style>

<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-10">

      <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-semibold mb-1">Roles</h4>
          <p class="text-muted small mb-0">Consulta y administra los roles registrados en el sistema.</p>
        </div>
        <a href="<?php echo getUrl('Roles','Roles','createRol')?>" class="btn btn-primary px-3">
          <i class="bi bi-plus-lg me-1"></i>Nuevo rol
        </a>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <span class="fw-semibold">
            <i class="bi bi-shield-lock-fill me-2 text-primary"></i>Roles registrados
          </span>
          <div class="input-group input-group-sm" style="max-width: 260px;">
            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
            <input type="text" id="buscadorRoles" class="form-control" placeholder="Buscar rol...">
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-roles table-striped align-middle mb-0" id="tablaRoles">
            <thead>
              <tr>
                <th class="ps-4">#</th>
                <th>Nombre del rol</th>
                <th>Descripción</th>
                <th class="text-center">Editar</th>
                <th class="text-center">Eliminar</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $hayRoles = isset($resultrol) && $resultrol && $resultrol->rowCount() > 0;
                if ($hayRoles):
                    $i = 1;
                    while ($rol = $resultrol->fetch(PDO::FETCH_ASSOC)):
              ?>
              <tr>
                <td class="ps-4 text-muted"><?php echo $i++; ?></td>
                <td>
                  <span class="badge-rol rounded-pill">
                    <?php echo htmlspecialchars($rol['nombrerol']); ?>
                  </span>
                </td>
                <td class="text-muted">
                  <?php echo !empty($rol['descripcionrol']) ? htmlspecialchars($rol['descripcionrol']) : '—'; ?>
                </td>
                <td class="text-center">
                  <a href="<?php echo getUrl('Roles','Roles','editRol', ['id' => $rol['codrol']]) ?>"
                     class="btn btn-outline-primary btn-icon rounded-circle" title="Editar">
                    <i class="bi bi-pencil-fill"></i>
                  </a>
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-outline-danger btn-icon rounded-circle btn-eliminar-rol"
                          data-id="<?php echo $rol['codrol']; ?>" title="Eliminar">
                    <i class="bi bi-trash-fill"></i>
                  </button>
                </td>
              </tr>
              <?php
                    endwhile;
                else:
              ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-5">
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

<script>
  // Filtro simple de búsqueda por nombre/descripción, sin recargar la página
  document.getElementById('buscadorRoles').addEventListener('keyup', function () {
      var filtro = this.value.toLowerCase();
      document.querySelectorAll('#tablaRoles tbody tr').forEach(function (fila) {
          fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
      });
  });

  // Confirmación antes de eliminar (el envío real se conecta más adelante con el controller)
  document.querySelectorAll('.btn-eliminar-rol').forEach(function (btn) {
      btn.addEventListener('click', function () {
          var id = this.dataset.id;
          if (confirm('¿Seguro que deseas eliminar este rol?')) {
              window.location.href = '<?php echo getUrl("Roles","Roles","deleteRol") ?>&id=' + id;
          }
      });
  });
</script>