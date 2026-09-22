<div id="comunaFormEdicion">
    <div class="container-fluid px-2 py-2">

        <!-- Alerta de error -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3 border-0 shadow-sm rounded-3 bg-danger-subtle text-danger-emphasis" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
                    <div><?php echo $_SESSION['error']; ?></div>
                </div>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form action="<?php echo getUrl('Parametros', 'Parametros', 'postUpdateComuna') ?>" method="post" novalidate class="needs-validation">
            <input type="hidden" name="codcomuna" value="<?php echo $comuna['codcomuna']; ?>">

            <!-- Estilo Minimalista Centrado -->
            <div class="text-center mb-4">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary-subtle text-primary rounded-circle mb-2" style="width: 48px; height: 48px;">
                    <i class="bi bi-geo-alt-fill fs-5"></i>
                </div>
                <label for="numero_comuna" class="d-block fw-bold text-dark mb-1">Modificar número de comuna</label>
                <p class="text-muted small mb-3">Actualiza el número correspondiente para renombrarla automáticamente.</p>

                <!-- Input destacado en el centro -->
                <div class="row justify-content-center">
                    <div class="col-md-7">
                        <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border">
                            <span class="input-group-text bg-light text-muted border-0 fw-semibold ps-4">N°</span>
                            <input type="number" min="1" step="1" class="form-control border-0 bg-white text-center fw-bold fs-4 shadow-none py-2" id="numero_comuna" name="numero_comuna"
                                   value="<?php echo htmlspecialchars($comuna['numero']); ?>" placeholder="0" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                <button type="button" class="btn btn-light px-4 py-2 rounded-3 fw-semibold text-secondary border" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                    <i class="bi bi-check-lg me-1"></i> Guardar cambios
                </button>
            </div>

        </form>

    </div>
</div>

<style>
  .input-group:focus-within, .border:focus-within {
    border-color: var(--bs-primary) !important;
    box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15) !important;
  }
</style>