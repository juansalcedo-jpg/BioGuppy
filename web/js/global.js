$(document).ready(function () {

    // ABRIR Y CERRAR EL MENÚ LATERAL
    $(document).on("click", "#btnToggleSidebar", function () {
        $(".app-layout").toggleClass("sidebar-collapsed");
    });


    // ======================================================
    // FILTRO DE BÚSQUEDA
    // ======================================================
    $(document).on("keyup", "#filtro", function () {
        let data = $(this).val();
        let url = $(this).data("url");

        $.ajax({
            url: url,
            type: "GET",
            data: {
                buscar: data
            },
            success: function (data) {
                if (data.trim() !== "") {
                    $("tbody").html(data);
                } else {
                    $("tbody").html(
                        "<tr>" +
                        "<td colspan='5'>" +
                        "Elemento no encontrado" +
                        "</td>" +
                        "</tr>"
                    );
                }
            }
        });
    });

    $(document).on("click", ".accesibilidad-wrapper [data-filtro]", function (e) {
        e.preventDefault();

        let filtro = $(this).data("filtro");

        $("body").removeClass("modo-protanopia modo-deuteranopia modo-tritanopia");

        if (filtro !== "ninguno") {
            $("body").addClass("modo-" + filtro);
        }

        $(".accesibilidad-wrapper .dropdown-item").removeClass("filtro-activo");
        $(this).addClass("filtro-activo");

        localStorage.setItem("modoDaltonico", filtro);
    });


    let filtroGuardado = localStorage.getItem("modoDaltonico");

    if (filtroGuardado && filtroGuardado !== "ninguno") {
        $("body").addClass("modo-" + filtroGuardado);
        $(".accesibilidad-wrapper [data-filtro='" + filtroGuardado + "']").addClass("filtro-activo");
    } else {
        $(".accesibilidad-wrapper [data-filtro='ninguno']").addClass("filtro-activo");
    }

    function actualizarIconoTema(esOscuro) {
        $("#iconoTema")
            .toggleClass("bi-moon-stars", !esOscuro)
            .toggleClass("bi-sun", esOscuro);
        $("#botonTema").attr(
            "title",
            esOscuro ? "Cambiar a tema claro" : "Cambiar a tema oscuro"
        );
    }

    $(document).on("click", "#botonTema", function () {
        $("body").toggleClass("tema-oscuro");

        let esOscuro = $("body").hasClass("tema-oscuro");
        localStorage.setItem("tema", esOscuro ? "oscuro" : "claro");
        actualizarIconoTema(esOscuro);
    });

    let temaGuardado = localStorage.getItem("tema") === "oscuro";

    if (temaGuardado) {
        $("body").addClass("tema-oscuro");
    }
    actualizarIconoTema(temaGuardado);

});



// ======================================================
// BUSCADOR DE USUARIOS
// ======================================================
const buscadorUsuarios = document.getElementById("buscadorUsuarios");

if (buscadorUsuarios) {
    buscadorUsuarios.addEventListener("keyup", function () {
        let filtro = this.value.toLowerCase();

        document.querySelectorAll("#tablaUsuarios tbody tr").forEach(function (fila) {
            fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? "" : "none";
        });
    });
}


// ======================================================
// ZOOCRIADERO
// FILTRAR BARRIOS SEGÚN LA COMUNA
//
// FUNCIONA PARA:
// - NUEVO ZOOCRIADERO
// - EDITAR ZOOCRIADERO
// ======================================================
function filtrarBarriosZoocriadero(selectComuna) {

    const codComuna = String(selectComuna.value);
    const selectBarrio = document.getElementById("codbarrio");

    if (!selectBarrio) {
        return;
    }

    const barrios = selectBarrio.querySelectorAll("option[data-comuna]");

    // Limpiar barrio anterior
    selectBarrio.value = "";

    // ==================================================
    // SI NO HAY COMUNA
    // ==================================================
    if (codComuna === "") {
        selectBarrio.disabled = true;

        if (selectBarrio.options.length > 0) {
            selectBarrio.options[0].textContent = "Primero seleccione una comuna...";
        }

        barrios.forEach(function (barrio) {
            barrio.hidden = true;
            barrio.disabled = true;
        });

        return;
    }

    // ==================================================
    // HABILITAR BARRIO
    // ==================================================
    selectBarrio.disabled = false;

    if (selectBarrio.options.length > 0) {
        selectBarrio.options[0].textContent = "Seleccione un barrio...";
    }

    // ==================================================
    // MOSTRAR SOLO BARRIOS DE LA COMUNA
    // ==================================================
    barrios.forEach(function (barrio) {
        const comunaBarrio = String(barrio.getAttribute("data-comuna"));

        if (comunaBarrio === codComuna) {
            barrio.hidden = false;
            barrio.disabled = false;
        } else {
            barrio.hidden = true;
            barrio.disabled = true;
        }
    });
}


