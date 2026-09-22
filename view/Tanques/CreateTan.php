<div id="tanqueFormRegistro">
    <div class="container-fluid py-2">
        <div class="row justify-content-center">
            <div class="col-xl-11">

                <form action="<?php echo getUrl('Tanques','Tanques','postCreateTan')?>" method="post" novalidate>

                    <div class="row g-3">
                        <!-- Zoocriadero -->
                        <div class="col-md-6">
                            <label for="codzoocriadero" class="form-label text-dark fw-bold small mb-1">Zoocriadero <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-building fs-5"></i></span>
                                <select class="form-select border-0 bg-transparent py-2 fs-6 shadow-none" id="codzoocriadero" name="codzoocriadero" required>
                                    <option value="" selected disabled>Seleccione un zoocriadero...</option>
                                    <?php
                                        $hayZoo = isset($zoocriaderos) && $zoocriaderos && $zoocriaderos->rowCount() > 0;
                                        if ($hayZoo):
                                            while($zoo = $zoocriaderos->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                        <option value="<?php echo $zoo['codzoocriadero']; ?>"
                                                data-siguiente="<?php echo $zoo['siguiente_numero']; ?>">
                                            <?php echo htmlspecialchars($zoo['nombrezoocriadero']); ?>
                                        </option>
                                    <?php
                                            endwhile;
                                        else:
                                    ?>
                                        <option value="">No hay zoocriaderos registrados</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Tipo de Tanque -->
                        <div class="col-md-6">
                            <label for="codtipotanque" class="form-label text-dark fw-bold small mb-1">Tipo de Tanque</label>
                            <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-tag fs-5"></i></span>
                                <select class="form-select border-0 bg-transparent py-2 fs-6 shadow-none" id="codtipotanque" name="codtipotanque">
                                    <?php
                                        $hayTipos = isset($tiposTanque) && $tiposTanque && $tiposTanque->rowCount() > 0;
                                        if ($hayTipos):
                                            while($tipo = $tiposTanque->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                        <option value="<?php echo $tipo['codtipotanque']; ?>">
                                            <?php echo htmlspecialchars($tipo['nombretipotanque']); ?>
                                        </option>
                                    <?php
                                            endwhile;
                                        else:
                                    ?>
                                        <option value="">No hay tipos de tanque registrados</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Capacidad -->
                        <div class="col-md-6">
                            <label for="capacidad" class="form-label text-dark fw-bold small mb-1">Capacidad (litros) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-white shadow-xs">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-droplet fs-5"></i></span>
                                <input type="text" inputmode="decimal" class="form-control border-0 bg-transparent py-2 fs-6 shadow-none" id="capacidad" name="capacidad"
                                       pattern="^\d+(,\d{1,2})?$"
                                       title="Solo números; usa una coma para decimales (ej: 20,5)"
                                       placeholder="Ej. 200 o 20,5" required>
                            </div>
                            <div class="form-text text-muted small ms-1 mt-1">Entre 5 y 1000 litros. Usa coma para decimales (ej: 20,5).</div>
                        </div>

                        <!-- Número de Tanques -->
                        <div class="col-md-6">
                            <label for="numero_tanque" class="form-label text-dark fw-bold small mb-1">Número de Tanques</label>
                            <div class="input-group input-group-lg rounded-4 overflow-hidden border bg-light shadow-xs">
                                <span class="input-group-text bg-transparent border-0 text-muted ps-3"><i class="bi bi-hash fs-5"></i></span>
                                <input type="text" class="form-control border-0 bg-transparent py-2 fs-6 shadow-none text-muted" id="numero_tanque" name="numero_tanque_preview"
                                       placeholder="Seleccione un zoocriadero" readonly tabindex="-1">
                            </div>
                            <div class="form-text text-muted small ms-1 mt-1">Se asigna automáticamente según el zoocriadero seleccionado.</div>
                        </div>

                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-end align-items-center gap-2 mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-light px-4 py-2 rounded-3 text-secondary fw-semibold border" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm fw-semibold">
                            <i class="bi bi-save me-1"></i> Guardar
                        </button>
                    </div>

                </form>

                <script>
                    (function () {
                        var comboZoo = document.getElementById('codzoocriadero');
                        var campoNumero = document.getElementById('numero_tanque');
                        if (!comboZoo || !campoNumero) return;

                        comboZoo.addEventListener('change', function () {
                            var opcion = comboZoo.options[comboZoo.selectedIndex];
                            var siguiente = opcion ? opcion.getAttribute('data-siguiente') : null;
                            campoNumero.value = siguiente ? siguiente : '';
                        });
                    })();
                </script>

            </div>
        </div>
    </div>

    <!-- Alertas de error -->
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