<?php
/*
 * Una fila de la tabla de roles. La usan listRol.php y filtro.php.
 * Espera: $rol (codrol, nombrerol, estado, totalmodulos) y $totalModulosSistema.
 */
$totalMods = (int) ($rol['totalmodulos'] ?? 0);
?>
<tr class="border-bottom">
  <td class="ps-4 py-3">
    <span class="fw-bold text-dark d-block"><?php echo htmlspecialchars($rol['nombrerol']); ?></span>
  </td>

  <td class="text-center py-3">
    <?php if ($rol['estado'] === 'A'): ?>
      <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">● Activo</span>
    <?php else: ?>
      <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1">● Inactivo</span>
    <?php endif; ?>
  </td>

  <td class="text-center py-3">
    <a href="<?php echo getUrl('Roles', 'Roles', 'permisos', ['id' => $rol['codrol']]) ?>"
      class="btn btn-sm <?php echo $totalMods > 0 ? 'btn-outline-primary' : 'btn-outline-warning'; ?> rounded-pill px-3"
      title="Elegir los módulos de este rol">
      <i class="bi bi-grid-3x3-gap-fill me-1"></i>
      <?php if ($totalMods > 0): ?>
        <?php echo $totalMods; ?> de <?php echo (int) ($totalModulosSistema ?? 0); ?> módulos
      <?php else: ?>
        Sin módulos
      <?php endif; ?>
    </a>
  </td>

  <td class="text-end pe-4 py-3">
    <div class="btn-group shadow-sm" role="group">
      <button type="button" class="btn btn-sm btn-light border text-primary px-2" title="Editar"
        onclick="cargarFormularioModal('<?php echo getUrl('Roles', 'Roles', 'editRol', ['id' => $rol['codrol']]) ?>', 'Editar rol', 'rolFormEdicion', '<?php echo getUrl('Roles', 'Roles', 'listRol') ?>', 'tablaRoles')">
        <i class="bi bi-pencil"></i>
      </button>

      <?php if ($rol['estado'] === 'A'): ?>
        <a href="<?php echo getUrl('Roles', 'Roles', 'activacion', array('id' => $rol['codrol'], 'estado' => $rol['estado'])) ?>"
          class="btn btn-sm btn-light border text-danger px-2" title="Inhabilitar"
          onclick="return confirm('¿Seguro que deseas inhabilitar este rol?')">
          <i class="bi bi-slash-circle"></i>
        </a>
      <?php else: ?>
        <a href="<?php echo getUrl('Roles', 'Roles', 'activacion', array('id' => $rol['codrol'], 'estado' => $rol['estado'])) ?>"
          class="btn btn-sm btn-light border text-success px-2" title="Activar">
          <i class="bi bi-check-lg"></i>
        </a>
      <?php endif; ?>
    </div>
  </td>
</tr>
