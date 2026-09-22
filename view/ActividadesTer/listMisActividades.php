<div class="container-fluid py-4 bg-light min-vh-100">
    <div class="row justify-content-center">
        <div class="col-xl-12">

<<<<<<< HEAD
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


            <?php if (isset($_SESSION['error'])): ?>

                <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">

                    <i class="bi bi-exclamation-triangle-fill me-2"></i>

                    <div>
                        <?php echo htmlspecialchars($_SESSION['error']); ?>
                    </div>

=======
            <!-- Alertas de Sesión o Errores -->
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm border-0 rounded-4">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-danger"></i>
                    <div><?php echo htmlspecialchars($_SESSION['error']); ?></div>
>>>>>>> 21f6b587a7ffa64119efc6efd29913824f6c867f
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

<<<<<<< HEAD

            <?php if (isset($_SESSION['exito'])): ?>

                <div class="alert alert-success d-flex align-items-center mb-3" role="alert">

                    <i class="bi bi-check-circle-fill me-2"></i>

                    <div>
                        <?php echo htmlspecialchars($_SESSION['exito']); ?>
                    </div>

=======
            <?php if(isset($_SESSION['exito'])): ?>
                <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm border-0 rounded-4">
                    <i class="bi bi-check-circle-fill me-3 fs-4 text-success"></i>
                    <div><?php echo htmlspecialchars($_SESSION['exito']); ?></div>
>>>>>>> 21f6b587a7ffa64119efc6efd29913824f6c867f
                </div>
                <?php unset($_SESSION['exito']); ?>
            <?php endif; ?>

            <?php if(!empty($errorFechas)): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm border-0 rounded-4">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-danger"></i>
                    <div><?php echo htmlspecialchars($errorFechas); ?></div>
                </div>
            <?php endif; ?>

<<<<<<< HEAD
            <div class="card border-0 shadow-sm">


                <div class="card-header bg-white border-bottom py-3">


                    <div class="d-flex align-items-center mb-3">

                        <i class="bi bi-file-earmark-text text-primary me-2 fs-5"></i>

                        <span class="fw-semibold">
                            Actividades registradas por mí
                        </span>

                    </div>


                    <form id="formFiltroMisActividadesTer"
                        action="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'filtro', false, 'ajax'); ?>"
                        method="POST">


                        <div class="row g-2 align-items-end">


                            <div class="col-6 col-md-2">

                                <label for="mesFiltro" class="form-label small text-muted mb-1">

                                    Mes

                                </label>

                                <div class="position-relative">
                                    <input type="hidden" id="mesFiltro" name="mes" value="">
                                    <button type="button" id="btnMesFiltro"
                                        class="form-control form-control-sm text-start d-flex justify-content-between align-items-center">
                                        <span id="textoMesFiltro">Seleccionar mes</span>
                                        <i class="bi bi-calendar3"></i>
                                    </button>

                                    <div id="selectorMesPersonalizado" class="selector-mes shadow-sm d-none">
                                        <div class="selector-mes-anio"><?php echo date('Y'); ?></div>

                                        <div class="selector-mes-grid">
                                            <?php
                                            $meses=[
                                                '01'=>'Ene','02'=>'Feb','03'=>'Mar','04'=>'Abr',
                                                '05'=>'May','06'=>'Jun','07'=>'Jul','08'=>'Ago',
                                                '09'=>'Sept','10'=>'Oct','11'=>'Nov','12'=>'Dic'
                                            ];
                                            foreach($meses as $numero=>$nombre):
                                            ?>
                                                <button type="button"
                                                    class="btn-mes"
                                                    data-mes="<?php echo date('Y').'-'.$numero; ?>"
                                                    data-texto="<?php echo $nombre; ?>">
                                                    <?php echo $nombre; ?>
                                                </button>
                                            <?php endforeach; ?>
                                        </div>

                                        <button type="button" id="borrarMesFiltro" class="btn-borrar-mes">
                                            Borrar
                                        </button>
                                    </div>
                                </div>

                            </div>


                            <div class="col-12 col-md-3">

                                <label for="selectDeposito" class="form-label small text-muted mb-1">

                                    Depósito

                                </label>

                                <select id="selectDeposito" name="coddeposito" class="form-select form-select-sm">

                                    <option value="">
                                        Todos
                                    </option>

                                    <?php if (isset($depositos) && $depositos): ?>

                                        <?php while ($dep = $depositos->fetch(PDO::FETCH_ASSOC)): ?>

                                            <option value="<?php echo $dep['id']; ?>">

                                                <?php
                                                echo htmlspecialchars(
                                                    $dep['tipodeposito'] . ' — ' . $dep['nombresitio']
                                                );
                                                ?>

                                            </option>

                                        <?php endwhile; ?>

                                    <?php endif; ?>

                                </select>

                            </div>


                            <div class="col-12 col-md-3">

                                <label for="selectTipoActividad" class="form-label small text-muted mb-1">

                                    Tipo de actividad

                                </label>

                                <select id="selectTipoActividad" name="tipoactividad"
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

                                <button type="submit" class="btn btn-primary btn-sm">

                                    <i class="bi bi-funnel me-1"></i>

                                    Filtrar

                                </button>

                            </div>
