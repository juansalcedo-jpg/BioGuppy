<?php if ($zoocriaderos && $zoocriaderos->rowCount() > 0): ?>
    <?php while($zoo = $zoocriaderos->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td class="ps-4"><?php echo htmlspecialchars($zoo['nombre']); ?></td>
            <td><?php echo htmlspecialchars($zoo['direccion']); ?></td>
            <td><?php echo htmlspecialchars($zoo['comuna']); ?></td>
            <td><?php echo htmlspecialchars($zoo['barrio']); ?></td>
            <td><?php echo htmlspecialchars($zoo['encargado']); ?></td>
            <td class="text-center">
                <?php if ($zoo['estado'] === 'A'): ?>
                    <span class="badge bg-success-subtle text-success-emphasis">Activo</span>
                <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Inactivo</span>
                <?php endif; ?>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
                        onclick="cargarFormularioModal('<?php echo getUrl('Zoocriadero','Zoocriadero','getUpdate',array('id'=>$zoo['id'])) ?>', 'Editar zoocriadero', 'zoocriaderoFormEdicion', '<?php echo getUrl('Zoocriadero','Zoocriadero','listZoo') ?>', 'tablaZoocriaderos')">
                    <i class="bi bi-pencil-fill"></i>
                </button>
            </td>
            <td class="text-center">
                <?php if ($zoo['estado'] === 'A'): ?>
                    <a href="<?php echo getUrl('Zoocriadero','Zoocriadero','delete',array('id'=>$zoo['id'])) ?>"
                       class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
                       onclick="return confirm('¿Seguro que deseas inhabilitar el zoocriadero <?php echo htmlspecialchars($zoo['nombre']); ?>?')">
                        <i class="bi bi-eye-slash"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo getUrl('Zoocriadero','Zoocriadero','delete',array('id'=>$zoo['id'])) ?>"
                       class="btn btn-outline-success btn-icon rounded-circle" title="Activar">
                        <i class="bi bi-check-lg"></i>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="8" class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
            No se encontraron zoocriaderos.
        </td>
    </tr>
<?php endif; ?>