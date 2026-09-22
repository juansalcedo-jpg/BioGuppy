<div class="container-fluid py-2">

    <div class="row justify-content-center">

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

                </div>

                <?php unset($_SESSION['error']); ?>

            <?php endif; ?>


            <?php if (isset($_SESSION['exito'])): ?>

                <div class="alert alert-success d-flex align-items-center mb-3" role="alert">

                    <i class="bi bi-check-circle-fill me-2"></i>

                    <div>
                        <?php echo htmlspecialchars($_SESSION['exito']); ?>
                    </div>

                </div>

                <?php unset($_SESSION['exito']); ?>

            <?php endif; ?>


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

                        </div>

                    </form>

                </div>


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

                </div>

            </div>

        </div>

    </div>

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