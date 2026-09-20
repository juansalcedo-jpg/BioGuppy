<div class="container-fluid py-2">

    <div class="row justify-content-center">

        <div class="col-xl-11">

            <!-- ========================================== -->
            <!-- ENCABEZADO -->
            <!-- ========================================== -->

            <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">

                <div>

                    <h4 class="fw-semibold mb-1">
                        Reportes — Zoocriadero
                    </h4>

                    <p class="text-muted small mb-0">
                        Genera y exporta reportes consolidados
                        sobre las actividades y registros de zoocriaderos.
                    </p>

                </div>

            </div>


            <!-- ========================================== -->
            <!-- MENSAJES -->
            <!-- ========================================== -->

            <div id="alertaReporte"></div>


            <!-- ========================================== -->
            <!-- FILTROS -->
            <!-- ========================================== -->

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body py-3">

                    <form
                        id="formGenerarReporteZoo"
                        action="<?php
                            echo getUrl(
                                'ReportesZoo',
                                'ReportesZoo',
                                'generar',
                                false,
                                'ajax'
                            );
                        ?>"
                        method="POST"
                    >

                        <div class="row g-2 align-items-end">


                            <!-- TIPO DE REPORTE -->
                            <div class="col-12 col-md-5">

                                <label
                                    for="tipoReporte"
                                    class="form-label small text-muted mb-1"
                                >
                                    Tipo de reporte
                                </label>

                                <select
                                    id="tipoReporte"
                                    name="tipoReporte"
                                    class="form-select form-select-sm"
                                    required
                                >

                                    <option value="seguimiento">
                                        Seguimiento de actividades
                                    </option>

                                    <option value="mortalidad">
                                        Nacidos y muertos por tanque
                                    </option>

                                    <option value="tanques">
                                        Tanques por zoocriadero
                                    </option>

                                </select>

                            </div>


                            <!-- DESDE -->
                            <div class="col-6 col-md-3">

                                <label
                                    for="fechaDesde"
                                    class="form-label small text-muted mb-1"
                                >
                                    Desde
                                </label>

                                <input
                                    type="date"
                                    id="fechaDesde"
                                    name="fechaDesde"
                                    class="form-control form-control-sm"
                                    min="2026-09-10"
                                    max="<?php echo date('Y-m-d'); ?>"
                                    required
                                >

                            </div>


                            <!-- HASTA -->
                            <div class="col-6 col-md-3">

                                <label
                                    for="fechaHasta"
                                    class="form-label small text-muted mb-1"
                                >
                                    Hasta
                                </label>

                                <input
                                    type="date"
                                    id="fechaHasta"
                                    name="fechaHasta"
                                    class="form-control form-control-sm"
                                    min="2026-09-10"
                                    max="<?php echo date('Y-m-d'); ?>"
                                    required
                                >

                            </div>


                            <!-- BOTÓN -->
                            <div class="col-12 col-md-1 d-grid">

                                <button
                                    type="submit"
                                    class="btn btn-primary btn-sm"
                                >

                                    <i class="bi bi-funnel me-1"></i>

                                    Generar

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>


            <!-- ========================================== -->
            <!-- RESULTADOS -->
            <!-- ========================================== -->

            <div id="resultadoReporte"></div>


        </div>

    </div>

</div>


<script>

