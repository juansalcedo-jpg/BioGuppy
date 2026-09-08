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

    .otro{
        color: #38D7C8;
    }
</style>
<aside class="sidebar-biogu d-flex flex-column vh-100 position-fixed top-0 start-0">

    <div class="d-flex align-items-center gap-2 p-3 border-bottom border-secondary border-opacity-25">
        <div class="rounded-3 overflow-hidden" style="width:50px;height:50px;">
            <img src="/BioGuppy/Img/logo.png" alt="BioGuppy" class="w-100 h-100" style="object-fit:cover;">
        </div>
        <div>
            <h1 class="h6 text-white mb-0"><strong>Bio</strong><span class="otro">Guppy</span></h1>
            
        </div>
    </div>

    <div class="text-accent text-uppercase small fw-semibold px-3 pt-3 pb-2" style="letter-spacing:.05em; font-size:.7rem;">
        Coord. Control Biológico
    </div>

    <ul class="nav nav-pills flex-column px-2 gap-1 flex-grow-1">
        <?php
        if (isset($_SESSION['menu_file'])) {
            include_once $_SESSION['menu_file'];
        }
        ?>
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

