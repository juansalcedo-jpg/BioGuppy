<?php
$zooActivo      = ($funcion == 'listZoo');
$tanquesActivo  = ($funcion == 'listTan');
$histZooActivo  = ($funcion == 'listHistZoo');
$repZooActivo   = ($funcion == 'listRepoZoo');
$sitiosActivo   = ($funcion == 'createSit' || $funcion == 'listSit' || $funcion == 'editSit');
$histTerActivo  = ($funcion == 'listActTer');
$repTerActivo   = ($funcion == 'repSitios' || $funcion == 'repActividadTer' || $funcion == 'repAuxiliar' || $funcion == 'repTipoDeposito');
?>

<!-- Zoocriaderos -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $zooActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('Zoocriadero', 'Zoocriadero', 'listZoo') ?>">
    <i class="bi bi-water me-2"></i> Zoocriaderos
  </a>
</li>

<!-- Tanques -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $tanquesActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('Tanques', 'Tanques', 'listTan') ?>">
    <i class="bi bi-droplet-fill me-2"></i> Tanques
  </a>
</li>

<!-- Depositos: una sola interfaz (lista + boton "Nuevo" que abre modal),
     igual que Usuarios -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $depositosActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('Depositos', 'Depositos', 'listDep') ?>">
    <i class="bi bi-bucket me-2"></i> Depósitos
  </a>
</li>

<!-- Sitios de Terreno -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $sitiosActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('Sitios', 'Sitios', 'listSit') ?>">
    <i class="bi bi-house-door me-2"></i> Sitios de Terreno
  </a>
</li>

<!-- Historial de Actividades Zoocriadero -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $histZooActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('HistorialZoo', 'HistorialZoo', 'listHistZoo') ?>">
    <i class="bi bi-layout-text-window me-2"></i> Historial Zoocriadero
  </a>
</li>

<!-- Historial de Actividades Terreno -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $histTerActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'listActTer') ?>">
    <i class="bi bi-list-check me-2"></i> Historial Terreno
  </a>
</li>

<!-- Reportes Zoocriadero -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $repZooActivo ? 'active' : ''; ?>"
    href="<?php echo getUrl('ReportesZoo', 'ReportesZoo', 'listRepoZoo') ?>">
    <i class="bi bi-bar-chart-line me-2"></i> Reportes Zoocriadero
  </a>
</li>

<!-- Reportes Terreno -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $repTerActivo ? '' : 'collapsed'; ?>"
    data-bs-toggle="collapse" href="#submenuRepTer" role="button"
    aria-expanded="<?php echo $repTerActivo ? 'true' : 'false'; ?>"
    aria-controls="submenuRepTer">
    <i class="bi bi-graph-up-arrow me-2"></i> Reportes Terreno
    <i class="bi bi-caret-down-fill ms-auto"></i>
  </a>
  <div class="collapse <?php echo $repTerActivo ? 'show' : ''; ?>" id="submenuRepTer">
    <ul class="list-unstyled ps-4">
      <li>
        <a class="nav-link <?php echo ($funcion == 'repSitios') ? 'active' : ''; ?>"
          href="<?php echo getUrl('ReportesTer', 'ReportesTer', 'repSitios') ?>">
          <i class="bi bi-file-earmark-text me-1"></i> Reporte de sitios
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'repActividadTer') ? 'active' : ''; ?>"
          href="<?php echo getUrl('ReportesTer', 'ReportesTer', 'repActividadTer') ?>">
          <i class="bi bi-card-checklist me-1"></i> Por tipo de actividad
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'repAuxiliar') ? 'active' : ''; ?>"
          href="<?php echo getUrl('ReportesTer', 'ReportesTer', 'repAuxiliar') ?>">
          <i class="bi bi-person-badge me-1"></i> Por auxiliar
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'repTipoDeposito') ? 'active' : ''; ?>"
          href="<?php echo getUrl('ReportesTer', 'ReportesTer', 'repTipoDeposito') ?>">
          <i class="bi bi-funnel me-1"></i> Por tipo de depósito
        </a>
      </li>
    </ul>
  </div>
</li>