<?php if ($Roles && $Roles->rowCount() > 0): ?>
    <?php while ($rol = $Roles->fetch(PDO::FETCH_ASSOC)):
              ?>
                  <tr>
                    <td class="ps-4"><?php echo htmlspecialchars($rol['nombrerol']); ?></td>
                    <td class="text-center">
                      <?php if ($rol['estado'] === 'A'): ?>
                        <span class="badge bg-success">Activo</span>
                      <?php else: ?>
                        <span class="badge bg-danger">Inactivo</span>
                      <?php endif; ?>
                    </td>
                    <td class="text-center">
                      <a href="<?php echo getUrl('Roles', 'Roles', 'editRol', ['id' => $rol['codrol']]) ?>"
                        class="btn btn-outline-primary btn-icon rounded-circle" title="Editar">
                        <i class="bi bi-pencil-fill"></i>
                      </a>
                    </td>
                    <td class="text-center">
                      <?php if ($rol['estado'] === 'A'): ?>
                        <a href="<?php echo getUrl('Roles', 'Roles', 'activacion', array('id' => $rol['codrol'], 'estado' => $rol['estado'])) ?>"
                          class="btn btn-outline-danger btn-icon rounded-circle" title="Inhabilitar"
                          onclick="return confirm('¿Seguro que deseas inhabilitar este tipo de tanque?')">
                          <i class="bi bi-slash-circle"></i>
                        </a>
                      <?php else: ?>
                        <a href="<?php echo getUrl('Roles', 'Roles', 'activacion', array('id' => $rol['codrol'], 'estado' => $rol['estado'])) ?>"
                          class="btn btn-outline-success btn-icon rounded-circle" title="Activar">
                          <i class="bi bi-check-lg"></i>
                        </a>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php
                endwhile;
              else:
                ?>
                <tr>
                  <td colspan="4" class="text-center text-muted py-5">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                    No hay roles registrados todavía.
                  </td>
                </tr>
              <?php endif; ?>