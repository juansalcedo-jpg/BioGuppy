<?php
$funcion           = $_GET['funcion'] ?? '';
$moduloActual      = strtolower($_GET['modulo'] ?? '');
$controladorActual = strtolower($_GET['controlador'] ?? '');

// El menú sale de la BD según los módulos que tiene el rol (tblrolmodulo).
$menuLateral = menuDelRol($_SESSION['codrol'] ?? null);

/*
 * Dentro de un módulo con varios enlaces, marca como activo:
 *  1) el enlace cuya función coincide exactamente, o si no
 *  2) el último enlace del mismo controlador (ej: Siembra -> "Registrar actividad").
 */
if (!function_exists('enlaceActivoDelModulo')) {
function enlaceActivoDelModulo($enlaces, $controladorActual, $funcion)
{
    $activo = null;
    foreach ($enlaces as $i => $enlace) {
        if (strtolower($enlace['controlador']) !== $controladorActual) {
            continue;
        }
        if ($enlace['funcion'] === $funcion) {
            return $i;
        }
        $activo = $i;
    }
    return $activo;
}
}
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

    .sidebar-biogu .sidebar-seccion {
        color: #7f93b5;
        font-size: .7rem;
        letter-spacing: .05em;
    }
    .sidebar-biogu .nav-link[data-bs-toggle="collapse"] .bi-caret-down-fill {
        transition: transform .2s ease;
        font-size: .7rem;
    }
    .sidebar-biogu .nav-link[data-bs-toggle="collapse"].collapsed .bi-caret-down-fill {
        transform: rotate(-90deg);
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

    /* [AGREGADO] Efecto al pasar el mouse sobre el bloque del usuario (ahora es un enlace al perfil) */
    .sidebar-perfil:hover,
    /* [AGREGADO] Mismo fondo cuando el usuario está en la página de su perfil */
    .sidebar-perfil.active {
        background-color: rgba(255,255,255,0.06); /* [AGREGADO] Fondo blanco muy transparente, igual al hover de .nav-link */
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
        <?php echo htmlspecialchars($_SESSION['nombre_rol'] ?? ''); ?>
    </div>

    <ul class="nav nav-pills flex-column px-2 gap-1 flex-grow-1">
        <?php if (empty($menuLateral)): ?>
            <li class="px-3 py-2 small text-white-50">
                Tu rol todavía no tiene módulos asignados.
            </li>
        <?php endif; ?>

        <?php foreach ($menuLateral as $seccion => $modulos): ?>
            <li class="sidebar-seccion text-uppercase fw-semibold px-3 pt-3 pb-1"><?php echo htmlspecialchars($seccion); ?></li>

            <?php foreach ($modulos as $mod):
                $esModuloActual = strtolower($mod['carpeta']) === $moduloActual;
                $enlaces        = $mod['enlaces'];
            ?>

                <?php if (count($enlaces) === 1):
                    $enlace = $enlaces[0]; ?>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold d-flex align-items-center <?php echo $esModuloActual ? 'active' : ''; ?>"
                           href="<?php echo getUrl($mod['carpeta'], $enlace['controlador'], $enlace['funcion']); ?>">
                            <i class="bi <?php echo htmlspecialchars($mod['icono']); ?> me-2"></i>
                            <?php echo htmlspecialchars($enlace['texto']); ?>
                        </a>
                    </li>

                <?php else:
                    $idSubmenu   = 'submenu' . (int) $mod['codmodulo'];
                    $indiceActivo = $esModuloActual ? enlaceActivoDelModulo($enlaces, $controladorActual, $funcion) : null;
                ?>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold d-flex align-items-center <?php echo $esModuloActual ? '' : 'collapsed'; ?>"
                           data-bs-toggle="collapse" href="#<?php echo $idSubmenu; ?>" role="button"
                           aria-expanded="<?php echo $esModuloActual ? 'true' : 'false'; ?>"
                           aria-controls="<?php echo $idSubmenu; ?>">
                            <i class="bi <?php echo htmlspecialchars($mod['icono']); ?> me-2"></i>
                            <?php echo htmlspecialchars($mod['nombremodulo']); ?>
                            <i class="bi bi-caret-down-fill ms-auto"></i>
                        </a>
                        <div class="collapse <?php echo $esModuloActual ? 'show' : ''; ?>" id="<?php echo $idSubmenu; ?>">
                            <ul class="list-unstyled ps-4">
                                <?php foreach ($enlaces as $i => $enlace): ?>
                                    <li>
                                        <a class="nav-link <?php echo ($indiceActivo === $i) ? 'active' : ''; ?>"
                                           href="<?php echo getUrl($mod['carpeta'], $enlace['controlador'], $enlace['funcion']); ?>">
                                            <?php echo htmlspecialchars($enlace['texto']); ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>

            <?php endforeach; ?>
        <?php endforeach; ?>
    </ul>

        <div class="px-3 pb-2 pt-1 border-top border-secondary border-opacity-25">
        <a href="<?php echo getUrl('SobreNosotros', 'SobreNosotros', 'index') ?>"
           class="d-flex align-items-center gap-2 text-decoration-none sn-menu-link <?php echo ($moduloActual === 'sobrenosotros') ? 'active' : ''; ?>">
            <i class="bi bi-info-circle"></i> Sobre nosotros
        </a>
    </div>

    <?php
    $fotoSidebar = fotoPerfilUrl($_SESSION['usu_id'] ?? null);
    ?>

    <a href="<?php echo getUrl('Perfil', 'Perfil', 'perfil') ?>"
       class="sidebar-perfil d-flex align-items-center gap-2 p-3 border-top border-secondary border-opacity-25 text-decoration-none <?php echo (($_GET['modulo'] ?? '') == 'Perfil') ? 'active' : ''; ?>"
       title="Ver mi perfil">
        <div class="bg-accent text-dark fw-bold rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 overflow-hidden" style="width:36px;height:36px;font-size:.85rem;">
            <?php if ($fotoSidebar): ?>
                <img src="<?php echo htmlspecialchars($fotoSidebar); ?>" alt="Foto de perfil" class="w-100 h-100" style="object-fit:cover;">
            <?php else: ?>
                <?php echo strtoupper(mb_substr($_SESSION['usu_nombre'], 0, 1)); ?>
            <?php endif; ?>
        </div>

        <div class="overflow-hidden flex-grow-1">
            <div class="text-white small fw-semibold text-truncate"><?php echo htmlspecialchars($_SESSION['usu_nombre']); ?></div>
            <div class="text-white-50 text-truncate" style="font-size:.7rem;">Ver mi perfil</div>
        </div>
        <i class="bi bi-chevron-right text-white-50 small"></i>
    </a>

    <a href="<?php echo getUrl("Acceso", "Acceso","logout")?>" class="d-flex align-items-center gap-2 text-white-50 text-decoration-none px-3 pb-3 small">
        <i class="bi bi-box-arrow-right"></i> Cerrar sesión
    </a>

</aside>