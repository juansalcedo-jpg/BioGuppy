<?php
?>
<div class="container-fluid py-3">

  <!-- Encabezado -->
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-3 p-4">
      <div>
        <h4 class="fw-bold text-dark mb-1">Permisos del rol: <?php echo htmlspecialchars($rol['nombrerol']); ?></h4>
        <p class="text-muted mb-0">Activa los módulos que puede usar este rol. Los que queden apagados no aparecen en su menú.</p>
      </div>
      <a href="<?php echo getUrl('Roles', 'Roles', 'listRol') ?>" class="btn btn-outline-secondary px-4 py-2">
        <i class="bi bi-arrow-left me-2"></i>Volver a roles
      </a>
    </div>
  </div>

  <!-- Alertas -->
  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger shadow-sm"><i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo $_SESSION['error']; ?></div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <?php if (isset($_SESSION['exito'])): ?>
    <div class="alert alert-success shadow-sm"><i class="bi bi-check-circle-fill me-2"></i><?php echo htmlspecialchars($_SESSION['exito']); ?></div>
    <?php unset($_SESSION['exito']); ?>
  <?php endif; ?>

  <?php if ($rol['estado'] !== 'A'): ?>
    <div class="alert alert-warning shadow-sm"><i class="bi bi-pause-circle-fill me-2"></i>Este rol está inactivo: nadie podrá entrar con él hasta que lo actives.</div>
  <?php endif; ?>

  <!-- Tabla de módulos -->
  <form action="<?php echo getUrl('Roles', 'Roles', 'postGuardarPermisos') ?>" method="post">
    <input type="hidden" name="codrol" value="<?php echo (int) $rol['codrol']; ?>">

    <div class="card border-0 shadow-sm">
      <div class="card-header bg-transparent border-0 pt-4 px-4 pb-3 d-flex justify-content-between align-items-center">
        <span class="fw-semibold text-dark">Módulos del sistema</span>
        <span class="text-muted small"><?php echo (int) $totalAsignados; ?> de <?php echo (int) $totalModulos; ?> activos</span>
      </div>

      <div class="card-body px-0 pb-0">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead class="table-light text-secondary text-uppercase small">
              <tr>
                <th class="py-3 ps-4">Módulo</th>
                <th class="py-3 text-center" style="width: 120px;">Acceso</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($modulosPorSeccion)): ?>
                <tr>
                  <td colspan="2" class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                    No hay módulos registrados.
                  </td>
                </tr>
              <?php endif; ?>

              <?php foreach ($modulosPorSeccion as $seccion => $modulos): ?>
                <tr class="table-light">
                  <td colspan="2" class="ps-4 py-2 small fw-bold text-secondary"><?php echo htmlspecialchars($seccion); ?></td>
                </tr>

                <?php foreach ($modulos as $mod):
                  $idCheck   = 'mod' . (int) $mod['codmodulo'];
                  $bloqueado = $esMiRol && strtolower($mod['carpeta']) === 'roles';
                  $activo    = $mod['asignado'] || $bloqueado;
                ?>
                  <tr>
                    <td class="ps-4 py-3">
                      <label for="<?php echo $idCheck; ?>" class="d-flex align-items-center gap-3 mb-0">
                        <i class="bi <?php echo htmlspecialchars($mod['icono']); ?> fs-5 text-primary"></i>
                        <span>
                          <span class="fw-semibold text-dark d-block"><?php echo htmlspecialchars($mod['nombremodulo']); ?></span>
                          <span class="small text-muted">
                            <?php echo $bloqueado
                              ? 'Es tu propio rol, no puedes quitarte este módulo.'
                              : htmlspecialchars($mod['descripcion'] ?? ''); ?>
                          </span>
                        </span>
                      </label>
                    </td>
                    <td class="text-center py-3">
                      <div class="form-check form-switch d-inline-block m-0">
                        <input class="form-check-input" type="checkbox" role="switch"
                               id="<?php echo $idCheck; ?>"
                               name="modulos[]"
                               value="<?php echo (int) $mod['codmodulo']; ?>"
                               <?php echo $activo ? 'checked' : ''; ?>
                               <?php echo $bloqueado ? 'disabled' : ''; ?>>
                      </div>
                      <?php if ($bloqueado): ?>
                        <!-- Un checkbox deshabilitado no se envía, por eso va este oculto. -->
                        <input type="hidden" name="modulos[]" value="<?php echo (int) $mod['codmodulo']; ?>">
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card-footer bg-transparent d-flex justify-content-end gap-2 p-4">
        <a href="<?php echo getUrl('Roles', 'Roles', 'listRol') ?>" class="btn btn-light border px-4 py-2 fw-semibold text-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold shadow-sm">
          <i class="bi bi-check-lg me-1"></i> Guardar permisos
        </button>
      </div>
    </div>
  </form>

</div>
