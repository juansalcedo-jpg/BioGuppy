<div id="usuarioFormRegistro">
<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-9">

      <div class="mb-4">
        <h4 class="fw-semibold mb-1">Registrar usuario</h4>
        <p class="text-muted small mb-0">Completa los datos para dar de alta un nuevo usuario en el sistema.</p>
      </div>

      <form action="<?php echo getUrl('Usuarios','Usuarios','postcreateUsu')?>" method="post" novalidate>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white border-bottom py-3">
            <span class="fw-semibold">
              <i class="bi bi-person-badge-fill me-2 text-primary"></i>Datos del usuario
            </span>
          </div>
          <div class="card-body p-4">

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="nombre" class="form-label fw-semibold">Nombre</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                  <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese su nombre">
                </div>
              </div>
              <div class="col-md-6">
                <label for="apellido" class="form-label fw-semibold">Apellido</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                  <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ingrese su apellido">
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="tipoDocumento" class="form-label fw-semibold">Tipo de documento</label>
                <select class="form-select" id="tipoDocumento" name="tipoDocumento">
                  <option selected disabled>Seleccione...</option>
                  <?php
                    while($tipoDocu = $resultdocu->fetch(PDO::FETCH_ASSOC)){
                        echo "<option value='".$tipoDocu['codtipodocumento']."'>".$tipoDocu['nombredocumento']."</option>";
                    }
                  ?>
                </select>
              </div>
              <div class="col-md-6">
                <label for="numeroDocumento" class="form-label fw-semibold">Número de documento</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-credit-card-2-front"></i></span>
                  <input type="text" class="form-control" id="numeroDocumento" name="numeroDocumento" placeholder="Ingrese su número de documento">
                </div>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="correo" class="form-label fw-semibold">Correo electrónico</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-envelope"></i></span>
                  <input type="text" class="form-control" id="correo" name="correo" placeholder="usuario@ejemplo.com">
                </div>
              </div>
              <div class="col-md-6">
                <label for="celular" class="form-label fw-semibold">Celular</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-telephone"></i></span>
                  <input type="tel" class="form-control" id="celular" name="celular" placeholder="Ingrese su celular">
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <label for="rol" class="form-label fw-semibold">Rol</label>
                <select class="form-select" id="rol" name="rol">
                  <option selected disabled>Seleccione...</option>
                  <?php
                    while($tipoRol = $resultrol->fetch(PDO::FETCH_ASSOC)){
                        echo "<option value='".$tipoRol['codrol']."'>".$tipoRol['nombrerol']."</option>";
                    }
                  ?>
                </select>
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