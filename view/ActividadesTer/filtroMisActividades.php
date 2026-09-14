<?php
/**
 * filtroMisActividades.php
 * ------------------------------------------------------------
 * Este archivo SI esta en uso activo -- es la respuesta del filtro
 * de "Mis actividades" (fecha desde/hasta, deposito, tipo). El
 * <script> agregado al final de listMisActividades.php intercepta
 * el submit del formulario, lo manda por fetch() a
 * ActividadesTer->filtro(), y mete lo que este archivo devuelva
 * directo dentro del <tbody> de la tabla (por eso aqui solo hay
 * filas <tr>, sin el <table> que las envuelve).
 *
 * $actividades llega desde ActividadesTerController::filtro(), ya
 * limitado al usuario en sesion (un auxiliar nunca ve actividades
 * de otro, ni siquiera filtrando).
 */
?>
<?php if ($actividades && $actividades->rowCount() > 0): ?>
    <?php while($act = $actividades->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td class="ps-4"><?php echo htmlspecialchars($act['fecha']); ?></td>
            <td class="fw-semibold"><?php echo htmlspecialchars($act['tipo_actividad']); ?></td>
            <td><?php echo htmlspecialchars($act['deposito']); ?></td>
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
                        onclick="cargarFormularioModal('<?php echo getUrl('ActividadesTer','ActividadesTer','getUpdate',array('id'=>$act['id'])) ?>', 'Editar actividad', 'actividadTerFormEdicion', '<?php echo getUrl('ActividadesTer','ActividadesTer','listMisActividades') ?>', 'tablaMisActividadesTer')">
                    <i class="bi bi-pencil-fill"></i>
                </button>
            </td>
            <td class="text-center">
                <?php if ($act['estado'] === 'A'): ?>
                    <a href="<?php echo getUrl('ActividadesTer','ActividadesTer','delete',array('id'=>$act['id'])) ?>"
                       class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar">
                        <i class="bi bi-slash-circle"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo getUrl('ActividadesTer','ActividadesTer','delete',array('id'=>$act['id'])) ?>"
                       class="btn btn-outline-success btn-icon rounded-circle" title="Activar">
                        <i class="bi bi-check-lg"></i>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="7" class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
            No se encontraron actividades para los filtros indicados.
        </td>
    </tr>
<?php endif; ?>
