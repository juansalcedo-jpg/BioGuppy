<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">

            <h2 class="text-center fw-bold mb-4 text-dark">Actividad de Terreno</h2>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">

                    <form id="formSiembra" action="<?php echo getUrl('ActividadesTer','ActividadesTer','postCreateSiembra'); ?>" method="POST" novalidate>

                        <?php
                        $errorServidor=$_SESSION['error']??'';
                        unset($_SESSION['error']);
                        ?>

                        <div id="alertaValidacion" class="alert alert-danger align-items-start mb-4 <?php echo $errorServidor!==''?'d-flex':'d-none'; ?>" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2 mt-1"></i>
                            <div id="mensajeAlerta" class="flex-grow-1"><?php echo htmlspecialchars($errorServidor); ?></div>
                            <button type="button" class="btn-close ms-2" id="cerrarAlerta" aria-label="Cerrar"></button>
                        </div>

                        <div class="row g-3 mb-4">

                            <div class="col-md-5">
                                <label for="deposito_id" class="form-label fw-semibold">Depósito *</label>

                                <select class="form-select form-select-lg fs-6" id="deposito_id" name="deposito_id">
                                    <option value="" selected disabled>Seleccione...</option>

                                    <?php if(isset($depositos)&&$depositos): ?>
                                        <?php while($dep=$depositos->fetch(PDO::FETCH_ASSOC)): ?>
                                            <option value="<?php echo $dep['id']; ?>">
                                                <?php echo htmlspecialchars($dep['nombresitio'].' — '.$dep['tipodeposito']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>

                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="fecha_actividad" class="form-label fw-semibold">Fecha *</label>

                                <input type="date"
                                class="form-control form-control-lg fs-6"
                                id="fecha_actividad"
                                name="fecha_actividad"
                                value="<?php echo date('Y-m-d'); ?>"
                                min="<?php echo date('Y-m-d',strtotime('-2 days')); ?>"
                                max="<?php echo date('Y-m-d'); ?>">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Hora *</label>

                                <div class="d-flex gap-2">

                                    <select class="form-select form-select-lg fs-6" id="hora">
                                        <option value="" selected disabled>Hora</option>
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

                                    <select class="form-select form-select-lg fs-6" id="minuto">
                                        <option value="" selected disabled>Min</option>

                                        <?php
                                        for($i=0;$i<=59;$i++):
                                            $minuto=str_pad($i,2,'0',STR_PAD_LEFT);
                                        ?>

                                            <option value="<?php echo $minuto; ?>">
                                                <?php echo $minuto; ?>
                                            </option>

                                        <?php endfor; ?>

                                    </select>

                                </div>

                                <input type="hidden" id="hora_actividad" name="hora_actividad">
                            </div>

                        </div>

                        <ul class="nav nav-tabs mb-4 border-bottom">

                            <li class="nav-item">
                                <a class="nav-link text-secondary fw-semibold"
                                href="<?php echo getUrl('ActividadesTer','ActividadesTer','Inspeccion'); ?>">
                                    Inspección
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link active fw-semibold text-primary border-0 border-bottom border-primary border-3"
                                href="<?php echo getUrl('ActividadesTer','ActividadesTer','Siembra'); ?>">
                                    Siembra
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link text-secondary fw-semibold"
                                href="<?php echo getUrl('ActividadesTer','ActividadesTer','Seguimiento'); ?>">
                                    Seguimiento
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link text-secondary fw-semibold"
                                href="<?php echo getUrl('ActividadesTer','ActividadesTer','Resiembra'); ?>">
                                    Resiembra
                                </a>
                            </li>

                        </ul>

                        <div class="row g-3 mb-4">

                            <div class="col-md-3">
                                <label for="cantidad_hembras" class="form-label fw-semibold">Cantidad de hembras *</label>

                                <input type="number"
                                min="0"
                                step="1"
                                class="form-control form-control-lg fs-6"
                                id="cantidad_hembras"
                                name="cantidad_hembras"
                                value="0">
                            </div>

                            <div class="col-md-3">
                                <label for="cantidad_machos" class="form-label fw-semibold">Cantidad de machos *</label>

                                <input type="number"
                                min="0"
                                step="1"
                                class="form-control form-control-lg fs-6"
                                id="cantidad_machos"
                                name="cantidad_machos"
                                value="0">
                            </div>

                            <div class="col-md-3">
                                <label for="tiempo_aclimatar" class="form-label fw-semibold">Tiempo de aclimatación (min) *</label>

                                <input type="number"
                                min="0"
                                step="1"
                                class="form-control form-control-lg fs-6"
                                id="tiempo_aclimatar"
                                name="tiempo_aclimatar"
                                value="0">
                            </div>

                            <div class="col-md-3">
                                <label for="volumen_agua" class="form-label fw-semibold">Volumen de agua (litros) *</label>

                                <input type="number"
                                min="0"
                                step="1"
                                class="form-control form-control-lg fs-6"
                                id="volumen_agua"
                                name="volumen_agua"
                                value="0">
                            </div>

                        </div>

                        <div class="row g-3 mb-4">

                            <div class="col-md-3">
                                <label for="recolectar_empacar" class="form-label fw-semibold">¿Recolectar y empacar? *</label>

                                <select class="form-select form-select-lg fs-6"
                                id="recolectar_empacar"
                                name="recolectar_empacar">

                                    <option value="S" selected>Sí</option>
                                    <option value="N">No</option>

                                </select>
                            </div>

                        </div>

                        <div class="row g-3 mb-4">

                            <div class="col-12">
                                <label for="observaciones" class="form-label fw-semibold">Observaciones</label>

                                <textarea class="form-control"
                                id="observaciones"
                                name="observaciones"
                                rows="2"></textarea>
                            </div>

                        </div>

                        <div class="d-flex justify-content-end pt-3">

                            <button type="submit" class="btn btn-primary px-4 py-2 fs-6 fw-semibold d-inline-flex align-items-center rounded-3">
                                <i class="bi bi-floppy me-2"></i> Guardar actividad
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
const formulario=document.getElementById("formSiembra");
const deposito=document.getElementById("deposito_id");
const fecha=document.getElementById("fecha_actividad");
const hora=document.getElementById("hora");
const minuto=document.getElementById("minuto");
const horaActividad=document.getElementById("hora_actividad");
const hembras=document.getElementById("cantidad_hembras");
const machos=document.getElementById("cantidad_machos");
const tiempo=document.getElementById("tiempo_aclimatar");
const volumen=document.getElementById("volumen_agua");
const recolectar=document.getElementById("recolectar_empacar");
const alerta=document.getElementById("alertaValidacion");
const mensajeAlerta=document.getElementById("mensajeAlerta");
const cerrarAlerta=document.getElementById("cerrarAlerta");

const fechaMinima="<?php echo date('Y-m-d',strtotime('-2 days')); ?>";
const fechaMaxima="<?php echo date('Y-m-d'); ?>";

function mostrarAlertas(errores){

    mensajeAlerta.innerHTML="";

    errores.forEach(function(error){
        const mensaje=document.createElement("div");
        mensaje.textContent=error;
        mensajeAlerta.appendChild(mensaje);
    });

    alerta.classList.remove("d-none");
    alerta.classList.add("d-flex");
}

function ocultarAlerta(){
    alerta.classList.add("d-none");
    alerta.classList.remove("d-flex");
}

function validarEntero(valor){
    return /^(0|[1-9][0-9]*)$/.test(valor);
}

cerrarAlerta.addEventListener("click",ocultarAlerta);

hora.addEventListener("change",function(){

    for(let i=1;i<minuto.options.length;i++){
        minuto.options[i].disabled=false;
    }

    if(hora.value==="18"){

        minuto.value="00";

        for(let i=1;i<minuto.options.length;i++){

            if(minuto.options[i].value!=="00"){
                minuto.options[i].disabled=true;
            }

        }
    }
});

formulario.addEventListener("submit",function(e){

    const errores=[];

    if(deposito.value===""){
        errores.push("Debe seleccionar un depósito.");
    }

    if(fecha.value===""){
        errores.push("Debe registrar la fecha.");
    }

    if(hora.value===""||minuto.value===""){
        errores.push("Debe registrar la hora.");
    }

    if(hembras.value===""){
        errores.push("Debe ingresar la cantidad de hembras.");
    }

    if(machos.value===""){
        errores.push("Debe ingresar la cantidad de machos.");
    }

    if(tiempo.value===""){
        errores.push("Debe ingresar el tiempo de aclimatación.");
    }

    if(volumen.value===""){
        errores.push("Debe ingresar el volumen de agua.");
    }

    if(recolectar.value===""){
        errores.push("Debe seleccionar si se recolecta y empaca.");
    }

    if(errores.length===0){

        if(fecha.value<fechaMinima||fecha.value>fechaMaxima){
            errores.push("Solo puede registrar actividades de hoy o de los últimos 2 días.");
        }

        if(Number(hembras.value)<0){
            errores.push("La cantidad de hembras no puede ser negativa.");
        }else if(!validarEntero(hembras.value)){
            errores.push("La cantidad de hembras debe ser un número entero sin ceros a la izquierda.");
        }

        if(Number(machos.value)<0){
            errores.push("La cantidad de machos no puede ser negativa.");
        }else if(!validarEntero(machos.value)){
            errores.push("La cantidad de machos debe ser un número entero sin ceros a la izquierda.");
        }

        if(Number(hembras.value)+Number(machos.value)<=0){
            errores.push("Debe registrar al menos un guppy, hembra o macho.");
        }

        if(Number(tiempo.value)<0){
            errores.push("El tiempo de aclimatación no puede ser negativo.");
        }else if(!validarEntero(tiempo.value)){
            errores.push("El tiempo de aclimatación debe ser un número entero sin ceros a la izquierda.");
        }

        if(Number(volumen.value)<0){
            errores.push("El volumen de agua no puede ser negativo.");
        }else if(!validarEntero(volumen.value)){
            errores.push("El volumen de agua debe ser un número entero sin ceros a la izquierda.");
        }
    }

    if(errores.length>0){
        e.preventDefault();
        mostrarAlertas(errores);
        return;
    }

    horaActividad.value=hora.value+":"+minuto.value;
});
</script>