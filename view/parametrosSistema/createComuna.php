<div id="comunaFormRegistro">
    <div class="container-fluid py-3">
        <div class="row justify-content-center">
            <div class="col-xl-8">

                <div class="mb-4 text-center">
                    <h4 class="fw-semibold mb-1">Registrar comuna</h4>
                    <p class="text-muted small mb-0">Ingresa el nombre de la nueva comuna para darla de alta en el sistema.</p>
                </div>

                <form action="<?php echo getUrl('Parametros', 'Parametros', 'postCreateComuna') ?>" method="post" novalidate>

                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-bottom py-3">
                            <span class="fw-semibold">
                                <i class="bi bi-geo-alt-fill me-2 text-primary"></i>Datos de la comuna
                            </span>
                        </div>
                        <div class="card-body p-4">

                            <div class="mb-3">
                                <label for="numero_comuna" class="form-label fw-semibold">Número de la comuna</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">Comuna</span>
                                    <input type="number" min="1" step="1" class="form-control" id="numero_comuna" name="numero_comuna" placeholder="Ejemplo: 20">
                                </div>
                                <div class="form-text">Solo ingresa el número; el nombre se genera automáticamente (ej. "Comuna 20").</div>
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