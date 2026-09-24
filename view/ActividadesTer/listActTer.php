<?php
$tipo = strtoupper($actividad['nombreactividad'] ?? '');
?>

<div class="row justify-content-center">
  <div class="col-xl-11">

    <?php if (isset($_SESSION['error'])): ?>
      <div class="alert alert-danger d-flex align-items-center mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <h4 class="fw-semibold mb-1">
          Editar actividad — <?php echo $tipo; ?>
        </h4>
      </div>
      <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['exito'])): ?>
      <div class="alert alert-success d-flex align-items-center mb-3">
        <i class="bi bi-check-circle-fill me-2"></i>
        <div><?php echo htmlspecialchars($_SESSION['exito']); ?></div>
      </div>
      <?php unset($_SESSION['exito']); ?>
    <?php endif; ?>

    <?php if (!empty($errorFechas)): ?>
      <div class="alert alert-danger d-flex align-items-center mb-3">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        <div><?php echo htmlspecialchars($errorFechas); ?></div>
      </div>
    <?php endif; ?>

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
      <div>
        <h4 class="fw-bold text-dark mb-1">Historial de Actividades — Terreno</h4>
        <p class="text-muted small mb-0">Consulta las actividades registradas en terreno.</p>
      </div>
    </div>

 



    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">


      <div class="card-header bg-white border-bottom py-3 px-4">

        <div class="d-flex align-items-center mb-3">

          <i class="bi bi-funnel text-primary me-2 fs-5"></i>

          <span class="fw-semibold">
            Filtros
          </span>

        </div>


        <form action="index.php" method="GET">

          <input
            type="hidden"
            name="modulo"
            value="ActividadesTer">

          <input
            type="hidden"
            name="controlador"
            value="ActividadesTer">

          <input
            type="hidden"
            name="funcion"
            value="listActTer">


          <div class="row g-3 align-items-end">


            <div class="col-md-2">

              <label
                for="fechaDesde"
                class="form-label fw-semibold">

                Desde

              </label>

              <input
                type="date"
                id="fechaDesde"
                name="fechaDesde"
                class="form-control"
                max="<?php echo date('Y-m-d'); ?>"
                value="<?php echo htmlspecialchars($_GET['fechaDesde'] ?? ''); ?>">

            </div>


            <div class="col-md-2">

              <label
                for="fechaHasta"
                class="form-label fw-semibold">

                Hasta

              </label>

              <input
                type="date"
                id="fechaHasta"
                name="fechaHasta"
                class="form-control"
                max="<?php echo date('Y-m-d'); ?>"
                value="<?php echo htmlspecialchars($_GET['fechaHasta'] ?? ''); ?>">

            </div>


            <div class="col-md-3">

              <label
                for="codsitio"
                class="form-label fw-semibold">

                Sitio

              </label>

              <select
                id="codsitio"
                name="codsitio"
                class="form-select">

                <option value="">
                  Todos
                </option>

                <?php if (isset($sitios) && $sitios): ?>

                  <?php while ($sitio = $sitios->fetch(PDO::FETCH_ASSOC)): ?>

                    <option
                      value="<?php echo $sitio['codsitio']; ?>"
                      <?php
                      echo (($_GET['codsitio'] ?? '') == $sitio['codsitio'])
                        ? 'selected'
                        : '';
                      ?>>

                      <?php echo htmlspecialchars($sitio['nombresitio']); ?>

                    </option>

                  <?php endwhile; ?>

                <?php endif; ?>

              </select>

            </div>


            <div class="col-md-3">

              <label
                for="codtipoactividad"
                class="form-label fw-semibold">

                Tipo de actividad

              </label>

              <select
                id="codtipoactividad"
                name="codtipoactividad"
                class="form-select">

                <option value="">
                  Todas
                </option>

                <?php if (isset($tiposActividad) && $tiposActividad): ?>

                  <?php while ($tipo = $tiposActividad->fetch(PDO::FETCH_ASSOC)): ?>

                    <option
                      value="<?php echo $tipo['codtipoactividad']; ?>"
                      <?php
                      echo (($_GET['codtipoactividad'] ?? '') == $tipo['codtipoactividad'])
                        ? 'selected'
                        : '';
                      ?>>

                      <?php echo htmlspecialchars($tipo['nombreactividad']); ?>

                    </option>

                  <?php endwhile; ?>

                <?php endif; ?>

              </select>

            </div>


            <div class="col-md-2 d-grid">

              <button
                type="submit"
                class="btn btn-primary">

                <i class="bi bi-funnel me-1"></i>

                Filtrar

              </button>

            </div>

          </div>

        </form>

      </div>


      <div class="table-responsive">

        <table class="table table-hover align-middle mb-0">

          <thead class="table-dark">

            <tr>

              <th class="ps-4">
                Fecha
              </th>

              <th>
                Tipo de actividad
              </th>

              <th>
                Sitio
              </th>

              <th>
                Responsable
              </th>

              <th>
                Observaciones
              </th>

              <th class="text-center">
                Estado
              </th>

              <th class="text-center">
                Acción
              </th>

            </tr>

          </thead>


          <tbody>

            <?php

            $filas = (isset($actividades) && $actividades)
              ? $actividades->fetchAll(PDO::FETCH_ASSOC)
              : [];

            ?>

            <?php if (!empty($filas)): ?>

              <?php foreach ($filas as $act): ?>

                <tr>

                  <td class="ps-4">

                    <?php echo htmlspecialchars($act['fecha']); ?>

                  </td>


                  <td class="fw-semibold">

                    <?php echo htmlspecialchars($act['tipo_actividad']); ?>

                  </td>


                  <td>

                    <?php echo htmlspecialchars($act['sitio']); ?>

                  </td>


                  <td>

                    <?php echo htmlspecialchars($act['responsable']); ?>

                  </td>


                  <td>

                    <?php
                    echo htmlspecialchars(
                      $act['observaciones'] ?? ''
                    );
                    ?>

                  </td>


                  <td class="text-center">

                    <?php if ($act['estado'] === 'A'): ?>

                      <span class="badge bg-success">
                        Activo
                      </span>

                    <?php else: ?>

                      <span class="badge bg-danger">
                        Inactivo
                      </span>

                    <?php endif; ?>

                  </td>


                  <td class="text-center">

                    <?php if ($act['estado'] === 'A'): ?>

                      <a
                        href="<?php echo getUrl('ActividadesListTer', 'ActividadesListTer', 'delete', array('id' => $act['codactividad'])); ?>"
                        class="btn btn-outline-danger btn-icon rounded-circle"
                        title="Inhabilitar">

                        <i class="bi bi-slash-circle"></i>

                      </a>

                    <?php else: ?>

                      <a
                        href="<?php echo getUrl('ActividadesListTer', 'ActividadesListTer', 'delete', array('id' => $act['codactividad'])); ?>"
                        class="btn btn-outline-success btn-icon rounded-circle"
                        title="Activar">

                        <i class="bi bi-check-lg"></i>

                      </a>

                    <?php endif; ?>

                  </td>

                </tr>

              <?php endforeach; ?>

            <?php else: ?>

              <tr>

                <td
                  colspan="7"
                  class="text-center text-muted py-5">

                  <i class="bi bi-inbox fs-3 d-block mb-2"></i>

                  No se encontraron actividades.

                </td>

              </tr>

            <?php endif; ?>

          </tbody>

        </table>

      </div>

    </div>

  </div>

</div>

</div>