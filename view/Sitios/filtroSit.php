<?php
?>
<?php if ($sitios && count($sitios) > 0): ?>
    <?php foreach ($sitios as $sitio): ?>
        <tr>
            <td class="ps-4"><?php echo htmlspecialchars($sitio['nombresitio']); ?></td>
            <td><?php echo htmlspecialchars($sitio['comuna']); ?></td>
            <td><?php echo htmlspecialchars($sitio['barrio']); ?></td>
            <td><?php echo htmlspecialchars($sitio['tipodeposito']); ?></td>
            <td><?php echo htmlspecialchars($sitio['direccion']); ?></td>
            <td class="text-center">
                <?php if ($sitio['estado'] === 'A'): ?>
                    <span class="badge bg-success">Activo</span>
                <?php else: ?>
                    <span class="badge bg-danger">Inactivo</span>
                <?php endif; ?>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
                        onclick="cargarFormularioModal('<?php echo getUrl('Sitios','Sitios','editSit',array('id'=>$sitio['id'])) ?>', 'Editar sitio de terreno', 'sitioFormEdicion', '<?php echo getUrl('Sitios','Sitios','listSit') ?>', 'tablaSitios')">
                    <i class="bi bi-pencil-fill"></i>
                </button>
            </td>
            <td class="text-center">
                <?php if ($sitio['estado'] === 'A'): ?>
                    <a href="<?php echo getUrl('Sitios','Sitios','delete',array('id'=>$sitio['id'])) ?>"
                       class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
                       onclick="return confirm('¿Seguro que deseas inhabilitar este sitio?')">
                        <i class="bi bi-slash-circle"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo getUrl('Sitios','Sitios','delete',array('id'=>$sitio['id'])) ?>"
                       class="btn btn-outline-success btn-icon rounded-circle" title="Activar">
                        <i class="bi bi-check-lg"></i>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="8" class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
            No se encontraron sitios.
        </td>
    </tr>
<?php endif; ?>
