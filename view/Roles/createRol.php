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
    <div class="col-xl-9">

      <div class="mb-4">
        <h4 class="fw-semibold mb-1">Registrar rol</h4>
        <p class="text-muted small mb-0">Define un nuevo rol, su descripción y los permisos por módulo.</p>
      </div>

      <form action="<?php echo getUrl('Roles','Roles','postcreateRol')?>" method="post" novalidate>

        <!-- Información básica del rol -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white border-bottom py-3">
            <span class="fw-semibold">
              <i class="bi bi-person-badge-fill me-2 text-primary"></i>Información del rol
            </span>
          </div>
          <div class="card-body p-4">
            <div class="row">
              <div class="col-md-5 mb-4 mb-md-0">
                <label for="nombreRol" class="form-label fw-semibold">Nombre del rol</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-tag"></i></span>
                  <input type="text" class="form-control" id="nombreRol" name="nombreRol"
                         placeholder="Ej: Coordinador de campo" maxlength="100" required>
                </div>
                <div class="form-text">Nombre corto y descriptivo del rol.</div>
              </div>
              <div class="col-md-7">
                <label for="descripcionRol" class="form-label fw-semibold">Descripción</label>
                <textarea class="form-control" id="descripcionRol" name="descripcionRol" rows="2"
                          placeholder="Describa brevemente las funciones y alcance de este rol" maxlength="255"></textarea>
                <div class="form-text">Opcional. Máximo 255 caracteres.</div>
              </div>
            </div>
          </div>
        </div>

        <!-- Matriz de permisos -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
            <span class="fw-semibold">
              <i class="bi bi-shield-lock-fill me-2 text-primary"></i>Permisos por módulo
            </span>
            <span class="text-muted small">Marca las acciones permitidas para cada módulo</span>
          </div>

          <div class="table-responsive">
            <table class="table table-permisos mb-0">
              <thead>
                <tr>
                  <th class="text-start ps-4">Módulo</th>
                  <th>
                    Registrar<br>
                    <input type="checkbox" class="form-check-input check-permiso check-columna" data-columna="registrar">
                  </th>
                  <th>
                    Consultar<br>
                    <input type="checkbox" class="form-check-input check-permiso check-columna" data-columna="consultar">
                  </th>
                  <th>
                    Editar<br>
                    <input type="checkbox" class="form-check-input check-permiso check-columna" data-columna="editar">
                  </th>
                  <th>
                    Eliminar<br>
                    <input type="checkbox" class="form-check-input check-permiso check-columna" data-columna="eliminar">
                  </th>
                </tr>
              </thead>
              <tbody>
                <?php
                  $modulos = [
                      'Usuarios'           => 'bi-people-fill',
                      'Roles'              => 'bi-shield-lock-fill',
                      'Zoocriadero'        => 'bi-water',
                      'Trabajo de Terreno' => 'bi-geo-alt-fill',
                  ];
                  foreach ($modulos as $modulo => $icono) {
                      $slug = strtolower(str_replace(' ', '_', $modulo));
                ?>
                <tr>
                  <th class="text-start ps-4">
                    <i class="bi <?php echo $icono; ?> me-2 text-muted"></i><?php echo $modulo; ?>
                  </th>
                  <td>
                    <input type="checkbox" class="form-check-input check-permiso check-fila-<?php echo $slug; ?>"
                           data-fila="<?php echo $slug; ?>"
                           name="permisos[<?php echo $slug; ?>][registrar]" value="1">
                  </td>
                  <td>
                    <input type="checkbox" class="form-check-input check-permiso check-fila-<?php echo $slug; ?>"
                           data-fila="<?php echo $slug; ?>"
                           name="permisos[<?php echo $slug; ?>][consultar]" value="1">
                  </td>
                  <td>
                    <input type="checkbox" class="form-check-input check-permiso check-fila-<?php echo $slug; ?>"
                           data-fila="<?php echo $slug; ?>"
                           name="permisos[<?php echo $slug; ?>][editar]" value="1">
                  </td>
                  <td>
                    <input type="checkbox" class="form-check-input check-permiso check-fila-<?php echo $slug; ?>"
                           data-fila="<?php echo $slug; ?>"
                           name="permisos[<?php echo $slug; ?>][eliminar]" value="1">
                  </td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
          <a href="<?php echo getUrl('Roles','Roles','listRol')?>" class="btn btn-outline-secondary px-4">
            Cancelar
          </a>
          <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-1"></i>Registrar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<?php
  if(isset($_SESSION['error'])){
?>
<div class="row justify-content-center">
  <div class="col-xl-9">
    <div class="alert alert-danger d-flex align-items-center mt-3 mb-0" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <div><?php echo $_SESSION['error']; ?></div>
    </div>
  </div>
</div>
<?php
      unset($_SESSION['error']);
  }
?>

<script>
  // Marcar/desmarcar toda una columna (acción) para todos los módulos
  document.querySelectorAll('.check-columna').forEach(function (checkColumna) {
      checkColumna.addEventListener('change', function () {
          var columna = this.dataset.columna;
          document.querySelectorAll('input[name$="[' + columna + ']"]').forEach(function (input) {
              input.checked = checkColumna.checked;
          });
      });
  });
</script>