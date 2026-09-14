<div id="depositoFormRegistro">
<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-9">

      <div class="mb-4">
        <h4 class="fw-semibold mb-1">Registrar depósito</h4>
        <p class="text-muted small mb-0">Asocia un nuevo depósito a un sitio de terreno.</p>
      </div>

      <form action="<?php echo getUrl('Depositos','Depositos','postCreateDep')?>" method="post" novalidate>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white border-bottom py-3">
            <span class="fw-semibold">
              <i class="bi bi-bucket me-2 text-primary"></i>Datos del depósito
            </span>
          </div>
          <div class="card-body p-4">

            <div class="row mb-3">
              <div class="col-md-6">
                <label for="sitio_id" class="form-label fw-semibold">Sitio de terreno *</label>
                <select class="form-select" id="sitio_id" name="sitio_id" required>
                  <option selected disabled>Seleccione...</option>
                  <?php
                    if (isset($sitios) && $sitios):
                        while($sitio = $sitios->fetch(PDO::FETCH_ASSOC)):
                  ?>
                  <option value="<?php echo $sitio['id']; ?>"><?php echo htmlspecialchars($sitio['nombre'] . ' — ' . $sitio['comuna'] . ', ' . $sitio['barrio']); ?></option>
                  <?php
                        endwhile;
                    endif;
                  ?>
                </select>
              </div>
              <div class="col-md-6">
                <label for="tipo_deposito_id" class="form-label fw-semibold">Tipo de depósito *</label>
                <select class="form-select" id="tipo_deposito_id" name="tipo_deposito_id" required>
                  <option selected disabled>Seleccione...</option>
                  <?php
                    if (isset($tiposDeposito) && $tiposDeposito):
                        while($tipo = $tiposDeposito->fetch(PDO::FETCH_ASSOC)):
                  ?>
                  <option value="<?php echo $tipo['id']; ?>"><?php echo htmlspecialchars($tipo['nombre']); ?></option>
                  <?php
                        endwhile;
                    endif;
                  ?>
                </select>
              </div>
            </div>

            <div class="row">
              <div class="col-md-6">
                <label for="fecha_registro" class="form-label fw-semibold">Fecha de registro *</label>
                <input type="date" class="form-control" id="fecha_registro" name="fecha_registro" value="<?php echo date('Y-m-d'); ?>" required>
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
