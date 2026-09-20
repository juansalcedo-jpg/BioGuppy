<?php
?>
<div id="sitioFormRegistro">
<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-9">

      <div class="mb-4">
        <h4 class="fw-semibold mb-1">Registrar sitio de terreno</h4>
        <p class="text-muted small mb-0">Agrega un nuevo sitio donde se realizarán actividades de terreno.</p>
      </div>

      <form action="<?php echo getUrl('Sitios','Sitios','postCreateSit')?>" method="post" novalidate>

        <div class="card border-0 shadow-sm mb-4">
          <div class="card-header bg-white border-bottom py-3">
            <span class="fw-semibold">
              <i class="bi bi-house-door-fill me-2 text-primary"></i>Datos del sitio
            </span>
          </div>
          <div class="card-body p-4">

            <div class="row g-3">
              <div class="col-md-12">
                <label for="nombresitio" class="form-label fw-semibold">Nombre del sitio</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-signpost"></i></span>
                  <input type="text" class="form-control" id="nombresitio" name="nombresitio" placeholder="Ej: Fuente Santa Mónica">
                </div>
              </div>

              <div class="col-md-6">
                <label for="codcomuna" class="form-label fw-semibold">Comuna</label>
                <select class="form-select" id="codcomuna" name="codcomuna">
                  <option value="" selected disabled>Seleccione...</option>
                  <?php foreach ($comunas as $comuna): ?>
                    <option value="<?php echo $comuna['id']; ?>"><?php echo htmlspecialchars($comuna['nombrecomuna']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

                <div class="col-md-6">
                <label for="codbarrio" class="form-label fw-semibold">Barrio</label>
                <select class="form-select" id="codbarrio" name="codbarrio">
                  <option value="" selected disabled>Seleccione...</option>
                  <?php foreach ($barrios as $barrio): ?>
                    <option value="<?php echo $barrio['id']; ?>" data-comuna="<?php echo $barrio['codcomuna']; ?>"><?php echo htmlspecialchars($barrio['nombrebarrio']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label for="codtipodeposito" class="form-label fw-semibold">Tipo de depósito</label>
                <select class="form-select" id="codtipodeposito" name="codtipodeposito">
                  <option value="" selected disabled>Seleccione...</option>
                  <?php foreach ($tiposDeposito as $tipo): ?>
                    <option value="<?php echo $tipo['id']; ?>"><?php echo htmlspecialchars($tipo['nombretipodeposito']); ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-6">
                <label for="direccion" class="form-label fw-semibold">Dirección</label>
                <div class="input-group">
                  <span class="input-group-text bg-light"><i class="bi bi-geo-alt"></i></span>
                  <input type="text" class="form-control" id="direccion" name="direccion" placeholder="Ej: Calle 30 #36-5">
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

        <script>
  (function () {
    var comboComuna = document.getElementById('codcomuna');
    var comboBarrio = document.getElementById('codbarrio');
    if (!comboComuna || !comboBarrio) return;

    var opcionesBarrio = Array.prototype.slice.call(comboBarrio.options);

    comboComuna.addEventListener('change', function () {
      var comunaSeleccionada = this.value;
      comboBarrio.value = '';

      opcionesBarrio.forEach(function (opcion) {
        if (!opcion.value) return;
        var coincide = opcion.dataset.comuna === comunaSeleccionada;
        opcion.hidden = !coincide;
      });
    });
  })();
</script>

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