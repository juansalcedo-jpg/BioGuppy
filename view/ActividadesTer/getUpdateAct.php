<?php

$esEdicion=isset($modo)&&$modo==='editar';

if($esEdicion){

    if(!isset($tipoActividad)){

        $tipo=strtoupper($actividad['nombreactividad']);

        if(strpos($tipo,'RESIEMBRA')!==false){
            $tipoActividad='Resiembra';
        }elseif(strpos($tipo,'INSPEC')!==false){
            $tipoActividad='Inspeccion';
        }elseif(strpos($tipo,'SEGUIMIENTO')!==false){
            $tipoActividad='Seguimiento';
        }elseif(strpos($tipo,'SIEMBRA')!==false){
            $tipoActividad='Siembra';
        }
    }
}

$esInspeccion=$tipoActividad==='Inspeccion';
$esSiembra=$tipoActividad==='Siembra';
$esSeguimiento=$tipoActividad==='Seguimiento';
$esResiembra=$tipoActividad==='Resiembra';

$nombreTipo=$tipoActividad;

if($tipoActividad==='Inspeccion'){
    $nombreTipo='Inspección';
}

$accion=$esEdicion
?getUrl('ActividadesTer','ActividadesTer','postUpdate')
:getUrl('ActividadesTer','ActividadesTer','postCreate');

?>

<div id="actividadTerFormEdicion">

