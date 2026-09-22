<div id="zoocriaderoFormEdicion">
    <div class="container-fluid py-2">

        <form action="<?php echo getUrl('Zoocriadero', 'Zoocriadero', 'postUpdateZoo'); ?>" method="post" novalidate>

            <input type="hidden" name="codzoocriadero" value="<?php echo htmlspecialchars($zoocriadero['codzoocriadero']); ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="nombre" class="form-label fw-semibold text-secondary small text-uppercase">Nombre del Zoocriadero *</label>
                    <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
                        <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-buildings"></i></span>
                        <input type="text" class="form-control border-0 bg-white py-2 shadow-none" id="nombre" name="nombre" maxlength="80" pattern="[A-Za-zÁÉÍÓÚáéíóúÑñ ]+" title="Solo letras y espacios (sin números ni símbolos)" value="<?php echo htmlspecialchars($zoocriadero['nombrezoocriadero']); ?>" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="direccion" class="form-label fw-semibold text-secondary small text-uppercase">Dirección del Zoocriadero *</label>
                    <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
                        <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-geo-alt"></i></span>
                        <input type="text" class="form-control border-0 bg-white py-2 shadow-none input-direccion" id="direccion" name="direccion" autocomplete="off" maxlength="100"
                                       data-url="<?php echo getUrl('Zoocriadero', 'Zoocriadero', 'buscarDireccion', false, 'ajax'); ?>"
                                       data-sugerencias="sugerenciasDireccionEdicion" data-error="errorDireccionEdicion" value="<?php echo htmlspecialchars($zoocriadero['direccion']); ?>" required>
                    </div>
                    <div class="list-group shadow-sm rounded-3 mt-1 d-none sugerencias-direccion" id="sugerenciasDireccionEdicion" style="max-height: 240px; overflow-y: auto;"></div>
                    <div class="text-danger small ms-1 mt-1 d-none" id="errorDireccionEdicion"></div>
                    <div class="form-text text-muted small ms-1 mt-1">Escribe la vía (ej: Calle 5), elige una opción y completa la placa: Calle 5 # 36-05.</div>
                </div>
            </div>

            <div class="row g-3 mt-1">
                <div class="col-md-4">
                    <label for="codcomuna" class="form-label fw-semibold text-secondary small text-uppercase">Comuna *</label>
                    <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
                        <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-map"></i></span>
                        <select class="form-select border-0 bg-white py-2 shadow-none" id="codcomuna" name="codcomuna" required>
                            <option value="">Seleccione...</option>
                            <?php
                            if (isset($comunas)) {
                                while ($comuna = $comunas->fetch(PDO::FETCH_ASSOC)) {
                                    $seleccionada = $comuna['codcomuna'] == $zoocriadero['codcomuna'];
                            ?>
                                    <option value="<?php echo htmlspecialchars($comuna['codcomuna']); ?>" <?php if ($seleccionada) { echo 'selected'; } ?>>
                                        <?php echo htmlspecialchars($comuna['nombrecomuna']); ?>
                                    </option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="codbarrio" class="form-label fw-semibold text-secondary small text-uppercase">Barrio *</label>
                    <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
                        <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-signpost-split"></i></span>
                        <select class="form-select border-0 bg-white py-2 shadow-none" id="codbarrio" name="codbarrio" required>
                            <option value="">Seleccione...</option>
                            <?php
                            if (isset($barrios)) {
                                while ($barrio = $barrios->fetch(PDO::FETCH_ASSOC)) {
                                    $barrioSeleccionado = $barrio['codbarrio'] == $zoocriadero['codbarrio'];
                                    $mismaComuna = $barrio['codcomuna'] == $zoocriadero['codcomuna'];
                            ?>
                                    <option value="<?php echo htmlspecialchars($barrio['codbarrio']); ?>" data-comuna="<?php echo htmlspecialchars($barrio['codcomuna']); ?>" <?php if ($barrioSeleccionado) { echo 'selected'; } ?> <?php if (!$mismaComuna) { echo 'style="display:none;" disabled'; } ?>>
                                        <?php echo htmlspecialchars($barrio['nombrebarrio']); ?>
                                    </option>
                            <?php
                                } 
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-4">
                    <label for="encargado" class="form-label fw-semibold text-secondary small text-uppercase">Encargado *</label>
                    <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
                        <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-person-badge"></i></span>
                        <select class="form-select border-0 bg-white py-2 shadow-none" id="encargado" name="encargado" required>
                            <option value="">Seleccione...</option>
                            <?php
                            if (isset($auxiliares)) {
                                while ($auxiliar = $auxiliares->fetch(PDO::FETCH_ASSOC)) {
                                    $nombreCompleto = $auxiliar['nombreusuario'] . ' ' . $auxiliar['apellidousuario'];
                                    $auxiliarSeleccionado = $auxiliar['codusuario'] == $zoocriadero['codusuario'];
                            ?>
                                    <option value="<?php echo htmlspecialchars($auxiliar['codusuario']); ?>" <?php if ($auxiliarSeleccionado) { echo 'selected'; } ?>>
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

            <hr class="my-4 text-muted opacity-25">

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light px-4 py-2 rounded-3 shadow-sm fw-semibold text-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm fw-semibold d-flex align-items-center">
                    <i class="bi bi-check-lg me-1"></i> Guardar cambios
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
</div>

<script>
    // Script dinámico opcional para actualizar barrios al cambiar la comuna en el formulario
    document.getElementById('codcomuna')?.addEventListener('change', function() {
        var comunaId = this.value;
        var selectBarrio = document.getElementById('codbarrio');
        selectBarrio.value = ""; // Resetear selección de barrio
        
        var opciones = selectBarrio.querySelectorAll('option');
        opciones.forEach(function(opt) {
            if (opt.value === "") return; // Ignorar la opción por defecto
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