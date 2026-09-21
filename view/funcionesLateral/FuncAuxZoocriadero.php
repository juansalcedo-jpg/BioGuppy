<?php
$ActividadAbierto = (
  $funcion == 'Alimentacion' ||
  $funcion == 'NacidosMuertos' ||
  $funcion == 'Limpieza' ||
  $funcion == 'AjusteNivel' ||
  $funcion == 'Lavado'
);

$ActividadesListZoo = ($funcion == 'ActividadesListZoo');
?>

<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $ActividadesListZoo ? 'active' : ''; ?>"
    href="<?php echo getUrl('ActividadesListZoo', 'ActividadesListZoo', 'ActividadesListZoo') ?>">
    <i class="bi bi-file-earmark-text me-2"></i> Mis actividades
  </a>
</li>

<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $misActividadesActivo ? '' : 'collapsed'; ?>"
    data-bs-toggle="collapse" href="#submenuActividad" role="button"
    aria-expanded="<?php echo $ActividadAbierto ? 'true' : 'false'; ?>"
    aria-controls="submenuActividad">
    <i class="bi bi-plus-lg me-2"></i> Registrar actividad
    <i class="bi bi-caret-down-fill ms-auto"></i>
  </a>
  <div class="collapse <?php echo $ActividadAbierto ? 'show' : ''; ?>" id="submenuActividad">
    <ul class="list-unstyled ps-4">
      <li>
        <a class="nav-link <?php echo ($funcion == 'Alimentacion') ? 'active' : ''; ?>"
          href="<?php echo getUrl('ActividadesZoo', 'Alimentacion', 'Alimentacion') ?>">
          <i class="bi bi-egg-fried me-2"></i> Alimentación
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'NacidosMuertos') ? 'active' : ''; ?>"
          href="<?php echo getUrl('ActividadesZoo', 'NacidosMuertos', 'NacidosMuertos') ?>">
          <i class="bi bi-heart-pulse me-2"></i> Nacidos / Muertos
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'Limpieza') ? 'active' : ''; ?>"
          href="<?php echo getUrl('ActividadesZoo', 'Limpieza', 'Limpieza') ?>">
          <i class="bi bi-stars me-2"></i> Limpieza
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'AjusteNivel') ? 'active' : ''; ?>"
          href="<?php echo getUrl('ActividadesZoo', 'AjusteNivel', 'AjusteNivel') ?>">
          <i class="bi bi-funnel me-2"></i> Ajuste de nivel
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'Lavado') ? 'active' : ''; ?>"
          href="<?php echo getUrl('ActividadesZoo', 'Lavado', 'Lavado') ?>">
          <i class="bi bi-arrow-repeat me-2"></i> Lavado
        </a>
      </li>
    </ul>
  </div>
</li>