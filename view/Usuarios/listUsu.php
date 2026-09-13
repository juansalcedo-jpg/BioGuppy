<?php $moduloActivoUsuariosRoles = 'usuarios'; ?>

<style>
  .table-usuarios thead th {
    background-color: #10254a;
    color: #fff;
    font-weight: 600;
    vertical-align: middle;
    white-space: nowrap;
  }
  .table-usuarios tbody tr:hover {
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
          <h4 class="fw-semibold mb-1">Usuarios</h4>
          <p class="text-muted small mb-0">Consulta y administra los usuarios registrados en el sistema.</p>
        </div>
        <button type="button" class="btn btn-primary px-3"
                onclick="cargarFormularioModal('<?php echo getUrl('Usuarios','Usuarios','createUsu') ?>', 'Registrar usuario', 'usuarioFormRegistro', '<?php echo getUrl('Usuarios','Usuarios','listUsu') ?>')"
          <i class="bi bi-plus-lg me-1"></i>Nuevo usuario
        </button>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <span class="fw-semibold">
            <i class="bi bi-people-fill me-2 text-primary"></i>Usuarios registrados
          </span>
          <div class="input-group input-group-sm" style="max-width: 260px;">
            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
            <input type="text" id="buscadorUsuarios" class="form-control" placeholder="Buscar usuario...">
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-usuarios table-striped align-middle mb-0" id="tablaUsuarios">
            <thead>
              <tr>
                <th class="ps-4">Nombre</th>
                <th>Apellido</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Documento</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Editar</th>
                <th class="text-center">Activar/Inactivar</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $hayUsuarios = isset($usuarios) && $usuarios && $usuarios->rowCount() > 0;
                if ($hayUsuarios):
                    while($usu = $usuarios->fetch(PDO::FETCH_ASSOC)):
              ?>
              <tr>
                <td class="ps-4"><?php echo htmlspecialchars($usu['nombreusuario']); ?></td>
                <td><?php echo htmlspecialchars($usu['apellidousuario']); ?></td>
                <td><?php echo htmlspecialchars($usu['correo']); ?></td>
                <td><span class="badge-rol rounded-pill"><?php echo htmlspecialchars($usu['nombrerol']); ?></span></td>
                <td class="text-muted"><?php echo htmlspecialchars($usu['nombredocumento']); ?> — <?php echo htmlspecialchars($usu['numerodocumento']); ?></td>
                <td class="text-center">
                  <?php if ($usu['estado'] === 'A'): ?>
                    <span class="badge bg-success-subtle text-success-emphasis">Activo</span>
                  <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Inactivo</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
                          onclick="cargarFormularioModal('<?php echo getUrl('Usuarios','Usuarios','getUpdateUsu',array('id'=>$usu['codusuario'])) ?>', 'Editar usuario', 'usuarioFormEdicion')">
                    <i class="bi bi-pencil-fill"></i>
                  </button>
                </td>
                <td class="text-center">
                  <?php if ($usu['estado'] === 'I'): ?>
                    <a href="<?php echo getUrl('Usuarios','Usuarios','activacion',array('id'=>$usu['codusuario'], 'estado'=>$usu['estado'])) ?>"
                       class="btn btn-outline-success btn-icon rounded-circle" title="Activar">
                      <i class="bi bi-check-lg"></i>
                    </a>
                  <?php else: ?>
                    <a href="<?php echo getUrl('Usuarios','Usuarios','activacion',array('id'=>$usu['codusuario'], 'estado'=>$usu['estado'])) ?>"
                       class="btn btn-outline-danger btn-icon rounded-circle" title="Inactivar">
                      <i class="bi bi-slash-circle"></i>
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
                  No hay usuarios registrados todavía.
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
  document.getElementById('buscadorUsuarios').addEventListener('keyup', function () {
      var filtro = this.value.toLowerCase();
      document.querySelectorAll('#tablaUsuarios tbody tr').forEach(function (fila) {
          fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
      });
  });
</script>

<?php include_once __DIR__ . '/../partials/modalFormulario.php'; ?>