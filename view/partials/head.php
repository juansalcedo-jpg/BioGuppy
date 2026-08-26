<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" 
    rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" 
    crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <title>Document</title>
    <style>
        :root {
            --sidebar-width: 260px;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            background-color: #eaf1fb;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .app-layout {
            display: flex;
            min-height: 100vh;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            min-height: 100vh;
        }
        .page-content {
            flex: 1;
            padding: 24px;
        }
    </style>
</head>