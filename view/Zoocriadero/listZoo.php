<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-10">

      <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-semibold mb-1">Zoocriaderos</h4>
          <p class="text-muted small mb-0">Consulta y administra los zoocriaderos registrados en el sistema.</p>
        </div>
        <button type="button" class="btn btn-primary px-3"
                onclick="cargarFormularioModal('<?php echo getUrl('Zoocriaderos','Zoocriaderos','create') ?>', 'Registrar zoocriadero', 'zoocriaderoFormRegistro', '<?php echo getUrl('Zoocriaderos','Zoocriaderos','list') ?>')">
          <i class="bi bi-plus-lg me-1"></i>Nuevo zoocriadero
        </button>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <span class="fw-semibold">
            Zoocriaderos registrados
          </span>
          <div class="input-group input-group-sm" style="max-width: 260px;">
            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
            <input type="text" id="buscadorZoocriaderos" class="form-control" placeholder="Buscar zoocriadero..."
                  data-url="<?php echo getUrl('Zoocriaderos','Zoocriaderos','filtro', false, 'ajax'); ?>">
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-striped align-middle mb-0" id="tablaZoocriaderos">
            <thead class="table-dark">
              <tr>
                <th class="ps-4">Nombre</th>
                <th>Dirección</th>
                <th>Comuna</th>
                <th>Barrio</th>
                <th>Encargado</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Editar</th>
                <th class="text-center">Inhabilitar</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $hayZoocriaderos = isset($zoocriaderos) && $zoocriaderos && $zoocriaderos->rowCount() > 0;
                if ($hayZoocriaderos):
                    while($zoo = $zoocriaderos->fetch(PDO::FETCH_ASSOC)):
              ?>
              <tr>
                <td class="ps-4"><?php echo htmlspecialchars($zoo['nombre']); ?></td>
                <td><?php echo htmlspecialchars($zoo['direccion']); ?></td>
                <td><?php echo htmlspecialchars($zoo['comuna']); ?></td>
                <td><?php echo htmlspecialchars($zoo['barrio']); ?></td>
                <td><?php echo htmlspecialchars($zoo['encargado']); ?></td>
                <td class="text-center">
                  <?php if ($zoo['estado'] === 'Activo'): ?>
                    <span class="badge bg-success-subtle text-success-emphasis">Activo</span>
                  <?php elseif ($zoo['estado'] === 'Mantenimiento'): ?>
                    <span class="badge bg-warning-subtle text-warning-emphasis">Mantenimiento</span>
                  <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Inactivo</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
                          onclick="cargarFormularioModal('<?php echo getUrl('Zoocriaderos','Zoocriaderos','getUpdate',array('id'=>$zoo['id'])) ?>', 'Editar zoocriadero', 'zoocriaderoFormEdicion', '<?php echo getUrl('Zoocriaderos','Zoocriaderos','list') ?>')">
                    <i class="bi bi-pencil-fill"></i>
                  </button>
                </td>
                <td class="text-center">
                  <a href="<?php echo getUrl('Zoocriaderos','Zoocriaderos','delete',array('id'=>$zoo['id'])) ?>"
                     class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
                     onclick="return confirm('¿Seguro que deseas inhabilitar el zoocriadero <?php echo $zoo['nombre']; ?>?')">
                    <i class="bi bi-eye-slash"></i>
                  </a>
                </td>
              </tr>
              <?php
                    endwhile;
                else:
              ?>
              <tr>
                <td colspan="8" class="text-center text-muted py-5">
                  <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                  No hay zoocriaderos registrados todavía.
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
