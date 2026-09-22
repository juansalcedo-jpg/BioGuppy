<div id="usuarioFormRegistro">
  <div class="container-fluid px-0 py-1">

    <!-- Alerta de error con diseño moderno -->
    <?php if(isset($_SESSION['error'])): ?>
      <div class="alert alert-danger alert-dismissible fade show py-2 px-3 small mb-4 border-0 shadow-sm rounded-3 bg-danger-subtle text-danger-emphasis" role="alert">
        <div class="d-flex align-items-center">
          <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
          <div><?php echo $_SESSION['error']; ?></div>
        </div>
        <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Formulario con estilo UX/UI Profesional -->
    <form action="<?php echo getUrl('Usuarios','Usuarios','postcreateUsu')?>" method="post" novalidate class="needs-validation">

      <div class="row g-3">
        
        <!-- Nombre -->
        <div class="col-md-6">
          <label for="nombre" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">Nombre <span class="text-danger">*</span></label>
          <div class="input-group shadow-sm rounded-3 overflow-hidden border">
            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-person fs-6"></i></span>
            <input type="text" class="form-control border-0 bg-white py-2 ps-2 shadow-none" id="nombre" name="nombre" placeholder="Ej. Carlos">
          </div>
        </div>

        <!-- Apellido -->
        <div class="col-md-6">
          <label for="apellido" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">Apellido <span class="text-danger">*</span></label>
          <div class="input-group shadow-sm rounded-3 overflow-hidden border">
            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-person-badge fs-6"></i></span>
            <input type="text" class="form-control border-0 bg-white py-2 ps-2 shadow-none" id="apellido" name="apellido" placeholder="Ej. Gómez">
          </div>
        </div>

        <!-- Tipo de documento -->
        <div class="col-md-6">
          <label for="tipoDocumento" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">Tipo de documento <span class="text-danger">*</span></label>
          <div class="input-group shadow-sm rounded-3 overflow-hidden border">
            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-file-earmark-text fs-6"></i></span>
            <select class="form-select border-0 bg-white py-2 ps-2 shadow-none" id="tipoDocumento" name="tipoDocumento">
              <option selected disabled>Seleccione el tipo...</option>
              <?php
                while($tipoDocu = $resultdocu->fetch(PDO::FETCH_ASSOC)){
                    echo "<option value='".$tipoDocu['codtipodocumento']."'>".$tipoDocu['nombredocumento']."</option>";
                }
              ?>
            </select>
          </div>
        </div>

        <!-- Número de documento -->
        <div class="col-md-6">
          <label for="numeroDocumento" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">Número de documento <span class="text-danger">*</span></label>
          <div class="input-group shadow-sm rounded-3 overflow-hidden border">
            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-123 fs-6"></i></span>
            <input type="text" class="form-control border-0 bg-white py-2 ps-2 shadow-none" id="numeroDocumento" name="numeroDocumento" placeholder="Ej. 1023456789">
          </div>
        </div>

        <!-- Correo electrónico -->
        <div class="col-md-6">
          <label for="correo" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">Correo electrónico <span class="text-danger">*</span></label>
          <div class="input-group shadow-sm rounded-3 overflow-hidden border">
            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-envelope fs-6"></i></span>
            <input type="email" class="form-control border-0 bg-white py-2 ps-2 shadow-none" id="correo" name="correo" placeholder="correo@ejemplo.com">
          </div>
        </div>

        <!-- Celular -->
        <div class="col-md-6">
          <label for="celular" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">Celular <span class="text-danger">*</span></label>
          <div class="input-group shadow-sm rounded-3 overflow-hidden border">
            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-phone fs-6"></i></span>
            <input type="tel" class="form-control border-0 bg-white py-2 ps-2 shadow-none" id="celular" name="celular" placeholder="Ej. 3001234567">
          </div>
        </div>

        <!-- Rol -->
        <div class="col-md-12">
          <label for="rol" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">Rol asignado <span class="text-danger">*</span></label>
          <div class="input-group shadow-sm rounded-3 overflow-hidden border">
            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-shield-lock fs-6"></i></span>
            <select class="form-select border-0 bg-white py-2 ps-2 shadow-none" id="rol" name="rol">
              <option selected disabled>Seleccione el rol correspondiente...</option>
              <?php
                while($tipoRol = $resultrol->fetch(PDO::FETCH_ASSOC)){
                    echo "<option value='".$tipoRol['codrol']."'>".$tipoRol['nombrerol']."</option>";
                }
              ?>
            </select>
          </div>
        </div>

      </div>

      <!-- Pie de formulario / Botones de acción limpios y elegantes -->
      <div class="d-flex justify-content-end align-items-center gap-2 pt-4 mt-4 border-top">
        <button type="button" class="btn btn-light px-4 py-2 rounded-3 fw-semibold text-secondary border" data-bs-dismiss="modal">
          Cancelar
        </button>
        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
          <i class="bi bi-check2-circle me-1"></i> Registrar usuario
        </button>
      </div>

    </form>

  </div>
</div>

<style>
  /* Detalles finos de UX para transiciones suaves al enfocar campos */
  .input-group:focus-within {
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