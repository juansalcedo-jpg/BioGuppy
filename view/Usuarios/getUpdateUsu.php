<div id="usuarioFormEdicion">
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

    <?php while($usu = $resultusu->fetch(PDO::FETCH_ASSOC)){ ?>
    <form action="<?php echo getUrl('Usuarios','Usuarios','postUpdateUsu')?>" method="post" novalidate class="needs-validation">
      <input type="hidden" name="codusuario" value="<?php echo $usu['codusuario'] ?>">

      <div class="row g-3 mb-3">
        
        <!-- Nombre -->
        <div class="col-md-6">
          <label for="nombre" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">Nombre <span class="text-danger">*</span></label>
          <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-person fs-6"></i></span>
            <input type="text" class="form-control border-0 bg-white py-2 ps-2 shadow-none" id="nombre" name="nombre"
                   value="<?php echo $usu['nombreusuario'] ?>" placeholder="Ingrese su nombre" required>
          </div>
        </div>

        <!-- Apellido -->
        <div class="col-md-6">
          <label for="apellido" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">Apellido <span class="text-danger">*</span></label>
          <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-person fs-6"></i></span>
            <input type="text" class="form-control border-0 bg-white py-2 ps-2 shadow-none" id="apellido" name="apellido"
                   value="<?php echo $usu['apellidousuario'] ?>" placeholder="Ingrese su apellido" required>
          </div>
        </div>

        <!-- Tipo de documento -->
        <div class="col-md-6">
          <label for="tipoDocumento" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">Tipo de documento <span class="text-danger">*</span></label>
          <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-card-text fs-6"></i></span>
            <select class="form-select border-0 bg-white py-2 ps-2 shadow-none" id="tipoDocumento" name="tipoDocumento" required>
              <option disabled>Seleccione...</option>
              <?php
                while($tipoDocu = $resultdocu->fetch(PDO::FETCH_ASSOC)){
                    $selected = ($usu['codtipodocumento'] == $tipoDocu['codtipodocumento']) ? "selected" : "";
                    echo "<option value='".$tipoDocu['codtipodocumento']."' $selected>".$tipoDocu['nombredocumento']."</option>";
                }
              ?>
            </select>
          </div>
        </div>

        <!-- Número de documento -->
        <div class="col-md-6">
          <label for="numeroDocumento" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">Número de documento <span class="text-danger">*</span></label>
          <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-credit-card-2-front fs-6"></i></span>
            <input type="text" class="form-control border-0 bg-white py-2 ps-2 shadow-none" id="numeroDocumento" name="numeroDocumento"
                   value="<?php echo $usu['numerodocumento'] ?>" placeholder="Ingrese su número de documento" required>
          </div>
        </div>

        <!-- Correo electrónico -->
        <div class="col-md-6">
          <label for="correo" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">Correo electrónico <span class="text-danger">*</span></label>
          <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-envelope fs-6"></i></span>
            <input type="email" class="form-control border-0 bg-white py-2 ps-2 shadow-none" id="correo" name="correo"
                   value="<?php echo $usu['correo'] ?>" placeholder="usuario@ejemplo.com" required>
          </div>
        </div>

        <!-- Celular -->
        <div class="col-md-6">
          <label for="celular" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">Celular <span class="text-danger">*</span></label>
          <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-telephone fs-6"></i></span>
            <input type="tel" class="form-control border-0 bg-white py-2 ps-2 shadow-none" id="celular" name="celular"
                   value="<?php echo $usu['usutelefono'] ?>" placeholder="Ingrese su celular" required>
          </div>
        </div>

        <!-- Rol -->
        <div class="col-md-12">
          <label for="rol" class="form-label text-secondary fs-7 fw-bold text-uppercase tracking-wider mb-1">Rol asignado <span class="text-danger">*</span></label>
          <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
            <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-shield-lock fs-6"></i></span>
            <select class="form-select border-0 bg-white py-2 ps-2 shadow-none" id="rol" name="rol" required>
              <option disabled>Seleccione...</option>
              <?php
                while($tipoRol = $resultrol->fetch(PDO::FETCH_ASSOC)){
                    $selected = ($usu['codrol'] == $tipoRol['codrol']) ? "selected" : "";
                    echo "<option value='".$tipoRol['codrol']."' $selected>".$tipoRol['nombrerol']."</option>";
                }
              ?>
            </select>
          </div>
        </div>

      </div>

      <!-- Botones de acción alineados y estilizados -->
      <div class="d-flex justify-content-end gap-2 pt-3 border-top mt-4">
        <button type="button" class="btn btn-light px-4 py-2 rounded-3 fw-semibold text-secondary border" data-bs-dismiss="modal">
          Cancelar
        </button>
        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
          <i class="bi bi-check-lg me-1"></i> Guardar cambios
        </button>
      </div>

    </form>
    <?php } ?>

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