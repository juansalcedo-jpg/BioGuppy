<style>
.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 64px;
    padding: 0 24px;
    background-color: #ffffff;
    border-bottom: 1px solid #e5e7eb;
    font-family: 'Segoe UI', Arial, sans-serif;
    position: sticky;
    top: 0;
    z-index: 1020;
}

.topbar-left {
    display: flex;
    align-items: center;
}

.menu-icon {
    width: 22px;
    height: 22px;
    cursor: pointer;
    color: #374151;
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 20px;
}

.topbar-date {
    font-size: 14px;
    color: #374151;
}

.btn-accesibilidad {
    background: none;
    border: none;
    padding: 0;
    display: flex;
    align-items: center;
    cursor: pointer;
    color: #4b5563;
    font-size: 20px;
}

.btn-accesibilidad:hover {
    color: #159EE8;
}

.accesibilidad-wrapper .dropdown-item.filtro-activo {
    font-weight: 600;
    color: #159EE8;
}

</style>

<div class="topbar">
    <div class="topbar-left">
    <svg id="btnToggleSidebar" class="menu-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
    </svg>
</div>

<div class="topbar-right">
    <span class="topbar-date">
        <?php
            date_default_timezone_set("America/Bogota");
            $formatter = new IntlDateFormatter(
                'es_ES',
                IntlDateFormatter::FULL,
                IntlDateFormatter::NONE,
                'America/Bogota',
                IntlDateFormatter::GREGORIAN,
                "EEEE, d 'de' MMMM 'de' y"
            );
            echo $formatter->format(new DateTime());
        ?>
    </span>
 <div class="dropdown accesibilidad-wrapper">
    <button class="btn-accesibilidad" id="btnDaltonismo" type="button"
            data-bs-toggle="dropdown" aria-expanded="false" title="Modo para daltonismo">
        <i class="bi bi-eye"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="btnDaltonismo">
        <li><a class="dropdown-item" href="#" data-filtro="ninguno">Sin filtro</a></li>
        <li><a class="dropdown-item" href="#" data-filtro="protanopia">Protanopia</a></li>
        <li><a class="dropdown-item" href="#" data-filtro="deuteranopia">Deuteranopia</a></li>
        <li><a class="dropdown-item" href="#" data-filtro="tritanopia">Tritanopia</a></li>
    </ul>
</div>


<button class="btn-accesibilidad" id="botonTema" type="button" title="Cambiar a tema oscuro">
    <i class="bi bi-moon-stars" id="iconoTema"></i>
</button>
    
    </div>

    

</div>