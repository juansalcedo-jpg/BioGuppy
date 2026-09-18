<?php
$hayActividades = isset($actividades) && $actividades && $actividades->rowCount() > 0;
?>
<?php if ($hayActividades): ?>
  <?php while ($act = $actividades->fetch(PDO::FETCH_ASSOC)): ?>
    <tr>
      <td class="ps-4"><?php echo htmlspecialchars($act['fecha']); ?></td>
      <td class="fw-semibold"><?php echo htmlspecialchars($act['tipo_actividad']); ?></td>
      <td><?php echo htmlspecialchars($act['zoocriadero']); ?></td>
      <td><span class="text-muted small">T-<?php echo str_pad($act['tanque'], 3, '0', STR_PAD_LEFT); ?></span></td>
      <td class="text-center">
        <?php if ($act['estado'] === 'A'): ?>
          <span class="badge bg-success">Activo</span>
        <?php else: ?>
          <span class="badge bg-danger">Inactivo</span>
        <?php endif; ?>
      </td>
      <td class="text-center">
        <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
                onclick="cargarFormularioModal('<?php echo getUrl('ActividadesListZoo','ActividadesListZoo','getUpdate',array('id'=>$act['id'])) ?>', 'Editar actividad', 'actividadZooFormEdicion', '<?php echo getUrl('ActividadesListZoo','ActividadesListZoo','ActividadesListZoo') ?>', 'tablaMisActividadesZoo')">
          <i class="bi bi-pencil-fill"></i>
        </button>
      </td>
      <td class="text-center">
        <?php if ($act['estado'] === 'A'): ?>
          <a href="<?php echo getUrl('ActividadesListZoo','ActividadesListZoo','delete',array('id'=>$act['id'])) ?>"
             class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar">
            <i class="bi bi-slash-circle"></i>
          </a>
        <?php else: ?>
          <a href="<?php echo getUrl('ActividadesListZoo','ActividadesListZoo','delete',array('id'=>$act['id'])) ?>"
             class="btn btn-outline-success btn-icon rounded-circle" title="Activar">
            <i class="bi bi-check-lg"></i>
          </a>
        <?php endif; ?>
      </td>
    </tr>
  <?php endwhile; ?>
<?php else: ?>
  <tr>
    <td colspan="7" class="text-center text-muted py-5">
      <i class="bi bi-inbox fs-3 d-block mb-2"></i>
      No has registrado actividades en este rango.
    </td>
  </tr>
<?php endif; ?>
