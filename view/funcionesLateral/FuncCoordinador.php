<?php
$zooActivo      = ($funcion == 'listZoo');
$tanquesActivo  = ($funcion == 'listTan');
$depositosActivo = ($funcion == 'listDep');
$histZooActivo  = ($funcion == 'listHistZoo');
$repZooActivo   = ($funcion == 'listRepoZoo');
$sitiosActivo   = ($funcion == 'createSit' || $funcion == 'listSit' || $funcion == 'editSit');
$histTerActivo  = ($funcion == 'listActTer');
$repTerActivo   = ($funcion == 'listRepoTer');
?>

<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $zooActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('Zoocriadero', 'Zoocriadero', 'listZoo') ?>">
    <i class="bi bi-water me-2"></i> Zoocriaderos
  </a>
</li>

<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $tanquesActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('Tanques', 'Tanques', 'listTan') ?>">
    <i class="bi bi-droplet-fill me-2"></i> Tanques
  </a>
</li>

<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $sitiosActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('Sitios', 'Sitios', 'listSit') ?>">
    <i class="bi bi-house-door me-2"></i> Sitios de Terreno
  </a>
</li>

<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $histZooActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('HistorialZoo', 'HistorialZoo', 'listHistZoo') ?>">
    <i class="bi bi-layout-text-window me-2"></i> Historial Zoocriadero
  </a>
</li>

<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $histTerActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'listActTer') ?>">
    <i class="bi bi-list-check me-2"></i> Historial Terreno
  </a>
</li>

<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $repZooActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('ReportesZoo', 'ReportesZoo', 'listRepoZoo') ?>">
    <i class="bi bi-bar-chart-line me-2"></i> Reportes Zoocriadero
  </a>
</li>

<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $repTerActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('ReportesTer', 'ReportesTer', 'listRepoTer') ?>">
    <i class="bi bi-graph-up-arrow me-2"></i> Reportes Terreno
  </a>
</li>