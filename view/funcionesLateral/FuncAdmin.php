<?php
$dashboardActivo  = in_array($funcion, ['listDashboard']);
$catalogosActivo  = in_array($funcion, ['listTipoTanque', 'listTipoDeposito', 'listAccionesZoo', 'listTipoActTerreno']);
?>

<!-- Dashboard consolidado -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $dashboardActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('Dashboard', 'Dashboard', 'listDashboard') ?>">
    <i class="bi bi-bar-chart-line me-2"></i> Dashboard consolidado
  </a>
</li>

<!-- Catálogos de configuración (con submódulos) -->
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
        <a class="nav-link <?php echo ($funcion == 'listTipoTanque') ? 'active' : ''; ?>"
          href="<?php echo getUrl('Catalogos', 'Catalogos', 'listTipoTanque') ?>">
          Tipo de Tanque
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'listTipoDeposito') ? 'active' : ''; ?>"
          href="<?php echo getUrl('Catalogos', 'Catalogos', 'listTipoDeposito') ?>">
          Tipo de Depósito
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'listAccionesZoo') ? 'active' : ''; ?>"
          href="<?php echo getUrl('Catalogos', 'Catalogos', 'listAccionesZoo') ?>">
          Acciones de Zoocriadero
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'listTipoActTerreno') ? 'active' : ''; ?>"
          href="<?php echo getUrl('Catalogos', 'Catalogos', 'listTipoActTerreno') ?>">
          Tipo de Act. de Terreno
        </a>
      </li>
    </ul>
  </div>
</li>