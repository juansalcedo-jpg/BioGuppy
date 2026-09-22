<?php
$funcion = $_GET['funcion'] ?? '';
?>
<style>
    .sidebar-biogu {
        width: var(--sidebar-width);
        background-color: #10254a;
        overflow-y: auto;
        z-index: 1030;
        transition: transform .25s ease;
    }
    .app-layout.sidebar-collapsed .sidebar-biogu {
        transform: translateX(-100%);
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
    color: #159EE8 !important;
}
    .bg-accent {
    background-color: #159EE8 !important;
}

    .otro{
        color:  #159EE8;
    }

        .sn-menu-link {
        color: #c9d4e6;
        font-size: .78rem;
        padding: 4px 0;
    }
    .sn-menu-link:hover,
    .sn-menu-link.active {
        color: #159EE8;
    }

    @media (max-width: 991.98px) {
    .sidebar-biogu {
        transform: translateX(-100%);
        z-index: 1050;
    }
    .app-layout.sidebar-collapsed .sidebar-biogu {
        transform: translateX(0);
    }
}
</style>
<aside class="sidebar-biogu d-flex flex-column vh-100 position-fixed top-0 start-0">

    <a href="<?php echo getUrl('Inicio', 'Inicio', 'index') ?>"
       class="d-flex align-items-center gap-2 p-3 border-bottom border-secondary border-opacity-25 text-decoration-none">
        <div class="rounded-3 overflow-hidden" style="width:70px;height:70px;">
            <img src="/BioGuppy/Img/logo.png" alt="BioGuppy" class="w-100 h-100" style="object-fit:cover;">
        </div>
        <div>
            <h1 class="h5 text-white mb-0"><strong>Bio</strong><span class="otro">Guppy</span></h1>
            
        </div>
    </a>

    <div class="text-accent text-uppercase small fw-semibold px-3 pt-3 pb-2" style="letter-spacing:.05em; font-size:.7rem;">
        <?php echo $_SESSION['nombre_rol']; ?>
    </div>

    <ul class="nav nav-pills flex-column px-2 gap-1 flex-grow-1">
        <?php
        if (isset($_SESSION['menu_file'])) {
            include_once $_SESSION['menu_file'];
        }
        ?>
    </ul>

        <div class="px-3 pb-2 pt-1 border-top border-secondary border-opacity-25">
        <a href="<?php echo getUrl('SobreNosotros', 'SobreNosotros', 'index') ?>"
           class="d-flex align-items-center gap-2 text-decoration-none sn-menu-link <?php echo ($_GET['modulo'] == 'SobreNosotros') ? 'active' : ''; ?>">
            <i class="bi bi-info-circle"></i> Sobre nosotros
        </a>
    </div>

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