=======
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
>>>>>>> 21f6b587a7ffa64119efc6efd29913824f6c867f

            <div class="row g-4">
                
                <!-- Panel Lateral de Filtros -->
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 20px;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <span class="fw-bold text-dark"><i class="bi bi-sliders me-2 text-primary"></i>Filtros</span>
                            <a href="index.php?modulo=ActividadesTer&controlador=ActividadesTer&funcion=listActTer" class="text-decoration-none small text-muted">Limpiar</a>
                        </div>

                        <form action="index.php" method="GET">
                            <input type="hidden" name="modulo" value="ActividadesTer">
                            <input type="hidden" name="controlador" value="ActividadesTer">
                            <input type="hidden" name="funcion" value="listActTer">

                            <div class="mb-3">
                                <label for="fechaDesde" class="form-label small fw-semibold text-secondary">Fecha Desde</label>
                                <input type="date" id="fechaDesde" name="fechaDesde" class="form-control bg-light border-0 rounded-3 py-2"
                                    max="<?php echo date('Y-m-d'); ?>"
                                    value="<?php echo htmlspecialchars($_GET['fechaDesde']??''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="fechaHasta" class="form-label small fw-semibold text-secondary">Fecha Hasta</label>
                                <input type="date" id="fechaHasta" name="fechaHasta" class="form-control bg-light border-0 rounded-3 py-2"
                                    max="<?php echo date('Y-m-d'); ?>"
                                    value="<?php echo htmlspecialchars($_GET['fechaHasta']??''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="codsitio" class="form-label small fw-semibold text-secondary">Sitio</label>
                                <select id="codsitio" name="codsitio" class="form-select bg-light border-0 rounded-3 py-2">
                                    <option value="">Todos los sitios</option>
                                    <?php if(isset($sitios)&&$sitios): ?>
                                        <?php while($sitio=$sitios->fetch(PDO::FETCH_ASSOC)): ?>
                                            <option value="<?php echo $sitio['codsitio']; ?>"
                                                <?php echo (($_GET['codsitio']??'')==$sitio['codsitio'])?'selected':''; ?>>
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
                                    <?php if(isset($tiposActividad)&&$tiposActividad): ?>
                                        <?php while($tipo=$tiposActividad->fetch(PDO::FETCH_ASSOC)): ?>
                                            <option value="<?php echo $tipo['codtipoactividad']; ?>"
                                                <?php echo (($_GET['codtipoactividad']??'')==$tipo['codtipoactividad'])?'selected':''; ?>>
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

                <div class="table-responsive">

                    <table class="table table-striped align-middle mb-0" id="tablaMisActividadesTer">


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

                            $filasActividades =
                                (isset($actividades) && $actividades)
                                ? $actividades->fetchAll(PDO::FETCH_ASSOC)
                                : [];

                            ?>

                            <?php if (!empty($filasActividades)): ?>

                                <?php foreach ($filasActividades as $act): ?>

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

                                            <?php if ($act['estado'] === 'A'): ?>

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

                                            <button type="button" class="btn btn-outline-primary btn-icon rounded-circle"
                                                title="Editar" onclick="cargarFormularioModal(
                                                '<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'getUpdate', array('id' => $act['id'])); ?>',
                                                'Editar actividad',
                                                'actividadTerFormEdicion',
                                                '<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'listMisActividades'); ?>',
                                                'tablaMisActividadesTer'
                                                )">

                                                <i class="bi bi-pencil-fill"></i>

                                            </button>

                                        </td>


                                        <td class="text-center">

                                            <?php if ($act['estado'] === 'A'): ?>

                                                <a href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'delete', array('id' => $act['id'])); ?>"
                                                    class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar">

                                                    <i class="bi bi-slash-circle"></i>

                                                </a>

                                            <?php else: ?>

                                                <a href="<?php echo getUrl('ActividadesTer', 'ActividadesTer', 'delete', array('id' => $act['id'])); ?>"
                                                    class="btn btn-outline-success btn-icon rounded-circle" title="Activar">

                                                    <i class="bi bi-check-lg"></i>

                                                </a>

                                            <?php endif; ?>

                                        </td>


                                    </tr>

                                <?php endforeach; ?>


                            <?php else: ?>


                                <tr>

                                    <td colspan="7" class="text-center text-muted py-5">

                                        <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                                        No has registrado actividades en este rango.

                                    </td>

                                </tr>


                            <?php endif; ?>

                        </tbody>

                    </table>

