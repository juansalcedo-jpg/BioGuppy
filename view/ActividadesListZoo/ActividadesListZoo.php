<div class="container-fluid py-2">
    <div class="row justify-content-center">
        <div class="col-xl-11">

            <!-- ENCABEZADO DE LA VISTA -->
            <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
                <div>
                    <h4 class="fw-semibold mb-1">Mis actividades — Terreno</h4>
                    <p class="text-muted small mb-0">Consulta y filtra las actividades de terreno que has registrado.</p>
                </div>
            </div>

            <!-- TARJETA CONTENEDORA -->
            <div class="card border-0 shadow-sm">

                <!-- ENCABEZADO DE TARJETA CON FILTROS -->
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-file-earmark-text text-primary me-2 fs-5"></i>
                        <span class="fw-semibold">Actividades registradas por mí</span>
                    </div>

                    <!-- FORMULARIO DE FILTROS -->
                    <form id="formFiltroMisActividadesTer" action="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'filtro', false, 'ajax'); ?>" method="POST">
                        <div class="row g-2 align-items-end">

                            <!-- Filtro por MES (no por dia): el documento del proyecto no
                   especifica que tan preciso debe ser el filtro de fecha,
                   asi que se simplifico a un solo selector de mes/año en
                   vez de pedir fecha "desde" y "hasta" por separado. -->
                            <div class="col-6 col-md-2">
                                <label for="mesFiltro" class="form-label small text-muted mb-1">Mes</label>
                                <input type="month" id="mesFiltro" name="mes" class="form-control form-control-sm">
                            </div>

                            <div class="col-12 col-md-3">
                                <label for="selectzoocriadero" class="form-label small text-muted mb-1">Zoocriadero</label>
                                <select id="selectzoocriadero" name="codzoocriadero" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                    <?php if (isset($zoocriaderos) && $zoocriaderos): ?>
                                        <?php while ($dep = $zoocriaderos->fetch(PDO::FETCH_ASSOC)): ?>
                                            <option value="<?php echo $dep['id']; ?>"><?php echo htmlspecialchars($dep['tipozoocriadero'] . ' — ' . $dep['nombresitio']); ?></option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="col-12 col-md-3">
                                <label for="selectTipoActividad" class="form-label small text-muted mb-1">Tipo de actividad</label>
                                <select id="selectTipoActividad" name="tipoactividad" class="form-select form-select-sm">
                                    <option value="">Todos</option>
                                </select>
                            </div>

                            <div class="col-12 col-md-2 d-grid">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-funnel me-1"></i>Filtrar
                                </button>
                            </div>

                        </div>
                    </form>
                </div>

                <!-- TABLA DE RESULTADOS -->
                <div class="table-responsive">
                    <table class="table table-striped align-middle mb-0" id="tablaMisActividadesTer">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-4">Fecha</th>
                                <th>Tipo de actividad</th>
                                <th>Zoocriadero</th>
                                <th>Tanque</th>
                                <th class="text-center">Estado</th>
                                <th class="text-center">Editar</th>
                                <th class="text-center">Inhabilitar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $hayActividades = isset($actividades) && $actividades && $actividades->rowCount() > 0;
                            if ($hayActividades):
                                while ($act = $actividades->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                    <tr>
                                        <td class="ps-4"><?php echo htmlspecialchars($act['fecha']); ?></td>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($act['tipo_actividad']); ?></td>
                                        <td><?php echo htmlspecialchars($act['zoocriadero']); ?></td>
                                        <td><span class="text-muted small"><?php echo htmlspecialchars($act['sitio']); ?></span></td>
                                        <td class="text-center">
                                            <?php if ($act['estado'] === 'A'): ?>
                                                <span class="badge bg-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
                                                onclick="cargarFormularioModal('<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'getUpdate', array('id' => $act['id'])) ?>', 'Editar actividad', 'actividadTerFormEdicion', '<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'listMisActividades') ?>', 'tablaMisActividadesTer')">
                                                <i class="bi bi-pencil-fill"></i>
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <!-- Mismo patron rojo/verde de Usuarios y zoocriaderos, sin confirm(). -->
                                            <?php if ($act['estado'] === 'A'): ?>
                                                <a href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'delete', array('id' => $act['id'])) ?>"
                                                    class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar">
                                                    <i class="bi bi-slash-circle"></i>
                                                </a>
                                            <?php else: ?>
                                                <a href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'delete', array('id' => $act['id'])) ?>"
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
                                    <td colspan="7" class="text-center text-muted py-5">
                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                        No has registrado actividades en este rango.
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
// Mensajes de error/exito que deja el controlador en sesion.
if (isset($_SESSION['error'])) {
?>
    <div class="row justify-content-center">
        <div class="col-xl-11">
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
        <div class="col-xl-11">
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
