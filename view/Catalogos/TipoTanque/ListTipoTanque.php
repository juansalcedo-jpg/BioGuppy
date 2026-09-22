<div class="container-fluid py-2">
    <div class="row justify-content-center">
        <div class="col-xl-11">

            <!-- Encabezado -->
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
                <div>
                    <h4 class="fw-bold mb-1 text-dark">Tipo de Tanque</h4>
                    <p class="text-muted small mb-0">Consulta y administra los tipos de tanque registrados en el sistema.</p>
                </div>
                <button type="button" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm"
                    onclick="cargarFormularioModal('<?php echo getUrl('Catalogos', 'TipoTanque', 'createTipoTanque') ?>', 'Registrar tipo de tanque', 'tanqueFormRegistro', '<?php echo getUrl('Catalogos', 'TipoTanque', 'listTipoTanq') ?>', 'tablaTanques')">
                    <i class="bi bi-plus-lg me-1"></i> Nuevo tipo
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
                        <i class="bi bi-box-seam me-2 text-primary"></i>Tipos de tanque registrados
                    </span>
                    <div class="input-group input-group-sm" style="max-width: 260px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="buscadorTanques" class="form-control border-start-0 shadow-none bg-light" placeholder="Buscar tipo..."
                            data-url="<?php echo getUrl('Catalogos', 'TipoTanque', 'filtro', false, 'ajax'); ?>">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="tablaTanques">
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
                            $hayTanques = isset($tanques) && $tanques && $tanques->rowCount() > 0;
                            if ($hayTanques):
                                while ($tanque = $tanques->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                    <tr>
                                        <td class="ps-4 fw-semibold"><?php echo htmlspecialchars($tanque['nombretipotanque']); ?></td>
                                        <td class="text-center">
                                            <?php if ($tanque['estado'] === 'A'): ?>
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-primary btn-sm rounded-circle p-2 lh-1" title="Editar"
                                                onclick="cargarFormularioModal('<?php echo getUrl('Catalogos', 'TipoTanque', 'getUpdateTipoTanque', array('id' => $tanque['codtipotanque'])) ?>', 'Editar tipo de tanque', 'tanqueFormEdicion', '<?php echo getUrl('Catalogos', 'TipoTanque', 'listTipoTanq') ?>', 'tablaTanques')">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($tanque['estado'] === 'A'): ?>
                                                <a href="<?php echo getUrl('Catalogos', 'TipoTanque', 'activacion', array('id' => $tanque['codtipotanque'])) ?>"
                                                    class="btn btn-outline-danger btn-sm rounded-circle p-2 lh-1" title="Inhabilitar"
                                                    onclick="return confirm('¿Seguro que deseas inhabilitar este tipo de tanque?')">
                                                    <i class="bi bi-slash-circle"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo getUrl('Catalogos', 'TipoTanque', 'activacion', array('id' => $tanque['codtipotanque'])) ?>"
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
                                        No hay tipos de tanque registrados todavía.
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
        buscadorTan.addEventListener('keyup', function() {
            var filtro = this.value.toLowerCase();
            document.querySelectorAll('#tablaTanques tbody tr').forEach(function(fila) {
                fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
            });
        });
    }
</script>

<?php include_once __DIR__ . '/../../partials/modalFormulario.php'; ?>