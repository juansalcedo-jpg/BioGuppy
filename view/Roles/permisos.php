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
</style>

<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-10">

      <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-semibold mb-1">Permisos del rol: <?php echo htmlspecialchars($rol['nombrerol']); ?></h4>
          <p class="text-muted small mb-0">Marca qué puede hacer este rol en cada módulo (Consultar, Insertar, Editar, Eliminar).</p>
        </div>
        <a href="<?php echo getUrl('Roles','Roles','listRol') ?>" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-arrow-left me-1"></i>Volver a roles
        </a>
      </div>

      <?php
        if(isset($_SESSION['error'])){
      ?>
        <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
          <i class="bi bi-exclamation-triangle-fill me-2"></i>
          <div><?php echo $_SESSION['error']; ?></div>
        </div>
      <?php
          unset($_SESSION['error']);
        }
        if(isset($_SESSION['exito'])){
      ?>
        <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
          <i class="bi bi-check-circle-fill me-2"></i>
          <div><?php echo $_SESSION['exito']; ?></div>
        </div>
      <?php
          unset($_SESSION['exito']);
        }
      ?>

      <form action="<?php echo getUrl('Roles','Roles','postGuardarPermisos') ?>" method="post">
        <input type="hidden" name="codrol" value="<?php echo $rol['codrol']; ?>">

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white border-bottom py-3">
            <span class="fw-semibold">
              <i class="bi bi-shield-lock-fill me-2 text-primary"></i>Permisos por módulo
            </span>
          </div>

          <div class="table-responsive">
            <table class="table table-permisos mb-0">
              <thead>
                <tr>
                  <th class="text-start ps-4">Módulo</th>
                  <?php foreach ($permisosCols as $permiso): ?>
                    <th>
                      <?php echo htmlspecialchars($permiso['nombrepermiso']); ?><br>
                      <input type="checkbox" class="form-check-input check-permiso check-columna" data-columna="<?php echo $permiso['codpermiso']; ?>">
                    </th>
                  <?php endforeach; ?>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($matriz as $nombreModulo => $permisosPorModulo): ?>
                  <tr>
                    <th class="text-start ps-4">
                      <i class="bi bi-folder-fill me-2 text-muted"></i><?php echo htmlspecialchars($nombreModulo); ?>
                    </th>
                    <?php foreach ($permisosCols as $permiso): ?>
                      <?php $celda = $permisosPorModulo[$permiso['codpermiso']] ?? null; ?>
                      <td>
                        <?php if ($celda): ?>
                          <input type="checkbox"
                                 class="form-check-input check-permiso"
                                 data-columna="<?php echo $permiso['codpermiso']; ?>"
                                 name="acciones[]"
                                 value="<?php echo $celda['codaccion']; ?>"
                                 <?php echo $celda['seleccionado'] ? 'checked' : ''; ?>>
                        <?php else: ?>
                          &mdash;
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
          <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-1"></i>Guardar permisos
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<script>
  // Marcar/desmarcar toda una columna (permiso) para todos los módulos
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