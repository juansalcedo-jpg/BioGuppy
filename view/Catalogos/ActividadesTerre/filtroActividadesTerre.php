<?php ?>
<?php if ($actividades && $actividades->rowCount() > 0): ?>
    <?php while ($actividad = $actividades->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td class="ps-4"><?php echo htmlspecialchars($actividad['nombreactividad']); ?></td>
            <td class="text-center">
                <?php if ($actividad['estado'] === 'A'): ?>
                    <span class="badge bg-success">Activo</span>
                <?php else: ?>
                    <span class="badge bg-danger">Inactivo</span>
                <?php endif; ?>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
                    onclick="cargarFormularioModal('<?php echo getUrl('Catalogos', 'ActividadesTerre', 'getUpdateActTerre', array('id' => $actividad['codtipoactividad'])) ?>', 'Editar actividad de terreno', 'actTerreFormEdicion', '<?php echo getUrl('Catalogos', 'ActividadesTerre', 'listActTerre') ?>', 'tablaActTerre')">
                    <i class="bi bi-pencil-fill"></i>
                </button>
            </td>
            <td class="text-center">
                <?php if ($actividad['estado'] === 'A'): ?>
                    <a href="<?php echo getUrl('Catalogos', 'ActividadesTerre', 'activacion', array('id' => $actividad['codtipoactividad'])) ?>"
                        class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
                        onclick="return confirm('¿Seguro que deseas inhabilitar esta actividad de terreno?')">
                        <i class="bi bi-slash-circle"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo getUrl('Catalogos', 'ActividadesTerre', 'activacion', array('id' => $actividad['codtipoactividad'])) ?>"
                        class="btn btn-outline-success btn-icon rounded-circle" title="Activar">
                        <i class="bi bi-check-lg"></i>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="4" class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
            No se encontraron actividades de terreno.
        </td>
    </tr>
<?php endif; ?>
