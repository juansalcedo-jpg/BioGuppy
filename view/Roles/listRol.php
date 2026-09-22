<div class="container-fluid py-3">
  <!-- Cabecera superior independiente estilo tarjeta -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3 p-4">
      <div>
        <h4 class="fw-bold text-dark mb-1">Roles</h4>
        <p class="text-muted mb-0">Consulta y administra los roles registrados en el sistema.</p>
      </div>
      <button type="button" class="btn btn-primary px-4 py-2 shadow-sm"
        onclick="cargarFormularioModal('<?php echo getUrl('Roles', 'Roles', 'createRol') ?>', 'Registrar rol', 'rolFormRegistro', '<?php echo getUrl('Roles', 'Roles', 'listRol') ?>')">
        <i class="bi bi-shield-plus me-2"></i>Nuevo rol
      </button>
    </div>
  </div>

  <!-- Contenedor principal de la tabla -->
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div class="input-group" style="max-width: 320px;">
        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
        <input type="text" id="buscadorRoles" class="form-control border-start-0 ps-0 shadow-none" placeholder="Buscar rol..."
          data-url="<?php echo getUrl('Roles', 'Roles', 'filtro', false, 'ajax'); ?>">
      </div>
      <span class="text-muted small">Listado general</span>
    </div>

    <div class="card-body px-0 pb-0">
      <div class="table-responsive">
        <table class="table align-middle mb-0" id="tablaRoles">
          <thead class="bg-light text-secondary text-uppercase fs-7 border-top border-bottom">
            <tr>
              <th class="py-3 ps-4">Nombre del rol</th>
              <th class="py-3 text-center">Estado</th>
              <th class="py-3 text-center">Permisos</th>
              <th class="py-3 text-end pe-4">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $hayRoles = isset($resultrol) && $resultrol && $resultrol->rowCount() > 0;
            if ($hayRoles):
              while ($rol = $resultrol->fetch(PDO::FETCH_ASSOC)):
            ?>
                <tr class="border-bottom">
                  <td class="ps-4 py-3">
                    <span class="fw-bold text-dark d-block"><?php echo htmlspecialchars($rol['nombrerol']); ?></span>
                  </td>
                  
                  <td class="text-center py-3">
                    <?php if ($rol['estado'] === 'A'): ?>
                      <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">● Activo</span>
                    <?php else: ?>
                      <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">● Inactivo</span>
                    <?php endif; ?>
                  </td>

                  <!-- Columna separada para Permisos con azul primario/oscuro -->
                  <td class="text-center py-3">
                    <a href="<?php echo getUrl('Roles', 'Roles', 'permisos', ['id' => $rol['codrol']]) ?>"
                      class="badge bg-primary bg-opacity-10 text-primary text-decoration-none px-3 py-2 fw-semibold" title="Gestionar permisos">
                      <i class="bi bi-key-fill me-1"></i> Permisos
                    </a>
                  </td>
                  
                  <!-- Acciones de edición y estado -->
                  <td class="text-end pe-4 py-3">
                    <div class="btn-group shadow-sm" role="group">
                      <!-- Botón Editar -->
                      <button type="button" class="btn btn-sm btn-light border text-primary px-2" title="Editar"
                        onclick="cargarFormularioModal('<?php echo getUrl('Roles', 'Roles', 'editRol', ['id' => $rol['codrol']]) ?>', 'Editar rol', 'rolFormEdicion', '<?php echo getUrl('Roles', 'Roles', 'listRol') ?>', 'tablaRoles')">
                        <i class="bi bi-pencil"></i>
                      </button>
                      
                      <!-- Botón Activar / Inhabilitar -->
                      <?php if ($rol['estado'] === 'A'): ?>
                        <a href="<?php echo getUrl('Roles', 'Roles', 'activacion', array('id' => $rol['codrol'], 'estado' => $rol['estado'])) ?>"
                          class="btn btn-sm btn-light border text-danger px-2" title="Inhabilitar"
                          onclick="return confirm('¿Seguro que deseas inhabilitar este rol?')">
                          <i class="bi bi-slash-circle"></i>
                        </a>
                      <?php else: ?>
                        <a href="<?php echo getUrl('Roles', 'Roles', 'activacion', array('id' => $rol['codrol'], 'estado' => $rol['estado'])) ?>"
                          class="btn btn-sm btn-light border text-success px-2" title="Activar">
                          <i class="bi bi-check-lg"></i>
                        </a>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php
              endwhile;
            else:
              ?>
              <tr>
                <td colspan="4" class="text-center text-muted py-5">
                  <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
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

<!-- Alertas de sesión -->
<?php
if (isset($_SESSION['error'])) {
  echo '<div class="row justify-content-center"><div class="col-xl-12"><div class="alert alert-danger mt-3 mb-0 shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i>' . $_SESSION['error'] . '</div></div></div>';
  unset($_SESSION['error']);
}
if (isset($_SESSION['exito'])) {
  echo '<div class="row justify-content-center"><div class="col-xl-12"><div class="alert alert-success mt-3 mb-0 shadow-sm"><i class="bi bi-check-circle-fill me-2"></i>' . $_SESSION['exito'] . '</div></div></div>';
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
</script>

<?php include_once __DIR__ . '/../partials/modalFormulario.php'; ?>