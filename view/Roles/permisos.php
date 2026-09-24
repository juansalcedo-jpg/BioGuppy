<?php
/*
 * Variables que llegan del controlador:
 *   $rol               -> codrol, nombrerol, estado
 *   $modulosPorSeccion -> [seccion => [ {codmodulo, nombremodulo, descripcion, icono, carpeta, asignado} ]]
 *   $totalModulos, $totalAsignados
 *   $esMiRol           -> true si es el rol del usuario en sesión
 */
?>
<style>
  .permisos-rol { max-width: 980px; }

  .permisos-resumen {
    background: #10254a;
    color: #fff;
    border-radius: .9rem;
  }
  .permisos-resumen .contador {
    font-size: 2.1rem;
    font-weight: 700;
    line-height: 1;
  }
  .permisos-resumen .contador small {
    font-size: 1rem;
    font-weight: 400;
    color: #9fb2d4;
  }
  .permisos-resumen .barra {
    height: 6px;
    background: rgba(255,255,255,.15);
    border-radius: 3px;
    overflow: hidden;
  }
  .permisos-resumen .barra > div {
    height: 100%;
    background: #22c1a4;
    transition: width .25s ease;
  }

  .seccion-permisos .seccion-titulo {
    color: #10254a;
    font-weight: 700;
    font-size: .95rem;
  }

  .modulo-permiso {
    display: flex;
    align-items: center;
    gap: .9rem;
    padding: .85rem 1rem;
    border: 1px solid #dde5f1;
    border-radius: .7rem;
    background: #fff;
    cursor: pointer;
    margin-bottom: .5rem;
    transition: border-color .15s ease, background-color .15s ease;
  }
  .modulo-permiso:hover { border-color: #b8c8e0; }
  .modulo-permiso.is-on {
    border-color: #22c1a4;
    background: #f1fbf8;
  }
  .modulo-permiso.is-bloqueado { cursor: not-allowed; opacity: .85; }

  .modulo-permiso .icono {
    width: 40px; height: 40px;
    flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    border-radius: .6rem;
    background: #eaf1fb;
    color: #24406f;
    font-size: 1.15rem;
  }
  .modulo-permiso.is-on .icono { background: #22c1a4; color: #fff; }

  .modulo-permiso .form-check-input {
    width: 2.6em; height: 1.35em;
    cursor: pointer;
    margin: 0;
  }
  .modulo-permiso .form-check-input:checked {
    background-color: #22c1a4;
    border-color: #22c1a4;
  }
  .modulo-permiso .form-check-input:focus-visible {
    box-shadow: 0 0 0 .2rem rgba(34,193,164,.35);
  }

  .barra-guardar {
    position: sticky;
    bottom: 0;
    background: #fff;
    border-top: 1px solid #dde5f1;
    z-index: 5;
  }
  .barra-guardar .aviso-cambios { display: none; }
  .barra-guardar.hay-cambios .aviso-cambios { display: inline-flex; }
</style>

<div class="container-fluid py-3 permisos-rol">

  <div class="d-flex flex-wrap align-items-start justify-content-between gap-2 mb-3">
    <div>
      <h4 class="fw-bold text-dark mb-1">Permisos de <?php echo htmlspecialchars($rol['nombrerol']); ?></h4>
      <p class="text-muted mb-0">
        Activa los módulos que este rol puede usar. Lo que quede apagado no aparece en su menú
        y tampoco se puede abrir escribiendo la dirección.
      </p>
    </div>
    <a href="<?php echo getUrl('Roles', 'Roles', 'listRol') ?>" class="btn btn-outline-secondary btn-sm px-3">
      <i class="bi bi-arrow-left me-1"></i> Volver a roles
    </a>
  </div>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-exclamation-triangle-fill"></i>
      <div><?php echo $_SESSION['error']; ?></div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['exito'])): ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
      <i class="bi bi-check-circle-fill"></i>
      <div><?php echo htmlspecialchars($_SESSION['exito']); ?></div>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php unset($_SESSION['exito']); ?>
  <?php endif; ?>

  <?php if ($rol['estado'] !== 'A'): ?>
    <div class="alert alert-warning d-flex align-items-center gap-2">
      <i class="bi bi-pause-circle-fill"></i>
      <div>Este rol está inactivo: aunque tenga módulos marcados, nadie podrá entrar con él hasta que lo actives.</div>
    </div>
  <?php endif; ?>

  <form action="<?php echo getUrl('Roles', 'Roles', 'postGuardarPermisos') ?>" method="post" id="formPermisos">
    <input type="hidden" name="codrol" value="<?php echo (int) $rol['codrol']; ?>">

    <!-- Resumen -->
    <div class="permisos-resumen p-4 mb-4">
      <div class="d-flex flex-wrap align-items-end justify-content-between gap-3">
        <div>
          <div class="contador"><span id="contadorAsignados"><?php echo $totalAsignados; ?></span>
            <small>de <?php echo $totalModulos; ?> módulos activados</small></div>
        </div>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-sm btn-light" id="btnActivarTodos">
            <i class="bi bi-check2-all me-1"></i> Activar todos
          </button>
          <button type="button" class="btn btn-sm btn-outline-light" id="btnApagarTodos">
            <i class="bi bi-x-lg me-1"></i> Apagar todos
          </button>
        </div>
      </div>
      <div class="barra mt-3">
        <div id="barraProgreso" style="width: <?php echo $totalModulos ? round($totalAsignados * 100 / $totalModulos) : 0; ?>%;"></div>
      </div>
    </div>

    <?php if (empty($modulosPorSeccion)): ?>
      <div class="text-center text-muted py-5">
        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
        No hay módulos registrados. Ejecuta el script <code>database/permisos_por_modulo.sql</code>.
      </div>
    <?php endif; ?>

    <?php foreach ($modulosPorSeccion as $seccion => $modulos): ?>
      <section class="seccion-permisos mb-4" data-seccion>
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h2 class="seccion-titulo mb-0"><?php echo htmlspecialchars($seccion); ?></h2>
          <button type="button" class="btn btn-link btn-sm text-decoration-none p-0" data-toggle-seccion>
            Activar sección
          </button>
        </div>

        <?php foreach ($modulos as $mod):
          $idCheck    = 'mod' . (int) $mod['codmodulo'];
          $bloqueado  = $esMiRol && strtolower($mod['carpeta']) === 'roles';
          $encendido  = $mod['asignado'] || $bloqueado;
        ?>
          <label class="modulo-permiso <?php echo $encendido ? 'is-on' : ''; ?> <?php echo $bloqueado ? 'is-bloqueado' : ''; ?>" for="<?php echo $idCheck; ?>">
            <span class="icono"><i class="bi <?php echo htmlspecialchars($mod['icono']); ?>"></i></span>

            <span class="flex-grow-1">
              <span class="fw-semibold text-dark d-block"><?php echo htmlspecialchars($mod['nombremodulo']); ?></span>
              <?php if ($bloqueado): ?>
                <span class="small text-muted">Es tu propio rol: no puedes quitarte este módulo.</span>
              <?php elseif (!empty($mod['descripcion'])): ?>
                <span class="small text-muted"><?php echo htmlspecialchars($mod['descripcion']); ?></span>
              <?php endif; ?>
            </span>

            <span class="form-check form-switch m-0">
              <input class="form-check-input check-modulo" type="checkbox" role="switch"
                     id="<?php echo $idCheck; ?>"
                     name="modulos[]"
                     value="<?php echo (int) $mod['codmodulo']; ?>"
                     <?php echo $encendido ? 'checked' : ''; ?>
                     <?php echo $bloqueado ? 'disabled data-bloqueado="1"' : ''; ?>>
            </span>

            <?php if ($bloqueado): ?>
              <!-- Los checkbox deshabilitados no se envían, por eso va oculto. -->
              <input type="hidden" name="modulos[]" value="<?php echo (int) $mod['codmodulo']; ?>">
            <?php endif; ?>
          </label>
        <?php endforeach; ?>
      </section>
    <?php endforeach; ?>

    <div class="barra-guardar py-3 d-flex flex-wrap align-items-center justify-content-end gap-3" id="barraGuardar">
      <span class="aviso-cambios align-items-center gap-1 small text-warning-emphasis">
        <i class="bi bi-circle-fill" style="font-size:.5rem;"></i> Tienes cambios sin guardar
      </span>
      <a href="<?php echo getUrl('Roles', 'Roles', 'permisos', ['id' => $rol['codrol']]) ?>" class="btn btn-light border px-3">Descartar</a>
      <button type="submit" class="btn btn-primary px-4 fw-semibold">
        <i class="bi bi-check-lg me-1"></i> Guardar permisos
      </button>
    </div>
  </form>
