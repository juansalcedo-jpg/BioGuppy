<div class="container-fluid py-2">

    <div class="row justify-content-center">

        <div class="col-xl-11">
            <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
                <div>
                    <h4 class="fw-semibold mb-1">Reportes — Terreno</h4>
                    <p class="text-muted small mb-0">Genera y exporta reportes consolidados sobre los sitios y actividades de terreno.</p>
                </div>
            </div>
            <div id="alertaReporte"></div>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body py-3">
                    <form id="formGenerarReporteTer" action="<?php echo getUrl('ReportesTer', 'ReportesTer', 'generar', false, 'ajax'); ?>" method="POST">
                        <div class="row g-2 align-items-end">
                            <div class="col-12 col-md-5">
                                <label for="tipoReporte" class="form-label small text-muted mb-1"></label>
                                <select id="tipoReporte" name="tipoReporte" class="form-select form-select-sm" required>
                                    <option value="sitios">Reporte de sitios</option>
                                    <option value="actividad">Por tipo de actividad</option>
                                    <option value="auxiliar">Por auxiliar</option>
                                    <option value="deposito">Por tipo de depósito</option>
                                </select>
                            </div>

                            <div class="col-6 col-md-3">
                                <label for="fechaDesde" class="form-label small text-muted mb-1">Desde</label>
                                <input type="date" id="fechaDesde" name="fechaDesde" class="form-control form-control-sm" min="2026-09-10" max="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="col-6 col-md-3">
                                <label for="fechaHasta" class="form-label small text-muted mb-1">Hasta</label>
                                <input type="date" id="fechaHasta" name="fechaHasta" class="form-control form-control-sm" min="2026-09-10" max="<?php echo date('Y-m-d'); ?>" required>
                            </div>

                            <div class="col-12 col-md-1 d-grid">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="bi bi-funnel me-1"></i>Generar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div id="resultadoReporte"></div>

        </div>

    </div>

</div>

