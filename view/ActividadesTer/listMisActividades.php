<div class="container-fluid py-4 bg-light min-vh-100">
    <div class="row justify-content-center">
        <div class="col-xl-11">

            <!-- Encabezado de la Sección -->
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">
                        <i class="bi bi-clipboard-data text-primary me-2"></i> Actividades de Terreno
                    </h3>
                    <p class="text-muted mb-0">
                        Monitoreo, control y filtrado general de labores en campo.
                    </p>
                </div>
                <div>
                    <span class="badge bg-white text-dark shadow-sm border px-3 py-2 rounded-pill fs-6">
                        <i class="bi bi-calendar-event text-primary me-1"></i> <?php echo date('d M, Y'); ?>
                    </span>
                </div>
            </div>

            <!-- Alertas de Sesión o Errores -->
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm border-0 rounded-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-danger"></i>
                    <div><?php echo htmlspecialchars($_SESSION['error']); ?></div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['exito'])): ?>
                <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm border-0 rounded-4" role="alert">
                    <i class="bi bi-check-circle-fill me-3 fs-4 text-success"></i>
                    <div><?php echo htmlspecialchars($_SESSION['exito']); ?></div>
                </div>
                <?php unset($_SESSION['exito']); ?>
            <?php endif; ?>

            <?php if(!empty($errorFechas)): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm border-0 rounded-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-danger"></i>
                    <div><?php echo htmlspecialchars($errorFechas); ?></div>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                
                <!-- Panel Lateral de Filtros -->
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 20px;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <span class="fw-bold text-dark"><i class="bi bi-sliders me-2 text-primary"></i>Filtros</span>
                            <a href="index.php?modulo=ActividadesTer&controlador=ActividadesTer&funcion=listActTer" class="text-decoration-none small text-muted">Limpiar</a>
                        </div>

                        <form action="index.php" method="GET">
                            <input type="hidden" name="modulo" value="ActividadesTer">
                            <input type="hidden" name="controlador" value="ActividadesTer">
                            <input type="hidden" name="funcion" value="listActTer">

                            <div class="mb-3">
                                <label for="fechaDesde" class="form-label small fw-semibold text-secondary">Fecha Desde</label>
                                <input type="date" id="fechaDesde" name="fechaDesde" class="form-control bg-light border-0 rounded-3 py-2"
                                    max="<?php echo date('Y-m-d'); ?>"
                                    value="<?php echo htmlspecialchars($_GET['fechaDesde'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="fechaHasta" class="form-label small fw-semibold text-secondary">Fecha Hasta</label>
                                <input type="date" id="fechaHasta" name="fechaHasta" class="form-control bg-light border-0 rounded-3 py-2"
                                    max="<?php echo date('Y-m-d'); ?>"
                                    value="<?php echo htmlspecialchars($_GET['fechaHasta'] ?? ''); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="codsitio" class="form-label small fw-semibold text-secondary">Sitio</label>
                                <select id="codsitio" name="codsitio" class="form-select bg-light border-0 rounded-3 py-2">
                                    <option value="">Todos los sitios</option>
                                    <?php if(isset($sitios) && $sitios): ?>
                                        <?php while($sitio = $sitios->fetch(PDO::FETCH_ASSOC)): ?>
                                            <option value="<?php echo $sitio['codsitio']; ?>"
                                                <?php echo (($_GET['codsitio'] ?? '') == $sitio['codsitio']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($sitio['nombresitio']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="codtipoactividad" class="form-label small fw-semibold text-secondary">Tipo de Actividad</label>
                                <select id="codtipoactividad" name="codtipoactividad" class="form-select bg-light border-0 rounded-3 py-2">
                                    <option value="">Todas las actividades</option>
                                    <?php if(isset($tiposActividad) && $tiposActividad): ?>
                                        <?php while($tipo = $tiposActividad->fetch(PDO::FETCH_ASSOC)): ?>
                                            <option value="<?php echo $tipo['codtipoactividad']; ?>"
                                                <?php echo (($_GET['codtipoactividad'] ?? '') == $tipo['codtipoactividad']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($tipo['nombreactividad']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-dark rounded-pill py-2 fw-semibold shadow-sm">
                                    <i class="bi bi-search me-1"></i> Aplicar Filtros
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Contenido Principal / Tabla Estilo Tarjeta Moderna -->
                <div class="col-lg-9">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0">Listado de Registros</h5>
                            <span class="text-muted small">Mostrando resultados actuales</span>
                        </div>

                        <div class="card-body px-0 pb-0">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0">
                                    <thead class="bg-light text-uppercase text-secondary fs-7">
                                        <tr>
                                            <th class="ps-4 py-3">Actividad / Sitio</th>
                                            <th class="py-3">Fecha</th>
                                            <th class="py-3">Observaciones</th>
                                            <th class="text-center py-3 pe-4">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $filas = (isset($actividades) && $actividades)
                                            ? $actividades->fetchAll(PDO::FETCH_ASSOC)
                                            : [];
                                        ?>

                                        <?php if(!empty($filas)): ?>
                                            <?php foreach($filas as $act): ?>
                                                <?php 
                                                    $tipoActividad = $act['tipo_actividad'] ?? $act['nombreactividad'] ?? 'Actividad';
                                                    $nombreSitio   = $act['sitio'] ?? $act['nombresitio'] ?? 'Sitio no asignado';
                                                    $fechaAct      = $act['fecha'] ?? '';
                                                    $observaciones = $act['observaciones'] ?? 'Sin observaciones';
                                                    $estadoAct     = $act['estado'] ?? 'A';
                                                ?>
                                                <tr class="border-bottom border-light">
                                                    <td class="ps-4 py-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-primary-subtle text-primary rounded-3 p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                                                <i class="bi bi-geo-alt fs-5"></i>
                                                            </div>
                                                            <div>
                                                                <span class="fw-bold text-dark d-block"><?php echo htmlspecialchars($tipoActividad); ?></span>
                                                                <span class="text-muted small"><i class="bi bi-pin-map me-1"></i><?php echo htmlspecialchars($nombreSitio); ?></span>
                                                            </div>
                                                        </div>
                                                    </td>

                                                    <td>
                                                        <span class="text-secondary small fw-semibold"><?php echo htmlspecialchars($fechaAct); ?></span>
                                                    </td>

                                                    <td>
                                                        <span class="text-muted small d-inline-block text-truncate" style="max-width: 180px;" title="<?php echo htmlspecialchars($observaciones); ?>">
                                                            <?php echo htmlspecialchars($observaciones); ?>
                                                        </span>
                                                    </td>

                                                    <td class="text-center pe-4">
                                                        <?php if($estadoAct === 'A'): ?>
                                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-3 fw-semibold small">
                                                                <i class="bi bi-dot"></i> Activo
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-3 fw-semibold small">
                                                                <i class="bi bi-dot"></i> Inactivo
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-5">
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
        </div>
    </div>
</div>