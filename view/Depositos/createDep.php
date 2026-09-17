<?php
?>
<div id="depositoFormRegistro">
<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-9">

      <div class="mb-4">
        <h4 class="fw-semibold mb-1">Registrar tipo de depósito</h4>
        <p class="text-muted small mb-0">Agrega un nuevo tipo de depósito al catálogo.</p>
      </div>

      <form action="<?php echo getUrl('Depositos','Depositos','postCreateDep')?>" method="post" novalidate>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white border-bottom py-3">
            <span class="fw-semibold">
              <i class="bi bi-bucket-fill me-2 text-primary"></i>Datos del tipo de depósito
            </span>
          </div>
          <div class="card-body p-4">

            <div class="row">
              <div class="col-md-8">
                <label for="nombre_deposito" class="form-label fw-semibold">Nombre</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-bucket"></i></span>
                  <input type="text" class="form-control" id="nombre_deposito" name="nombre_deposito" placeholder="Ej: Piscina abandonada, aguas estancadas...">
                </div>
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
