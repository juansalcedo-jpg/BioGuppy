<div id="sitioFormEdicion">
    <div class="container-fluid py-2">
        <div class="row justify-content-center">
            <div class="col-xl-11">

                <form action="<?php echo getUrl('Sitios','Sitios','postUpdateSit')?>" method="post" novalidate>
                    <input type="hidden" name="codsitio" value="<?php echo $sitio['codsitio']; ?>">

                    <div class="row g-3">
                        <!-- Nombre del Sitio -->
                        <div class="col-md-12">
                            <label for="nombresitio" class="form-label text-dark fw-bold small mb-1">Nombre del Sitio <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-signpost fs-5"></i></span>
                                <input type="text" class="form-control border-0 bg-transparent py-2 fs-6 shadow-none" id="nombresitio" name="nombresitio"
                                       maxlength="80" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+"
                                       title="Solo letras y espacios (sin números ni símbolos)"
                                       value="<?php echo htmlspecialchars($sitio['nombresitio']); ?>" required>
                            </div>
                            <div class="form-text text-muted small ms-1 mt-1">Solo letras y espacios (máximo 80 caracteres).</div>
                        </div>

                        <!-- Comuna -->
                        <div class="col-md-6">
                            <label for="codcomuna" class="form-label text-dark fw-bold small mb-1">Comuna <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-map fs-5"></i></span>
                                <select class="form-select border-0 bg-transparent py-2 fs-6 shadow-none" id="codcomuna" name="codcomuna" required>
                                    <option value="" disabled>Seleccione una comuna...</option>
                                    <?php foreach ($comunas as $comuna): ?>
                                        <option value="<?php echo $comuna['id']; ?>" <?php echo ($comuna['id'] == $sitio['codcomuna']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($comuna['nombrecomuna']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Barrio -->
                        <div class="col-md-6">
                            <label for="codbarrio" class="form-label text-dark fw-bold small mb-1">Barrio <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-geo fs-5"></i></span>
                                <select class="form-select border-0 bg-transparent py-2 fs-6 shadow-none" id="codbarrio" name="codbarrio" required>
                                    <option value="" disabled>Seleccione un barrio...</option>
                                    <?php foreach ($barrios as $barrio): ?>
                                        <?php
                                            $barrioSeleccionado = $barrio['id'] == $sitio['codbarrio'];
                                            $mismaComuna = $barrio['codcomuna'] == $sitio['codcomuna'];
                                        ?>
                                        <option value="<?php echo $barrio['id']; ?>"
                                                data-comuna="<?php echo $barrio['codcomuna']; ?>"
                                                <?php echo $barrioSeleccionado ? 'selected' : ''; ?>
                                                <?php echo !$mismaComuna ? 'hidden disabled' : ''; ?>>
                                            <?php echo htmlspecialchars($barrio['nombrebarrio']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Tipo de depósito -->
                        <div class="col-md-6">
                            <label for="codtipodeposito" class="form-label text-dark fw-bold small mb-1">Tipo de depósito <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-box-seam fs-5"></i></span>
                                <select class="form-select border-0 bg-transparent py-2 fs-6 shadow-none" id="codtipodeposito" name="codtipodeposito" required>
                                    <option value="" disabled>Seleccione un tipo...</option>
                                    <?php foreach ($tiposDeposito as $tipo): ?>
                                        <option value="<?php echo $tipo['id']; ?>" <?php echo ($tipo['id'] == $sitio['codtipodeposito']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($tipo['nombretipodeposito']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Dirección -->
                        <div class="col-md-6">
                            <label for="direccion" class="form-label text-dark fw-bold small mb-1">Dirección <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-geo-alt fs-5"></i></span>
                                <input type="text" class="form-control border-0 bg-transparent py-2 fs-6 shadow-none" id="direccion" name="direccion"
                                       pattern="^(Calle|Carrera|Avenida)\b.*"
                                       title="Debe iniciar con Calle, Carrera o Avenida"
                                       value="<?php echo htmlspecialchars($sitio['direccion']); ?>" required>
                            </div>
                            <div class="form-text text-muted small ms-1 mt-1">Debe iniciar con Calle, Carrera o Avenida.</div>
                        </div>

                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-light px-4 py-2 rounded-3 text-secondary fw-semibold border" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm fw-semibold">
                            <i class="bi bi-check-lg me-1"></i> Guardar cambios
                        </button>
                    </div>

                </form>

                <!-- Script para actualizar los barrios según la comuna seleccionada -->
                <script>
                    (function () {
                        var selectComuna = document.getElementById('codcomuna');
                        var selectBarrio = document.getElementById('codbarrio');
                        if (!selectComuna || !selectBarrio) return;

                        selectComuna.addEventListener('change', function () {
                            var comunaId = this.value;
                            var opcionesBarrio = selectBarrio.querySelectorAll('option');
                            var primerBarrioValido = null;

                            selectBarrio.value = ""; // Limpiar selección actual

                            opcionesBarrio.forEach(function (opt) {
                                if (!opt.getAttribute('data-comuna')) return; // Omitir placeholder

                                if (opt.getAttribute('data-comuna') === comunaId) {
                                    opt.hidden = false;
                                    opt.disabled = false;
                                    if (!primerBarrioValido) primerBarrioValido = opt.value;
                                } else {
                                    opt.hidden = true;
                                    opt.disabled = true;
                                    opt.selected = false;
                                }
                            });

                            if (primerBarrioValido) {
                                selectBarrio.value = primerBarrioValido;
                            }
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