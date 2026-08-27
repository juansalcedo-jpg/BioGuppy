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