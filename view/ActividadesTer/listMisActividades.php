<div class="container-fluid py-2">

<<<<<<< HEAD
=======
    <div class="row justify-content-center">
<<<<<<< HEAD
        <div class="col-xl-11">

=======

<<<<<<< HEAD
>>>>>>> c8d9b80a90ca4c0fc1f67f3b9b448d554867cf4b
            <!-- Alertas de Sesión o Errores -->
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm border-0 rounded-4">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-danger"></i>
                    <div><?php echo htmlspecialchars($_SESSION['error']); ?></div>
<<<<<<< HEAD
=======
=======
        <div class="col-xl-11">

            <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">

                <div>

                    <h4 class="fw-semibold mb-1">
                        Mis actividades — Terreno
                    </h4>

                    <p class="text-muted small mb-0">
                        Consulta y filtra las actividades de terreno que has registrado.
                    </p>

                </div>

            </div>


            <?php if(isset($_SESSION['error'])): ?>

                <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">

                    <i class="bi bi-exclamation-triangle-fill me-2"></i>

                    <div>
                        <?php echo htmlspecialchars($_SESSION['error']); ?>
                    </div>

>>>>>>> f9469daa2251999c245857af06840f7437ecb8e3
>>>>>>> c8d9b80a90ca4c0fc1f67f3b9b448d554867cf4b
                </div>

                <?php unset($_SESSION['error']); ?>

            <?php endif; ?>

<<<<<<< HEAD
=======
<<<<<<< HEAD
            <?php if(isset($_SESSION['exito'])): ?>
                <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm border-0 rounded-4">
                    <i class="bi bi-check-circle-fill me-3 fs-4 text-success"></i>
                    <div><?php echo htmlspecialchars($_SESSION['exito']); ?></div>
=======

            <?php if(isset($_SESSION['exito'])): ?>

                <div class="alert alert-success d-flex align-items-center mb-3" role="alert">

                    <i class="bi bi-check-circle-fill me-2"></i>

                    <div>
                        <?php echo htmlspecialchars($_SESSION['exito']); ?>
                    </div>

>>>>>>> f9469daa2251999c245857af06840f7437ecb8e3
                </div>

                <?php unset($_SESSION['exito']); ?>

            <?php endif; ?>


<<<<<<< HEAD
>>>>>>> 15f7c46c63a2484819d5aa978944bd22c24b9ed4
            <!-- Encabezado de la Sección -->
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">
                        <i class="bi bi-clipboard-data text-primary me-2"></i> Actividades de Terreno
                    </h3>
                    <p class="text-muted mb-0">
                        Monitoreo, control y filtrado general de labores en campo.
                    </p>
                </div>
                <div>
                    <span class="badge bg-white text-dark shadow-sm border px-3 py-2 rounded-pill fs-6">
                        <i class="bi bi-calendar-event text-primary me-1"></i> <?php echo date('d M, Y'); ?>
                    </span>
                </div>
            </div>
<<<<<<< HEAD

            <!-- Alertas de Sesión o Errores -->
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm border-0 rounded-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-danger"></i>
                    <div><?php echo htmlspecialchars($_SESSION['error']); ?></div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

>>>>>>> c8d9b80a90ca4c0fc1f67f3b9b448d554867cf4b
            <?php if(isset($_SESSION['exito'])): ?>
                <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm border-0 rounded-4" role="alert">
                    <i class="bi bi-check-circle-fill me-3 fs-4 text-success"></i>
                    <div><?php echo htmlspecialchars($_SESSION['exito']); ?></div>
                </div>
                <?php unset($_SESSION['exito']); ?>
            <?php endif; ?>

            <?php if(!empty($errorFechas)): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm border-0 rounded-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-danger"></i>
                    <div><?php echo htmlspecialchars($errorFechas); ?></div>
                </div>
            <?php endif; ?>
