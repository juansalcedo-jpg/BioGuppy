<?php
?>
<div id="tanqueFormRegistro">
<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-11">

      <form action="<?php echo getUrl('Tanques','Tanques','postCreateTan')?>" method="post" novalidate>

        <div class="row g-3 mb-3">

          <div class="col-md-6">
            <label for="codzoocriadero" class="form-label fw-semibold">Zoocriadero <span class="text-danger">*</span></label>
            <select class="form-select" id="codzoocriadero" name="codzoocriadero" required>
              <option value="" selected disabled>Seleccione un zoocriadero...</option>
              <?php
                $hayZoo = isset($zoocriaderos) && $zoocriaderos && $zoocriaderos->rowCount() > 0;
                if ($hayZoo):
                    while($zoo = $zoocriaderos->fetch(PDO::FETCH_ASSOC)):
              ?>
                <option value="<?php echo $zoo['codzoocriadero']; ?>"
                        data-siguiente="<?php echo $zoo['siguiente_numero']; ?>">
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

          <div class="col-md-6">
            <label for="codtipotanque" class="form-label fw-semibold">Tipo de tanque</label>
            <select class="form-select" id="codtipotanque" name="codtipotanque">
              <?php
                $hayTipos = isset($tiposTanque) && $tiposTanque && $tiposTanque->rowCount() > 0;
                if ($hayTipos):
                    while($tipo = $tiposTanque->fetch(PDO::FETCH_ASSOC)):
              ?>
                <option value="<?php echo $tipo['codtipotanque']; ?>">
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
              <input type="text" inputmode="decimal" class="form-control" id="capacidad" name="capacidad"
                     pattern="^\d+(,\d{1,2})?$"
                     title="Solo números; usa una coma para decimales (ej: 20,5)"
                     placeholder="Ej. 200 o 20,5" required>
            </div>
            <div class="form-text">Entre 5 y 1000 litros. Usa coma para decimales (ej: 20,5).</div>
          </div>

          <div class="col-md-6">
            <label for="numero_tanque" class="form-label fw-semibold">Número de tanque</label>
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="bi bi-hash"></i></span>
              <input type="text" class="form-control" id="numero_tanque" name="numero_tanque_preview"
                     placeholder="Seleccione un zoocriadero" readonly tabindex="-1">
            </div>
            <div class="form-text">Se asigna automáticamente según el zoocriadero seleccionado.</div>
          </div>

        </div>

        <div class="d-flex justify-content-end gap-2">
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
            Cancelar
          </button>
          <button type="submit" class="btn btn-primary px-4">
            <i class="bi bi-save me-1"></i>Guardar
          </button>
        </div>

      </form>

      <script>
        (function () {
          var comboZoo = document.getElementById('codzoocriadero');
          var campoNumero = document.getElementById('numero_tanque');
          if (!comboZoo || !campoNumero) return;

          comboZoo.addEventListener('change', function () {
            var opcion = comboZoo.options[comboZoo.selectedIndex];
            var siguiente = opcion ? opcion.getAttribute('data-siguiente') : null;
            campoNumero.value = siguiente ? siguiente : '';
          });
        })();
      </script>

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
