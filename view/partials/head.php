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
            overflow: hidden; /* la página en sí nunca hace scroll */
        }
        .app-layout {
            display: flex;
            flex: 1 1 auto;
            min-height: 0; /* permite que el contenido interno se encoja y scrollee en vez de crecer */
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
        .page-content {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto; /* único punto de scroll de toda la app */
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
    </style>
</head>