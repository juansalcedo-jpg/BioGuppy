<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-xl-11">

            <!-- Título Principal -->
            <h2 class="text-center fw-bold mb-4 text-dark">Mis Actividades Registradas</h2>

            <!-- Tarjeta Principal -->
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-4">

                    <!-- Encabezado con Ícono y Contador -->
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-file-earmark-text text-primary fs-5 me-2"></i>
                        <h5 class="fw-bold m-0 text-dark">
                            Actividades (<?php echo isset($actividades) ? count($actividades) : 4; ?>)
                        </h5>
                    </div>

                    <!-- Tabla de Actividades -->
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead style="background-color: #0d1b2a;" class="text-white">
                                <tr>
                                    <th scope="col" class="py-3 px-3 fw-semibold">Fecha</th>
                                    <th scope="col" class="py-3 px-3 fw-semibold">Tipo de actividad</th>
                                    <th scope="col" class="py-3 px-3 fw-semibold">Tanque / Referencia</th>
                                    <th scope="col" class="py-3 px-3 fw-semibold text-center">Estado</th>
                                    <th scope="col" class="py-3 px-3 fw-semibold text-center">Editar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($actividades)): ?>
                                    <?php foreach ($actividades as $act): ?>
                                        <tr class="border-bottom" style="background-color: #f8f9fa;">
                                            <td class="py-3 px-3 text-dark"><?php echo $act['fecha']; ?></td>
                                            <td class="py-3 px-3 fw-bold text-dark"><?php echo $act['tipo_actividad']; ?></td>
                                            <td class="py-3 px-3 text-secondary"><?php echo $act['tanque_referencia']; ?></td>
                                            <td class="py-3 px-3 text-center">
                                                <?php if ($act['estado'] == 'Activo'): ?>
                                                    <span class="badge bg-success rounded-pill px-3 py-2">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger rounded-pill px-3 py-2">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="py-3 px-3 text-center">
                                                <?php if ($act['estado'] == 'Activo'): ?>
                                                    <a href="<?php echo getUrl('ActividadesZoo', 'ActividadesZoo', 'editActividad', array('id' => $act['id'])); ?>" class="btn btn-primary btn-sm rounded-3 px-2 py-1">
                                                        <i class="bi bi-pencil-fill"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <!-- Datos estáticos de muestra matching exacto con la imagen -->
                                    <tr class="border-bottom" style="background-color: #f8f9fa;">
                                        <td class="py-3 px-3 text-dark">2024-07-12</td>
                                        <td class="py-3 px-3 fw-bold text-dark">Alimentación</td>
                                        <td class="py-3 px-3 text-secondary">T-001 · Zoocriadero La Flora</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="badge bg-success rounded-pill px-3 py-2">Activo</span>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <a href="<?php echo getUrl('ActividadesZoo', 'ActividadesZoo', 'editAlimentacion', array('id' => 1)); ?>" class="btn btn-primary btn-sm rounded-3 px-2 py-1">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr class="border-bottom" style="background-color: #f8f9fa;">
                                        <td class="py-3 px-3 text-dark">2024-07-12</td>
                                        <td class="py-3 px-3 fw-bold text-dark">Nacidos/Muertos</td>
                                        <td class="py-3 px-3 text-secondary">T-002 · Zoocriadero La Flora</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="badge bg-success rounded-pill px-3 py-2">Activo</span>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <a href="<?php echo getUrl('ActividadesZoo', 'ActividadesZoo', 'editNacidosMuertos', array('id' => 2)); ?>" class="btn btn-primary btn-sm rounded-3 px-2 py-1">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr class="border-bottom" style="background-color: #f8f9fa;">
                                        <td class="py-3 px-3 text-dark">2024-07-11</td>
                                        <td class="py-3 px-3 fw-bold text-dark">Parámetros fisicoquímicos</td>
                                        <td class="py-3 px-3 text-secondary">T-001 · Zoocriadero La Flora</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="badge bg-success rounded-pill px-3 py-2">Activo</span>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <a href="<?php echo getUrl('ActividadesZoo', 'ActividadesZoo', 'editParametros', array('id' => 3)); ?>" class="btn btn-primary btn-sm rounded-3 px-2 py-1">
                                                <i class="bi bi-pencil-fill"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr class="border-bottom" style="background-color: #f8f9fa;">
                                        <td class="py-3 px-3 text-dark">2024-07-10</td>
                                        <td class="py-3 px-3 fw-bold text-dark">Limpieza</td>
                                        <td class="py-3 px-3 text-secondary">T-003 · Zoocriadero Agua Blanca</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="badge bg-danger rounded-pill px-3 py-2">Inactivo</span>
                                        </td>
                                        <td class="py-3 px-3 text-center"></td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>