<div id="actTerreFormRegistro">
    <div class="container-fluid px-1 py-1">

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

        <form action="<?php echo getUrl('Catalogos', 'ActividadesTerre', 'postCreateActTerre') ?>" method="post" novalidate class="needs-validation">

            <!-- Campo del nombre de la actividad de terreno -->
            <div class="mb-3">
                <label for="nombreactividad" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">
                    Nombre de la actividad <span class="text-danger">*</span>
                </label>
                <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
                    <span class="input-group-text bg-white border-0 text-muted ps-3">
                        <i class="bi bi-map-fill text-primary"></i>
                    </span>
                    <input type="text" class="form-control border-0 bg-white py-2 ps-2 shadow-none" id="nombreactividad" name="nombreactividad" placeholder="Ej: Inspección, Monitoreo..." required>
                </div>
                <div class="form-text text-muted small mt-1">Ingresa el nombre con el que se identificará esta nueva actividad de terreno en el sistema.</div>
            </div>

            <!-- Botones de acción alineados -->
            <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
                <button type="button" class="btn btn-light px-4 py-2 rounded-3 fw-semibold text-secondary border" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
                    <i class="bi bi-check-lg me-1"></i> Registrar
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
  .fs-7 {
    font-size: 0.75rem;
  }
</style>