<?php
$filasActividades=(isset($actividades)&&$actividades)
?$actividades->fetchAll(PDO::FETCH_ASSOC)
:[];
?>

<?php if(!empty($filasActividades)): ?>

    <?php foreach($filasActividades as $act): ?>

        <tr>

            <td class="ps-4">
                <?php echo htmlspecialchars($act['fecha']); ?>
            </td>

            <td class="fw-semibold">
                <?php echo htmlspecialchars($act['tipo_actividad']); ?>
            </td>

            <td>
                <?php echo htmlspecialchars($act['deposito']); ?>
            </td>

            <td>
                <span class="text-muted small">
                    <?php echo htmlspecialchars($act['sitio']); ?>
                </span>
            </td>

            <td class="text-center">

                <?php if($act['estado']==='A'): ?>

                    <span class="badge bg-success">Activo</span>

                <?php else: ?>

                    <span class="badge bg-danger">Inactivo</span>

                <?php endif; ?>

            </td>

            <td class="text-center">

                <button
                    type="button"
                    class="btn btn-outline-primary btn-icon rounded-circle"
                    title="Editar"
                    onclick="cargarFormularioModal(
                    '<?php echo getUrl('ActividadesListTer','ActividadesListTer','getUpdate',array('id'=>$act['id'])); ?>',
                    'Editar actividad',
                    'actividadTerFormEdicion',
                    '<?php echo getUrl('ActividadesListTer','ActividadesListTer','listMisActividades'); ?>',
                    'tablaMisActividadesTer'
                    )">

                    <i class="bi bi-pencil-fill"></i>

                </button>

            </td>

            <td class="text-center">

                <?php if($act['estado']==='A'): ?>

                    <a
                        href="<?php echo getUrl('ActividadesListTer','ActividadesListTer','delete',array('id'=>$act['id'])); ?>"
                        class="btn btn-outline-danger btn-icon rounded-circle"
                        title="Inhabilitar">

                        <i class="bi bi-slash-circle"></i>

                    </a>

                <?php else: ?>

                    <a
                        href="<?php echo getUrl('ActividadesListTer','ActividadesListTer','delete',array('id'=>$act['id'])); ?>"
                        class="btn btn-outline-success btn-icon rounded-circle"
                        title="Activar">

                        <i class="bi bi-check-lg"></i>

                    </a>

                <?php endif; ?>

            </td>

        </tr>

    <?php endforeach; ?>

<?php else: ?>

    <tr>

        <td colspan="7" class="text-center text-muted py-5">

            <i class="bi bi-inbox fs-3 d-block mb-2"></i>

            No se encontraron actividades para los filtros indicados.

        </td>

    </tr>

<?php endif; ?>