<div class="container-fluid py-2">
    <div class="row justify-content-center">
        <div class="col-xl-10">

            <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
                <div>
                    <h4 class="fw-semibold mb-1">Tipo de Tanque</h4>
                    <p class="text-muted small mb-0">Consulta y administra los tipos de tanque registrados en el sistema.</p>
                </div>
                <button type="button" class="btn btn-primary px-3"
                    onclick="cargarFormularioModal('<?php echo getUrl('Catalogos', 'TipoTanque', 'createTipoTanque') ?>', 'Registrar tipo de tanque', 'tanqueFormRegistro', '<?php echo getUrl('Catalogos', 'TipoTanque', 'listTipoTanq') ?>', 'tablaTanques')">
                    <i class="bi bi-plus-lg me-1"></i>Nuevo
                </button>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <span class="fw-semibold">
                        <i class="bi bi-box-seam me-2 text-primary"></i>Tipos de tanque registrados
                    </span>
                    <div class="input-group input-group-sm" style="max-width: 260px;">
                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                        <input type="text" id="buscadorTanques" class="form-control" placeholder="Buscar tipo..."
                            data-url="<?php echo getUrl('Catalogos', 'TipoTanque', 'filtro', false, 'ajax'); ?>">
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-striped align-middle mb-0" id="tablaTanques">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4">Nombre</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Editar</th>
                                <th class="text-center">Inhabilitar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $hayTanques = isset($tanques) && $tanques && $tanques->rowCount() > 0;
                            if ($hayTanques):
                                while ($tanque = $tanques->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                    <tr>
                                        <td class="ps-4"><?php echo htmlspecialchars($tanque['nombretipotanque']); ?></td>
                                        <td class="text-center">
                                            <?php if ($tanque['estado'] === 'A'): ?>
                                                <span class="badge bg-success-subtle text-success-emphasis">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary-subtle text-secondary-emphasis">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
                                                onclick="cargarFormularioModal('<?php echo getUrl('Catalogos', 'TipoTanque', 'getUpdateTipoTanque', array('id' => $tanque['codtipotanque'])) ?>', 'Editar tipo de tanque', 'tanqueFormEdicion', '<?php echo getUrl('Catalogos', 'TipoTanque', 'listTipoTanq') ?>', 'tablaTanques')">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($tanque['estado'] === 'A'): ?>
                                                <a href="<?php echo getUrl('Catalogos', 'TipoTanque', 'activacion', array('id' => $tanque['codtipotanque'])) ?>"
                                                    class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
                                                    onclick="return confirm('¿Seguro que deseas inhabilitar este tipo de tanque?')">
                                                    <i class="bi bi-eye-slash"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo getUrl('Catalogos', 'TipoTanque', 'activacion', array('id' => $tanque['codtipotanque'])) ?>"
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
    document.getElementById('buscadorTanques').addEventListener('keyup', function() {
        var filtro = this.value.toLowerCase();
        document.querySelectorAll('#tablaTanques tbody tr').forEach(function(fila) {
            fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
        });
    });
</script>

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

<?php include_once __DIR__ . '/../../partials/modalFormulario.php'; ?>