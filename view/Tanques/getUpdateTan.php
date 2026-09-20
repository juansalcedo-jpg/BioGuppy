<?php
$tanque = $tanque->fetch(PDO::FETCH_ASSOC);
?>
<div id="tanqueFormEdicion">
<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-11">

      <form action="<?php echo getUrl('Tanques','Tanques','postUpdateTan')?>" method="post" novalidate>
        <input type="hidden" name="codtanque" value="<?php echo $tanque['codtanque']; ?>">

        <div class="row g-3 mb-3">

          <div class="col-md-6">
            <label for="numero_tanque" class="form-label fw-semibold">Número de tanque <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="bi bi-hash"></i></span>
              <input type="number" min="1" step="1" class="form-control" id="numero_tanque" name="numero_tanque"
                     value="<?php echo htmlspecialchars($tanque['numerotanque']); ?>" required>
            </div>
          </div>

          <div class="col-md-6">
            <label for="codtipotanque" class="form-label fw-semibold">Tipo de tanque</label>
            <select class="form-select" id="codtipotanque" name="codtipotanque">
              <?php
                $hayTipos = isset($tiposTanque) && $tiposTanque && $tiposTanque->rowCount() > 0;
                if ($hayTipos):
                    while($tipo = $tiposTanque->fetch(PDO::FETCH_ASSOC)):
                        $seleccionado = ($tipo['codtipotanque'] == $tanque['codtipotanque']) ? 'selected' : '';
              ?>
                <option value="<?php echo $tipo['codtipotanque']; ?>" <?php echo $seleccionado; ?>>
                  <?php echo htmlspecialchars($tipo['nombretipotanque']); ?>
                </option>
              <?php
                    endwhile;
                else:
              ?>
                <option value="">No hay tipos de tanque registrados</option>
              <?php endif; ?>
            </select>
          </div>

        </div>

        <div class="row g-3 mb-3">

          <div class="col-md-6">
            <label for="capacidad" class="form-label fw-semibold">Capacidad (litros) <span class="text-danger">*</span></label>
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="bi bi-droplet-fill"></i></span>
              <input type="number" min="1" step="1" class="form-control" id="capacidad" name="capacidad"
                     value="<?php echo htmlspecialchars($tanque['capacidad']); ?>" required>
            </div>
          </div>

          <div class="col-md-6">
            <label for="codzoocriadero" class="form-label fw-semibold">Zoocriadero</label>
            <select class="form-select" id="codzoocriadero" name="codzoocriadero">
              <?php
                $hayZoo = isset($zoocriaderos) && $zoocriaderos && $zoocriaderos->rowCount() > 0;
                if ($hayZoo):
                    while($zoo = $zoocriaderos->fetch(PDO::FETCH_ASSOC)):
                        $seleccionado = ($zoo['codzoocriadero'] == $tanque['codzoocriadero']) ? 'selected' : '';
              ?>
                <option value="<?php echo $zoo['codzoocriadero']; ?>" <?php echo $seleccionado; ?>>
                  <?php echo htmlspecialchars($zoo['nombrezoocriadero']); ?>
                </option>
              <?php
                    endwhile;
                else:
              ?>
                <option value="">No hay zoocriaderos registrados</option>
              <?php endif; ?>
            </select>
          </div>

        </div>

        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
            Cancelar
          </button>
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
  <div class="col-xl-11">
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
