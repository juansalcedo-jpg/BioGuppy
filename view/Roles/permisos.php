<style>
  .table-permisos thead th {
    background-color: #10254a;
    color: #fff;
    font-weight: 600;
    vertical-align: middle;
    white-space: nowrap;
  }
  .table-permisos tbody th {
    background-color: #f4f7fb;
    font-weight: 600;
    vertical-align: middle;
    white-space: nowrap;
  }
  .table-permisos td {
    text-align: center;
    vertical-align: middle;
  }
  .form-check-input.check-permiso {
    width: 1.2em;
    height: 1.2em;
    cursor: pointer;
  }
  .form-check-input.check-permiso:checked {
    background-color: #22c1a4;
    border-color: #22c1a4;
  }
  /* Estilo específico para los checkboxes de la cabecera (sobre fondo oscuro) */
  .table-permisos thead .check-columna {
    background-color: rgba(255, 255, 255, 0.1);
    border-color: rgba(255, 255, 255, 0.5);
  }
  .table-permisos thead .check-columna:checked {
    background-color: #22c1a4;
    border-color: #22c1a4;
  }
</style>

<div class="container-fluid px-1 py-2">
  <!-- Encabezado y botón volver -->
  <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
    <div>
      <h4 class="fw-semibold mb-1">Permisos del rol: <?php echo htmlspecialchars($rol['nombrerol']); ?></h4>
      <p class="text-muted small mb-0">Marca qué puede hacer este rol en cada módulo (Consultar, Insertar, Editar, Eliminar).</p>
    </div>
    <a href="<?php echo getUrl('Roles','Roles','listRol') ?>" class="btn btn-outline-secondary btn-sm px-3 rounded-3">
      <i class="bi bi-arrow-left me-1"></i> Volver a roles
    </a>
  </div>

  <!-- Alerta de error -->
  <?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3 border-0 shadow-sm rounded-3 bg-danger-subtle text-danger-emphasis" role="alert">
      <div class="d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
        <div><?php echo $_SESSION['error']; ?></div>
      </div>
      <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <!-- Alerta de éxito -->
  <?php if(isset($_SESSION['exito'])): ?>
    <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3 border-0 shadow-sm rounded-3 bg-success-subtle text-success-emphasis" role="alert">
      <div class="d-flex align-items-center">
        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
        <div><?php echo $_SESSION['exito']; ?></div>
      </div>
      <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['exito']); ?>
  <?php endif; ?>

  <form action="<?php echo getUrl('Roles','Roles','postGuardarPermisos') ?>" method="post">
    <input type="hidden" name="codrol" value="<?php echo $rol['codrol']; ?>">

    <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
      <div class="card-header bg-white border-bottom py-3 px-4">
        <span class="fw-semibold text-secondary">
          <i class="bi bi-shield-lock-fill me-2 text-primary"></i> Permisos por módulo
        </span>
      </div>

      <div class="table-responsive">
        <table class="table table-permisos mb-0 align-middle">
          <thead>
            <tr>
              <th class="text-start ps-4 py-3">Módulo</th>
              <?php foreach ($permisosCols as $permiso): ?>
                <th class="py-3 text-center">
                  <div class="mb-1"><?php echo htmlspecialchars($permiso['nombrepermiso']); ?></div>
                  <input type="checkbox" class="form-check-input check-permiso check-columna shadow-none mx-auto" data-columna="<?php echo $permiso['codpermiso']; ?>" title="Seleccionar columna">
                </th>
              <?php endforeach; ?>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($matriz as $nombreModulo => $permisosPorModulo): ?>
              <tr>
                <th class="text-start ps-4 py-3">
                  <i class="bi bi-folder-fill me-2 text-muted"></i><?php echo htmlspecialchars($nombreModulo); ?>
                </th>
                <?php foreach ($permisosCols as $permiso): ?>
                  <?php $celda = $permisosPorModulo[$permiso['codpermiso']] ?? null; ?>
                  <td class="py-3">
                    <?php if ($celda): ?>
                      <input type="checkbox"
                             class="form-check-input check-permiso shadow-none"
                             data-columna="<?php echo $permiso['codpermiso']; ?>"
                             name="acciones[]"
                             value="<?php echo $celda['codaccion']; ?>"
                             <?php echo $celda['seleccionado'] ? 'checked' : ''; ?>>
                    <?php else: ?>
                      <span class="text-muted">&mdash;</span>
                    <?php endif; ?>
                  </td>
                <?php endforeach; ?>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
      <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
        <i class="bi bi-check-lg me-1"></i> Guardar permisos
      </button>
    </div>

  </form>
</div>

<script>
  document.querySelectorAll('.check-columna').forEach(function (checkColumna) {
      checkColumna.addEventListener('change', function () {
          var columna = this.dataset.columna;
          document.querySelectorAll('input.form-check-input.check-permiso[data-columna="' + columna + '"]').forEach(function (input) {
              if (!input.classList.contains('check-columna')) {
                  input.checked = checkColumna.checked;
              }
          });
      });
  });
</script>