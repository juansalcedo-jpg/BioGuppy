<?php
$usuariosActivo = in_array($funcion, ['createUsu','listUsu','getUpdateUsu','postcreateUsu','postUpdateUsu','activacion']);
$rolesActivo    = in_array($funcion, ['createRol','listRol','editRol','deleteRol','postcreateRol']);
$auditoriaActivo  = in_array($funcion, ['listAuditoria']);
$parametrosActivo = in_array($funcion, ['listParametros']);
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
<!-- Encabezado grupo SISTEMA -->
<div class="text-accent text-uppercase small fw-semibold px-3 pt-3 pb-1" style="letter-spacing:.05em; font-size:.7rem;">
  Sistema
</div>

<!-- Auditoría del sistema -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $auditoriaActivo ? 'active' : ''; ?>"
     href="<?php echo getUrl('Auditoria','Auditoria','listAuditoria') ?>">
    <i class="bi bi-activity me-2"></i> Auditoría del sistema
  </a>
</li>

<!-- Parámetros del sistema -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $parametrosActivo ? 'active' : ''; ?>"
     href="<?php echo getUrl('Parametros','Parametros','listParametros') ?>">
    <i class="bi bi-gear-fill me-2"></i> Parámetros del sistema
  </a>
</li>