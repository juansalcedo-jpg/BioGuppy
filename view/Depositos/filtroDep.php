<?php
?>
<?php if ($tiposDeposito && $tiposDeposito->rowCount() > 0): ?>
    <?php while($tipo = $tiposDeposito->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td class="ps-4"><?php echo htmlspecialchars($tipo['nombredeposito']); ?></td>
            <td><?php echo htmlspecialchars($tipo['fechacreacion']); ?></td>
            <td class="text-center">
                <?php if ($tipo['estado'] === 'A'): ?>
                    <span class="badge bg-success">Activo</span>
                <?php else: ?>
                    <span class="badge bg-danger">Inactivo</span>
                <?php endif; ?>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
                        onclick="cargarFormularioModal('<?php echo getUrl('Depositos','Depositos','getUpdate',array('id'=>$tipo['id'])) ?>', 'Editar tipo de depósito', 'depositoFormEdicion', '<?php echo getUrl('Depositos','Depositos','listDep') ?>')">
                    <i class="bi bi-pencil-fill"></i>
                </button>
            </td>
            <td class="text-center">
                <?php if ($tipo['estado'] === 'A'): ?>
                    <a href="<?php echo getUrl('Depositos','Depositos','delete',array('id'=>$tipo['id'])) ?>"
                       class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
                       onclick="return confirm('¿Seguro que deseas inhabilitar este tipo de depósito?')">
                        <i class="bi bi-slash-circle"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo getUrl('Depositos','Depositos','delete',array('id'=>$tipo['id'])) ?>"
                       class="btn btn-outline-success btn-icon rounded-circle" title="Activar">
                        <i class="bi bi-check-lg"></i>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="5" class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
            No se encontraron tipos de depósito.
        </td>
    </tr>
<?php endif; ?>
