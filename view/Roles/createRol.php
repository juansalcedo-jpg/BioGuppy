<div id="rolFormRegistro">
<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-9">

      <div class="mb-4">
        <h4 class="fw-semibold mb-1">Registrar rol</h4>
        <p class="text-muted small mb-0">Define un nuevo rol, su descripción y los permisos por módulo.</p>
      </div>

      <form action="<?php echo getUrl('Roles','Roles','postcreateRol')?>" method="post" novalidate>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white border-bottom py-3">
            <span class="fw-semibold">
              <i class="bi bi-person-badge-fill me-2 text-primary"></i>Información del rol
            </span>
          </div>
          <div class="card-body p-4">
            <div class="row">
              <div class="col-md-5 mb-4 mb-md-0">
                <label for="nombreRol" class="form-label fw-semibold">Nombre del rol</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-tag"></i></span>
                  <input type="text" class="form-control" id="nombreRol" name="nombreRol"
                         placeholder="Ej: Coordinador de campo" maxlength="100" required>
                </div>
                <div class="form-text">Nombre corto y descriptivo del rol.</div>
              </div>
              <div class="col-md-7">
                <label for="descripcionRol" class="form-label fw-semibold">Descripción</label>
                <textarea class="form-control" id="descripcionRol" name="descripcionRol" rows="2"
                          placeholder="Describa brevemente las funciones y alcance de este rol" maxlength="255"></textarea>
                <div class="form-text">Opcional. Máximo 255 caracteres.</div>
              </div>
            </div>
          </div>
        </div>

        <div class="alert alert-info d-flex align-items-center mb-4">
          <i class="bi bi-info-circle-fill me-2"></i>
          <div>Después de registrar el rol, podrás asignarle sus permisos por módulo desde el botón <strong>"Permisos"</strong> en el listado.</div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
          <a href="<?php echo getUrl('Roles','Roles','listRol')?>" class="btn btn-outline-secondary px-4">
            Cancelar
          </a>
          <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-1"></i>Registrar
          </button>
        </div>

      </form>

    </div>
  </div>
</div>
</div>
<?php
  if(isset($_SESSION['error'])){
?>
<div class="row justify-content-center">
  <div class="col-xl-9">
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