</div>

<script>
(function () {
  var form      = document.getElementById('formPermisos');
  var checks    = Array.prototype.slice.call(form.querySelectorAll('.check-modulo'));
  var editables = checks.filter(function (c) { return !c.dataset.bloqueado; });
  var total     = checks.length;
  var contador  = document.getElementById('contadorAsignados');
  var barra     = document.getElementById('barraProgreso');
  var barraGuardar = document.getElementById('barraGuardar');

  // Estado original para saber si hay cambios sin guardar.
  var original = checks.map(function (c) { return c.checked; }).join();

  function refrescar() {
    var activos = checks.filter(function (c) { return c.checked; }).length;
    contador.textContent = activos;
    barra.style.width = total ? Math.round(activos * 100 / total) + '%' : '0%';

    checks.forEach(function (c) {
      c.closest('.modulo-permiso').classList.toggle('is-on', c.checked);
    });

    document.querySelectorAll('[data-seccion]').forEach(function (sec) {
      var propios = Array.prototype.slice.call(sec.querySelectorAll('.check-modulo:not([data-bloqueado])'));
      var todos = propios.length > 0 && propios.every(function (c) { return c.checked; });
      sec.querySelector('[data-toggle-seccion]').textContent = todos ? 'Apagar sección' : 'Activar sección';
    });

    var actual = checks.map(function (c) { return c.checked; }).join();
    barraGuardar.classList.toggle('hay-cambios', actual !== original);
  }

  function marcar(lista, valor) {
    lista.forEach(function (c) { c.checked = valor; });
    refrescar();
  }

  checks.forEach(function (c) { c.addEventListener('change', refrescar); });

  document.getElementById('btnActivarTodos').addEventListener('click', function () { marcar(editables, true); });
  document.getElementById('btnApagarTodos').addEventListener('click', function () { marcar(editables, false); });

  document.querySelectorAll('[data-toggle-seccion]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var sec = btn.closest('[data-seccion]');
      var propios = Array.prototype.slice.call(sec.querySelectorAll('.check-modulo:not([data-bloqueado])'));
      var todos = propios.every(function (c) { return c.checked; });
      marcar(propios, !todos);
    });
  });

  // Aviso si intenta salir con cambios sin guardar.
  var enviando = false;
  form.addEventListener('submit', function () { enviando = true; });
  window.addEventListener('beforeunload', function (e) {
    if (!enviando && barraGuardar.classList.contains('hay-cambios')) {
      e.preventDefault();
      e.returnValue = '';
    }
  });

  refrescar();
})();
</script>
