<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-11">

      <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-semibold mb-1">Mis actividades — Zoocriadero</h4>
          <p class="text-muted small mb-0">Consulta y filtra las actividades de zoocriadero que has registrado.</p>
        </div>
      </div>

      <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-bottom py-3">
          <div class="d-flex align-items-center mb-3">
            <i class="bi bi-file-earmark-text text-primary me-2 fs-5"></i>
            <span class="fw-semibold">Actividades registradas por mí</span>
          </div>

          <form id="formFiltroMisActividadesZoo" action="<?php echo getUrl('ActividadesListZoo', 'ActividadesListZoo', 'filtro', false, 'ajax'); ?>" method="POST">
            <div class="row g-2 align-items-end">

              <div class="col-6 col-md-2">
                <label for="mesFiltro" class="form-label small text-muted mb-1">Mes</label>
                <input type="month" id="mesFiltro" name="mes" class="form-control form-control-sm">
              </div>

              <div class="col-12 col-md-3">
                <label for="selectZoocriadero" class="form-label small text-muted mb-1">Zoocriadero</label>
                <select id="selectZoocriadero" name="codzoocriadero" class="form-select form-select-sm">
                  <option value="">Todos</option>
                  <?php if (isset($zoocriaderos) && $zoocriaderos): ?>
                    <?php while ($z = $zoocriaderos->fetch(PDO::FETCH_ASSOC)): ?>
                      <option value="<?php echo $z['id']; ?>"><?php echo htmlspecialchars($z['nombrezoocriadero']); ?></option>
                    <?php endwhile; ?>
                  <?php endif; ?>
                </select>
              </div>

              <div class="col-12 col-md-3">
                <label for="selectTipoActividad" class="form-label small text-muted mb-1">Tipo de actividad</label>
                <select id="selectTipoActividad" name="tipoactividad" class="form-select form-select-sm">
                  <option value="">Todos</option>
                  <option value="ALIMENTACIÓN">Alimentación</option>
                  <option value="RECOLECCIÓN">Nacidos / Muertos</option>
                  <option value="LIMPIEZA">Limpieza</option>
                  <option value="AJUSTE DE NIVEL">Ajuste de nivel</option>
                  <option value="LAVADO">Lavado</option>
                </select>
              </div>

              <div class="col-12 col-md-2 d-grid">
                <button type="submit" class="btn btn-primary btn-sm">
                  <i class="bi bi-funnel me-1"></i>Filtrar
                </button>
              </div>

            </div>
          </form>
        </div>

        <div class="table-responsive">
          <table class="table table-striped align-middle mb-0" id="tablaMisActividadesZoo">
            <thead class="table-dark">
              <tr>
                <th class="ps-4">Fecha</th>
                <th>Tipo de actividad</th>
                <th>Zoocriadero</th>
                <th>Tanque</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Editar</th>
                <th class="text-center">Inhabilitar</th>
              </tr>
            </thead>
            <tbody>
              <?php include __DIR__ . '/filaMisActividadesZoo.php'; ?>
            </tbody>
          </table>
        </div>

      </div>

    </div>
  </div>
</div>

<?php
if (isset($_SESSION['error'])) {
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
if (isset($_SESSION['exito'])) {
?>
<div class="row justify-content-center">
  <div class="col-xl-11">
    <div class="alert alert-success d-flex align-items-center mt-3 mb-0" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>
      <div><?php echo $_SESSION['exito']; ?></div>
    </div>
  </div>
</div>
<?php
  unset($_SESSION['exito']);
}
?>

<script>
  var formFiltroZoo = document.getElementById('formFiltroMisActividadesZoo');
  if (formFiltroZoo) {
    formFiltroZoo.addEventListener('submit', function (evento) {
      evento.preventDefault();

      var datos = new FormData(formFiltroZoo);
      var tbody = document.querySelector('#tablaMisActividadesZoo tbody');

      fetch(formFiltroZoo.action, { method: 'POST', body: datos })
        .then(function (respuesta) { return respuesta.text(); })
        .then(function (html) {
          tbody.innerHTML = html;
        })
        .catch(function () {
          tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger py-4">Ocurrió un error al filtrar. Intenta nuevamente.</td></tr>';
        });
    });
  }
</script>

<?php include_once __DIR__ . '/../partials/modalFormulario.php'; ?>