=======
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
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
>>>>>>> 21f6b587a7ffa64119efc6efd29913824f6c867f
                </div>

            </div>

        </div>
    </div>
<<<<<<< HEAD

</div>


<style>
.selector-mes{
    position:absolute;
    top:100%;
    left:0;
    z-index:1050;
    width:230px;
    background:#fff;
    border:1px solid #ced4da;
    padding:10px;
}
.selector-mes-anio{
    background:#f1f3f5;
    padding:6px 8px;
    font-size:13px;
    font-weight:600;
    margin-bottom:8px;
}
.selector-mes-grid{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:4px;
}
.btn-mes{
    border:0;
    background:transparent;
    padding:7px 4px;
    font-size:12px;
    border-radius:3px;
}
.btn-mes:hover{
    background:#e9ecef;
}
.btn-mes.activo{
    background:#0d6efd;
    color:#fff;
}
.btn-borrar-mes{
    border:0;
    background:transparent;
    color:#0d6efd;
    font-size:12px;
    margin-top:8px;
    padding:4px 0;
}
</style>

<script>

    var btnMesFiltro=document.getElementById('btnMesFiltro');
    var selectorMes=document.getElementById('selectorMesPersonalizado');
    var inputMes=document.getElementById('mesFiltro');
    var textoMes=document.getElementById('textoMesFiltro');
    var borrarMes=document.getElementById('borrarMesFiltro');

    if(btnMesFiltro&&selectorMes){
        btnMesFiltro.addEventListener('click',function(){
            selectorMes.classList.toggle('d-none');
        });

        document.querySelectorAll('.btn-mes').forEach(function(boton){
            boton.addEventListener('click',function(){
                document.querySelectorAll('.btn-mes').forEach(function(b){
                    b.classList.remove('activo');
                });
                boton.classList.add('activo');
                inputMes.value=boton.dataset.mes;
                textoMes.textContent=boton.dataset.texto+' <?php echo date('Y'); ?>';
                selectorMes.classList.add('d-none');
            });
        });

        borrarMes.addEventListener('click',function(){
            inputMes.value='';
            textoMes.textContent='Seleccionar mes';
            document.querySelectorAll('.btn-mes').forEach(function(b){
                b.classList.remove('activo');
            });
            selectorMes.classList.add('d-none');
        });

        document.addEventListener('click',function(e){
            if(!selectorMes.contains(e.target)&&!btnMesFiltro.contains(e.target)){
                selectorMes.classList.add('d-none');
            }
        });
    }


    var formFiltro =
        document.getElementById('formFiltroMisActividadesTer');

    if (formFiltro) {

        formFiltro.addEventListener('submit', function (evento) {

            evento.preventDefault();

            var datos =
                new FormData(formFiltro);

            var tbody =
                document.querySelector(
                    '#tablaMisActividadesTer tbody'
                );

            fetch(formFiltro.action, {
                method: 'POST',
                body: datos
            })

                .then(function (respuesta) {

                    return respuesta.text();

                })

                .then(function (html) {

                    tbody.innerHTML = html;

                })

                .catch(function () {

                    tbody.innerHTML =
                        '<tr>' +
                        '<td colspan="7" class="text-center text-danger py-4">' +
                        'Ocurrió un error al filtrar. Intenta nuevamente.' +
                        '</td>' +
                        '</tr>';

                });

        });

    }

</script>


<?php
include_once __DIR__ . '/../partials/modalFormulario.php';
?>
=======
</div>
>>>>>>> 21f6b587a7ffa64119efc6efd29913824f6c867f
