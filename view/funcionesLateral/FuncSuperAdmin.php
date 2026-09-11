<?php
$usuariosAbierto = ($funcion == 'createUsu' || $funcion == 'listUsu');
$rolesAbierto    = ($funcion == 'createRol' || $funcion == 'listRol');
?>

<!--  Usuarios -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $usuariosAbierto ? '' : 'collapsed'; ?>" 
     data-bs-toggle="collapse" href="#submenuUsuarios" role="button" 
     aria-expanded="<?php echo $usuariosAbierto ? 'true' : 'false'; ?>" 
     aria-controls="submenuUsuarios">
    <i class="bi bi-people-fill me-2"></i>  Usuarios
    <i class="bi bi-caret-down-fill ms-auto"></i>
  </a>
  <div class="collapse <?php echo $usuariosAbierto ? 'show' : ''; ?>" id="submenuUsuarios">
    <ul class="list-unstyled ps-4">
      <li>
        <a class="nav-link <?php echo ($funcion == 'createUsu') ? 'active' : ''; ?>" 
           href="<?php echo getUrl('Usuarios','Usuarios','createUsu') ?>">
          Registrar
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'listUsu') ? 'active' : ''; ?>" 
           href="<?php echo getUrl('Usuarios','Usuarios','listUsu') ?>">
          Consultar
        </a>
      </li>
    </ul>
  </div>
</li>

<!-- Roles -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $rolesAbierto ? '' : 'collapsed'; ?>" 
     data-bs-toggle="collapse" href="#submenuRoles" role="button" 
     aria-expanded="<?php echo $rolesAbierto ? 'true' : 'false'; ?>" 
     aria-controls="submenuRoles">
    <i class="bi bi-people-fill me-2"></i> Roles
    <i class="bi bi-caret-down-fill ms-auto"></i>
  </a>
  <div class="collapse <?php echo $rolesAbierto ? 'show' : ''; ?>" id="submenuRoles">
    <ul class="list-unstyled ps-4">
      <li>
        <a class="nav-link <?php echo ($funcion == 'createRol') ? 'active' : ''; ?>" 
           href="<?php echo getUrl('Roles','Roles','createRol') ?>">
          Registrar
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'listRol') ? 'active' : ''; ?>" 
           href="<?php echo getUrl('Roles','Roles','listRol') ?>">
          Consultar
        </a>
      </li>
    </ul>
  </div>
</li>
