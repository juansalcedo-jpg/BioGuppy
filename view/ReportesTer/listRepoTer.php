<div class="container-fluid px-4 py-4">

    <!-- Encabezado de la Sección -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 p-4 bg-white rounded-4 shadow-xs border-0">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4">
                        <i class="bi bi-geo-alt fs-3"></i>
                    </div>
                    <div>
                        
                        <h3 class="fw-bold text-dark mb-0 mt-1">Reportes — Terreno</h3>
                        <p class="text-muted small mb-0">Genera y exporta reportes de inspecciones, sitios y actividades de terreno registradas.</p>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill fw-semibold" onclick="location.reload();">
                        <i class="bi bi-arrow-clockwise me-1"></i> Restablecer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas del Sistema -->
    <div id="alertaReporte"></div>

    <!-- Tarjeta con el formulario de generación de reportes -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
        <div class="card-header bg-white border-bottom py-3 px-4">
            <div class="d-flex align-items-center gap-2">
                <div class="bg-light p-2 rounded-3 text-secondary">
                    <i class="bi bi-sliders fs-5"></i>
                </div>
                <div>
                    <h6 class="fw-bold text-dark mb-0">Parámetros de Filtrado</h6>
                    <span class="text-muted fs-7">Define los criterios para procesar las inspecciones de terreno</span>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form id="formGenerarReporteTerreno" action="<?php echo getUrl('ReportesTer', 'ReportesTer', 'generar', false, 'ajax'); ?>">
                <div class="row g-4 align-items-end">

                    <!-- Tipo de Reporte (Terreno) -->
                    <div class="col-12 col-xl-5">
                        <label for="tipoReporte" class="form-label text-secondary fw-bold fs-7 text-uppercase mb-2">Tipo de reporte</label>
                        <div class="input-group input-group-md rounded-3 overflow-hidden border bg-light shadow-xs">
                            <span class="input-group-text bg-transparent border-0 text-primary ps-3"><i class="bi bi-clipboard-data"></i></span>
                            <select id="tipoReporte" name="tipoReporte" class="form-select border-0 bg-transparent py-2.5 shadow-none cursor-pointer" required>
                                <option value="" selected disabled>Seleccione una opción de reporte...</option>
                                <option value="inspecciones">Inspecciones de terreno</option>
                                <option value="sitios">Estado de sitios y áreas</option>
                                <option value="actividades">Actividades de campo</option>
                            </select>
                        </div>
                    </div>

                    <!-- Fecha Desde -->
                    <div class="col-12 col-md-6 col-xl-3">
                        <label for="fechaDesde" class="form-label text-secondary fw-bold fs-7 text-uppercase mb-2">Fecha Desde</label>
                        <div class="input-group input-group-md rounded-3 overflow-hidden border bg-light shadow-xs">
                            <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-calendar-event"></i></span>
                            <input type="date" id="fechaDesde" name="fechaDesde" class="form-control border-0 bg-transparent py-2.5 shadow-none" min="2026-09-10" max="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <!-- Fecha Hasta -->
                    <div class="col-12 col-md-6 col-xl-2">
                        <label for="fechaHasta" class="form-label text-secondary fw-bold fs-7 text-uppercase mb-2">Fecha Hasta</label>
                        <div class="input-group input-group-md rounded-3 overflow-hidden border bg-light shadow-xs">
                            <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-calendar-check"></i></span>
                            <input type="date" id="fechaHasta" name="fechaHasta" class="form-control border-0 bg-transparent py-2.5 shadow-none" min="2026-09-10" max="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                    </div>

                    <!-- Botón Generar -->
                    <div class="col-12 col-xl-2 d-grid">
                        <button type="submit" class="btn btn-primary btn-md py-2.5 rounded-3 shadow-sm fw-semibold d-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-funnel-fill"></i> Generar
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Contenedor dinámico de resultados -->
    <div id="resultadoReporte"></div>

</div>