// ======================================================
// DETECTAR CAMBIO DE COMUNA
// ======================================================
document.addEventListener("change", function (event) {
    if (event.target.id === "codcomuna") {
        filtrarBarriosZoocriadero(event.target);
    }
});


// ======================================================
// REPORTES ZOOCRIADERO
// ======================================================
// ESTA FUNCIÓN ENVÍA EL FORMULARIO SIN RECARGAR
// ======================================================
function generarReporteZoo(event, formulario) {

    if (event) {
        event.preventDefault();
    }

    // Evita enviar dos veces si existe
    // también un onsubmit en el HTML.
    if (formulario.dataset.enviandoReporte === "1") {
        return false;
    }

    formulario.dataset.enviandoReporte = "1";

    const boton = formulario.querySelector('button[type="submit"]');
    const resultado = document.getElementById("resultadoReporte");
    const alerta = document.getElementById("alertaReporte");

    if (alerta) {
        alerta.innerHTML = "";
    }

    if (boton) {
        boton.disabled = true;
    }

    // ==================================================
    // AJAX
    // ==================================================
    $.ajax({

        url: formulario.action,
        type: "POST",
        data: new FormData(formulario),
        processData: false,
        contentType: false,
        dataType: "json",

        // ==================================================
        // RESPUESTA CORRECTA
        // ==================================================
        success: function (respuesta) {

            // ==============================================
            // ERROR DE VALIDACIÓN
            // ==============================================
            if (!respuesta.ok) {
                if (alerta) {
                    alerta.innerHTML =
                        '<div class="alert alert-danger">' +
                        respuesta.mensaje +
                        "</div>";
                }
                return;
            }

            let html = "";

            // ==================================================
            // 1. SEGUIMIENTO DE ACTIVIDADES
            // ==================================================
            if (respuesta.tipo === "seguimiento") {

                html +=
                    '<div class="card border-0 shadow-sm">' +
                    '<div class="card-header bg-white border-bottom py-3">' +
                    '<div class="d-flex align-items-center">' +
                    '<i class="bi bi-file-earmark-text text-primary me-2"></i>' +
                    '<span class="fw-semibold">' +
                    "Resultados: " +
                    respuesta.titulo +
                    "</span>" +
                    "</div>" +
                    "</div>" +

                    '<div class="table-responsive">' +
                    '<table class="table table-striped align-middle mb-0">' +
                    '<thead class="table-dark">' +
                    "<tr>" +
                    '<th class="ps-4">Fecha</th>' +
                    "<th>Tipo</th>" +
                    "<th>Tanque</th>" +
                    "<th>Responsable</th>" +
                    "<th>Observaciones</th>" +
                    '<th class="text-center">Estado</th>' +
                    "</tr>" +
                    "</thead>" +
                    "<tbody>";

                // ==========================================
                // SIN REGISTROS
                // ==========================================
                if (respuesta.datos.length === 0) {
                    html +=
                        "<tr>" +
                        '<td colspan="6" ' +
                        'class="text-center text-muted py-5">' +
                        '<i class="bi bi-inbox fs-3 d-block mb-2"></i>' +
                        "No se encontraron registros para la consulta seleccionada." +
                        "</td>" +
                        "</tr>";
                } else {

                    // ======================================
                    // MOSTRAR REGISTROS
                    // ======================================
                    respuesta.datos.forEach(function (fila) {

                        let estado = "";

                        if (fila.estado === "A") {
                            estado =
                                '<span class="badge bg-success">' +
                                "Activo" +
                                "</span>";
                        } else {
                            estado =
                                '<span class="badge bg-danger">' +
                                "Inactivo" +
                                "</span>";
                        }

                        html +=
                            "<tr>" +
                            '<td class="ps-4">' +
                            fila.fecha +
                            "</td>" +
                            "<td>" +
                            fila.tipo +
                            "</td>" +
                            "<td>" +
                            fila.tanque +
                            "</td>" +
                            "<td>" +
                            fila.responsable +
                            "</td>" +
                            "<td>" +
                            fila.observaciones +
                            "</td>" +
                            '<td class="text-center">' +
                            estado +
                            "</td>" +
                            "</tr>";
                    });
                }

                html +=
                    "</tbody>" +
                    "</table>" +
                    "</div>" +
                    "</div>";
            }


            // ==================================================
            // 2. NACIDOS Y MUERTOS POR TANQUE
            // ==================================================
            if (respuesta.tipo === "mortalidad") {

                html +=
                    '<div class="card border-0 shadow-sm">' +
                    '<div class="card-header bg-white border-bottom py-3">' +
                    '<div class="d-flex align-items-center">' +
                    '<i class="bi bi-file-earmark-text text-primary me-2"></i>' +
                    '<span class="fw-semibold">' +
                    "Resultados: " +
                    respuesta.titulo +
                    "</span>" +
                    "</div>" +
                    "</div>" +

                    '<div class="table-responsive">' +
                    '<table class="table table-striped align-middle mb-0">' +
                    '<thead class="table-dark">' +
                    "<tr>" +
                    '<th class="ps-4">Fecha</th>' +
                    "<th>Zoocriadero</th>" +
                    "<th>Tanque</th>" +
                    "<th>Nacidos</th>" +
                    "<th>Muertos</th>" +
                    "<th>Responsable</th>" +
                    '<th class="text-center">Estado</th>' +
                    "</tr>" +
                    "</thead>" +
                    "<tbody>";

                // ==========================================
                // SIN REGISTROS
                // ==========================================
                if (respuesta.datos.length === 0) {
                    html +=
                        "<tr>" +
                        '<td colspan="7" ' +
                        'class="text-center text-muted py-5">' +
                        '<i class="bi bi-inbox fs-3 d-block mb-2"></i>' +
                        "No se encontraron registros para la consulta seleccionada." +
                        "</td>" +
                        "</tr>";
                } else {

                    // ======================================
                    // MOSTRAR REGISTROS
                    // ======================================
                    respuesta.datos.forEach(function (fila) {

                        let estado = "";

                        if (fila.estado === "A") {
                            estado =
                                '<span class="badge bg-success">' +
                                "Activo" +
                                "</span>";
                        } else {
                            estado =
                                '<span class="badge bg-danger">' +
                                "Inactivo" +
                                "</span>";
                        }

                        html +=
                            "<tr>" +
                            '<td class="ps-4">' +
                            fila.fecha +
                            "</td>" +
                            "<td>" +
                            fila.zoocriadero +
                            "</td>" +
                            "<td>" +
                            fila.tanque +
                            "</td>" +
                            "<td>" +
                            fila.nacidos +
                            "</td>" +
                            "<td>" +
                            fila.muertos +
                            "</td>" +
                            "<td>" +
                            fila.responsable +
                            "</td>" +
                            '<td class="text-center">' +
                            estado +
                            "</td>" +
                            "</tr>";
                    });
                }

                html +=
                    "</tbody>" +
                    "</table>" +
                    "</div>" +
                    "</div>";
            }


            // ==================================================
            // 3. TANQUES POR ZOOCRIADERO
            // ==================================================
            if (respuesta.tipo === "tanques") {

                html +=
                    '<div class="card border-0 shadow-sm">' +
                    '<div class="card-header bg-white border-bottom py-3">' +
                    '<div class="d-flex align-items-center">' +
                    '<i class="bi bi-file-earmark-text text-primary me-2"></i>' +
                    '<span class="fw-semibold">' +
                    "Resultados: " +
                    respuesta.titulo +
                    "</span>" +
                    "</div>" +
                    "</div>" +

                    '<div class="table-responsive">' +
                    '<table class="table table-striped align-middle mb-0">' +
                    '<thead class="table-dark">' +
                    "<tr>" +
                    '<th class="ps-4">Fecha</th>' +
                    "<th>Zoocriadero</th>" +
                    "<th>Tanque</th>" +
                    "<th>Tipo</th>" +
                    "<th>Capacidad</th>" +
                    '<th class="text-center">Estado</th>' +
                    "</tr>" +
                    "</thead>" +
                    "<tbody>";

                // ==========================================
                // SIN REGISTROS
                // ==========================================
                if (respuesta.datos.length === 0) {
                    html +=
                        "<tr>" +
                        '<td colspan="6" ' +
                        'class="text-center text-muted py-5">' +
                        '<i class="bi bi-inbox fs-3 d-block mb-2"></i>' +
                        "No se encontraron registros para la consulta seleccionada." +
                        "</td>" +
                        "</tr>";
                } else {

                    // ======================================
                    // MOSTRAR REGISTROS
                    // ======================================
                    respuesta.datos.forEach(function (fila) {

                        let estado = "";

                        if (fila.estado === "A") {
                            estado =
                                '<span class="badge bg-success">' +
                                "Activo" +
                                "</span>";
                        } else {
                            estado =
                                '<span class="badge bg-danger">' +
                                "Inactivo" +
                                "</span>";
                        }

                        html +=
                            "<tr>" +
                            '<td class="ps-4">' +
                            fila.fecha +
                            "</td>" +
                            "<td>" +
                            fila.zoocriadero +
                            "</td>" +
                            "<td>" +
                            fila.tanque +
                            "</td>" +
                            "<td>" +
                            fila.tipo +
                            "</td>" +
                            "<td>" +
                            fila.capacidad +
                            " L" +
                            "</td>" +
                            '<td class="text-center">' +
                            estado +
                            "</td>" +
                            "</tr>";
                    });
                }

                html +=
                    "</tbody>" +
                    "</table>" +
                    "</div>" +
                    "</div>";
            }


            // ==================================================
            // INSERTAR TABLA EN LA PÁGINA
            // ==================================================
            if (resultado) {
                resultado.innerHTML = html;
            }
        },

        // ==================================================
        // ERROR
        // ==================================================
        error: function (xhr, status, error) {
            console.log(xhr.responseText);

            if (alerta) {
                alerta.innerHTML =
                    '<div class="alert alert-danger">' +
                    "Ocurrió un error al generar el reporte." +
                    "</div>";
            }
        },

        // ==================================================
        // TERMINÓ LA PETICIÓN
        // ==================================================
        complete: function () {
            formulario.dataset.enviandoReporte = "0";

            if (boton) {
                boton.disabled = false;
            }
        }

    });

    return false;
}


