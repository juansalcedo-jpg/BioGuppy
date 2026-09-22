<div id="sitioFormRegistro">
    <div class="container-fluid py-2">
        <div class="row justify-content-center">
            <div class="col-xl-11">

                <form action="<?php echo getUrl('Sitios','Sitios','postCreateSit')?>" method="post" novalidate>

                    <div class="row g-3">
                        <!-- Nombre del Sitio -->
                        <div class="col-md-12">
                            <label for="nombresitio" class="form-label text-dark fw-bold small mb-1">Nombre del Sitio <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-signpost fs-5"></i></span>
                                <input type="text" class="form-control border-0 bg-transparent py-2 fs-6 shadow-none" id="nombresitio" name="nombresitio"
                                       maxlength="80" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                                       title="Solo letras y espacios (sin números ni símbolos)"
                                       placeholder="Ej: Fuente Santa Mónica" required>
                            </div>
                            <div class="form-text text-muted small ms-1 mt-1">Solo letras y espacios (máximo 80 caracteres).</div>
                        </div>

                        <!-- Comuna -->
                        <div class="col-md-6">
                            <label for="codcomuna" class="form-label text-dark fw-bold small mb-1">Comuna <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-map fs-5"></i></span>
                                <select class="form-select border-0 bg-transparent py-2 fs-6 shadow-none" id="codcomuna" name="codcomuna" required>
                                    <option value="" selected disabled>Seleccione una comuna...</option>
                                    <?php foreach ($comunas as $comuna): ?>
                                        <option value="<?php echo $comuna['id']; ?>"><?php echo htmlspecialchars($comuna['nombrecomuna']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Barrio -->
                        <div class="col-md-6">
                            <label for="codbarrio" class="form-label text-dark fw-bold small mb-1">Barrio <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-light shadow-xs" id="groupBarrio">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-geo fs-5"></i></span>
                                <select class="form-select border-0 bg-transparent py-2 fs-6 shadow-none text-muted" id="codbarrio" name="codbarrio" disabled required>
                                    <option value="" selected disabled>Primero seleccione una comuna...</option>
                                    <?php foreach ($barrios as $barrio): ?>
                                        <option value="<?php echo $barrio['id']; ?>" data-comuna="<?php echo $barrio['codcomuna']; ?>" hidden disabled><?php echo htmlspecialchars($barrio['nombrebarrio']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-text text-muted small ms-1 mt-1">Se habilitará al seleccionar una comuna.</div>
                        </div>

                        <!-- Tipo de depósito -->
                        <div class="col-md-6">
                            <label for="codtipodeposito" class="form-label text-dark fw-bold small mb-1">Tipo de depósito <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-box-seam fs-5"></i></span>
                                <select class="form-select border-0 bg-transparent py-2 fs-6 shadow-none" id="codtipodeposito" name="codtipodeposito" required>
                                    <option value="" selected disabled>Seleccione un tipo...</option>
                                    <?php foreach ($tiposDeposito as $tipo): ?>
                                        <option value="<?php echo $tipo['id']; ?>"><?php echo htmlspecialchars($tipo['nombretipodeposito']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="col-md-6">
                            <label for="direccion" class="form-label text-dark fw-bold small mb-1">Dirección <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-geo-alt fs-5"></i></span>
                                <input type="text" class="form-control border-0 bg-transparent py-2 fs-6 shadow-none input-direccion" id="direccion" name="direccion" autocomplete="off" maxlength="100"
                                       data-url="<?php echo getUrl('Sitios', 'Sitios', 'buscarDireccion', false, 'ajax'); ?>"
                                       data-sugerencias="sugerenciasDireccionRegistro" data-error="errorDireccionRegistro"
                                       placeholder="Ej: Calle 5 # 36-05" required>
                            </div>
                            <div class="list-group shadow-sm rounded-3 mt-1 d-none sugerencias-direccion" id="sugerenciasDireccionRegistro" style="max-height: 240px; overflow-y: auto;"></div>
                            <div class="text-danger small ms-1 mt-1 d-none" id="errorDireccionRegistro"></div>
                            <div class="form-text text-muted small ms-1 mt-1">Escribe la vía (ej: Calle 5), elige una opción y completa la placa: Calle 5 # 36-05.</div>
                        </div>

                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-light px-4 py-2 rounded-3 text-secondary fw-semibold border" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm fw-semibold">
                            <i class="bi bi-check-lg me-1"></i> Registrar
                        </button>
                    </div>

                </form>

                <!-- Script dinámico para habilitar y filtrar barrios -->
                <script>
                    (function () {
                        var comboComuna = document.getElementById('codcomuna');
                        var comboBarrio = document.getElementById('codbarrio');
                        var groupBarrio = document.getElementById('groupBarrio');
                        if (!comboComuna || !comboBarrio) return;

                        var opcionesBarrio = Array.prototype.slice.call(comboBarrio.options);

                        comboComuna.addEventListener('change', function () {
                            var comunaSeleccionada = this.value;
                            comboBarrio.value = '';
                            comboBarrio.disabled = false;
                            
                            // Cambiar estilo visual del contenedor al habilitarse
                            if (groupBarrio) {
                                groupBarrio.classList.remove('bg-light', 'text-muted');
                                groupBarrio.classList.add('bg-white');
                            }
                            comboBarrio.classList.remove('text-muted');

                            opcionesBarrio.forEach(function (opcion) {
                                if (!opcion.value) return; // Mantener opción por defecto oculta en filtro
                                var coincide = opcion.dataset.comuna === comunaSeleccionada;
                                opcion.hidden = !coincide;
                                opcion.disabled = !coincide;
                            });
                        });
                    })();
                </script>

            </div>
        </div>
    </div>

    <!-- Alertas de error en sesión -->
    <?php if(isset($_SESSION['error'])): ?>
    <div class="row justify-content-center px-3">
        <div class="col-xl-11">
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mt-3 mb-0 border-0 shadow-sm rounded-3 bg-danger-subtle text-danger-emphasis" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                    <div><?php echo htmlspecialchars($_SESSION['error']); ?></div>
                </div>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</div>

<style>
    /* Efecto al hacer foco en los contenedores con bordes estilizados */
    .input-group:focus-within {
        border-color: var(--bs-primary) !important;
        box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15);
    }
</style>