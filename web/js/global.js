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
    document.getElementById('buscadorUsuarios');


if (buscadorUsuarios) {

    buscadorUsuarios.addEventListener(
        'keyup',
        function () {

            var filtro =
                this.value.toLowerCase();


            document
                .querySelectorAll(
                    '#tablaUsuarios tbody tr'
                )
                .forEach(function (fila) {

                    fila.style.display =
                        fila.textContent
                            .toLowerCase()
                            .includes(filtro)
                            ? ''
                            : 'none';

                });

        }
    );

}


// ======================================================
// ENVIAR FORMULARIO DEL MODAL POR AJAX
// ======================================================

function enviarFormularioModalPorAjax(
    form,
    contenedorId,
    urlExito
) {

    var alerta =
        document.getElementById(
            'modalFormularioAjaxAlerta'
        );


    var datos =
        new FormData(form);


    var boton =
        form.querySelector(
            'button[type="submit"]'
        );


    // Desactivar botón mientras se guarda
    if (boton) {

        boton.disabled = true;

    }


    fetch(
        form.action,
        {
            method: 'POST',
            body: datos
        }
    )

        .then(function (r) {

            return r.text();

        })

        .then(function (texto) {


            // ==================================================
            // EXTRAER URL DEL REDIRECT DEVUELTO POR PHP
            // ==================================================

            var match =
                texto.match(
                    /window\.location\.href\s*=\s*'([^']+)'/
                );


            if (!match) {

                if (alerta) {

                    alerta.innerHTML =
                        '<div class="alert alert-warning mb-3">' +
                        'No se pudo interpretar la respuesta ' +
                        'del servidor.' +
                        '</div>';

                }

                return;

            }


            var destino = match[1];


            // ==================================================
            // SI EL REGISTRO O EDICIÓN FUE CORRECTA
            // ==================================================

            if (
                destino === urlExito ||
                destino.includes('listUsu') ||
                destino.includes('listZoo')
            ) {

                window.location.href =
                    destino;

                return;

            }


            // ==================================================
            // SI PHP DEVUELVE DE NUEVO EL FORMULARIO
            // SIGNIFICA QUE HUBO ERROR DE VALIDACIÓN
            // ==================================================

            fetch(destino)

                .then(function (r) {

                    return r.text();

                })

                .then(function (html) {

                    var parser =
                        new DOMParser();


                    var doc =
                        parser.parseFromString(
                            html,
                            'text/html'
                        );


                    var seccion =
                        doc.getElementById(
                            contenedorId
                        );


                    var mensajeError =
                        seccion
                            ? seccion.querySelector(
                                '.alert-danger, ' +
                                '.alert-warning'
                            )
                            : null;


                    if (alerta) {

                        alerta.innerHTML =
                            mensajeError
                                ? mensajeError.outerHTML
                                : '<div ' +
                                'class="alert alert-danger mb-3">' +
                                'Revisa los datos ingresados.' +
                                '</div>';

                    }

                })

                .catch(function () {

                    if (alerta) {

                        alerta.innerHTML =
                            '<div ' +
                            'class="alert alert-danger mb-3">' +
                            'Ocurrió un error al procesar ' +
                            'la validación.' +
                            '</div>';

                    }

                });

        })

        .catch(function () {

            if (alerta) {

                alerta.innerHTML =
                    '<div ' +
                    'class="alert alert-danger mb-3">' +
                    'No se pudo conectar con el servidor.' +
                    '</div>';

            }

        })

        .finally(function () {

            if (boton) {

                boton.disabled = false;

            }

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


function filtrarBarriosZoocriadero(
    selectComuna
) {

    // Código de la comuna seleccionada
    const codComuna =
        String(selectComuna.value);


    // Buscar el campo barrio
    const selectBarrio =
        document.getElementById(
            "codbarrio"
        );


    // Si no existe el select de barrio
    if (!selectBarrio) {

        return;

    }


    // Obtener todos los barrios
    const barrios =
        selectBarrio.querySelectorAll(
            "option[data-comuna]"
        );


    // Limpiar barrio seleccionado
    selectBarrio.value = "";


    // ==================================================
    // SI NO HAY COMUNA SELECCIONADA
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
    // HABILITAR CAMPO BARRIO
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
    // MOSTRAR SOLO LOS BARRIOS DE LA COMUNA
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
                comunaBarrio ===
                codComuna
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
//
// SE USA document.addEventListener PORQUE LOS FORMULARIOS
// DE NUEVO Y EDITAR SE CARGAN DINÁMICAMENTE EN EL MODAL.
// ======================================================

document.addEventListener(
    "change",
    function (event) {

        if (
            event.target.id ===
            "codcomuna"
        ) {

            filtrarBarriosZoocriadero(
                event.target
            );

        }

    }
);