// ======================================================
// DETECTAR ENVÍO DEL FORMULARIO DE REPORTES
// ======================================================
// FUNCIONA AUNQUE EL FORMULARIO SE CARGUE DESPUÉS.
// ======================================================
$(document).on("submit", "#formGenerarReporte", function (event) {
    event.preventDefault();
    generarReporteZoo(event, this);
    return false;
});


// ======================================================
// AUTOCOMPLETADO DE DIRECCIONES (Nominatim / OpenStreetMap)
//
// FUNCIONA PARA:
// - CREAR / EDITAR ZOOCRIADERO
// - CREAR / EDITAR SITIO DE TERRENO
//
// El input necesita la clase "input-direccion" y:
//   data-url          -> ajax.php?...&funcion=buscarDireccion
//   data-sugerencias  -> id del div donde se pintan las opciones
//   data-error        -> id del div donde se muestra el error
// El servidor devuelve las opciones ya en HTML.
// ======================================================
const PATRON_DIRECCION = /^(Calle|Carrera|Avenida)\s+(\d{1,3}\s?[A-Z]?(\s?Bis)?(\s?[A-Z])?|[A-ZÁÉÍÓÚÑ]{3,}(\s[A-ZÁÉÍÓÚÑ]{2,}){0,3})(\s(Norte|Sur|Este|Oeste))?\s*#\s*\d{1,3}\s?[A-Z]?(\s?Bis)?\s*-\s*\d{1,3}(\s(Norte|Sur|Este|Oeste))?$/iu;

