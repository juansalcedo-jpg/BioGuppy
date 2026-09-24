<div class="container-fluid py-4 bg-light min-vh-100">
    <div class="row justify-content-center">
        <div class="col-xl-12">

            <!-- Alertas del Sistema (Sesiones) -->
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm border-0 rounded-4">
                    <i class="bi bi-exclamation-triangle-fill me-3 fs-4 text-danger"></i>
                    <div><?php echo htmlspecialchars($_SESSION['error']); ?></div>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['exito'])): ?>
                <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm border-0 rounded-4">
                    <i class="bi bi-check-circle-fill me-3 fs-4 text-success"></i>
                    <div><?php echo htmlspecialchars($_SESSION['exito']); ?></div>
                </div>
                <?php unset($_SESSION['exito']); ?>
            <?php endif; ?>

            <!-- Encabezado de la Sección -->
            <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
                <div>
                    <h3 class="fw-bold text-dark mb-1">
                        <i class="bi bi-file-earmark-text text-primary me-2"></i> Mis actividades — Zoocriadero
                    </h3>
                    <p class="text-muted mb-0">
                        Consulta y filtra las actividades de zoocriadero que has registrado.
                    </p>
                </div>
            </div>

            <div class="row g-4">
                
                <!-- Panel Lateral de Filtros -->
                <div class="col-lg-3">
                    <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 20px;">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                            <span class="fw-bold text-dark"><i class="bi bi-sliders me-2 text-primary"></i>Filtros</span>
                            <a href="#" onclick="location.reload(); return false;" class="text-decoration-none small text-muted">Limpiar</a>
                        </div>

                        <form id="formFiltroMisActividadesZoo" action="<?php echo getUrl('ActividadesListZoo', 'ActividadesListZoo', 'filtro', false, 'ajax'); ?>" method="POST">
                            
                            <div class="mb-3">
                                <label for="mesFiltro" class="form-label small fw-semibold text-secondary">Mes</label>
                                <input type="month" id="mesFiltro" name="mes" class="form-control bg-light border-0 rounded-3 py-2">
                            </div>

                            <div class="mb-3">
                                <label for="selectZoocriadero" class="form-label small fw-semibold text-secondary">Zoocriadero</label>
                                <select id="selectZoocriadero" name="codzoocriadero" class="form-select bg-light border-0 rounded-3 py-2">
                                    <option value="">Todos los zoocriaderos</option>
                                    <?php if (isset($zoocriaderos) &&$zoocriaderos): ?>
                                        <?php while ($z =$zoocriaderos->fetch(PDO::FETCH_ASSOC)): ?>
                                            <option value="<?php echo $z['id']; ?>"><?php echo htmlspecialchars($z['nombrezoocriadero']); ?></option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="mb-4">
                                <label for="selectTipoActividad" class="form-label small fw-semibold text-secondary">Tipo de actividad</label>
                                <select id="selectTipoActividad" name="tipoactividad" class="form-select bg-light border-0 rounded-3 py-2">
                                    <option value="">Todos los tipos</option>
                                    <option value="ALIMENTACIÓN">Alimentación</option>
                                    <option value="RECOLECCIÓN">Nacidos / Muertos</option>
                                    <option value="LIMPIEZA">Limpieza</option>
                                    <option value="AJUSTE DE NIVEL">Ajuste de nivel</option>
                                    <option value="LAVADO">Lavado</option>
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-dark rounded-pill py-2 fw-semibold shadow-sm">
                                    <i class="bi bi-funnel-fill me-1"></i> Filtrar
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

                <!-- Contenido Principal / Tabla Estilo Tarjeta Moderna -->
                <div class="col-lg-9">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                        
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-dark mb-0">Actividades Registradas</h5>
                            <span class="text-muted small">Mostrando resultados actuales</span>
                        </div>

                        <div class="card-body px-0 pb-0">
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0" id="tablaMisActividadesZoo">
                                    <thead class="bg-light text-uppercase text-secondary fs-7">
                                        <tr>
                                            <th class="ps-4 py-3">Fecha</th>
                                            <th class="py-3">Tipo de actividad</th>
                                            <th class="py-3">Zoocriadero</th>
                                            <th class="py-3">Tanque</th>
                                            <th class="text-center py-3">Estado</th>
                                            <th class="text-center py-3">Editar</th>
                                            <th class="text-center py-3 pe-4">Inhabilitar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php include __DIR__ . '/filaMisActividadesZoo.php'; ?>
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

<script>
(function () {
    var formFiltroZoo = document.getElementById('formFiltroMisActividadesZoo');
    if (!formFiltroZoo) return;

    formFiltroZoo.addEventListener('submit', function (evento) {
        evento.preventDefault();

        var datos = new FormData(formFiltroZoo);
        var tbody = document.querySelector('#tablaMisActividadesZoo tbody');
        var boton = formFiltroZoo.querySelector('button[type="submit"]');

        // Estado de carga en el botón
        if (boton) {
            boton.disabled = true;
            boton.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Filtrando...`;
        }

        // Indicador visual en la tabla
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-5">
                    <div class="my-3">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 2rem; height: 2rem;"></div>
                        <h6 class="fw-bold text-dark">Buscando actividades...</h6>
                    </div>
                </td>
            </tr>
        `;

        fetch(formFiltroZoo.action, { 
            method: 'POST', 
            body: datos 
        })
        .then(function (respuesta) { 
            return respuesta.text(); 
        })
        .then(function (html) {
            tbody.innerHTML = html;
        })
        .catch(function () {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center text-danger py-5">
                        <i class="bi bi-wifi-off fs-2 text-danger mb-2 d-block"></i>
                        <strong>Ocurrió un error al filtrar.</strong> Intenta nuevamente.
                    </td>
                </tr>
            `;
        })
        .finally(function () {
            if (boton) {
                boton.disabled = false;
                boton.innerHTML = `<i class="bi bi-funnel-fill me-1"></i> Filtrar`;
            }
        });
    });
})();
</script>

<?php include_once __DIR__ . '/../partials/modalFormulario.php'; ?>

<style>
    .fs-7 {
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }
</style>