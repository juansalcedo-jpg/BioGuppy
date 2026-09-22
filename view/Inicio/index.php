<?php
$nombreUsuario = $_SESSION['usu_nombre'] ?? '';
?>
<div class="container-fluid py-4">
  <div class="row justify-content-center align-items-center g-5">

    <div class="col-lg-4 text-center">
      <img src="/BioGuppy/img/logosinfondo.png" alt="BioGuppy" class="img-fluid" style="max-width:260px;">
    </div>

    <div class="col-lg-7">
      <div class="p-4 h-100 rounded-4" style="background:linear-gradient(to right,#3366CC,#004884);">
        <h3 class="fw-semibold mb-4 text-white">
          Bienvenid@ <span style="color:#ffffff; text-decoration:underline;"><?php echo htmlspecialchars($nombreUsuario); ?></span>
        </h3>
        <p class="mb-0 lh-lg text-white" style="font-size:1.05rem;">
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

  <div class="row justify-content-center align-items-center g-4 mt-4">

    <div class="col-lg-7">
      <div class="p-4 h-100 rounded-4" style="background:linear-gradient(to left,#3366CC,#004884);">
        <h5 class="fw-semibold mb-3 text-white">
          Secretaría de Salud Pública de Santiago de Cali
        </h5>
        <p class="mb-0 lh-lg text-white" style="font-size:0.98rem;">
          La Secretaría de Salud Pública de Santiago de Cali, a través de la Alcaldía Distrital,
          es la entidad encargada de liderar las acciones de prevención, vigilancia y control de
          enfermedades transmitidas por vectores (ETV) en la ciudad, entre ellas el dengue. Dentro
          de esta labor, el Subgrupo ETV coordina estrategias de control biológico como la cría y
          siembra de peces guppies (<em>Poecilia reticulata</em>) en depósitos de agua, con el fin
          de reducir la proliferación del mosquito <em>Aedes aegypti</em>. BioGuppy nace como
          respuesta a la necesidad de esta Secretaría de contar con una herramienta tecnológica que
          sistematice, agilice y haga trazable la información generada por el zoocriadero y el
          trabajo de campo, apoyando así la toma de decisiones y el fortalecimiento de la salud
          pública en Cali.
        </p>
      </div>
    </div>

    <div class="col-lg-4 text-center">
      <img src="/BioGuppy/img/logo-alcaldia-cali.png" alt="Secretaría de Salud Pública de Santiago de Cali" class="img-fluid" style="max-width:260px;">
    </div>

  </div>
</div>