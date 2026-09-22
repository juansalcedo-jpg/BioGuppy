<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" 
    rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" 
    crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <title>BioGuppy</title>
    <style>
        :root {
            --sidebar-width: 260px;
        }
        * {
            box-sizing: border-box;
        }
        html, body {
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
        background: rgba(0,0,0,0.5);
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

body.tema-oscuro {
    background-color: #0f172a;
    color: #e2e8f0;
}

body.tema-oscuro .topbar {
    background-color: #16213a !important;
    border-bottom-color: #263354 !important;
}

body.tema-oscuro .topbar-date,
body.tema-oscuro .menu-icon,
body.tema-oscuro .btn-accesibilidad {
    color: #c9d4e6 !important;
}

body.tema-oscuro .dropdown-menu {
    background-color: #16213a;
    border-color: #263354;
}

body.tema-oscuro .dropdown-item {
    color: #e2e8f0;
}

body.tema-oscuro .dropdown-item:hover,
body.tema-oscuro .dropdown-item:focus {
    background-color: #263354;
    color: #fff;
}

body.tema-oscuro .card,
body.tema-oscuro .modal-content,
body.tema-oscuro .bg-white {
    background-color: #16213a !important;
    color: #e2e8f0 !important;
    border-color: #263354 !important;
}

body.tema-oscuro .table {
    color: #e2e8f0;
}

body.tema-oscuro .table > :not(caption) > * > * {
    background-color: #16213a;
    color: #e2e8f0;
    border-bottom-color: #263354;
}

body.tema-oscuro .table-dark {
    --bs-table-bg: #10254a;
}

body.tema-oscuro .table-striped > tbody > tr:nth-of-type(odd) > * {
    background-color: #1b2942;
}

body.tema-oscuro .form-control,
body.tema-oscuro .form-select {
    background-color: #16213a;
    color: #e2e8f0;
    border-color: #263354;
}

body.tema-oscuro .form-control::placeholder {
    color: #7d8aa3;
}

body.tema-oscuro .text-dark,
body.tema-oscuro .text-body,
body.tema-oscuro .text-secondary {
    color: #ffffff !important;
}

body.tema-oscuro .text-muted {
    color: #93a3bf !important;
}

body.tema-oscuro .border,
body.tema-oscuro .border-bottom,
body.tema-oscuro .border-top {
    border-color: #263354 !important;
}
    </style>
</head>