<<<<<<< HEAD

            <!-- Encabezado de la Sección -->
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">
                        <i class="bi bi-clipboard-data text-primary me-2"></i> Actividades de Terreno
                    </h3>
                    <p class="text-muted mb-0">
                        Monitoreo, control y filtrado general de labores en campo.
                    </p>
                </div>
                <div>
                    <span class="badge bg-white text-dark shadow-sm border px-3 py-2 rounded-pill fs-6">
                        <i class="bi bi-calendar-event text-primary me-1"></i> <?php echo date('d M, Y'); ?>
                    </span>
                </div>
            </div>
=======
=======
=======
            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-bottom py-3">

                    <div class="d-flex align-items-center mb-3">

                        <i class="bi bi-file-earmark-text text-primary me-2 fs-5"></i>

                        <span class="fw-semibold">
                            Actividades registradas por mí
                        </span>

                    </div>


                    <form
                        id="formFiltroMisActividadesTer"
                        action="<?php echo getUrl('ActividadesTer','ActividadesTer','filtro',false,'ajax'); ?>"
                        method="POST">

                        <div class="row g-2 align-items-end">


                            <div class="col-6 col-md-2">

                                <label
                                    for="btnMesFiltro"
                                    class="form-label small text-muted mb-1">

                                    Mes

                                </label>

                                <div class="dropdown">

                                    <input
                                        type="hidden"
                                        id="mesFiltro"
                                        name="mes"
                                        value="">

                                    <button
                                        class="form-select form-select-sm text-start"
                                        type="button"
                                        id="btnMesFiltro"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false">

                                        Todos

                                    </button>


                                    <div class="dropdown-menu p-2">

                                        <div class="fw-semibold bg-light px-2 py-1 mb-2">

                                            <?php echo date('Y'); ?>

                                        </div>


                                        <div class="row row-cols-4 g-1">

                                            <?php

                                            $meses=[
                                                '01'=>'Ene',
                                                '02'=>'Feb',
                                                '03'=>'Mar',
                                                '04'=>'Abr',
                                                '05'=>'May',
                                                '06'=>'Jun',
                                                '07'=>'Jul',
                                                '08'=>'Ago',
                                                '09'=>'Sept',
                                                '10'=>'Oct',
                                                '11'=>'Nov',
                                                '12'=>'Dic'
                                            ];

                                            foreach($meses as $numero=>$nombre):

                                            ?>

                                                <div class="col">

                                                    <button
                                                        type="button"
                                                        class="dropdown-item text-center px-2"
                                                        onclick="seleccionarMes(
                                                            '<?php echo date('Y').'-'.$numero; ?>',
                                                            '<?php echo $nombre; ?>'
                                                        )">

                                                        <?php echo $nombre; ?>

                                                    </button>

                                                </div>

                                            <?php endforeach; ?>

                                        </div>


                                        <div class="border-top mt-2 pt-2">

                                            <button
                                                type="button"
                                                class="btn btn-link btn-sm text-decoration-none p-0"
                                                onclick="seleccionarMes('','Todos')">

                                                Borrar

                                            </button>

                                        </div>

                                    </div>

                                </div>

                            </div>


                            <div class="col-12 col-md-3">

                                <label
                                    for="selectDeposito"
                                    class="form-label small text-muted mb-1">

                                    Depósito

                                </label>

                                <select
                                    id="selectDeposito"
                                    name="coddeposito"
                                    class="form-select form-select-sm">

                                    <option value="">
                                        Todos
                                    </option>

                                    <?php if(isset($depositos)&&$depositos): ?>

                                        <?php while($dep=$depositos->fetch(PDO::FETCH_ASSOC)): ?>

                                            <option value="<?php echo $dep['id']; ?>">

                                                <?php
                                                echo htmlspecialchars(
                                                    $dep['tipodeposito'].' — '.$dep['nombresitio']
                                                );
                                                ?>

                                            </option>

                                        <?php endwhile; ?>

                                    <?php endif; ?>

                                </select>

                            </div>


                            <div class="col-12 col-md-3">

                                <label
                                    for="selectTipoActividad"
                                    class="form-label small text-muted mb-1">

                                    Tipo de actividad

                                </label>

                                <select
                                    id="selectTipoActividad"
                                    name="tipoactividad"
                                    class="form-select form-select-sm">

                                    <option value="">
                                        Todos
                                    </option>

                                    <option value="Inspección">
                                        Inspección
                                    </option>

                                    <option value="Siembra">
                                        Siembra
                                    </option>

                                    <option value="Seguimiento">
                                        Seguimiento
                                    </option>

                                    <option value="Resiembra">
                                        Resiembra
                                    </option>

                                </select>

                            </div>


                            <div class="col-12 col-md-2 d-grid">

                                <button
                                    type="submit"
                                    class="btn btn-primary btn-sm">

                                    <i class="bi bi-funnel me-1"></i>

                                    Filtrar

                                </button>

                            </div>
