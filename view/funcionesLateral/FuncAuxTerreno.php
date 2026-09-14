<?php
$depositosActivo = ($funcion == 'listDep');

$registrarActividadAbierto = (
  $funcion == 'Inspeccion' ||
  $funcion == 'Siembra' ||
  $funcion == 'Seguimiento' ||
  $funcion == 'Resiembra'
);

$misActividadesActivo = ($funcion == 'listMisActividades');
?>

<!-- Mis actividades -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $misActividadesActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'listMisActividades') ?>">
    <i class="bi bi-file-earmark-text me-2"></i> Mis actividades
  </a>
</li>

<!-- Depósitos -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $depositosActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('Depositos', 'Depositos', 'listDep') ?>">
    <i class="bi bi-bucket me-2"></i> Depósitos
  </a>
</li>

<!-- Registrar actividad (con submódulos) -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $registrarActividadAbierto ? '' : 'collapsed'; ?>"
    data-bs-toggle="collapse" href="#submenuActividadTer" role="button"
    aria-expanded="<?php echo $registrarActividadAbierto ? 'true' : 'false'; ?>"
    aria-controls="submenuActividadTer">
    <i class="bi bi-plus-lg me-2"></i> Registrar actividad
    <i class="bi bi-caret-down-fill ms-auto"></i>
  </a>
  <div class="collapse <?php echo $registrarActividadAbierto ? 'show' : ''; ?>" id="submenuActividadTer">
    <ul class="list-unstyled ps-4">
      <li>
        <a class="nav-link <?php echo ($funcion == 'Inspeccion') ? 'active' : ''; ?>"
          href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'Inspeccion') ?>">
          <i class="bi bi-search me-2"></i> Inspección
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'Siembra') ? 'active' : ''; ?>"
          href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'Siembra') ?>">
          <i class="bi bi-flower1 me-2"></i> Siembra
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'Seguimiento') ? 'active' : ''; ?>"
          href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'Seguimiento') ?>">
          <i class="bi bi-eye me-2"></i> Seguimiento
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'Resiembra') ? 'active' : ''; ?>"
          href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'Resiembra') ?>">
          <i class="bi bi-arrow-repeat me-2"></i> Resiembra
        </a>
      </li>
    </ul>
  </div>
</li>
