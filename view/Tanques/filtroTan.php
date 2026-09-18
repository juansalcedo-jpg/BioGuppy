<?php
?>
<?php
  $hayTanques = isset($tanques) && $tanques && $tanques->rowCount() > 0;
  if ($hayTanques):
      while($tanque = $tanques->fetch(PDO::FETCH_ASSOC)):
?>
<tr>
  <td class="ps-4"><?php echo htmlspecialchars($tanque['numero']); ?></td>
  <td><?php echo htmlspecialchars($tanque['tipo']); ?></td>
  <td><?php echo htmlspecialchars($tanque['capacidad']); ?></td>
  <td><?php echo htmlspecialchars($tanque['zoocriadero']); ?></td>
  <td class="text-center">
    <?php if ($tanque['estado'] === 'Activo'): ?>
      <span class="badge bg-success-subtle text-success-emphasis">Activo</span>
    <?php else: ?>
      <span class="badge bg-secondary-subtle text-secondary-emphasis">Inactivo</span>
    <?php endif; ?>
  </td>
  <td class="text-center">
    <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
            onclick="cargarFormularioModal('<?php echo getUrl('Tanques','Tanques','getUpdate',array('id'=>$tanque['id'])) ?>', 'Editar tanque', 'tanqueFormEdicion', '<?php echo getUrl('Tanques','Tanques','listTan') ?>', 'tablaTanques')">
      <i class="bi bi-pencil-fill"></i>
    </button>
  </td>
  <td class="text-center">
    <a href="<?php echo getUrl('Tanques','Tanques','delete',array('id'=>$tanque['id'])) ?>"
       class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
       onclick="return confirm('¿Seguro que deseas inhabilitar el tanque <?php echo $tanque['numero']; ?>?')">
      <i class="bi bi-eye-slash"></i>
    </a>
  </td>
</tr>
<?php
      endwhile;
  else:
?>
<tr>
  <td colspan="7" class="text-center text-muted py-5">
    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
    No se encontraron tanques.
  </td>
</tr>
<?php endif; ?>
