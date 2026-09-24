<?php if ($Roles && $Roles->rowCount() > 0): ?>
  <?php while ($rol = $Roles->fetch(PDO::FETCH_ASSOC)): ?>
    <?php include __DIR__ . '/filaRol.php'; ?>
  <?php endwhile; ?>
<?php else: ?>
  <tr>
    <td colspan="4" class="text-center text-muted py-5">
      <i class="bi bi-search fs-3 d-block mb-2"></i>
      Ningún rol coincide con la búsqueda.
    </td>
  </tr>
<?php endif; ?>
