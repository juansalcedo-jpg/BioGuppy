<?php
$hayRegistros = isset($resultAuditoria) && $resultAuditoria && $resultAuditoria->rowCount() > 0;
if ($hayRegistros):
  while ($log = $resultAuditoria->fetch(PDO::FETCH_ASSOC)):

    // Formato legible: "24/09/2026" y "1:22 a. m."
    try {
      $fechaObj   = new DateTime($log['fecha']);
      $fechaTexto = $fechaObj->format('d/m/Y');
      $horaTexto  = $fechaObj->format('g:i') . ($fechaObj->format('A') === 'AM' ? ' a. m.' : ' p. m.');
    } catch (\Throwable $e) {
      $fechaTexto = $log['fecha'];
      $horaTexto  = '';
    }
?>
    <tr class="border-bottom">
      <td class="ps-4 py-2 text-nowrap" title="<?php echo htmlspecialchars($log['fecha']); ?>">
        <div class="fw-medium text-dark small">
          <i class="bi bi-calendar3 me-1 text-secondary"></i><?php echo htmlspecialchars($fechaTexto); ?>
        </div>
        <?php if ($horaTexto !== ''): ?>
          <div class="text-muted small">
            <i class="bi bi-clock me-1"></i><?php echo htmlspecialchars($horaTexto); ?>
          </div>
        <?php endif; ?>
      </td>
      <td class="py-2 fw-medium text-dark"><?php echo htmlspecialchars($log['usuario']); ?></td>
      <td class="py-2 text-secondary"><?php echo htmlspecialchars($log['accion']); ?></td>
      <td class="py-2">
        <span class="badge bg-light text-dark border px-2 py-1 fw-normal"><?php echo htmlspecialchars($log['modulo']); ?></span>
      </td>
      <td class="py-2 text-muted small font-monospace" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($log['valoranterior'] ?? ''); ?>">
        <?php echo !empty($log['valoranterior']) ? htmlspecialchars($log['valoranterior']) : '&mdash;'; ?>
      </td>
      <td class="py-2 pe-4 text-muted small font-monospace" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo htmlspecialchars($log['valornuevo'] ?? ''); ?>">
        <?php echo !empty($log['valornuevo']) ? htmlspecialchars($log['valornuevo']) : '&mdash;'; ?>
      </td>
    </tr>
<?php
  endwhile;
else:
?>
  <tr>
    <td colspan="6" class="text-center text-muted py-4">
      <i class="bi bi-inbox fs-3 d-block mb-1 text-secondary opacity-50"></i>
      No se encontraron registros para los filtros aplicados
    </td>
  </tr>
<?php endif; ?>
