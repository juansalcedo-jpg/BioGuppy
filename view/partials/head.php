<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        (function() {
            try {
                if (localStorage.getItem("tema") === "oscuro") {
                    document.documentElement.setAttribute("data-bs-theme", "dark");
                }
            } catch (e) {}
        })();
    </script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
        crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <title>BioGuppy</title>
    <link rel="icon" type="image/png" href="../img/logosinfondo.png">
    <style>
        :root {
            --sidebar-width: 260px;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            background-color: #eaf1fb;
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .app-layout {
            display: flex;
            flex: 1 1 auto;
            min-height: 0;
        }


        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 0;
            transition: margin-left .25s ease;
        }

        @media (max-width: 991.98px) {
            .main-content {
                margin-left: 0 !important;
            }
        }

        .sidebar-backdrop {
            display: none;
        }

        @media (max-width: 991.98px) {
            .app-layout.sidebar-collapsed .sidebar-backdrop {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1040;
            }
        }

        .page-content {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            padding: 24px;
        }

        .app-layout.sidebar-collapsed .main-content {
            margin-left: 0;
        }

        body.modo-protanopia {
            filter: url(#filtro-protanopia);
        }

        body.modo-deuteranopia {
            filter: url(#filtro-deuteranopia);
        }

        body.modo-tritanopia {
            filter: url(#filtro-tritanopia);
        }

        body {
            transition: background-color .2s ease, color .2s ease;
        }

        html[data-bs-theme="dark"] {
            --bg-oscuro-fondo: #0f172a;
            /* fondo general          */
            --bg-oscuro-superficie: #16213a;
            /* cards, modales, tablas */
            --bg-oscuro-elevado: #1b2942;
            /* encabezados, hover     */
            --bg-oscuro-hover: #22324f;
            --borde-oscuro: #2b3a5c;
            --texto-oscuro: #e2e8f0;
            --texto-oscuro-suave: #b6c2d6;
            --texto-oscuro-muted: #93a3bf;

            /* Variables de Bootstrap para que todo lo nativo use la paleta */
            --bs-body-bg: var(--bg-oscuro-fondo);
            --bs-body-bg-rgb: 15, 23, 42;
            --bs-body-color: var(--texto-oscuro);
            --bs-body-color-rgb: 226, 232, 240;
            --bs-emphasis-color: #ffffff;
            --bs-secondary-color: var(--texto-oscuro-muted);
            --bs-secondary-bg: var(--bg-oscuro-elevado);
            --bs-tertiary-bg: var(--bg-oscuro-elevado);
            --bs-tertiary-color: var(--texto-oscuro-muted);
            --bs-border-color: var(--borde-oscuro);
            --bs-border-color-translucent: rgba(148, 163, 191, 0.18);
            --bs-heading-color: #f1f5f9;
            --bs-link-color: #60a5fa;
            --bs-link-hover-color: #93c5fd;
            --bs-link-color-rgb: 96, 165, 250;
            --bs-light-rgb: 27, 41, 66;
            /* bg-light (con o sin opacidad) */

            color-scheme: dark;
            /* scrollbars e íconos de fecha oscuros */
        }

        html[data-bs-theme="dark"] body {
            background-color: var(--bg-oscuro-fondo);
            color: var(--texto-oscuro);
        }

        /* ---------- 2. Superficies ---------- */
        html[data-bs-theme="dark"] .card,
        html[data-bs-theme="dark"] .modal-content,
        html[data-bs-theme="dark"] .bg-white,
        html[data-bs-theme="dark"] .bg-body,
        html[data-bs-theme="dark"] .list-group-item,
        html[data-bs-theme="dark"] .offcanvas {
            background-color: var(--bg-oscuro-superficie) !important;
            color: var(--texto-oscuro);
            border-color: var(--borde-oscuro) !important;
        }

        html[data-bs-theme="dark"] .bg-light,
        html[data-bs-theme="dark"] .bg-body-tertiary,
        html[data-bs-theme="dark"] .bg-body-secondary {
            background-color: var(--bg-oscuro-elevado) !important;
            color: var(--texto-oscuro);
        }

        html[data-bs-theme="dark"] .card-header,
        html[data-bs-theme="dark"] .card-footer,
        html[data-bs-theme="dark"] .modal-header,
        html[data-bs-theme="dark"] .modal-footer {
            border-color: var(--borde-oscuro) !important;
        }

        html[data-bs-theme="dark"] .bg-transparent {
            background-color: transparent !important;
        }

        /* ---------- 3. Textos ---------- */
        html[data-bs-theme="dark"] h1,
        html[data-bs-theme="dark"] h2,
        html[data-bs-theme="dark"] h3,
        html[data-bs-theme="dark"] h4,
        html[data-bs-theme="dark"] h5,
        html[data-bs-theme="dark"] h6,
        html[data-bs-theme="dark"] .text-dark,
        html[data-bs-theme="dark"] .text-black,
        html[data-bs-theme="dark"] .text-body,
        html[data-bs-theme="dark"] .text-body-emphasis {
            color: #f1f5f9 !important;
        }

        html[data-bs-theme="dark"] .text-secondary,
        html[data-bs-theme="dark"] .form-label,
        html[data-bs-theme="dark"] .form-check-label {
            color: var(--texto-oscuro-suave) !important;
        }

        html[data-bs-theme="dark"] .text-muted,
        html[data-bs-theme="dark"] .text-body-secondary,
        html[data-bs-theme="dark"] .form-text {
            color: var(--texto-oscuro-muted) !important;
        }

        html[data-bs-theme="dark"] .text-primary {
            color: #60a5fa !important;
        }

        html[data-bs-theme="dark"] .text-danger {
            color: #f87171 !important;
        }

        html[data-bs-theme="dark"] .text-success {
            color: #4ade80 !important;
        }

        /* Textos que deben seguir siendo blancos (sobre fondos de color) */
        html[data-bs-theme="dark"] .text-white,
        html[data-bs-theme="dark"] .bg-primary .text-white,
        html[data-bs-theme="dark"] .btn-primary,
        html[data-bs-theme="dark"] .btn-success,
        html[data-bs-theme="dark"] .btn-danger {
            color: #ffffff !important;
        }

        /* ---------- 4. Bordes ---------- */
        html[data-bs-theme="dark"] .border,
        html[data-bs-theme="dark"] .border-top,
        html[data-bs-theme="dark"] .border-bottom,
        html[data-bs-theme="dark"] .border-start,
        html[data-bs-theme="dark"] .border-end,
        html[data-bs-theme="dark"] hr {
            border-color: var(--borde-oscuro) !important;
        }

        /* Los bordes de color se conservan */
        html[data-bs-theme="dark"] .border-primary {
            border-color: #3b82f6 !important;
        }

        html[data-bs-theme="dark"] .border-danger,
        html[data-bs-theme="dark"] .border-danger-subtle {
            border-color: rgba(248, 113, 113, .45) !important;
        }

        html[data-bs-theme="dark"] .border-success,
        html[data-bs-theme="dark"] .border-success-subtle {
            border-color: rgba(74, 222, 128, .45) !important;
        }

        /* ---------- 5. Tablas ---------- */
        html[data-bs-theme="dark"] .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--texto-oscuro);
            --bs-table-border-color: var(--borde-oscuro);
            --bs-table-striped-bg: rgba(255, 255, 255, 0.03);
            --bs-table-striped-color: var(--texto-oscuro);
            --bs-table-hover-bg: var(--bg-oscuro-hover);
            --bs-table-hover-color: #ffffff;
            color: var(--texto-oscuro);
            border-color: var(--borde-oscuro);
        }

        html[data-bs-theme="dark"] .table> :not(caption)>*>* {
            background-color: var(--bs-table-bg);
            color: var(--bs-table-color);
            border-bottom-color: var(--borde-oscuro);
        }

        html[data-bs-theme="dark"] .table-hover>tbody>tr:hover>* {
            background-color: var(--bg-oscuro-hover);
            color: #ffffff;
        }

        /* Encabezados de tabla (table-light, thead bg-white/bg-light, sticky) */
        html[data-bs-theme="dark"] .table-light,
        html[data-bs-theme="dark"] .table>thead,
        html[data-bs-theme="dark"] .table>thead>tr>th {
            --bs-table-bg: var(--bg-oscuro-elevado);
            --bs-table-color: var(--texto-oscuro-suave);
            background-color: var(--bg-oscuro-elevado) !important;
            color: var(--texto-oscuro-suave) !important;
            border-color: var(--borde-oscuro) !important;
        }

        html[data-bs-theme="dark"] .table-dark {
            --bs-table-bg: #10254a;
            --bs-table-color: #ffffff;
        }

        /* ---------- 6. Formularios ---------- */
        html[data-bs-theme="dark"] .form-control,
        html[data-bs-theme="dark"] .form-select {
            background-color: var(--bg-oscuro-superficie);
            color: var(--texto-oscuro);
            border-color: var(--borde-oscuro);
        }

        html[data-bs-theme="dark"] .form-control.bg-white,
        html[data-bs-theme="dark"] .form-select.bg-white {
            background-color: var(--bg-oscuro-superficie) !important;
        }

        html[data-bs-theme="dark"] .form-control:focus,
        html[data-bs-theme="dark"] .form-select:focus {
            background-color: var(--bg-oscuro-elevado);
            color: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 .2rem rgba(59, 130, 246, .25);
        }

        html[data-bs-theme="dark"] .form-control:disabled,
        html[data-bs-theme="dark"] .form-control[readonly],
        html[data-bs-theme="dark"] .form-select:disabled {
            background-color: #111b30;
            color: var(--texto-oscuro-muted);
        }

        html[data-bs-theme="dark"] .form-control::placeholder {
            color: #7d8aa3;
            opacity: 1;
        }

        html[data-bs-theme="dark"] .form-select option {
            background-color: var(--bg-oscuro-superficie);
            color: var(--texto-oscuro);
        }

        html[data-bs-theme="dark"] .input-group-text {
            background-color: var(--bg-oscuro-elevado) !important;
            color: var(--texto-oscuro-suave) !important;
            border-color: var(--borde-oscuro) !important;
        }

        /* Contenedores de input con borde propio (reportes, historiales, formularios) */
        html[data-bs-theme="dark"] .input-group:focus-within {
            background-color: var(--bg-oscuro-elevado) !important;
        }

        html[data-bs-theme="dark"] .form-check-input {
            background-color: var(--bg-oscuro-elevado);
            border-color: #475569;
        }

        html[data-bs-theme="dark"] .form-check-input:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        /* ---------- 7. Botones ---------- */
        html[data-bs-theme="dark"] .btn-light,
        html[data-bs-theme="dark"] .btn-white {
            --bs-btn-bg: var(--bg-oscuro-elevado);
            --bs-btn-color: var(--texto-oscuro);
            --bs-btn-border-color: var(--borde-oscuro);
            --bs-btn-hover-bg: var(--bg-oscuro-hover);
            --bs-btn-hover-color: #ffffff;
            --bs-btn-hover-border-color: #3b4c70;
            --bs-btn-active-bg: #2a3d60;
            --bs-btn-active-color: #ffffff;
            --bs-btn-active-border-color: #3b4c70;
            --bs-btn-disabled-bg: var(--bg-oscuro-elevado);
            --bs-btn-disabled-color: var(--texto-oscuro-muted);
        }

        html[data-bs-theme="dark"] .btn-outline-secondary,
        html[data-bs-theme="dark"] .btn-outline-dark {
            --bs-btn-color: var(--texto-oscuro-suave);
            --bs-btn-border-color: #475569;
            --bs-btn-hover-bg: var(--bg-oscuro-hover);
            --bs-btn-hover-color: #ffffff;
            --bs-btn-hover-border-color: #64748b;
            --bs-btn-active-bg: #2a3d60;
            --bs-btn-active-color: #ffffff;
        }

        html[data-bs-theme="dark"] .btn-outline-primary {
            --bs-btn-color: #60a5fa;
            --bs-btn-border-color: #3b82f6;
            --bs-btn-hover-bg: #2563eb;
            --bs-btn-hover-color: #ffffff;
            --bs-btn-hover-border-color: #2563eb;
        }

        html[data-bs-theme="dark"] .btn-outline-danger {
            --bs-btn-color: #f87171;
            --bs-btn-border-color: #ef4444;
            --bs-btn-hover-bg: #dc2626;
            --bs-btn-hover-color: #ffffff;
            --bs-btn-hover-border-color: #dc2626;
        }

        html[data-bs-theme="dark"] .btn-outline-success {
            --bs-btn-color: #4ade80;
            --bs-btn-border-color: #22c55e;
            --bs-btn-hover-bg: #16a34a;
            --bs-btn-hover-color: #ffffff;
            --bs-btn-hover-border-color: #16a34a;
        }

        /* Botones de outline con fondo blanco forzado (ej. "Exportar PDF") */
        html[data-bs-theme="dark"] .btn.bg-white {
            background-color: transparent !important;
        }

        html[data-bs-theme="dark"] .btn-dark {
            --bs-btn-bg: #334155;
            --bs-btn-border-color: #334155;
            --bs-btn-hover-bg: #475569;
            --bs-btn-hover-border-color: #475569;
        }

        /* ---------- 8. Badges ---------- */
        html[data-bs-theme="dark"] .badge.bg-light,
        html[data-bs-theme="dark"] .badge.bg-white {
            background-color: var(--bg-oscuro-elevado) !important;
            color: var(--texto-oscuro) !important;
            border-color: var(--borde-oscuro) !important;
        }

        /* ---------- 9. Navegación, pestañas y menús ---------- */
        html[data-bs-theme="dark"] .nav-tabs {
            border-color: var(--borde-oscuro);
        }

        html[data-bs-theme="dark"] .nav-tabs .nav-link {
            color: var(--texto-oscuro-suave);
        }

        html[data-bs-theme="dark"] .nav-tabs .nav-link.active {
            background-color: var(--bg-oscuro-superficie);
            color: #60a5fa;
            border-color: var(--borde-oscuro) var(--borde-oscuro) var(--bg-oscuro-superficie);
        }

        html[data-bs-theme="dark"] .dropdown-menu {
            background-color: var(--bg-oscuro-superficie);
            border-color: var(--borde-oscuro);
        }

        html[data-bs-theme="dark"] .dropdown-item {
            color: var(--texto-oscuro);
        }

        html[data-bs-theme="dark"] .dropdown-item:hover,
        html[data-bs-theme="dark"] .dropdown-item:focus {
            background-color: var(--bg-oscuro-hover);
            color: #ffffff;
        }

        /* Barra superior del sistema */
        html[data-bs-theme="dark"] .topbar {
            background-color: var(--bg-oscuro-superficie) !important;
            border-bottom-color: var(--borde-oscuro) !important;
        }

        html[data-bs-theme="dark"] .topbar-date,
        html[data-bs-theme="dark"] .menu-icon,
        html[data-bs-theme="dark"] .btn-accesibilidad {
            color: #c9d4e6 !important;
        }

        /* ---------- 10. Otros ---------- */
        html[data-bs-theme="dark"] .shadow-sm,
        html[data-bs-theme="dark"] .shadow,
        html[data-bs-theme="dark"] .shadow-xs {
            box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .35) !important;
        }

        html[data-bs-theme="dark"] .pagination {
            --bs-pagination-bg: var(--bg-oscuro-superficie);
            --bs-pagination-color: var(--texto-oscuro);
            --bs-pagination-border-color: var(--borde-oscuro);
            --bs-pagination-hover-bg: var(--bg-oscuro-hover);
            --bs-pagination-hover-color: #ffffff;
            --bs-pagination-disabled-bg: var(--bg-oscuro-superficie);
        }

        /* Íconos de calendario y reloj en inputs de fecha */
        html[data-bs-theme="dark"] input[type="date"]::-webkit-calendar-picker-indicator,
        html[data-bs-theme="dark"] input[type="time"]::-webkit-calendar-picker-indicator {
            filter: invert(0.85);
        }

        /* Barra de desplazamiento */
        html[data-bs-theme="dark"] ::-webkit-scrollbar-thumb {
            background-color: #334155;
            border-radius: 8px;
        }

        html[data-bs-theme="dark"] ::-webkit-scrollbar-track {
            background-color: var(--bg-oscuro-fondo);
        }

        /* Vistas de Inicio y Sobre nosotros (variables institucionales) */
        html[data-bs-theme="dark"] {
            --gov-gris-texto: #c9d4e6;
            --gov-marine-suave: rgba(96, 165, 250, 0.08);
        }
    </style>
</head>