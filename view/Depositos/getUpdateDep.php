<?php
/**
 * getUpdateDep.php
 * ------------------------------------------------------------
 * Formulario de EDICION de un tipo de deposito (ej: "Piscina
 * abandonada"). Llega aqui cuando el usuario hace clic en el lapiz
 * de la tabla de Depositos -> cargarFormularioModal() (ver
 * listDep.php) pide esta vista por AJAX y la mete dentro del modal
 * generico (modalFormulario.php).
 *
 * $tipoDeposito llega ya armado desde DepositosController::getUpdate():
 * es un PDOStatement con UNA sola fila (el registro que se va a editar).
 * Lo convertimos aqui en un array asociativo con fetch().
 */
$tipo = $tipoDeposito->fetch(PDO::FETCH_ASSOC);
?>
<div id="depositoFormEdicion">
<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-9">

      <div class="mb-4">
        <h4 class="fw-semibold mb-1">Editar tipo de depósito</h4>
        <p class="text-muted small mb-0">Actualiza el nombre del tipo de depósito.</p>
      </div>

      <form action="<?php echo getUrl('Depositos','Depositos','postUpdateDep')?>" method="post" novalidate>
        <input type="hidden" name="codtipodeposito" value="<?php echo $tipo['codtipodeposito']; ?>">

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white border-bottom py-3">
            <span class="fw-semibold">
              <i class="bi bi-bucket me-2 text-primary"></i>Datos del tipo de depósito
            </span>
          </div>
          <div class="card-body p-4">

            <div class="row">
              <div class="col-md-8">
                <label for="nombre_deposito" class="form-label fw-semibold">Nombre</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-bucket"></i></span>
                  <input type="text" class="form-control" id="nombre_deposito" name="nombre_deposito" value="<?php echo htmlspecialchars($tipo['nombredeposito']); ?>">
                </div>
              </div>
            </div>

          </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
          <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-check-lg me-1"></i>Guardar cambios
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
