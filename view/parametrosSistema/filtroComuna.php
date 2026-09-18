<?php
?>
<?php
  $hayComunas = isset($resultComunas) && $resultComunas && $resultComunas->rowCount() > 0;
  if ($hayComunas):
      while ($comuna = $resultComunas->fetch(PDO::FETCH_ASSOC)):
?>
<tr>
  <td class="ps-4"><?php echo htmlspecialchars($comuna['nombrecomuna']); ?></td>
  <td class="text-center">
    <?php if ($comuna['estado'] === 'A'): ?>
      <span class="badge bg-success-subtle text-success-emphasis">Activo</span>
    <?php else: ?>
      <span class="badge bg-secondary-subtle text-secondary-emphasis">Inactivo</span>
    <?php endif; ?>
  </td>
  <td class="text-center">
    <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
            onclick="cargarFormularioModal('<?php echo getUrl('Parametros','Parametros','getUpdateComuna',array('id'=>$comuna['id'])) ?>', 'Editar comuna', 'comunaFormEdicion', '<?php echo getUrl('Parametros','Parametros','listParametros') ?>', 'tablaComunas')">
      <i class="bi bi-pencil-fill"></i>
    </button>
  </td>
  <td class="text-center">
    <?php if ($comuna['estado'] === 'A'): ?>
      <a href="<?php echo getUrl('Parametros','Parametros','deleteComuna',array('id'=>$comuna['id'])) ?>"
         class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
         onclick="return confirm('¿Seguro que deseas inhabilitar esta comuna?')">
        <i class="bi bi-slash-circle"></i>
      </a>
    <?php else: ?>
      <a href="<?php echo getUrl('Parametros','Parametros','deleteComuna',array('id'=>$comuna['id'])) ?>"
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
  <td colspan="4" class="text-center text-muted py-5">
    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
    No se encontraron comunas.
  </td>
</tr>
<?php endif; ?>
