<?php
$ActividadesListZoo = ($funcion == 'ActividadesListZoo');

$ActividadAbierto = (
  $funcion == 'Alimentacion' ||
  $funcion == 'NacidosMuertos' ||
  $funcion == 'Limpieza' ||
  $funcion == 'AjusteNivel' ||
  $funcion == 'Lavado'
);
?>

<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $ActividadesListZoo ? 'active' : ''; ?>"
    href="<?php echo getUrl('ActividadesListZoo', 'ActividadesListZoo', 'ActividadesListZoo') ?>">
    <i class="bi bi-file-earmark-text me-2"></i> Mis actividades
  </a>
</li>

<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $ActividadAbierto ? 'active' : ''; ?>"
    href="<?php echo getUrl('ActividadesZoo', 'Alimentacion', 'Alimentacion') ?>">
    <i class="bi bi-plus-lg me-2"></i> Registrar actividad
  </a>
</li>
