<?php
$nombreUsuario = $_SESSION['usu_nombre'] ?? '';
?>
<div class="container-fluid py-4">
  <div class="row justify-content-center align-items-center g-5">

    <div class="col-lg-4 text-center">
      <img src="/BioGuppy/img/logosinfondo.png" alt="BioGuppy" class="img-fluid" style="max-width:260px;">
    </div>

    <div class="col-lg-7">
      <h3 class="fw-semibold mb-4">
        Bienvenid@ <span style="color:#159EE8;"><?php echo htmlspecialchars($nombreUsuario); ?></span>
      </h3>
      <p class="lh-lg text-secondary" style="font-size:1.05rem;">
        BioGuppy es el sistema de información que apoya el Control Biológico del dengue mediante
        peces guppies (Poecilia reticulata), desarrollado para el Subgrupo de Prevención, Vigilancia
        y Control de Enfermedades Transmitidas por Vectores de la Secretaría de Salud Pública de
        Santiago de Cali. Su propósito es facilitar el registro, el seguimiento y la trazabilidad de
        las actividades del zoocriadero y del trabajo en campo, para fortalecer la toma de decisiones
        y ampliar la cobertura del control biológico en la ciudad.
      </p>
    </div>

  </div>
</div>
