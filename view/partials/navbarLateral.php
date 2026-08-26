<?php
$modulo = $_GET['modulo'] ?? '';
?>
<style>
    .sidebar-biogu {
        width: var(--sidebar-width);
        background-color: #10254a;
        overflow-y: auto;
        z-index: 1030;
    }
    .sidebar-biogu .nav-link {
        color: #c9d4e6;
    }
    .sidebar-biogu .nav-link:hover {
        background-color: rgba(255,255,255,0.06);
        color: #fff;
    }
    .sidebar-biogu .nav-link.active {
        background-color: #24406f;
        color: #fff;
    }
    .sidebar-biogu .text-accent {
        color: #22c1a4 !important;
    }
    .bg-accent {
        background-color: #22c1a4 !important;
    }
</style>
<aside class="sidebar-biogu d-flex flex-column vh-100 position-fixed top-0 start-0">

    <div class="d-flex align-items-center gap-2 p-3 border-bottom border-secondary border-opacity-25">
        <div class="rounded-3 overflow-hidden" style="width:38px;height:38px;">
            <img src="/BioGuppy/Img/logo.jpeg" alt="BioGuppy" class="w-100 h-100" style="object-fit:cover;">
        </div>
        <div>
            <h1 class="h6 text-white mb-0">BioGuppy</h1>
        </div>
    </div>

    <div class="text-accent text-uppercase small fw-semibold px-3 pt-3 pb-2" style="letter-spacing:.05em; font-size:.7rem;">
        Coord. Control Biológico
    </div>

    <ul class="nav nav-pills flex-column px-2 gap-1 flex-grow-1">
        <li class="nav-item">
            <a href=" <?php echo getUrl("Zoocriaderos","Zoocriaderos","list") ?>" class="nav-link d-flex align-items-center gap-2
            <?php echo ($modulo == 'Zoocriaderos') ? 'active' : ''; ?>">
                <i class="bi bi-flask"></i> Zoocriaderos
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo getUrl("Sitios","Sitios","list") ?>" class="nav-link d-flex align-items-center gap-2
            <?php echo ($modulo == 'Sitios') ? 'active' : ''; ?>">
                <i class="bi bi-geo-alt"></i> Sitios / Depósitos
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo getUrl("Validar","Validar","list") ?>" class="nav-link d-flex align-items-center gap-2
            <?php echo ($modulo == 'Validar') ? 'active' : ''; ?>">
                <i class="bi bi-person-check"></i> Validar registros
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo getUrl("Auxiliares","Auxiliares","list") ?>" class="nav-link d-flex align-items-center gap-2
            <?php echo ($modulo == 'Auxiliares') ? 'active' : ''; ?>">
                <i class="bi bi-people"></i> Auxiliares
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo getUrl("Reportes","Reportes","list") ?>" class="nav-link d-flex align-items-center gap-2
            <?php echo ($modulo == 'Reportes') ? 'active' : ''; ?>">
                <i class="bi bi-bar-chart"></i> Reportes
            </a>
        </li>
        <li class="nav-item">
            <a href="<?php echo getUrl("Mapa","Mapa","list") ?>" class="nav-link d-flex align-items-center gap-2
            <?php echo ($modulo == 'Mapa') ? 'active' : ''; ?>">
                <i class="bi bi-map"></i> Mapa
            </a>
        </li>
    </ul>

    <div class="d-flex align-items-center gap-2 p-3 border-top border-secondary border-opacity-25">
        <div class="bg-accent text-dark fw-bold rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;font-size:.85rem;">
            <?php echo $primeraLetra = strtoupper(substr($_SESSION['usu_nombre'], 0, 1)); ?>
        </div>
        <div class="overflow-hidden">
            <div class="text-white small fw-semibold text-truncate"><?php echo $_SESSION['usu_nombre']; ?></div>
        </div>
    </div>

    <a href="<?php echo getUrl("Acceso", "Acceso","logout")?>" class="d-flex align-items-center gap-2 text-white-50 text-decoration-none px-3 pb-3 small">
        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
    </a>

</aside>

