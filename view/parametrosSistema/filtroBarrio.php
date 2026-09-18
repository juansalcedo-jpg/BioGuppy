<?php
?>
<?php
  $hayBarrios = isset($resultBarrios) && $resultBarrios && $resultBarrios->rowCount() > 0;
  if ($hayBarrios):
      while ($barrio = $resultBarrios->fetch(PDO::FETCH_ASSOC)):
?>
<tr>
  <td class="ps-4"><?php echo htmlspecialchars($barrio['nombrebarrio']); ?></td>
  <td><?php echo htmlspecialchars($barrio['nombrecomuna']); ?></td>
  <td class="text-center">
    <?php if ($barrio['estado'] === 'A'): ?>
      <span class="badge bg-success-subtle text-success-emphasis">Activo</span>
    <?php else: ?>
      <span class="badge bg-secondary-subtle text-secondary-emphasis">Inactivo</span>
    <?php endif; ?>
  </td>
  <td class="text-center">
    <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
            onclick="cargarFormularioModal('<?php echo getUrl('Parametros','Parametros','getUpdateBarrio',array('id'=>$barrio['id'])) ?>', 'Editar barrio', 'barrioFormEdicion', '<?php echo getUrl('Parametros','Parametros','listParametros') ?>', 'tablaBarrios')">
      <i class="bi bi-pencil-fill"></i>
    </button>
  </td>
  <td class="text-center">
    <?php if ($barrio['estado'] === 'A'): ?>
      <a href="<?php echo getUrl('Parametros','Parametros','deleteBarrio',array('id'=>$barrio['id'])) ?>"
         class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
         onclick="return confirm('¿Seguro que deseas inhabilitar este barrio?')">
        <i class="bi bi-slash-circle"></i>
      </a>
    <?php else: ?>
      <a href="<?php echo getUrl('Parametros','Parametros','deleteBarrio',array('id'=>$barrio['id'])) ?>"
         class="btn btn-outline-success btn-icon rounded-circle" title="Activar">
        <i class="bi bi-check-lg"></i>
      </a>
    <?php endif; ?>
  </td>
</tr>
<?php
      endwhile;
  else:
?>
<tr>
  <td colspan="5" class="text-center text-muted py-5">
    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
    No se encontraron barrios.
  </td>
</tr>
<?php endif; ?>
