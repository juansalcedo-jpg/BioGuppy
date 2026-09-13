<?php
$zooActivo      = ($funcion == 'createZoo' || $funcion == 'listZoo' || $funcion == 'editZoo');
$tanquesActivo  = ($funcion == 'createTan' || $funcion == 'listTan' || $funcion == 'editTan');
$histZooActivo  = ($funcion == 'listActZoo');
$repZooActivo   = ($funcion == 'repSeguimientoZoo' || $funcion == 'repNacidosMuertos' || $funcion == 'repTanquesZoo');
$sitiosActivo   = ($funcion == 'createSit' || $funcion == 'listSit' || $funcion == 'editSit');
$histTerActivo  = ($funcion == 'listActTer');
$repTerActivo   = ($funcion == 'repSitios' || $funcion == 'repActividadTer' || $funcion == 'repAuxiliar' || $funcion == 'repTipoDeposito');
?>

<!-- Zoocriaderos -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $zooActivo ? 'active' : ''; ?>" 
     href="<?php echo getUrl('Zoocriadero','Zoocriadero','listZoo') ?>">
    <i class="bi bi-leaf-fill me-2"></i> Zoocriaderos
  </a>
</li>

<!-- Tanques -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $tanquesActivo ? 'active' : ''; ?>" 
     href="<?php echo getUrl('Tanques','Tanques','listTan') ?>">
    <i class="bi bi-droplet-fill me-2"></i> Tanques
  </a>
</li>

<!-- Sitios de Terreno -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $sitiosActivo ? 'active' : ''; ?>" 
     href="<?php echo getUrl('Sitios','Sitios','listSit') ?>">
    <i class="bi bi-bucket me-2"></i> Sitios de Terreno
  </a>
</li>

<!-- Historial de Actividades Zoocriadero -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $histZooActivo ? 'active' : ''; ?>" 
     href="<?php echo getUrl('ActividadesZoo','ActividadesZoo','listActZoo') ?>">
    <i class="bi bi-layout-text-window me-2"></i></i> Historial Zoocriadero
  </a>
</li>

<!-- Historial de Actividades Terreno -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $histTerActivo ? 'active' : ''; ?>" 
     href="<?php echo getUrl('ActividadesTer','ActividadesTer','listActTer') ?>">
    <i class="bi bi-list-check me-2"></i> Historial Terreno
  </a>
</li>

<!-- Reportes Zoocriadero -->
<li class="nav-item">
  <a class="nav-link fw-semibold d-flex align-items-center <?php echo $repZooActivo ? '' : 'collapsed'; ?>" 
     data-bs-toggle="collapse" href="#submenuRepZoo" role="button" 
     aria-expanded="<?php echo $repZooActivo ? 'true' : 'false'; ?>" 
     aria-controls="submenuRepZoo">
    <i class="bi bi-bar-chart-line me-2"></i> Reportes Zoocriadero
    <i class="bi bi-caret-down-fill ms-auto"></i>
  </a>
  <div class="collapse <?php echo $repZooActivo ? 'show' : ''; ?>" id="submenuRepZoo">
    <ul class="list-unstyled ps-4">
      <li>
        <a class="nav-link <?php echo ($funcion == 'repSeguimientoZoo') ? 'active' : ''; ?>" 
           href="<?php echo getUrl('ReportesZoo','ReportesZoo','repSeguimientoZoo') ?>">
          Seguimiento de actividades
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'repNacidosMuertos') ? 'active' : ''; ?>" 
           href="<?php echo getUrl('ReportesZoo','ReportesZoo','repNacidosMuertos') ?>">
          Nacidos y muertos por tanque
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'repTanquesZoo') ? 'active' : ''; ?>" 
           href="<?php echo getUrl('ReportesZoo','ReportesZoo','repTanquesZoo') ?>">
          Tanques por zoocriadero
        </a>
      </li>
    </ul>
  </div>
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
           href="<?php echo getUrl('ReportesTer','ReportesTer','repSitios') ?>">
          Reporte de sitios
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'repActividadTer') ? 'active' : ''; ?>" 
           href="<?php echo getUrl('ReportesTer','ReportesTer','repActividadTer') ?>">
          Por tipo de actividad
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'repAuxiliar') ? 'active' : ''; ?>" 
           href="<?php echo getUrl('ReportesTer','ReportesTer','repAuxiliar') ?>">
          Por auxiliar
        </a>
      </li>
      <li>
        <a class="nav-link <?php echo ($funcion == 'repTipoDeposito') ? 'active' : ''; ?>" 
           href="<?php echo getUrl('ReportesTer','ReportesTer','repTipoDeposito') ?>">
          Por tipo de depósito
        </a>
      </li>
    </ul>
  </div>
</li>