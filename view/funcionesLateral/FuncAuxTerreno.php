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