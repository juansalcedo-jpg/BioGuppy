<div id="barrioFormEdicion">
    <div class="container-fluid py-3">
        <div class="row justify-content-center">
            <div class="col-xl-7 col-lg-8">

                <!-- Encabezado minimalista -->
                <div class="d-flex align-items-center justify-content-between mb-4 pb-3 border-bottom">
                    <div>
                        <h4 class="fw-bold mb-1 text-dark">
                            <i class="bi bi-signpost-split-fill me-2 text-primary"></i>Editar Barrio
                        </h4>
                        <p class="text-muted small mb-0">Corrige la comuna o el nombre del barrio seleccionado.</p>
                    </div>
                </div>

                <!-- Alerta de error -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-4 border-0 shadow-sm rounded-3 bg-danger-subtle text-danger-emphasis" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                            <div><?php echo $_SESSION['error']; ?></div>
                        </div>
                        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="<?php echo getUrl('Parametros', 'Parametros', 'postUpdateBarrio') ?>" method="post" novalidate>
                    <input type="hidden" name="codbarrio" value="<?php echo $barrio['codbarrio']; ?>">

                    <!-- Campos organizados sin tarjeta grande -->
                    <div class="row g-3">
                        
                        <!-- Comuna -->
                        <div class="col-12">
                            <label for="codcomuna" class="form-label fw-semibold text-secondary small text-uppercase tracking-wider">Comuna</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-geo-alt-fill"></i></span>
                                <select class="form-select border-start-0 shadow-none bg-light" id="codcomuna" name="codcomuna">
                                    <option value="" disabled>Selecciona una comuna...</option>
                                    <?php
                                    $hayComunas = isset($resultComunas) && $resultComunas && $resultComunas->rowCount() > 0;
                                    if ($hayComunas):
                                        while ($comuna = $resultComunas->fetch(PDO::FETCH_ASSOC)):
                                    ?>
                                            <option value="<?php echo $comuna['codcomuna']; ?>"
                                                <?php echo ($comuna['codcomuna'] == $barrio['codcomuna']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($comuna['nombrecomuna']); ?>
                                            </option>
                                    <?php
                                        endwhile;
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </div>

                        <!-- Nombre del barrio -->
                        <div class="col-12">
                            <label for="nombre_barrio" class="form-label fw-semibold text-secondary small text-uppercase tracking-wider">Nombre del barrio</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-white border-end-0 text-primary"><i class="bi bi-signpost-2-fill"></i></span>
                                <input type="text" class="form-control border-start-0 shadow-none bg-light" id="nombre_barrio" name="nombre_barrio"
                                       value="<?php echo htmlspecialchars($barrio['nombrebarrio']); ?>" placeholder="Ingrese el nombre del barrio">
                            </div>
                        </div>

                    </div>

                    <!-- Botones de acción limpios -->
                    <div class="d-flex align-items-center justify-content-end gap-2 mt-5 pt-3 border-top">
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                            <i class="bi bi-check-lg me-1"></i> Guardar cambios
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>