let temporizadorDireccion = null;
let peticionDireccion = null;

function ocultarSugerenciasDireccion() {
    $(".sugerencias-direccion").addClass("d-none").html("");
}

function mostrarErrorDireccion(input, mensaje) {
    const error = $("#" + input.attr("data-error"));
    const grupo = input.closest(".input-group");

    if (mensaje) {
        error.text(mensaje).removeClass("d-none");
        grupo.addClass("border-danger");
    } else {
        error.text("").addClass("d-none");
        grupo.removeClass("border-danger");
    }
}

// Mientras escribe: espera 700 ms sin teclear y consulta
$(document).on("input", ".input-direccion", function () {
    const input = $(this);
    const lista = $("#" + input.attr("data-sugerencias"));
    const texto = input.val().trim();
    const via = texto.split("#")[0].trim();

    mostrarErrorDireccion(input, null);
    clearTimeout(temporizadorDireccion);

    if (peticionDireccion) {
        peticionDireccion.abort();
    }

    // Si escribió muy poco no se busca
    if (via.length < 4) {
        lista.addClass("d-none").html("");
        return;
    }

    temporizadorDireccion = setTimeout(function () {
        lista.removeClass("d-none").html(
            '<div class="list-group-item small text-muted py-2">' +
            '<span class="spinner-border spinner-border-sm me-2"></span>Buscando direcciones...' +
            "</div>"
        );

        peticionDireccion = $.ajax({
            url: input.attr("data-url"),
            type: "GET",
            data: { direccion: texto },
            success: function (html) {
                lista.html(html);
            },
            error: function (xhr, estado) {
                if (estado !== "abort") {
                    lista.html('<div class="list-group-item small text-danger py-2">No se pudo consultar las direcciones.</div>');
                }
            }
        });
    }, 700);
});