(function () {

    const formulario =
        document.getElementById(
            "formGenerarReporteZoo"
        );

    const resultado =
        document.getElementById(
            "resultadoReporte"
        );

    const alerta =
        document.getElementById(
            "alertaReporte"
        );


    if (!formulario) {
        return;
    }


    // ======================================================
    // EVITAR RECARGA DE LA PÁGINA
    // ======================================================

    formulario.addEventListener(
        "submit",
        function (event) {

            event.preventDefault();


            const boton =
                formulario.querySelector(
                    'button[type="submit"]'
                );


            if (boton) {
                boton.disabled = true;
            }


            alerta.innerHTML = "";


            // ==================================================
            // ENVIAR AL CONTROLLER
            // ==================================================

            fetch(
                formulario.action,
                {
                    method: "POST",
                    body: new FormData(formulario)
                }
            )

            .then(function (respuesta) {

                return respuesta.text();

            })

            .then(function (texto) {

                let respuesta;


                try {

                    respuesta =
                        JSON.parse(texto);

                } catch (error) {

                    console.log(texto);

                    alerta.innerHTML =
                        '<div class="alert alert-danger">' +
                        'No se pudo interpretar la respuesta del servidor.' +
                        '</div>';

                    return;
                }


                // ==========================================
                // ERROR DEL CONTROLLER
                // ==========================================

                if (!respuesta.ok) {

                    alerta.innerHTML =
                        '<div class="alert alert-danger">' +
                        respuesta.mensaje +
                        '</div>';

                    return;
                }


                // ==========================================
                // GENERAR TABLA
                // ==========================================

                if (
                    respuesta.tipo ===
                    "seguimiento"
                ) {

                    mostrarSeguimiento(
                        respuesta
                    );

                }


                if (
                    respuesta.tipo ===
                    "mortalidad"
                ) {

                    mostrarMortalidad(
                        respuesta
                    );

                }


                if (
                    respuesta.tipo ===
                    "tanques"
                ) {

                    mostrarTanques(
                        respuesta
                    );

                }

            })

            .catch(function (error) {

                console.log(error);

                alerta.innerHTML =
                    '<div class="alert alert-danger">' +
                    'Ocurrió un error al generar el reporte.' +
                    '</div>';

            })

            .finally(function () {

                if (boton) {
                    boton.disabled = false;
                }

            });

        }
    );


    // ======================================================
    // SEGUIMIENTO DE ACTIVIDADES
    // ======================================================

    function mostrarSeguimiento(respuesta) {

        let html = encabezado(
            respuesta.titulo,
            respuesta.tipo
        );


        html += `
            <div class="table-responsive">

                <table class="table table-striped align-middle mb-0">

                    <thead class="table-dark">

                        <tr>

                            <th class="ps-4">
                                Fecha
                            </th>

                            <th>
                                Tipo
                            </th>

                            <th>
                                Tanque
                            </th>

                            <th>
                                Responsable
                            </th>

                            <th>
                                Observaciones
                            </th>

                            <th class="text-center">
                                Estado
                            </th>

                        </tr>

                    </thead>

                    <tbody>
        `;


        if (respuesta.datos.length === 0) {

            html += sinResultados(6);

        } else {

            respuesta.datos.forEach(
                function (fila) {

                    html += `

                        <tr>

                            <td class="ps-4">
                                ${escapar(fila.fecha)}
                            </td>

                            <td>
                                ${escapar(fila.tipo)}
                            </td>

                            <td>
                                ${escapar(fila.tanque)}
                            </td>

                            <td>
                                ${escapar(fila.responsable)}
                            </td>

                            <td>
                                ${escapar(fila.observaciones)}
                            </td>

                            <td class="text-center">
                                ${mostrarEstado(fila.estado)}
                            </td>

                        </tr>

                    `;

                }
            );

        }


        html += `
                    </tbody>

                </table>

            </div>

        </div>
        `;


        resultado.innerHTML =
            html;
    }


    // ======================================================
    // NACIDOS Y MUERTOS
    // ======================================================

    function mostrarMortalidad(respuesta) {

        let html = encabezado(
            respuesta.titulo,
            respuesta.tipo
        );


        html += `
            <div class="table-responsive">

                <table class="table table-striped align-middle mb-0">

                    <thead class="table-dark">

                        <tr>

                            <th class="ps-4">
                                Fecha
                            </th>

                            <th>
                                Zoocriadero
                            </th>

                            <th>
                                Tanque
                            </th>

                            <th>
                                Nacidos
                            </th>

                            <th>
                                Muertos
                            </th>

                            <th>
                                Responsable
                            </th>

                            <th class="text-center">
                                Estado
                            </th>

                        </tr>

                    </thead>

                    <tbody>
        `;


        if (respuesta.datos.length === 0) {

            html += sinResultados(7);

        } else {

            respuesta.datos.forEach(
                function (fila) {

                    html += `

                        <tr>

                            <td class="ps-4">
                                ${escapar(fila.fecha)}
                            </td>

                            <td>
                                ${escapar(fila.zoocriadero)}
                            </td>

                            <td>
                                ${escapar(fila.tanque)}
                            </td>

                            <td>
                                ${escapar(fila.nacidos)}
                            </td>

                            <td>
                                ${escapar(fila.muertos)}
                            </td>

                            <td>
                                ${escapar(fila.responsable)}
                            </td>

                            <td class="text-center">
                                ${mostrarEstado(fila.estado)}
                            </td>

                        </tr>

                    `;

                }
            );

        }


        html += `
                    </tbody>

                </table>

            </div>

        </div>
        `;


        resultado.innerHTML =
            html;
    }


    // ======================================================
    // TANQUES POR ZOOCRIADERO
    // ======================================================

    function mostrarTanques(respuesta) {

        let html = encabezado(
            respuesta.titulo,
            respuesta.tipo
        );


        html += `
            <div class="table-responsive">

                <table class="table table-striped align-middle mb-0">

                    <thead class="table-dark">

                        <tr>

                            <th class="ps-4">
                                Fecha
                            </th>

                            <th>
                                Zoocriadero
                            </th>

                            <th>
                                Tanque
                            </th>

                            <th>
                                Tipo
                            </th>

                            <th>
                                Capacidad
                            </th>

                            <th class="text-center">
                                Estado
                            </th>

                        </tr>

                    </thead>

                    <tbody>
        `;


        if (respuesta.datos.length === 0) {

            html += sinResultados(6);

        } else {

            respuesta.datos.forEach(
                function (fila) {

                    html += `

                        <tr>

                            <td class="ps-4">
                                ${escapar(fila.fecha)}
                            </td>

                            <td>
                                ${escapar(fila.zoocriadero)}
                            </td>

                            <td>
                                ${escapar(fila.tanque)}
                            </td>

                            <td>
                                ${escapar(fila.tipo)}
                            </td>

                            <td>
                                ${escapar(fila.capacidad)} L
                            </td>

                            <td class="text-center">
                                ${mostrarEstado(fila.estado)}
                            </td>

                        </tr>

                    `;

                }
            );

        }


        html += `
                    </tbody>

                </table>

            </div>

        </div>
        `;


        resultado.innerHTML =
            html;
    }


    // ======================================================
    // ENCABEZADO DE RESULTADOS
    // ======================================================

    function urlExportarPdf(tipo) {

        const fechaDesde =
            document.getElementById(
                "fechaDesde"
            ).value;

        const fechaHasta =
            document.getElementById(
                "fechaHasta"
            ).value;

        return "<?php echo getUrl('ReportesZoo', 'ReportesZoo', 'exportarPdf', false, 'ajax'); ?>" +
            "&tipoReporte=" + encodeURIComponent(tipo) +
            "&fechaDesde=" + encodeURIComponent(fechaDesde) +
            "&fechaHasta=" + encodeURIComponent(fechaHasta);
    }


    function encabezado(titulo, tipo) {

        return `

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">

                    <div class="d-flex align-items-center">

                        <i
                            class="bi bi-file-earmark-text
                                   text-primary me-2 fs-5"
                        ></i>

                        <span class="fw-semibold">

                            Resultados:
                            ${escapar(titulo)}

                        </span>

                    </div>

                    <a
                        href="${urlExportarPdf(tipo)}"
                        class="btn btn-outline-danger btn-sm"
                    >
                        <i class="bi bi-file-earmark-pdf me-1"></i>
                        Exportar PDF
                    </a>

                </div>
        `;

    }


    // ======================================================
    // SIN RESULTADOS
    // ======================================================

    function sinResultados(columnas) {

        return `

            <tr>

                <td
                    colspan="${columnas}"
                    class="text-center text-muted py-5"
                >

                    <i
                        class="bi bi-inbox
                               fs-3 d-block mb-2"
                    ></i>

                    No se encontraron registros
                    para la consulta seleccionada.

                </td>

            </tr>
        `;

    }


    // ======================================================
    // ESTADO
    // ======================================================

    function mostrarEstado(estado) {

        if (estado === "A") {

            return `
                <span class="badge bg-success">
                    Activo
                </span>
            `;

        }


        return `
            <span class="badge bg-danger">
                Inactivo
            </span>
        `;

    }


    // ======================================================
    // EVITAR HTML EN LOS DATOS
    // ======================================================

    function escapar(valor) {

        if (
            valor === null ||
            valor === undefined
        ) {

            return "";

        }


        return String(valor)

            .replaceAll(
                "&",
                "&amp;"
            )

            .replaceAll(
                "<",
                "&lt;"
            )

            .replaceAll(
                ">",
                "&gt;"
            )

            .replaceAll(
                '"',
                "&quot;"
            )

            .replaceAll(
                "'",
                "&#039;"
            );

    }

})();

</script>