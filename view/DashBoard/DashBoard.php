<div class="container-fluid py-2">
    <div class="row justify-content-center">
        <div class="col-xl-11">

            <!-- ENCABEZADO DE LA VISTA -->
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
                <div>
                    <h4 class="fw-semibold mb-1">Dashboard Consolidado</h4>
                    <p class="text-muted small mb-0">Resumen y métricas del sistema.</p>
                </div>
            </div>

            <!-- 1. TARJETAS DE INDICADORES (KPIs) -->
            <div class="row g-3 mb-4">
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 py-2">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-3 p-3 bg-primary bg-opacity-10 text-primary me-3">
                                <i class="bi bi-layers fs-3"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($kpis['tanques_activos'] ?? 0); ?></h3>
                                <span class="text-muted small">Tanques activos</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 py-2">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-3 p-3 bg-success bg-opacity-10 text-success me-3">
                                <i class="bi bi-geo-alt fs-3"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($kpis['sitios_visitados'] ?? 0); ?></h3>
                                <span class="text-muted small">Sitios visitados</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 py-2">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-3 p-3 bg-info bg-opacity-10 text-info me-3">
                                <i class="bi bi-activity fs-3"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($kpis['actividades_mes'] ?? 0); ?></h3>
                                <span class="text-muted small">Actividades del mes</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100 py-2">
                        <div class="card-body d-flex align-items-center">
                            <div class="rounded-3 p-3 bg-warning bg-opacity-10 text-warning me-3">
                                <i class="bi bi-exclamation-triangle fs-3"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($kpis['focos_larvas'] ?? 0); ?></h3>
                                <span class="text-muted small">Focos con larvas</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. SECCIÓN DE GRÁFICAS PROCESADAS POR PHP -->
            <div class="row g-4 mb-4">

                <!-- Contenedor para Gráfica de Barras (Producción) -->
                <div class="col-12 col-lg-8">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 pt-3 pb-0">
                            <h6 class="fw-semibold mb-0 text-dark d-flex align-items-center">
                                <i class="bi bi-bar-chart-line text-primary me-2"></i>Producción mensual de guppies
                            </h6>
                        </div>
                        <div class="card-body text-center d-flex align-items-center justify-content-center p-2">
                            <!-- La imagen llama a la ruta PHP del controlador/método que renderiza la gráfica JPGraph -->
                            <img src="<?php echo getUrl('Dashboard', 'Dashboard', 'graficaBarras', false, 'ajax'); ?>"
                                alt="Gráfica de Producción Mensual"
                                class="img-fluid rounded">
                        </div>
                    </div>
                </div>

                <!-- Contenedor para Gráfica Circular (Tipos de Actividad) -->
                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-header bg-white border-0 pt-3 pb-0">
                            <h6 class="fw-semibold mb-0 text-dark d-flex align-items-center">
                                <i class="bi bi-pie-chart text-primary me-2"></i>Por tipo de actividad
                            </h6>
                        </div>
                        <div class="card-body text-center d-flex align-items-center justify-content-center p-2">
                            <!-- La imagen llama a la ruta PHP del gráfico de pastel -->
                            <img src="<?php echo getUrl('Dashboard', 'Dashboard', 'graficaPastel', false, 'ajax'); ?>"
                                alt="Gráfica Por Tipo de Actividad"
                                class="img-fluid rounded">
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>