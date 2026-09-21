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

<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $misActividadesActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'listMisActividades') ?>">
    <i class="bi bi-file-earmark-text me-2"></i> Mis actividades
  </a>
</li>

<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $registrarActividadAbierto ? 'active' : ''; ?>"
    href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'Inspeccion') ?>">
    <i class="bi bi-plus-lg me-2"></i> Registrar actividad
  </a>
</li>