<script>
(function () {

    const formulario = document.getElementById("formGenerarReporteTer");
    const resultado = document.getElementById("resultadoReporte");
    const alerta = document.getElementById("alertaReporte");

    if (!formulario) {
        return;
    }

    let enviando = false;

    formulario.addEventListener("submit", function (event) {
        event.preventDefault();

        if (enviando) {
            return;
        }

        enviando = true;

        const boton = formulario.querySelector('button[type="submit"]');

        if (boton) {
            boton.disabled = true;
        }

        alerta.innerHTML = "";
        resultado.innerHTML = "";

        fetch(formulario.action, {
            method: "POST",
            body: new FormData(formulario)
        })
        .then(function (respuesta) {
            return respuesta.text();
        })
        .then(function (texto) {
            let respuesta;

            try {
                respuesta = JSON.parse(texto);
            } catch (error) {
                console.log(texto);
                alerta.innerHTML = '<div class="alert alert-danger">No se pudo interpretar la respuesta del servidor.</div>';
                return;
            }

            if (!respuesta.ok) {
                alerta.innerHTML = '<div class="alert alert-danger">' + respuesta.mensaje + '</div>';
                return;
            }

            if (respuesta.tipo === "sitios") {
                mostrarSitios(respuesta);
            }

            if (respuesta.tipo === "actividad") {
                mostrarActividad(respuesta);
            }

            if (respuesta.tipo === "auxiliar") {
                mostrarAuxiliar(respuesta);
            }

            if (respuesta.tipo === "deposito") {
                mostrarDeposito(respuesta);
            }
        })
        .catch(function (error) {
            console.log(error);
            alerta.innerHTML = '<div class="alert alert-danger">Ocurrió un error al generar el reporte.</div>';
        })
        .finally(function () {
            enviando = false;

            if (boton) {
                boton.disabled = false;
            }
        });
    });

    function mostrarSitios(respuesta) {
        let html = encabezado(respuesta.titulo, respuesta.tipo);

        html += `
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">Fecha</th>
                            <th>Sitio</th>
                            <th>Ubicación</th>
                            <th>Responsable</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        if (respuesta.datos.length === 0) {
            html += sinResultados(5);
        } else {
            respuesta.datos.forEach(function (fila) {
                html += `
                    <tr>
                        <td class="ps-4">${escapar(fila.fecha)}</td>
                        <td>${escapar(fila.sitio)}</td>
                        <td>${escapar(fila.ubicacion)}</td>
                        <td>${escapar(fila.responsable)}</td>
                        <td class="text-center">${mostrarEstado(fila.estado)}</td>
                    </tr>
                `;
            });
        }

        html += `
                    </tbody>
                </table>
            </div>
        </div>
        `;

        resultado.innerHTML = html;
    }

    function mostrarActividad(respuesta) {
        let html = encabezado(respuesta.titulo, respuesta.tipo);

        html += `
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">Fecha</th>
                            <th>Tipo de actividad</th>
                            <th>Sitio</th>
                            <th>Responsable</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        if (respuesta.datos.length === 0) {
            html += sinResultados(5);
        } else {
            respuesta.datos.forEach(function (fila) {
                html += `
                    <tr>
                        <td class="ps-4">${escapar(fila.fecha)}</td>
                        <td>${escapar(fila.tipo)}</td>
                        <td>${escapar(fila.sitio)}</td>
                        <td>${escapar(fila.responsable)}</td>
                        <td class="text-center">${mostrarEstado(fila.estado)}</td>
                    </tr>
                `;
            });
        }

        html += `
                    </tbody>
                </table>
            </div>
        </div>
        `;

        resultado.innerHTML = html;
    }

    function mostrarAuxiliar(respuesta) {
        let html = encabezado(respuesta.titulo, respuesta.tipo);

        html += `
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">Fecha</th>
                            <th>Auxiliar</th>
                            <th>Sitio</th>
                            <th>Tipo de actividad</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        if (respuesta.datos.length === 0) {
            html += sinResultados(5);
        } else {
            respuesta.datos.forEach(function (fila) {
                html += `
                    <tr>
                        <td class="ps-4">${escapar(fila.fecha)}</td>
                        <td>${escapar(fila.auxiliar)}</td>
                        <td>${escapar(fila.sitio)}</td>
                        <td>${escapar(fila.tipo)}</td>
                        <td class="text-center">${mostrarEstado(fila.estado)}</td>
                    </tr>
                `;
            });
        }

        html += `
                    </tbody>
                </table>
            </div>
        </div>
        `;

        resultado.innerHTML = html;
    }

    function mostrarDeposito(respuesta) {
        let html = encabezado(respuesta.titulo, respuesta.tipo);

        html += `
            <div class="table-responsive">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">Fecha</th>
                            <th>Sitio</th>
                            <th>Tipo de depósito</th>
                            <th>Cantidad</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        if (respuesta.datos.length === 0) {
            html += sinResultados(5);
        } else {
            respuesta.datos.forEach(function (fila) {
                html += `
                    <tr>
                        <td class="ps-4">${escapar(fila.fecha)}</td>
                        <td>${escapar(fila.sitio)}</td>
                        <td>${escapar(fila.tipo)}</td>
                        <td>${escapar(fila.cantidad)}</td>
                        <td class="text-center">${mostrarEstado(fila.estado)}</td>
                    </tr>
                `;
            });
        }

        html += `
                    </tbody>
                </table>
            </div>
        </div>
        `;

        resultado.innerHTML = html;
    }

    function urlExportarPdf(tipo) {
        const fechaDesde = document.getElementById("fechaDesde").value;
        const fechaHasta = document.getElementById("fechaHasta").value;

        return "<?php echo getUrl('ReportesTer', 'ReportesTer', 'exportarPdf', false, 'ajax'); ?>" +
            "&tipoReporte=" + encodeURIComponent(tipo) +
            "&fechaDesde=" + encodeURIComponent(fechaDesde) +
            "&fechaHasta=" + encodeURIComponent(fechaHasta);
    }

    function encabezado(titulo, tipo) {
        return `
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-text text-primary me-2 fs-5"></i>
                        <span class="fw-semibold">Resultados: ${escapar(titulo)}</span>
                    </div>
                    <a href="${urlExportarPdf(tipo)}" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-file-earmark-pdf me-1"></i>
                        Exportar PDF
                    </a>
                </div>
        `;
    }

    function sinResultados(columnas) {
        return `
            <tr>
                <td colspan="${columnas}" class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    No se encontraron registros para la consulta seleccionada.
                </td>
            </tr>
        `;
    }

    function mostrarEstado(estado) {
        if (estado === "A") {
            return `<span class="badge bg-success">Activo</span>`;
        }

        return `<span class="badge bg-danger">Inactivo</span>`;
    }

    function escapar(valor) {
        if (valor === null || valor === undefined) {
            return "";
        }

        return String(valor)
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }

})();
</script>