// Quita tildes y mayúsculas para comparar nombres de barrios
function textoComparable(texto) {
    return String(texto || "").toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "").replace(/\s+/g, " ").trim();
}

// Si el barrio que trae el mapa existe en el formulario, se seleccionan la comuna y el barrio
function seleccionarBarrioDireccion(formulario, nombreBarrio) {
    if (!formulario || !nombreBarrio) {
        return;
    }

    const selectComuna = formulario.querySelector("#codcomuna");
    const selectBarrio = formulario.querySelector("#codbarrio");

    if (!selectComuna || !selectBarrio) {
        return;
    }

    const buscado = textoComparable(nombreBarrio);
    const opcion = Array.prototype.find.call(selectBarrio.options, function (op) {
        return op.value !== "" && textoComparable(op.textContent) === buscado;
    });

    if (!opcion) {
        return;
    }

    selectComuna.value = opcion.getAttribute("data-comuna");
    selectComuna.dispatchEvent(new Event("change", { bubbles: true }));

    opcion.hidden = false;
    opcion.disabled = false;
    opcion.style.display = "";
    selectBarrio.disabled = false;
    selectBarrio.value = opcion.value;
}

// Al elegir una opción: se pone en el input y, si se puede, se selecciona el barrio
$(document).on("click", ".opcion-direccion", function () {
    const lista = $(this).closest(".sugerencias-direccion");
    const input = $('.input-direccion[data-sugerencias="' + lista.attr("id") + '"]');
    const valor = $(this).attr("data-direccion");

    input.val(valor).trigger("focus");
    input[0].setSelectionRange(valor.length, valor.length);

    seleccionarBarrioDireccion(input[0].form, $(this).attr("data-barrio"));

    ocultarSugerenciasDireccion();
    mostrarErrorDireccion(input, null);
});

// Cerrar la lista al hacer clic afuera o con Escape
$(document).on("click", function (event) {
    if (!$(event.target).closest(".input-direccion, .sugerencias-direccion").length) {
        ocultarSugerenciasDireccion();
    }
});

$(document).on("keydown", ".input-direccion", function (event) {
    if (event.key === "Escape") {
        ocultarSugerenciasDireccion();
    }
});

// Validar antes de enviar. Se registra en fase de captura para que corra
// antes del envío por AJAX del modal y lo pueda detener si hay error.
document.addEventListener("submit", function (event) {
    const campo = event.target.querySelector(".input-direccion");

    if (!campo) {
        return;
    }

    const input = $(campo);
    campo.value = campo.value.replace(/\s+/g, " ").trim();

    if (!PATRON_DIRECCION.test(campo.value)) {
        event.preventDefault();
        event.stopPropagation();

        const mensaje = campo.value.indexOf("#") !== -1
            ? "Completa la placa, por ejemplo: " + campo.value.split("#")[0].trim() + " # 36-05"
            : "Formato no válido. Ejemplo: Calle 5 # 36-05, Carrera 8A # 3-15 o Avenida Roosevelt # 38-20";

        mostrarErrorDireccion(input, mensaje);
        campo.focus();
    }
}, true);

document.addEventListener("DOMContentLoaded", function () {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
