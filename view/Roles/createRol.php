<div id="rolFormRegistro">
  <div class="container-fluid px-1 py-1">

    <!-- Alerta de error superior (si existe) -->
    <?php if(isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3 border-0 shadow-sm rounded-3 bg-danger-subtle text-danger-emphasis" role="alert">
        <div class="d-flex align-items-center">
          <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
          <div><?php echo $_SESSION['error']; ?></div>
        </div>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <form action="<?php echo getUrl('Roles','Roles','postcreateRol')?>" method="post" novalidate class="needs-validation">

      <div class="row g-3 mb-3">
        
        <!-- Nombre del rol -->
        <div class="col-md-5">
          <label for="nombreRol" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">Nombre del rol <span class="text-danger">*</span></label>
          <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-tag fs-6"></i></span>
            <input type="text" class="form-control border-0 bg-white py-2 ps-2 shadow-none" id="nombreRol" name="nombreRol"
                   placeholder="Ej: Coordinador de campo" maxlength="100" required>
          </div>
          <div class="form-text text-muted small mt-1">Nombre corto y descriptivo.</div>
        </div>

        <!-- Descripción -->
        <div class="col-md-7">
          <label for="descripcionRol" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">Descripción</label>
          <div class="shadow-sm rounded-3 overflow-hidden border bg-white">
            <textarea class="form-control border-0 bg-white p-2 shadow-none" id="descripcionRol" name="descripcionRol" rows="3"
                      placeholder="Describa brevemente las funciones y alcance de este rol..." maxlength="255"></textarea>
          </div>
          <div class="form-text text-muted small mt-1">Opcional. Máximo 255 caracteres.</div>
        </div>

      </div>

      <!-- Alerta informativa moderna -->
      <div class="alert alert-info border-0 shadow-sm rounded-3 py-2 px-3 d-flex align-items-center mb-3 bg-info-subtle text-info-emphasis">
        <i class="bi bi-info-circle-fill fs-5 me-2 flex-shrink-0"></i>
        <div class="small">
          Después de registrar el rol, podrás asignarle sus permisos desde el botón <strong class="fw-bold">"Permisos"</strong>.
        </div>
      </div>

      <!-- Botones de acción alineados y estilizados -->
      <div class="d-flex justify-content-end gap-2 pt-3 border-top">
        <button type="button" class="btn btn-light px-4 py-2 rounded-3 fw-semibold text-secondary border" data-bs-dismiss="modal">
          Cancelar
        </button>
        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
          <i class="bi bi-check-lg me-1"></i> Registrar rol
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
  .input-group:focus-within .input-group-text {
    color: var(--bs-primary) !important;
  }
  .fs-7 {
    font-size: 0.75rem;
  }
</style>