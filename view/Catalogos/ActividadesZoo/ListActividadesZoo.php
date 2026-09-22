<div class="container-fluid py-2">
    <div class="row justify-content-center">
        <div class="col-xl-11">

            <!-- Encabezado -->
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Actividades de Zoocriadero</h4>
                    <p class="text-muted small mb-0">Consulta y administra los tipos de actividad de zoocriadero registrados en el sistema.</p>
                </div>
                <button type="button" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm"
                    onclick="cargarFormularioModal('<?php echo getUrl('Catalogos', 'ActividadesZoo', 'createActZoo') ?>', 'Registrar actividad de zoocriadero', 'actZooFormRegistro', '<?php echo getUrl('Catalogos', 'ActividadesZoo', 'listActZoo') ?>', 'tablaActZoo')">
                    <i class="bi bi-plus-lg me-1"></i> Nueva actividad
                </button>
            </div>

            <!-- Alertas de sesión (Error / Éxito) -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3 border-0 shadow-sm rounded-3 bg-danger-subtle text-danger-emphasis" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                        <div><?php echo $_SESSION['error']; ?></div>
                    </div>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['exito'])): ?>
                <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3 border-0 shadow-sm rounded-3 bg-success-subtle text-success-emphasis" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
                        <div><?php echo $_SESSION['exito']; ?></div>
                    </div>
                    <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['exito']); ?>
            <?php endif; ?>

            <!-- Tabla de Contenido -->
            <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="fw-semibold text-secondary">
                        <i class="bi bi-water me-2 text-primary"></i>Actividades de zoocriadero registradas
                    </span>
                    <div class="input-group input-group-sm" style="max-width: 260px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="buscadorActZoo" class="form-control border-start-0 shadow-none bg-light" placeholder="Buscar actividad..."
                            data-url="<?php echo getUrl('Catalogos', 'ActividadesZoo', 'filtro', false, 'ajax'); ?>">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tablaActZoo">
                        <thead class="table-light text-secondary text-uppercase fs-7">
                            <tr>
                                <th class="ps-4 py-3">Nombre</th>
                                <th class="text-center py-3">Estado</th>
                                <th class="text-center py-3">Editar</th>
                                <th class="text-center py-3">Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $hayActividades = isset($actividades) && $actividades && $actividades->rowCount() > 0;
                            if ($hayActividades):
                                while ($actividad = $actividades->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                    <tr>
                                        <td class="ps-4 fw-semibold"><?php echo htmlspecialchars($actividad['nombreactividad']); ?></td>
                                        <td class="text-center">
                                            <?php if ($actividad['estado'] === 'A'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-primary btn-sm rounded-circle p-2 lh-1" title="Editar"
                                                onclick="cargarFormularioModal('<?php echo getUrl('Catalogos', 'ActividadesZoo', 'getUpdateActZoo', array('id' => $actividad['codtipoactividad'])) ?>', 'Editar actividad de zoocriadero', 'actZooFormEdicion', '<?php echo getUrl('Catalogos', 'ActividadesZoo', 'listActZoo') ?>', 'tablaActZoo')">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($actividad['estado'] === 'A'): ?>
                                                <a href="<?php echo getUrl('Catalogos', 'ActividadesZoo', 'activacion', array('id' => $actividad['codtipoactividad'])) ?>"
                                                    class="btn btn-outline-danger btn-sm rounded-circle p-2 lh-1" title="Inhabilitar"
                                                    onclick="return confirm('¿Seguro que deseas inhabilitar esta actividad de zoocriadero?')">
                                                    <i class="bi bi-slash-circle"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo getUrl('Catalogos', 'ActividadesZoo', 'activacion', array('id' => $actividad['codtipoactividad'])) ?>"
                                                    class="btn btn-outline-success btn-sm rounded-circle p-2 lh-1" title="Activar">
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
                                        <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                        No hay actividades de zoocriadero registradas todavía.
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
    var buscadorAct = document.getElementById('buscadorActZoo');
    if (buscadorAct) {
        buscadorAct.addEventListener('keyup', function() {
            var filtro = this.value.toLowerCase();
            document.querySelectorAll('#tablaActZoo tbody tr').forEach(function(fila) {
                fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
            });
        });
    }
</script>

<style>
  .fs-7 {
    font-size: 0.75rem;
  }
</style>

<?php include_once __DIR__ . '/../../partials/modalFormulario.php'; ?>