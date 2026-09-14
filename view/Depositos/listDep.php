<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-10">

      <div class="d-flex flex-wrap align-items-end justify-content-between mb-4 gap-2">
        <div>
          <h4 class="fw-semibold mb-1">Depósitos</h4>
          <p class="text-muted small mb-0">Consulta y registra los depósitos encontrados en los sitios de terreno.</p>
        </div>
        <button type="button" class="btn btn-primary px-3"
                onclick="cargarFormularioModal('<?php echo getUrl('Depositos','Depositos','create') ?>', 'Registrar depósito', 'depositoFormRegistro', '<?php echo getUrl('Depositos','Depositos','listDep') ?>')">
          <i class="bi bi-plus-lg me-1"></i>Nuevo depósito
        </button>
      </div>

      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
          <span class="fw-semibold">
            <i class="bi bi-bucket me-2 text-primary"></i>Depósitos registrados
          </span>
          <div class="input-group input-group-sm" style="max-width: 260px;">
            <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
            <input type="text" id="buscadorDepositos" class="form-control" placeholder="Buscar depósito..."
                  data-url="<?php echo getUrl('Depositos','Depositos','filtro', false, 'ajax'); ?>">
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-striped align-middle mb-0" id="tablaDepositos">
            <thead class="table-dark">
              <tr>
                <th class="ps-4">Sitio</th>
                <th>Comuna</th>
                <th>Barrio</th>
                <th>Tipo de depósito</th>
                <th>Fecha registro</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Editar</th>
                <th class="text-center">Inhabilitar</th>
              </tr>
            </thead>
            <tbody>
              <?php
                $hayDepositos = isset($depositos) && $depositos && $depositos->rowCount() > 0;
                if ($hayDepositos):
                    while($dep = $depositos->fetch(PDO::FETCH_ASSOC)):
              ?>
              <tr>
                <td class="ps-4"><?php echo htmlspecialchars($dep['sitio']); ?></td>
                <td><?php echo htmlspecialchars($dep['comuna']); ?></td>
                <td><?php echo htmlspecialchars($dep['barrio']); ?></td>
                <td><?php echo htmlspecialchars($dep['tipo_deposito']); ?></td>
                <td><?php echo htmlspecialchars($dep['fecha_registro']); ?></td>
                <td class="text-center">
                  <?php if ($dep['estado'] === 'Activo'): ?>
                    <span class="badge bg-success-subtle text-success-emphasis">Activo</span>
                  <?php else: ?>
                    <span class="badge bg-secondary-subtle text-secondary-emphasis">Inactivo</span>
                  <?php endif; ?>
                </td>
                <td class="text-center">
                  <button type="button" class="btn btn-outline-primary btn-icon rounded-circle" title="Editar"
                          onclick="cargarFormularioModal('<?php echo getUrl('Depositos','Depositos','getUpdate',array('id'=>$dep['id'])) ?>', 'Editar depósito', 'depositoFormEdicion', '<?php echo getUrl('Depositos','Depositos','listDep') ?>')">
                    <i class="bi bi-pencil-fill"></i>
                  </button>
                </td>
                <td class="text-center">
                  <a href="<?php echo getUrl('Depositos','Depositos','delete',array('id'=>$dep['id'])) ?>"
                     class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
                     onclick="return confirm('¿Seguro que deseas inhabilitar este depósito?')">
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
                  No hay depósitos registrados todavía.
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