<div class="container-fluid <?php echo $esEdicion?'py-2':'py-4'; ?>">

    <div class="row justify-content-center">

        <div class="<?php echo $esEdicion?'col-xl-9':'col-12 col-xl-10'; ?>">

            <?php if($esEdicion): ?>

                <div class="mb-4">

                    <h4 class="fw-semibold mb-1">
                        Editar actividad — <?php echo htmlspecialchars($actividad['nombreactividad']); ?>
                    </h4>

                    <p class="text-muted small mb-0">
                        Corrige los datos de esta actividad de terreno.
                    </p>

                </div>

            <?php else: ?>

                <h2 class="text-center fw-bold mb-4 text-dark">
                    Actividad de Terreno
                </h2>

            <?php endif; ?>


            <?php if(isset($_SESSION['error'])): ?>

                <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">

                    <i class="bi bi-exclamation-triangle-fill me-2"></i>

                    <div>
                        <?php echo htmlspecialchars($_SESSION['error']); ?>
                    </div>

                </div>

                <?php unset($_SESSION['error']); ?>

            <?php endif; ?>


            <form action="<?php echo $accion; ?>" method="POST">

                <?php if($esEdicion): ?>

                    <input
                        type="hidden"
                        name="codactividad"
                        value="<?php echo $actividad['codactividad']; ?>">

                <?php else: ?>

                    <input
                        type="hidden"
                        name="tipo_actividad"
                        value="<?php echo htmlspecialchars($tipoActividad); ?>">

                <?php endif; ?>


                <div class="card border-0 shadow-sm rounded-3">

                    <?php if($esEdicion): ?>

                        <div class="card-header bg-white border-bottom py-3">

                            <span class="fw-semibold">

                                <i class="bi bi-pencil-square me-2 text-primary"></i>

                                Datos de la actividad

                            </span>

                        </div>

                    <?php endif; ?>


                    <div class="card-body p-4">


                        <?php if(!$esEdicion): ?>

                            <div class="row g-3 mb-4">

                                <div class="col-md-5">

                                    <label
                                        for="deposito_id"
                                        class="form-label fw-semibold">

                                        Depósito *

                                    </label>

                                    <select
                                        class="form-select form-select-lg fs-6"
                                        id="deposito_id"
                                        name="deposito_id"
                                        required>

                                        <option value="" selected disabled>
                                            Seleccione...
                                        </option>

                                        <?php if(isset($depositos)&&$depositos): ?>

                                            <?php while($dep=$depositos->fetch(PDO::FETCH_ASSOC)): ?>

                                                <option value="<?php echo $dep['id']; ?>">

                                                    <?php
                                                    echo htmlspecialchars(
                                                        $dep['nombresitio'].' — '.$dep['tipodeposito']
                                                    );
                                                    ?>

                                                </option>

                                            <?php endwhile; ?>

                                        <?php endif; ?>

                                    </select>

                                </div>


                                <div class="col-md-4">

                                    <label
                                        for="fecha_actividad"
                                        class="form-label fw-semibold">

                                        Fecha *

                                    </label>

                                    <input
                                        type="date"
                                        class="form-control form-control-lg fs-6"
                                        id="fecha_actividad"
                                        name="fecha_actividad"
                                        value="<?php echo date('Y-m-d'); ?>"
                                        min="<?php echo date('Y-m-d',strtotime('-2 days')); ?>"
                                        max="<?php echo date('Y-m-d'); ?>"
                                        required>

                                </div>


                                <div class="col-md-3">

                                    <label class="form-label fw-semibold">
                                        Hora *
                                    </label>

                                    <div class="d-flex gap-2">

                                        <select
                                            class="form-select form-select-lg fs-6"
                                            id="hora"
                                            required>

                                            <option value="" selected disabled>
                                                Hora
                                            </option>

                                            <option value="08">08</option>
                                            <option value="09">09</option>
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                            <option value="13">13</option>
                                            <option value="14">14</option>
                                            <option value="15">15</option>
                                            <option value="16">16</option>
                                            <option value="17">17</option>
                                            <option value="18">18</option>

                                        </select>


                                        <select
                                            class="form-select form-select-lg fs-6"
                                            id="minuto"
                                            required>

                                            <option value="" selected disabled>
                                                Min
                                            </option>

                                            <?php for($i=0;$i<=59;$i++): ?>

                                                <?php
                                                $minuto=str_pad(
                                                    $i,
                                                    2,
                                                    '0',
                                                    STR_PAD_LEFT
                                                );
                                                ?>

                                                <option value="<?php echo $minuto; ?>">

                                                    <?php echo $minuto; ?>

                                                </option>

                                            <?php endfor; ?>

                                        </select>

                                    </div>

                                    <input
                                        type="hidden"
                                        id="hora_actividad"
                                        name="hora_actividad">

                                </div>

                            </div>


                            <ul class="nav nav-tabs mb-4 border-bottom">

                                <li class="nav-item">

                                    <a
                                        class="nav-link <?php echo $esInspeccion?'active fw-semibold text-primary border-0 border-bottom border-primary border-3':'text-secondary fw-semibold'; ?>"
                                        href="<?php echo getUrl('ActividadesTer','ActividadesTer','Inspeccion'); ?>">

                                        Inspección

                                    </a>

                                </li>


                                <li class="nav-item">

                                    <a
                                        class="nav-link <?php echo $esSiembra?'active fw-semibold text-primary border-0 border-bottom border-primary border-3':'text-secondary fw-semibold'; ?>"
                                        href="<?php echo getUrl('ActividadesTer','ActividadesTer','Siembra'); ?>">

                                        Siembra

                                    </a>

                                </li>


                                <li class="nav-item">

                                    <a
                                        class="nav-link <?php echo $esSeguimiento?'active fw-semibold text-primary border-0 border-bottom border-primary border-3':'text-secondary fw-semibold'; ?>"
                                        href="<?php echo getUrl('ActividadesTer','ActividadesTer','Seguimiento'); ?>">

                                        Seguimiento

                                    </a>

                                </li>


                                <li class="nav-item">

                                    <a
                                        class="nav-link <?php echo $esResiembra?'active fw-semibold text-primary border-0 border-bottom border-primary border-3':'text-secondary fw-semibold'; ?>"
                                        href="<?php echo getUrl('ActividadesTer','ActividadesTer','Resiembra'); ?>">

                                        Resiembra

                                    </a>

                                </li>

                            </ul>

                        <?php else: ?>


                            <div class="row g-3 mb-3">

                                <div class="col-md-6">

                                    <label
                                        for="fecha_actividad"
                                        class="form-label fw-semibold">

                                        Fecha *

                                    </label>

                                    <input
                                        type="date"
                                        class="form-control"
                                        id="fecha_actividad"
                                        name="fecha_actividad"
                                        value="<?php echo htmlspecialchars($actividad['fecha']); ?>"
                                        max="<?php echo date('Y-m-d'); ?>"
                                        required>

                                </div>


                                <div class="col-md-6">

                                    <label
                                        for="hora_actividad"
                                        class="form-label fw-semibold">

                                        Hora *

                                    </label>

                                    <input
                                        type="time"
                                        class="form-control"
                                        id="hora_actividad"
                                        name="hora_actividad"
                                        min="08:00"
                                        max="18:00"
                                        value="<?php echo htmlspecialchars(substr($actividad['hora']??'',0,5)); ?>"
                                        required>

                                </div>

                            </div>

                        <?php endif; ?>


                        <?php if($esInspeccion): ?>

                            <div class="row g-3 mb-4">

                                <div class="col-md-3">

                                    <label
                                        for="ph"
                                        class="form-label fw-semibold">

                                        pH del agua *

                                    </label>

                                    <input
                                        type="number"
                                        min="0"
                                        max="14"
                                        step="0.01"
                                        class="form-control"
                                        id="ph"
                                        name="ph"
                                        value="<?php echo $esEdicion?htmlspecialchars($actividad['ph']??''):''; ?>"
                                        required>

                                </div>


                                <div class="col-md-3">

                                    <label
                                        for="temperatura"
                                        class="form-label fw-semibold">

                                        Temperatura (°C) *

                                    </label>

                                    <input
                                        type="number"
                                        min="0"
                                        max="40"
                                        step="0.01"
                                        class="form-control"
                                        id="temperatura"
                                        name="temperatura"
                                        value="<?php echo $esEdicion?htmlspecialchars($actividad['temperatura']??''):''; ?>"
                                        required>

                                </div>


                                <div class="col-md-2">

                                    <label
                                        for="larvas_aedes"
                                        class="form-label fw-semibold">

                                        Larvas Aedes *

                                    </label>

                                    <input
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="form-control"
                                        id="larvas_aedes"
                                        name="larvas_aedes"
                                        value="<?php echo $esEdicion?htmlspecialchars($actividad['larvasaedes']??0):0; ?>"
                                        required>

                                </div>


                                <div class="col-md-2">

                                    <label
                                        for="pupas"
                                        class="form-label fw-semibold">

                                        Pupas *

                                    </label>

                                    <input
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="form-control"
                                        id="pupas"
                                        name="pupas"
                                        value="<?php echo $esEdicion?htmlspecialchars($actividad['pupas']??0):0; ?>"
                                        required>

                                </div>


                                <div class="col-md-2">

                                    <label
                                        for="larvas_culex"
                                        class="form-label fw-semibold">

                                        Larvas Culex *

                                    </label>

                                    <input
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="form-control"
                                        id="larvas_culex"
                                        name="larvas_culex"
                                        value="<?php echo $esEdicion?htmlspecialchars($actividad['larvasculex']??0):0; ?>"
                                        required>

                                </div>

                            </div>

                        <?php endif; ?>


                        <?php if($esSiembra): ?>

                            <div class="row g-3 mb-4">

                                <div class="col-md-3">

                                    <label
                                        for="cantidad_hembras"
                                        class="form-label fw-semibold">

                                        Cantidad de hembras *

                                    </label>

                                    <input
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="form-control"
                                        id="cantidad_hembras"
                                        name="cantidad_hembras"
                                        value="<?php echo $esEdicion?htmlspecialchars($actividad['cantidadhembras']??0):0; ?>"
                                        required>

                                </div>


                                <div class="col-md-3">

                                    <label
                                        for="cantidad_machos"
                                        class="form-label fw-semibold">

                                        Cantidad de machos *

                                    </label>

                                    <input
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="form-control"
                                        id="cantidad_machos"
                                        name="cantidad_machos"
                                        value="<?php echo $esEdicion?htmlspecialchars($actividad['cantidadmachos']??0):0; ?>"
                                        required>

                                </div>


                                <div class="col-md-3">

                                    <label
                                        for="tiempo_aclimatar"
                                        class="form-label fw-semibold">

                                        Tiempo de aclimatación (min) *

                                    </label>

                                    <input
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="form-control"
                                        id="tiempo_aclimatar"
                                        name="tiempo_aclimatar"
                                        value="<?php echo $esEdicion?htmlspecialchars($actividad['tiempoaclimatacionmin']??0):0; ?>"
                                        required>

                                </div>


                                <div class="col-md-3">

                                    <label
                                        for="volumen_agua"
                                        class="form-label fw-semibold">

                                        Volumen de agua (litros) *

                                    </label>

                                    <input
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="form-control"
                                        id="volumen_agua"
                                        name="volumen_agua"
                                        value="<?php echo $esEdicion?htmlspecialchars($actividad['volumenagualitros']??0):0; ?>"
                                        required>

                                </div>

                            </div>


                            <div class="row g-3 mb-4">

                                <div class="col-md-3">

                                    <label
                                        for="recolectar_empacar"
                                        class="form-label fw-semibold">

                                        ¿Recolectar y empacar? *

                                    </label>

                                    <select
                                        class="form-select"
                                        id="recolectar_empacar"
                                        name="recolectar_empacar"
                                        required>

                                        <option
                                            value="S"
                                            <?php
                                            echo $esEdicion&&($actividad['recolectarempacar']??'')==='S'
                                            ?'selected'
                                            :'';
                                            ?>>

                                            Sí

                                        </option>

                                        <option
                                            value="N"
                                            <?php
                                            echo $esEdicion&&($actividad['recolectarempacar']??'')==='N'
                                            ?'selected'
                                            :'';
                                            ?>>

                                            No

                                        </option>

                                    </select>

                                </div>

                            </div>

                        <?php endif; ?>


                        <?php if($esSeguimiento): ?>

                            <div class="row g-3 mb-4">

                                <div class="col-md-6">

                                    <label
                                        for="peces"
                                        class="form-label fw-semibold">

                                        ¿Se evidencia presencia de peces? *

                                    </label>

                                    <select
                                        class="form-select"
                                        id="peces"
                                        name="peces"
                                        required>

                                        <option
                                            value="S"
                                            <?php
                                            echo !$esEdicion||($actividad['peces']??'')==='S'
                                            ?'selected'
                                            :'';
                                            ?>>

                                            Sí

                                        </option>

                                        <option
                                            value="N"
                                            <?php
                                            echo $esEdicion&&($actividad['peces']??'')==='N'
                                            ?'selected'
                                            :'';
                                            ?>>

                                            No

                                        </option>

                                    </select>

                                </div>


                                <div class="col-md-6">

                                    <label
                                        for="larvas"
                                        class="form-label fw-semibold">

                                        ¿Se evidencia presencia de larvas? *

                                    </label>

                                    <select
                                        class="form-select"
                                        id="larvas"
                                        name="larvas"
                                        required>

                                        <option
                                            value="N"
                                            <?php
                                            echo !$esEdicion||($actividad['larvas']??'')==='N'
                                            ?'selected'
                                            :'';
                                            ?>>

                                            No

                                        </option>

                                        <option
                                            value="S"
                                            <?php
                                            echo $esEdicion&&($actividad['larvas']??'')==='S'
                                            ?'selected'
                                            :'';
                                            ?>>

                                            Sí

                                        </option>

                                    </select>

                                </div>

                            </div>

                        <?php endif; ?>


                        <?php if($esResiembra): ?>

                            <div class="row g-3 mb-4">

                                <div class="col-md-3">

                                    <label
                                        for="cantidad_hembras"
                                        class="form-label fw-semibold">

                                        Cantidad de hembras *

                                    </label>

                                    <input
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="form-control"
                                        id="cantidad_hembras"
                                        name="cantidad_hembras"
                                        value="<?php echo $esEdicion?htmlspecialchars($actividad['cantidadhembras']??0):0; ?>"
                                        required>

                                </div>


                                <div class="col-md-3">

                                    <label
                                        for="cantidad_machos"
                                        class="form-label fw-semibold">

                                        Cantidad de machos *

                                    </label>

                                    <input
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="form-control"
                                        id="cantidad_machos"
                                        name="cantidad_machos"
                                        value="<?php echo $esEdicion?htmlspecialchars($actividad['cantidadmachos']??0):0; ?>"
                                        required>

                                </div>


                                <div class="col-md-3">

                                    <label
                                        for="tiempo_aclimatar"
                                        class="form-label fw-semibold">

                                        Tiempo de aclimatación (min) *

                                    </label>

                                    <input
                                        type="number"
                                        min="0"
                                        step="1"
                                        class="form-control"
                                        id="tiempo_aclimatar"
                                        name="tiempo_aclimatar"
                                        value="<?php echo $esEdicion?htmlspecialchars($actividad['tiempoaclimatacionmin']??0):0; ?>"
                                        required>

                                </div>


                                <div class="col-md-3">

                                    <label
                                        for="recolectar_empacar"
                                        class="form-label fw-semibold">

                                        ¿Recolectar y empacar? *

                                    </label>

                                    <select
                                        class="form-select"
                                        id="recolectar_empacar"
                                        name="recolectar_empacar"
                                        required>

                                        <option
                                            value="S"
                                            <?php
                                            echo !$esEdicion||($actividad['recolectarempacar']??'')==='S'
                                            ?'selected'
                                            :'';
                                            ?>>

                                            Sí

                                        </option>

                                        <option
                                            value="N"
                                            <?php
                                            echo $esEdicion&&($actividad['recolectarempacar']??'')==='N'
                                            ?'selected'
                                            :'';
                                            ?>>

                                            No

                                        </option>

                                    </select>

                                </div>

                            </div>

                        <?php endif; ?>


                        <div class="row g-3 mb-4">

                            <div class="col-12">

                                <label
                                    for="observaciones"
                                    class="form-label fw-semibold">

                                    Observaciones

                                </label>

                                <textarea
                                    class="form-control"
                                    id="observaciones"
                                    name="observaciones"
                                    rows="2"><?php echo $esEdicion?htmlspecialchars($actividad['observaciones']??''):''; ?></textarea>

                            </div>

                        </div>


                        <div class="d-flex justify-content-end pt-3">

                            <button
                                type="submit"
                                class="btn btn-primary px-4 py-2">

                                <i class="bi bi-floppy me-2"></i>

                                <?php echo $esEdicion?'Guardar cambios':'Guardar actividad'; ?>

                            </button>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

</div>


<?php if(!$esEdicion): ?>

<script>

var formulario=document.querySelector(
    '#actividadTerFormEdicion form'
);

var hora=document.getElementById('hora');
var minuto=document.getElementById('minuto');
var horaActividad=document.getElementById('hora_actividad');

if(hora&&minuto){

    hora.addEventListener('change',function(){

        for(var i=1;i<minuto.options.length;i++){

            minuto.options[i].disabled=false;

        }

        if(hora.value==='18'){

            minuto.value='00';

            for(var i=1;i<minuto.options.length;i++){

                if(minuto.options[i].value!=='00'){

                    minuto.options[i].disabled=true;

                }

            }

        }

    });

}

if(formulario){

    formulario.addEventListener('submit',function(){

        if(hora&&minuto&&hora.value!==''&&minuto.value!==''){

            horaActividad.value=
            hora.value+':'+minuto.value;

        }

    });

}

</script>

<?php endif; ?>