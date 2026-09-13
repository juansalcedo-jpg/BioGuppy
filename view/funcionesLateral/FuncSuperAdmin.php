<?php
$usuariosActivo = in_array($funcion, ['createUsu','listUsu','getUpdateUsu','postcreateUsu','postUpdateUsu','activacion']);
$rolesActivo    = in_array($funcion, ['createRol','listRol','editRol','deleteRol','postcreateRol']);
?>

<!-- Usuarios -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $usuariosActivo ? 'active' : ''; ?>"
     href="<?php echo getUrl('Usuarios','Usuarios','listUsu') ?>">
    <i class="bi bi-people-fill me-2"></i> Usuarios
  </a>
</li>

<!-- Roles -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $rolesActivo ? 'active' : ''; ?>"
     href="<?php echo getUrl('Roles','Roles','listRol') ?>">
    <i class="bi bi-shield-lock-fill me-2"></i> Roles
  </a>
</li>