<script>
(function () {

    const formulario = document.getElementById("formGenerarReporteTerreno");
    const resultado = document.getElementById("resultadoReporte");
    const alerta = document.getElementById("alertaReporte");
    const inputDesde = document.getElementById("fechaDesde");
    const inputHasta = document.getElementById("fechaHasta");

    if (!formulario) {
        return;
    }

    // Sincronización inteligente de fechas
    inputDesde.addEventListener("change", function () {
        inputHasta.min = inputDesde.value;
        if (inputHasta.value && inputHasta.value < inputDesde.value) {
            inputHasta.value = inputDesde.value;
        }
    });

    inputHasta.addEventListener("change", function () {
        inputDesde.max = inputHasta.value;
        if (inputDesde.value && inputDesde.value > inputHasta.value) {
            inputDesde.value = inputHasta.value;
        }
    });

    let enviando = false;

    formulario.addEventListener("submit", function (event) {
        event.preventDefault();

        if (inputDesde.value > inputHasta.value) {
            alerta.innerHTML = `
                <div class="alert alert-warning alert-dismissible fade show py-3 px-4 mb-4 border-0 shadow-sm rounded-4 bg-warning-subtle text-warning-emphasis" role="alert">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                        <div><strong>Atención:</strong> La fecha "Desde" no puede ser posterior a la fecha "Hasta".</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            return;
        }

        if (enviando) {
            return;
        }

        enviando = true;
        const boton = formulario.querySelector('button[type="submit"]');

        if (boton) {
            boton.disabled = true;
            boton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Procesando...`;
        }

        alerta.innerHTML = "";
        resultado.innerHTML = `
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center bg-white">
                <div class="py-4">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 2.5rem; height: 2.5rem;"></div>
                    <h6 class="fw-bold text-dark">Cargando reporte...</h6>
                    <p class="text-muted small mb-0">Consultando la información de terreno solicitada.</p>
                </div>
            </div>
        `;

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
                alerta.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show py-3 px-4 mb-4 border-0 shadow-sm rounded-4 bg-danger-subtle text-danger-emphasis" role="alert">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                            <div><strong>Error:</strong> No se pudo interpretar la respuesta del servidor.</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
                resultado.innerHTML = "";
                return;
            }

            if (!respuesta.ok) {
                alerta.innerHTML = `
                    <div class="alert alert-danger alert-dismissible fade show py-3 px-4 mb-4 border-0 shadow-sm rounded-4 bg-danger-subtle text-danger-emphasis" role="alert">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-exclamation-circle-fill fs-4"></i>
                            <div><strong>Aviso:</strong> ${escapar(respuesta.mensaje)}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
                resultado.innerHTML = "";
                return;
            }

            if (respuesta.tipo === "inspecciones") mostrarInspecciones(respuesta);
            else if (respuesta.tipo === "sitios") mostrarSitios(respuesta);
            else if (respuesta.tipo === "actividades") mostrarActividades(respuesta);
            else {
                alerta.innerHTML = `
                    <div class="alert alert-warning alert-dismissible fade show py-3 px-4 mb-4 border-0 shadow-sm rounded-4 bg-warning-subtle text-warning-emphasis" role="alert">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                            <div><strong>Aviso:</strong> Tipo de reporte no reconocido.</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`;
                resultado.innerHTML = "";
            }
        })
        .catch(function (error) {
            console.log(error);
            alerta.innerHTML = `
                <div class="alert alert-danger alert-dismissible fade show py-3 px-4 mb-4 border-0 shadow-sm rounded-4 bg-danger-subtle text-danger-emphasis" role="alert">
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-wifi-off fs-4"></i>
                        <div><strong>Error de red:</strong> Ocurrió un problema al conectar con el servidor.</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            resultado.innerHTML = "";
        })
        .finally(function () {
            enviando = false;
            if (boton) {
                boton.disabled = false;
                boton.innerHTML = `<i class="bi bi-funnel-fill me-1"></i> Generar`;
            }
        });
    });

    function mostrarInspecciones(respuesta) {
        let datos = respuesta.datos || [];
        let html = encabezado(respuesta.titulo, respuesta.tipo, datos.length);
        html += `
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary fw-bold">
                        <tr>
                            <th class="ps-4 py-3">Fecha</th>
                            <th class="py-3">Sitio / Área</th>
                            <th class="py-3">Inspector</th>
                            <th class="py-3">Observaciones</th>
                            <th class="text-center py-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        if (datos.length === 0) {
            html += sinResultados(5);
        } else {
            datos.forEach(function (fila) {
                html += `
                    <tr>
                        <td class="ps-4 py-3 text-muted">${escapar(fila.fecha)}</td>
                        <td class="py-3 fw-semibold text-dark">${escapar(fila.sitio)}</td>
                        <td class="py-3 text-muted">${escapar(fila.inspector)}</td>
                        <td class="py-3 text-muted small">${escapar(fila.observaciones)}</td>
                        <td class="text-center py-3">${mostrarEstado(fila.estado)}</td>
                    </tr>
                `;
            });
        }

        html += `</tbody></table></div></div>`;
        resultado.innerHTML = html;
    }

    function mostrarSitios(respuesta) {
        let datos = respuesta.datos || [];
        let html = encabezado(respuesta.titulo, respuesta.tipo, datos.length);
        html += `
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary fw-bold">
                        <tr>
                            <th class="ps-4 py-3">Fecha Registro</th>
                            <th class="py-3">Nombre del Sitio</th>
                            <th class="py-3">Ubicación</th>
                            <th class="py-3">Capacidad / Límite</th>
                            <th class="text-center py-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        if (datos.length === 0) {
            html += sinResultados(5);
        } else {
            datos.forEach(function (fila) {
                html += `
                    <tr>
                        <td class="ps-4 py-3 text-muted">${escapar(fila.fecha)}</td>
                        <td class="py-3 fw-semibold text-dark">${escapar(fila.nombre_sitio)}</td>
                        <td class="py-3 text-muted">${escapar(fila.ubicacion)}</td>
                        <td class="py-3 text-muted">${escapar(fila.capacidad)}</td>
                        <td class="text-center py-3">${mostrarEstado(fila.estado)}</td>
                    </tr>
                `;
            });
        }

        html += `</tbody></table></div></div>`;
        resultado.innerHTML = html;
    }

    function mostrarActividades(respuesta) {
        let datos = respuesta.datos || [];
        let html = encabezado(respuesta.titulo, respuesta.tipo, datos.length);
        html += `
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light text-uppercase fs-7 text-secondary fw-bold">
                        <tr>
                            <th class="ps-4 py-3">Fecha</th>
                            <th class="py-3">Actividad</th>
                            <th class="py-3">Sitio</th>
                            <th class="py-3">Responsable</th>
                            <th class="text-center py-3">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        if (datos.length === 0) {
            html += sinResultados(5);
        } else {
            datos.forEach(function (fila) {
                html += `
                    <tr>
                        <td class="ps-4 py-3 text-muted">${escapar(fila.fecha)}</td>
                        <td class="py-3 fw-semibold text-dark">${escapar(fila.actividad)}</td>
                        <td class="py-3 text-muted">${escapar(fila.sitio)}</td>
                        <td class="py-3 text-muted">${escapar(fila.responsable)}</td>
                        <td class="text-center py-3">${mostrarEstado(fila.estado)}</td>
                    </tr>
                `;
            });
        }

        html += `</tbody></table></div></div>`;
        resultado.innerHTML = html;
    }

    function urlExportarPdf(tipo) {
        const fechaDesde = inputDesde.value;
        const fechaHasta = inputHasta.value;

        return "<?php echo getUrl('ReportesTer', 'ReportesTer', 'exportarPdf', false, 'ajax'); ?>" +
            "&tipoReporte=" + encodeURIComponent(tipo) +
            "&fechaDesde=" + encodeURIComponent(fechaDesde) +
            "&fechaHasta=" + encodeURIComponent(fechaHasta);
    }

    function encabezado(titulo, tipo, totalRegistros) {
        return `
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 bg-white">
                <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                        <div>
                            <span class="fw-semibold text-secondary">Resultados: ${escapar(titulo)}</span>
                            <span class="text-muted fs-7 d-block">${totalRegistros} registro(s) encontrado(s)</span>
                        </div>
                    </div>
                    <a href="${urlExportarPdf(tipo)}" target="_blank" class="btn btn-outline-danger btn-sm py-2 px-3 rounded-3 shadow-sm d-flex align-items-center gap-2 fw-semibold">
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
                    <div class="my-3">
                        <i class="bi bi-inbox fs-1 text-muted opacity-50 d-block mb-2"></i>
                        <span class="fs-6">No se encontraron registros para la consulta seleccionada.</span>
                    </div>
                </td>
            </tr>
        `;
    }

    function mostrarEstado(estado) {
        if (estado === "A" || estado === "Activo" || estado === 1) {
            return `<span class="badge bg-success-subtle text-success-emphasis px-3 py-1 rounded-pill fw-semibold">Activo</span>`;
        }
        return `<span class="badge bg-danger-subtle text-danger-emphasis px-3 py-1 rounded-pill fw-semibold">Inactivo</span>`;
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

<style>
    .fs-7 {
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }
    .shadow-xs {
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.03)!important;
    }
    .input-group:focus-within {
        border-color: var(--bs-primary) !important;
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15);
        background-color: #fff !important;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>