<?php if ($usuarios && $usuarios->rowCount() > 0): ?>
    <?php while($usu = $usuarios->fetch(PDO::FETCH_ASSOC)): ?>
        <tr>
            <td class="ps-4"><?php echo htmlspecialchars($usu['nombreusuario']); ?></td>
            <td><?php echo htmlspecialchars($usu['apellidousuario']); ?></td>
            <td><?php echo htmlspecialchars($usu['correo']); ?></td>
            <td><span class="badge-rol rounded-pill"><?php echo htmlspecialchars($usu['codrol']); ?></span></td>
            <td class="text-muted"><?php echo htmlspecialchars($usu['numerodocumento']); ?></td>
            <td class="text-center">
                <?php if ($usu['estado'] === 'A'): ?>
                    <span class="badge bg-success-subtle text-success-emphasis">Activo</span>
                <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Inactivo</span>
                <?php endif; ?>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
                        onclick="cargarFormularioModal('<?php echo getUrl('Usuarios','Usuarios','getUpdateUsu',array('id'=>$usu['codusuario'])) ?>', 'Editar usuario', 'usuarioFormEdicion', '<?php echo getUrl('Usuarios','Usuarios','listUsu') ?>')">
                    <i class="bi bi-pencil-fill"></i>
                </button>
            </td>
            <td class="text-center">
                <?php if ($usu['estado'] === 'I'): ?>
                    <a href="<?php echo getUrl('Usuarios','Usuarios','activacion',array('id'=>$usu['codusuario'], 'estado'=>$usu['estado'])) ?>"
                       class="btn btn-outline-success btn-icon rounded-circle" title="Activar">
                        <i class="bi bi-check-lg"></i>
                    </a>
                <?php else: ?>
                    <a href="<?php echo getUrl('Usuarios','Usuarios','activacion',array('id'=>$usu['codusuario'], 'estado'=>$usu['estado'])) ?>"
                       class="btn btn-outline-danger btn-icon rounded-circle" title="Inactivar">
                        <i class="bi bi-slash-circle"></i>
                    </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="8" class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
            No se encontraron usuarios.
        </td>
    </tr>
<?php endif; ?>
