<div id="barrioFormRegistro">
    <div class="container-fluid py-3">
        <div class="row justify-content-center">
            <div class="col-xl-8">

                <div class="mb-4 text-center">
                    <h4 class="fw-semibold mb-1">Registrar barrio</h4>
                    <p class="text-muted small mb-0">Selecciona la comuna e ingresa el nombre del nuevo barrio.</p>
                </div>

                <form action="<?php echo getUrl('Parametros', 'Parametros', 'postCreateBarrio') ?>" method="post" novalidate>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <span class="fw-semibold">
                                <i class="bi bi-signpost-split-fill me-2 text-primary"></i>Datos del barrio
                            </span>
                        </div>
                        <div class="card-body p-4">

                            <div class="mb-3">
                                <label for="codcomuna" class="form-label fw-semibold">Comuna</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light"><i class="bi bi-geo-alt-fill"></i></span>
                                    <select class="form-select" id="codcomuna" name="codcomuna">
                                        <option value="" selected disabled>Selecciona una comuna...</option>
                                        <?php
                                        $hayComunas = isset($resultComunas) && $resultComunas && $resultComunas->rowCount() > 0;
                                        if ($hayComunas):
                                            while ($comuna = $resultComunas->fetch(PDO::FETCH_ASSOC)):
                                        ?>
                                            <option value="<?php echo $comuna['codcomuna']; ?>">
                                                <?php echo htmlspecialchars($comuna['nombrecomuna']); ?>
                                            </option>
                                        <?php
                                            endwhile;
                                        endif;
                                        ?>
                                    </select>
                                </div>
                                <?php if (!$hayComunas): ?>
                                    <div class="form-text text-danger">No hay comunas activas registradas. Registra una comuna primero.</div>
                                <?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label for="nombre_barrio" class="form-label fw-semibold">Nombre del barrio</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light"><i class="bi bi-signpost-2"></i></span>
                                    <input type="text" class="form-control" id="nombre_barrio" name="nombre_barrio" placeholder="Ejemplo: Cañaveralejo">
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mb-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-lg me-1"></i>Registrar
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <?php
    if (isset($_SESSION['error'])) {
    ?>
        <div class="row justify-content-center">
            <div class="col-xl-8">
                <div class="alert alert-danger d-flex align-items-center mt-3 mb-0" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <div><?php echo $_SESSION['error']; ?></div>
                </div>
            </div>
        </div>
    <?php
        unset($_SESSION['error']);
    }
    ?>
</div>