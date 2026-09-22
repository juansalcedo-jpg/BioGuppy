<div id="zoocriaderoFormRegistro">
    <div class="container-fluid px-2 py-3">

        <form action="<?php echo getUrl('Zoocriadero', 'Zoocriadero', 'postCreateZoo'); ?>" method="post" novalidate>

            <div class="row g-3">
                <!-- Nombre del Zoocriadero -->
                <div class="col-md-6">
                    <label for="nombre" class="form-label text-dark fw-bold small mb-1">Nombre del Zoocriadero <span class="text-danger">*</span></label>
                    <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-tag fs-5"></i></span>
                        <input type="text" class="form-control border-0 bg-transparent py-2 fs-6 shadow-none" id="nombre" name="nombre" maxlength="80" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+" title="Solo letras y espacios (sin números ni símbolos)" placeholder="Ej: Zoocriadero Central" required>
                    </div>
                </div>

                <!-- Dirección -->
                <div class="col-md-6">
                    <label for="direccion" class="form-label text-dark fw-bold small mb-1">Dirección <span class="text-danger">*</span></label>
                    <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-geo-alt fs-5"></i></span>
                        <input type="text" class="form-control border-0 bg-transparent py-2 fs-6 shadow-none input-direccion" id="direccion" name="direccion" autocomplete="off" maxlength="100"
                                       data-url="<?php echo getUrl('Zoocriadero', 'Zoocriadero', 'buscarDireccion', false, 'ajax'); ?>"
                                       data-sugerencias="sugerenciasDireccionRegistro" data-error="errorDireccionRegistro" placeholder="Ej: Calle 5 # 36-05" required>
                    </div>
                    <div class="list-group shadow-sm rounded-3 mt-1 d-none sugerencias-direccion" id="sugerenciasDireccionRegistro" style="max-height: 240px; overflow-y: auto;"></div>
                    <div class="text-danger small ms-1 mt-1 d-none" id="errorDireccionRegistro"></div>
                    <div class="form-text text-muted small ms-1 mt-1">Escribe la vía (ej: Calle 5), elige una opción y completa la placa: Calle 5 # 36-05.</div>
                </div>

                <!-- Comuna -->
                <div class="col-md-4">
                    <label for="codcomuna" class="form-label text-dark fw-bold small mb-1">Comuna <span class="text-danger">*</span></label>
                    <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-map fs-5"></i></span>
                        <select class="form-select border-0 bg-transparent py-2 fs-6 shadow-none" id="codcomuna" name="codcomuna" required>
                            <option value="">Seleccione...</option>
                            <?php
                            if (isset($comunas)) {
                                while ($comuna = $comunas->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                                    <option value="<?php echo htmlspecialchars($comuna['codcomuna']); ?>">
                                        <?php echo htmlspecialchars($comuna['nombrecomuna']); ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Barrio -->
                <div class="col-md-4">
                    <label for="codbarrio" class="form-label text-dark fw-bold small mb-1">Barrio <span class="text-danger">*</span></label>
                    <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-signpost fs-5"></i></span>
                        <select class="form-select border-0 bg-transparent py-2 fs-6 shadow-none" id="codbarrio" name="codbarrio" required disabled>
                            <option value="">Seleccione comuna...</option>
                            <?php
                            if (isset($barrios)) {
                                while ($barrio = $barrios->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                                    <option value="<?php echo htmlspecialchars($barrio['codbarrio']); ?>" data-comuna="<?php echo htmlspecialchars($barrio['codcomuna']); ?>" style="display:none;" disabled>
                                        <?php echo htmlspecialchars($barrio['nombrebarrio']); ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <!-- Encargado -->
                <div class="col-md-4">
                    <label for="encargado" class="form-label text-dark fw-bold small mb-1">Encargado <span class="text-danger">*</span></label>
                    <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                        <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-person-badge fs-5"></i></span>
                        <select class="form-select border-0 bg-transparent py-2 fs-6 shadow-none" id="encargado" name="encargado" required>
                            <option value="">Seleccione...</option>
                            <?php
                            if (isset($auxiliares)) {
                                while ($auxiliar = $auxiliares->fetch(PDO::FETCH_ASSOC)) {
                                    $nombreCompleto = $auxiliar['nombreusuario'] . ' ' . $auxiliar['apellidousuario'];
                            ?>
                                    <option value="<?php echo htmlspecialchars($auxiliar['codusuario']); ?>">
                                        <?php echo htmlspecialchars($nombreCompleto); ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
                <button type="button" class="btn btn-light px-4 py-2 rounded-3 text-secondary fw-semibold border" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm fw-semibold">
                    <i class="bi bi-check-lg me-1"></i> Registrar Zoocriadero
                </button>
            </div>

        </form>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mt-4 border-0 shadow-sm rounded-3 bg-danger-subtle text-danger-emphasis" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                    <div><?php echo htmlspecialchars($_SESSION['error']); ?></div>
                </div>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

    </div>

    <style>
        /* Efecto al hacer foco en los contenedores de los inputs */
        .input-group:focus-within {
            border-color: var(--bs-primary) !important;
            box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15);
        }
    </style>

    <script>
        document.getElementById('codcomuna')?.addEventListener('change', function() {
            var comunaId = this.value;
            var selectBarrio = document.getElementById('codbarrio');

            selectBarrio.value = "";

            if (comunaId === "") {
                selectBarrio.setAttribute('disabled', 'true');
                selectBarrio.options[0].textContent = "Seleccione comuna...";
            } else {
                selectBarrio.removeAttribute('disabled');
                selectBarrio.options[0].textContent = "Seleccione un barrio...";
            }

            var opciones = selectBarrio.querySelectorAll('option');
            opciones.forEach(function(opt) {
                if (opt.value === "") return;
                var cBarrio = opt.getAttribute('data-comuna');
                if (cBarrio === comunaId) {
                    opt.style.display = '';
                    opt.removeAttribute('disabled');
                } else {
                    opt.style.display = 'none';
                    opt.setAttribute('disabled', 'true');
                }
            });
        });
    </script>
</div>