<?php
$dashboardActivo  = in_array($funcion, ['listDashboard']);
$catalogosActivo  = in_array($funcion, ['listTipoTanq', 'listTipoDepo', 'listActZoo', 'listActTerre']);
?>

<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $dashboardActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('Dashboard', 'Dashboard', 'listDashboard') ?>">
    <i class="bi bi-bar-chart-line me-2"></i> Dashboard consolidado
  </a>
</li>

<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $catalogosActivo ? '' : 'collapsed'; ?>"
    data-bs-toggle="collapse" href="#submenuCatalogos" role="button"
    aria-expanded="<?php echo $catalogosActivo ? 'true' : 'false'; ?>"
    aria-controls="submenuCatalogos">
    <i class="bi bi-database-gear me-2"></i> Catálogos de configuración
    <i class="bi bi-caret-down-fill ms-auto"></i>
  </a>
  <div class="collapse <?php echo $catalogosActivo ? 'show' : ''; ?>" id="submenuCatalogos">
    <ul class="list-unstyled ps-4">
      <li>
        <a class="nav-link <?php echo ($funcion == 'listTipoTanq') ? 'active' : ''; ?>"
          href="<?php echo getUrl('Catalogos', 'TipoTanque', 'listTipoTanq') ?>">
          Tipo de Tanque
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'listTipoDepo') ? 'active' : ''; ?>"
          href="<?php echo getUrl('Catalogos', 'TipoDeposito', 'listTipoDepo') ?>">
          Tipo de Depósito
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'listActZoo') ? 'active' : ''; ?>"
          href="<?php echo getUrl('Catalogos', 'ActividadesZoo', 'listActZoo') ?>">
          Actividades Zoocriadero
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'listActTerre') ? 'active' : ''; ?>"
          href="<?php echo getUrl('Catalogos', 'ActividadesTerre', 'listActTerre') ?>">
          Actividades Terreno
        </a>
      </li>
    </ul>
  </div>
</li>