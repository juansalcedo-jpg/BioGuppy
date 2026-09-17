<div id="zoocriaderoFormEdicion">

    <div class="container-fluid py-2">

        <form
            action="<?php echo getUrl(
                'Zoocriadero',
                'Zoocriadero',
                'postUpdateZoo'
            ); ?>"
            method="post"
        >

            <!-- ID DEL ZOOCRIADERO -->
            <input
                type="hidden"
                name="codzoocriadero"
                value="<?php
                    echo htmlspecialchars(
                        $zoocriadero['codzoocriadero']
                    );
                ?>"
            >


            <!-- ========================================== -->
            <!-- NOMBRE Y DIRECCIÓN -->
            <!-- ========================================== -->

            <div class="row g-3">

                <!-- NOMBRE -->
                <div class="col-md-6">

                    <label
                        for="nombre"
                        class="form-label fw-semibold"
                    >
                        Nombre *
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="nombre"
                        name="nombre"
                        value="<?php
                            echo htmlspecialchars(
                                $zoocriadero['nombrezoocriadero']
                            );
                        ?>"
                        required
                    >

                </div>


                <!-- DIRECCIÓN -->
                <div class="col-md-6">

                    <label
                        for="direccion"
                        class="form-label fw-semibold"
                    >
                        Dirección *
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="direccion"
                        name="direccion"
                        value="<?php
                            echo htmlspecialchars(
                                $zoocriadero['direccion']
                            );
                        ?>"
                        required
                    >

                </div>

            </div>


            <!-- ========================================== -->
            <!-- COMUNA - BARRIO - ENCARGADO -->
            <!-- ========================================== -->

            <div class="row g-3 mt-2">


                <!-- COMUNA -->
                <div class="col-md-4">

                    <label
                        for="codcomuna"
                        class="form-label fw-semibold"
                    >
                        Comuna *
                    </label>

                    <select
                        class="form-select"
                        id="codcomuna"
                        name="codcomuna"
                        required
                    >

                        <option value="">
                            Seleccione una comuna...
                        </option>


                        <?php

                        if (isset($comunas)) {

                            while (
                                $comuna = $comunas->fetch(PDO::FETCH_ASSOC)
                            ) {

                                $comunaSeleccionada =
                                    $comuna['codcomuna'] ==
                                    $zoocriadero['codcomuna'];

                        ?>

                            <option
                                value="<?php
                                    echo htmlspecialchars(
                                        $comuna['codcomuna']
                                    );
                                ?>"
                                <?php
                                    if ($comunaSeleccionada) {
                                        echo 'selected';
                                    }
                                ?>
                            >

                                <?php
                                    echo htmlspecialchars(
                                        $comuna['nombrecomuna']
                                    );
                                ?>

                            </option>

                        <?php

                            }

                        }

                        ?>

                    </select>

                </div>


                <!-- BARRIO -->
                <div class="col-md-4">

                    <label
                        for="codbarrio"
                        class="form-label fw-semibold"
                    >
                        Barrio *
                    </label>

                    <select
                        class="form-select"
                        id="codbarrio"
                        name="codbarrio"
                        required
                    >

                        <option value="">
                            Seleccione un barrio...
                        </option>


                        <?php

                        if (isset($barrios)) {

                            while (
                                $barrio = $barrios->fetch(PDO::FETCH_ASSOC)
                            ) {

                                $barrioSeleccionado =
                                    $barrio['codbarrio'] ==
                                    $zoocriadero['codbarrio'];

                                $perteneceComuna =
                                    $barrio['codcomuna'] ==
                                    $zoocriadero['codcomuna'];

                        ?>

                            <option
                                value="<?php
                                    echo htmlspecialchars(
                                        $barrio['codbarrio']
                                    );
                                ?>"

                                data-comuna="<?php
                                    echo htmlspecialchars(
                                        $barrio['codcomuna']
                                    );
                                ?>"

                                <?php
                                    if ($barrioSeleccionado) {
                                        echo 'selected';
                                    }
                                ?>

                                <?php
                                    if (!$perteneceComuna) {
                                        echo 'hidden disabled';
                                    }
                                ?>
                            >

                                <?php
                                    echo htmlspecialchars(
                                        $barrio['nombrebarrio']
                                    );
                                ?>

                            </option>

                        <?php

                            }

                        }

                        ?>

                    </select>

                </div>


                <!-- ENCARGADO -->
                <div class="col-md-4">

                    <label
                        for="encargado"
                        class="form-label fw-semibold"
                    >
                        Encargado *
                    </label>

                    <input
                        type="text"
                        class="form-control"
                        id="encargado"
                        name="encargado"
                        value="<?php
                            echo htmlspecialchars(
                                $zoocriadero['auxencargado'] ?? ''
                            );
                        ?>"
                        placeholder="Nombre del encargado"
                        required
                    >

                </div>

            </div>


            <!-- ========================================== -->
            <!-- ESTADO -->
            <!-- ========================================== -->

            <div class="row mt-4">

                <div class="col-md-4">

                    <label
                        class="form-label fw-semibold d-block"
                    >
                        Estado
                    </label>

                    <div class="form-check form-switch">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            id="estado"
                            name="estado"
                            value="A"

                            <?php

                            if (
                                isset($zoocriadero['estado']) &&
                                $zoocriadero['estado'] === 'A'
                            ) {

                                echo 'checked';

                            }

                            ?>
                        >

                        <label
                            class="form-check-label"
                            for="estado"
                        >
                            Activo
                        </label>

                    </div>

                </div>

            </div>


            <hr class="mt-4">


            <!-- ========================================== -->
            <!-- BOTONES -->
            <!-- ========================================== -->

            <div class="d-flex justify-content-end gap-2 mt-3">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Cancelar
                </button>


                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    <i class="bi bi-check-lg me-1"></i>

                    Guardar cambios

                </button>

            </div>

        </form>


        <!-- ========================================== -->
        <!-- MENSAJES DE ERROR -->
        <!-- ========================================== -->

        <?php

        if (isset($_SESSION['error'])) {

        ?>

            <div
                class="alert alert-danger mt-3"
                role="alert"
            >

                <?php

                    echo htmlspecialchars(
                        $_SESSION['error']
                    );

                ?>

            </div>

        <?php

            unset($_SESSION['error']);

        }

        ?>

    </div>

</div>