>>>>>>> f9469daa2251999c245857af06840f7437ecb8e3
>>>>>>> 15f7c46c63a2484819d5aa978944bd22c24b9ed4
>>>>>>> c8d9b80a90ca4c0fc1f67f3b9b448d554867cf4b

                        </div>

                    </form>

<<<<<<< HEAD
                            <div class="mb-3">
                                <label for="fechaDesde" class="form-label small fw-semibold text-secondary">Fecha Desde</label>
                                <input type="date" id="fechaDesde" name="fechaDesde" class="form-control bg-light border-0 rounded-3 py-2"
                                    max="<?php echo date('Y-m-d'); ?>"
                                    value="<?php echo htmlspecialchars($_GET['fechaDesde'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="fechaHasta" class="form-label small fw-semibold text-secondary">Fecha Hasta</label>
                                <input type="date" id="fechaHasta" name="fechaHasta" class="form-control bg-light border-0 rounded-3 py-2"
                                    max="<?php echo date('Y-m-d'); ?>"
                                    value="<?php echo htmlspecialchars($_GET['fechaHasta'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="codsitio" class="form-label small fw-semibold text-secondary">Sitio</label>
                                <select id="codsitio" name="codsitio" class="form-select bg-light border-0 rounded-3 py-2">
                                    <option value="">Todos los sitios</option>
                                    <?php if(isset($sitios) && $sitios): ?>
                                        <?php while($sitio = $sitios->fetch(PDO::FETCH_ASSOC)): ?>
                                            <option value="<?php echo $sitio['codsitio']; ?>"
                                                <?php echo (($_GET['codsitio'] ?? '') == $sitio['codsitio']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($sitio['nombresitio']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="codtipoactividad" class="form-label small fw-semibold text-secondary">Tipo de Actividad</label>
                                <select id="codtipoactividad" name="codtipoactividad" class="form-select bg-light border-0 rounded-3 py-2">
                                    <option value="">Todas las actividades</option>
                                    <?php if(isset($tiposActividad) && $tiposActividad): ?>
                                        <?php while($tipo = $tiposActividad->fetch(PDO::FETCH_ASSOC)): ?>
                                            <option value="<?php echo $tipo['codtipoactividad']; ?>"
                                                <?php echo (($_GET['codtipoactividad'] ?? '') == $tipo['codtipoactividad']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($tipo['nombreactividad']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-dark rounded-pill py-2 fw-semibold shadow-sm">
                                    <i class="bi bi-search me-1"></i> Aplicar Filtros
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

<<<<<<< HEAD
=======
=======
                </div>

<<<<<<< HEAD
>>>>>>> 15f7c46c63a2484819d5aa978944bd22c24b9ed4
>>>>>>> c8d9b80a90ca4c0fc1f67f3b9b448d554867cf4b
                <!-- Contenido Principal / Tabla Estilo Tarjeta Moderna -->
                <div class="col-lg-9">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0">Listado de Registros</h5>
                            <span class="text-muted small">Mostrando resultados actuales</span>
                        </div>

                        <div class="card-body px-0 pb-0">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0">
                                    <thead class="bg-light text-uppercase text-secondary fs-7">
                                        <tr>
                                            <th class="ps-4 py-3">Actividad / Sitio</th>
<<<<<<< HEAD
                                            <th class="py-3">Responsable</th>
                                            <th class="py-3">Observaciones</th>
                                            <th class="text-center py-3">Estado</th>
                                            <th class="text-center py-3 pe-4">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $filas = (isset($actividades) && $actividades)
                                        ? $actividades->fetchAll(PDO::FETCH_ASSOC)
                                        : [];
                                    ?>

                                    <?php if(!empty($filas)): ?>
                                        <?php foreach($filas as $act): ?>
                                            <?php 
                                                // Definición segura de variables para evitar Undefined array key
                                                $tipoActividad = $act['tipo_actividad'] ?? $act['nombreactividad'] ?? 'Actividad';
                                                $nombreSitio   = $act['sitio'] ?? $act['nombresitio'] ?? 'Sitio no asignado';
                                                $fechaAct      = $act['fecha'] ?? '';
                                                $nombreResp    = $act['responsable'] ?? $act['nombre_responsable'] ?? 'N/A';
                                                $observaciones = $act['observaciones'] ?? 'Sin observaciones';
                                                $estadoAct     = $act['estado'] ?? 'A';
                                                $codActividad  = $act['codactividad'] ?? $act['id'] ?? 0;
                                            ?>
                                            <tr class="border-bottom border-light">
                                                <td class="ps-4 py-3">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-primary-subtle text-primary rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                                            <i class="bi bi-geo-alt fs-5"></i>
                                                        </div>
                                                        <div>
                                                            <span class="fw-bold text-dark d-block"><?php echo htmlspecialchars($tipoActividad); ?></span>
                                                            <span class="text-muted small"><i class="bi bi-pin-map me-1"></i><?php echo htmlspecialchars($nombreSitio); ?> &bull; <span class="text-secondary"><?php echo htmlspecialchars($fechaAct); ?></span></span>
                                                        </div>
                                                    </div>
                                                </td>

                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-secondary-subtle text-secondary rounded-circle me-2 d-flex align-items-center justify-content-center fw-bold small" style="width: 30px; height: 30px;">
                                                            <?php echo strtoupper(substr($nombreResp, 0, 1)); ?>
                                                        </div>
                                                        <span class="text-dark small fw-semibold"><?php echo htmlspecialchars($nombreResp); ?></span>
                                                    </div>
                                                </td>

                                                <td>
                                                    <span class="text-muted small d-inline-block text-truncate" style="max-width: 180px;" title="<?php echo htmlspecialchars($observaciones); ?>">
                                                        <?php echo htmlspecialchars($observaciones); ?>
                                                    </span>
                                                </td>

                                                <td class="text-center">
                                                    <?php if($estadoAct === 'A'): ?>
                                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-3 fw-semibold small">
                                                            <i class="bi bi-dot"></i> Activo
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-3 fw-semibold small">
                                                            <i class="bi bi-dot"></i> Inactivo
                                                        </span>
                                                    <?php endif; ?>
                                                </td>

                                                <td class="text-center pe-4">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <?php if($estadoAct === 'A'): ?>
                                                            <a href="<?php echo getUrl('ActividadesTer','ActividadesTer','delete',array('id'=>$codActividad)); ?>"
                                                                class="btn btn-light btn-sm text-danger rounded-2 p-2"
                                                                title="Inhabilitar registro">
                                                                <i class="bi bi-trash fs-6"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <a href="<?php echo getUrl('ActividadesTer','ActividadesTer','delete',array('id'=>$codActividad)); ?>"
                                                                class="btn btn-light btn-sm text-success rounded-2 p-2"
                                                                title="Activar registro">
                                                                <i class="bi bi-arrow-counterclockwise fs-6"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-5">
                                                <div class="py-4">
                                                    <i class="bi bi-folder2-open fs-1 text-secondary opacity-50 d-block mb-3"></i>
                                                    <h6 class="fw-semibold text-dark">No hay registros disponibles</h6>
                                                    <p class="text-muted small mb-0">Intenta ajustando los filtros de búsqueda laterales.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
=======
                                            <th class="py-3">Fecha</th>
                                            <th class="py-3">Observaciones</th>
                                            <th class="text-center py-3 pe-4">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $filas = (isset($actividades) && $actividades)
                                            ? $actividades->fetchAll(PDO::FETCH_ASSOC)
                                            : [];
                                        ?>

                                        <?php if(!empty($filas)): ?>
                                            <?php foreach($filas as $act): ?>
                                                <?php 
                                                    $tipoActividad = $act['tipo_actividad'] ?? $act['nombreactividad'] ?? 'Actividad';
                                                    $nombreSitio   = $act['sitio'] ?? $act['nombresitio'] ?? 'Sitio no asignado';
                                                    $fechaAct      = $act['fecha'] ?? '';
                                                    $observaciones = $act['observaciones'] ?? 'Sin observaciones';
                                                    $estadoAct     = $act['estado'] ?? 'A';
                                                ?>
                                                <tr class="border-bottom border-light">
                                                    <td class="ps-4 py-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-primary-subtle text-primary rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                                                <i class="bi bi-geo-alt fs-5"></i>
                                                            </div>
                                                            <div>
                                                                <span class="fw-bold text-dark d-block"><?php echo htmlspecialchars($tipoActividad); ?></span>
                                                                <span class="text-muted small"><i class="bi bi-pin-map me-1"></i><?php echo htmlspecialchars($nombreSitio); ?></span>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <span class="text-secondary small fw-semibold"><?php echo htmlspecialchars($fechaAct); ?></span>
                                                    </td>

                                                    <td>
                                                        <span class="text-muted small d-inline-block text-truncate" style="max-width: 180px;" title="<?php echo htmlspecialchars($observaciones); ?>">
                                                            <?php echo htmlspecialchars($observaciones); ?>
                                                        </span>
                                                    </td>

                                                    <td class="text-center pe-4">
                                                        <?php if($estadoAct === 'A'): ?>
                                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-3 fw-semibold small">
                                                                <i class="bi bi-dot"></i> Activo
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-3 fw-semibold small">
                                                                <i class="bi bi-dot"></i> Inactivo
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-5">
                                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                                                    No has registrado actividades en este rango.
                                                </td>
                                            </tr>
                                        <?php endif; ?>
>>>>>>> c8d9b80a90ca4c0fc1f67f3b9b448d554867cf4b
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
<<<<<<< HEAD
=======
<<<<<<< HEAD
=======
=======

                <div class="table-responsive">

                    <table
                        class="table table-striped align-middle mb-0"
                        id="tablaMisActividadesTer">

                        <thead class="table-dark">

                            <tr>

                                <th class="ps-4">
                                    Fecha
                                </th>

                                <th>
                                    Tipo de actividad
                                </th>

                                <th>
                                    Depósito
                                </th>

                                <th>
                                    Sitio
                                </th>

                                <th class="text-center">
                                    Estado
                                </th>

                                <th class="text-center">
                                    Editar
                                </th>

                                <th class="text-center">
                                    Inhabilitar
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php

                            $filasActividades=
                            (isset($actividades)&&$actividades)
                            ?$actividades->fetchAll(PDO::FETCH_ASSOC)
                            :[];

                            ?>


                            <?php if(!empty($filasActividades)): ?>

                                <?php foreach($filasActividades as $act): ?>

                                    <tr>

                                        <td class="ps-4">

                                            <?php echo htmlspecialchars($act['fecha']); ?>

                                        </td>


                                        <td class="fw-semibold">

                                            <?php echo htmlspecialchars($act['tipo_actividad']); ?>

                                        </td>


                                        <td>

                                            <?php echo htmlspecialchars($act['deposito']); ?>

                                        </td>


                                        <td>

                                            <span class="text-muted small">

                                                <?php echo htmlspecialchars($act['sitio']); ?>

                                            </span>

                                        </td>


                                        <td class="text-center">

                                            <?php if($act['estado']==='A'): ?>

                                                <span class="badge bg-success">
                                                    Activo
                                                </span>

                                            <?php else: ?>

                                                <span class="badge bg-danger">
                                                    Inactivo
                                                </span>

                                            <?php endif; ?>

                                        </td>


                                        <td class="text-center">

                                            <button
                                                type="button"
                                                class="btn btn-outline-primary btn-icon rounded-circle"
                                                title="Editar"
                                                onclick="cargarFormularioModal(
                                                '<?php echo getUrl('ActividadesTer','ActividadesTer','getUpdate',array('id'=>$act['id'])); ?>',
                                                'Editar actividad',
                                                'actividadTerFormEdicion',
                                                '<?php echo getUrl('ActividadesTer','ActividadesTer','listMisActividades'); ?>',
                                                'tablaMisActividadesTer'
                                                )">

                                                <i class="bi bi-pencil-fill"></i>

                                            </button>

                                        </td>


                                        <td class="text-center">

                                            <?php if($act['estado']==='A'): ?>

                                                <a
                                                    href="<?php echo getUrl('ActividadesTer','ActividadesTer','delete',array('id'=>$act['id'])); ?>"
                                                    class="btn btn-outline-danger btn-icon rounded-circle"
                                                    title="Inhabilitar">

                                                    <i class="bi bi-slash-circle"></i>

                                                </a>

                                            <?php else: ?>

                                                <a
                                                    href="<?php echo getUrl('ActividadesTer','ActividadesTer','delete',array('id'=>$act['id'])); ?>"
                                                    class="btn btn-outline-success btn-icon rounded-circle"
                                                    title="Activar">

                                                    <i class="bi bi-check-lg"></i>

                                                </a>

                                            <?php endif; ?>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>


                            <?php else: ?>

                                <tr>

                                    <td
                                        colspan="7"
                                        class="text-center text-muted py-5">

                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                        No has registrado actividades en este rango.

                                    </td>

                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

>>>>>>> f9469daa2251999c245857af06840f7437ecb8e3
>>>>>>> 15f7c46c63a2484819d5aa978944bd22c24b9ed4
>>>>>>> c8d9b80a90ca4c0fc1f67f3b9b448d554867cf4b
                </div>

            </div>
        </div>

    </div>
<<<<<<< HEAD
</div>

=======
<<<<<<< HEAD
</div>
=======
<<<<<<< HEAD
</div>
=======

</div>


<script>

function seleccionarMes(valor,texto){

    document.getElementById('mesFiltro').value=valor;

    document.getElementById('btnMesFiltro').textContent=texto;

}


var formFiltro=
document.getElementById('formFiltroMisActividadesTer');


if(formFiltro){

    formFiltro.addEventListener('submit',function(evento){

        evento.preventDefault();


        var datos=
        new FormData(formFiltro);


        var tbody=
        document.querySelector(
            '#tablaMisActividadesTer tbody'
        );


        fetch(formFiltro.action,{

            method:'POST',

            body:datos

        })

        .then(function(respuesta){

            return respuesta.text();

        })

        .then(function(html){

            tbody.innerHTML=html;

        })

        .catch(function(){

            tbody.innerHTML=
            '<tr>'+
            '<td colspan="7" class="text-center text-danger py-4">'+
            'Ocurrió un error al filtrar. Intenta nuevamente.'+
            '</td>'+
            '</tr>';

        });

    });

}

</script>


<?php
include_once __DIR__.'/../partials/modalFormulario.php';
?>
>>>>>>> f9469daa2251999c245857af06840f7437ecb8e3
>>>>>>> 15f7c46c63a2484819d5aa978944bd22c24b9ed4
>>>>>>> c8d9b80a90ca4c0fc1f67f3b9b448d554867cf4b
