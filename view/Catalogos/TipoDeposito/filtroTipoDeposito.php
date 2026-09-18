<?php ?>
<?php if ($depositos && $depositos->rowCount() > 0): ?>
    <?php while ($deposito = $depositos->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td class="ps-4"><?php echo htmlspecialchars($deposito['nombretipodeposito']); ?></td>
            <td class="text-center">
                <?php if ($deposito['estado'] === 'A'): ?>
                    <span class="badge bg-success-subtle text-success-emphasis">Activo</span>
                <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Inactivo</span>
                <?php endif; ?>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
                    onclick="cargarFormularioModal('<?php echo getUrl('Catalogos', 'TipoDeposito', 'getUpdateTipoDeposito', array('id' => $deposito['codtipodeposito'])) ?>', 'Editar tipo de depósito', 'depositoFormEdicion', '<?php echo getUrl('Catalogos', 'TipoDeposito', 'listTipoDepo') ?>', 'tablaDepositos')">
                    <i class="bi bi-pencil-fill"></i>
                </button>
            </td>
            <td class="text-center">
                <?php if ($deposito['estado'] === 'A'): ?>
                    <a href="<?php echo getUrl('Catalogos', 'TipoDeposito', 'activacion', array('id' => $deposito['codtipodeposito'])) ?>"
                        class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
                        onclick="return confirm('¿Seguro que deseas inhabilitar este tipo de depósito?')">
                        <i class="bi bi-eye-slash"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo getUrl('Catalogos', 'TipoDeposito', 'activacion', array('id' => $deposito['codtipodeposito'])) ?>"
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
            No se encontraron tipos de depósito.
        </td>
    </tr>
<?php endif; ?>
