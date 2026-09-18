$(document).ready(function () {

    // ======================================================
    // ABRIR Y CERRAR EL MENÚ LATERAL
    // ======================================================

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

});


// ======================================================
// BUSCADOR DE USUARIOS
// ======================================================

const buscadorUsuarios =
    document.getElementById("buscadorUsuarios");


if (buscadorUsuarios) {

    buscadorUsuarios.addEventListener(
        "keyup",
        function () {

            let filtro =
                this.value.toLowerCase();

            document
                .querySelectorAll(
                    "#tablaUsuarios tbody tr"
                )
                .forEach(function (fila) {

                    fila.style.display =
                        fila.textContent
                            .toLowerCase()
                            .includes(filtro)
                            ? ""
                            : "none";

                });

        }
    );

}



// ======================================================
// ZOOCRIADERO
// FILTRAR BARRIOS SEGÚN LA COMUNA
//
// FUNCIONA PARA:
// - NUEVO ZOOCRIADERO
// - EDITAR ZOOCRIADERO
// ======================================================

function filtrarBarriosZoocriadero(
    selectComuna
) {

    const codComuna =
        String(selectComuna.value);


    const selectBarrio =
        document.getElementById(
            "codbarrio"
        );


    if (!selectBarrio) {

        return;

    }


    const barrios =
        selectBarrio.querySelectorAll(
            "option[data-comuna]"
        );


    // Limpiar barrio anterior
    selectBarrio.value = "";


    // ==================================================
    // SI NO HAY COMUNA
    // ==================================================

    if (codComuna === "") {

        selectBarrio.disabled = true;


        if (
            selectBarrio.options.length > 0
        ) {

            selectBarrio.options[0]
                .textContent =
                "Primero seleccione una comuna...";

        }


        barrios.forEach(
            function (barrio) {

                barrio.hidden = true;
                barrio.disabled = true;

            }
        );


        return;

    }


    // ==================================================
    // HABILITAR BARRIO
    // ==================================================

    selectBarrio.disabled = false;


    if (
        selectBarrio.options.length > 0
    ) {

        selectBarrio.options[0]
            .textContent =
            "Seleccione un barrio...";

    }


    // ==================================================
    // MOSTRAR SOLO BARRIOS DE LA COMUNA
    // ==================================================

    barrios.forEach(
        function (barrio) {

            const comunaBarrio =
                String(
                    barrio.getAttribute(
                        "data-comuna"
                    )
                );


            if (
                comunaBarrio === codComuna
            ) {

                barrio.hidden = false;
                barrio.disabled = false;

            } else {

                barrio.hidden = true;
                barrio.disabled = true;

            }

        }
    );

}


// ======================================================
// DETECTAR CAMBIO DE COMUNA
// ======================================================

document.addEventListener(
    "change",
    function (event) {

        if (
            event.target.id === "codcomuna"
        ) {

            filtrarBarriosZoocriadero(
                event.target
            );

        }

    }
);


// ======================================================
// REPORTES ZOOCRIADERO
// ======================================================
// ESTA FUNCIÓN ENVÍA EL FORMULARIO SIN RECARGAR
// ======================================================

function generarReporteZoo(
    event,
    formulario
) {

    if (event) {

        event.preventDefault();

    }


    // Evita enviar dos veces si existe
    // también un onsubmit en el HTML.
    if (
        formulario.dataset.enviandoReporte === "1"
    ) {

        return false;

    }


    formulario.dataset.enviandoReporte =
        "1";


    const boton =
        formulario.querySelector(
            'button[type="submit"]'
        );


    const resultado =
        document.getElementById(
            "resultadoReporte"
        );


    const alerta =
        document.getElementById(
            "alertaReporte"
        );


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

            if (
                respuesta.tipo === "seguimiento"
            ) {

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

                if (
                    respuesta.datos.length === 0
                ) {

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

                    respuesta.datos.forEach(
                        function (fila) {

                            let estado = "";


                            if (
                                fila.estado === "A"
                            ) {

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

                        }
                    );

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

            if (
                respuesta.tipo === "mortalidad"
            ) {

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

                if (
                    respuesta.datos.length === 0
                ) {

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

                    respuesta.datos.forEach(
                        function (fila) {

                            let estado = "";


                            if (
                                fila.estado === "A"
                            ) {

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

                        }
                    );

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

            if (
                respuesta.tipo === "tanques"
            ) {

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

                if (
                    respuesta.datos.length === 0
                ) {

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

                    respuesta.datos.forEach(
                        function (fila) {

                            let estado = "";


                            if (
                                fila.estado === "A"
                            ) {

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

                        }
                    );

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

                resultado.innerHTML =
                    html;

            }

        },


        // ==================================================
        // ERROR
        // ==================================================

        error: function (
            xhr,
            status,
            error
        ) {

            console.log(
                xhr.responseText
            );


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

            formulario.dataset.enviandoReporte =
                "0";


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

$(document).on(
    "submit",
    "#formGenerarReporte",
    function (event) {

        event.preventDefault();

        generarReporteZoo(
            event,
            this
        );

        return false;

    }
);