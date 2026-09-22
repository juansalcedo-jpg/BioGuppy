<?php
$nombreUsuario = $_SESSION['usu_nombre'] ?? '';
?>
<style>
  :root {
    --gov-azul-oscuro:       #10254a;
    --gov-marine:            #3366CC;
    --gov-azul-claro:        #159EE8;
    --gov-naranja:           #F29100;
    --gov-verde:             #4B8A3E;
    --gov-blanco:            #FFFFFF;
    --gov-gris-texto:        #4B4B4B;

    --gov-azul-claro-suave:  rgba(21, 158, 232, 0.14);
    --gov-marine-suave:      rgba(51, 102, 204, 0.06);
  }

  .gov-hero {
    background: linear-gradient(135deg, var(--gov-azul-oscuro) 0%, var(--gov-marine) 100%) !important;
    color: var(--gov-blanco) !important;
  }

  .otro{
        color:  #159EE8;
    }
  .gov-hero-logo-box {
    background-color: var(--gov-azul-claro-suave) !important;
    border: 1px solid var(--gov-azul-claro) !important;
  }
  .gov-hero-muted {
    color: var(--gov-blanco) !important;
    opacity: 0.85;
  }
  .gov-username {
    color: var(--gov-azul-claro) !important;
  }

  .gov-card {
    background-color: var(--gov-blanco) !important;
    color: var(--gov-gris-texto) !important;
  }
  .gov-border-bottom {
    border-color: var(--gov-marine-suave) !important;
  }
  .gov-institution-label {
    color: black !important;
  }
  .gov-main-title {
    color: var(--gov-azul-oscuro) !important;
  }
  .gov-body-text {
    color: var(--gov-gris-texto) !important;
  }
</style>

<section class="gov-hero position-relative pt-5 pb-5">
  <div class="container">
    <div class="d-flex align-items-center gap-2 mb-5">
      <div class="d-flex align-items-center">
  <img src="/BioGuppy/img/logosinfondo.png" alt="BioGuppy" width="60" height="60">
</div>
      <div>
        <div class="fw-semibold">Bio<span class= "otro">Guppy</span></div>
        <div class="small gov-hero-muted">Control Biológico · Secretaría de Salud de Cali</div>
      </div>
    </div>

    <div class="row justify-content-center text-center">
      <div class="col-lg-8">
        <h1 class="display-6 fw-semibold mb-3">
          Bienvenid@, <span><?php echo htmlspecialchars($nombreUsuario); ?></span>
        </h1>
        <p class="lead gov-hero-muted mb-0">
          Sistema de información del Control Biológico del dengue mediante peces guppy
          (<em class="fst-italic">Poecilia reticulata</em>) Aquí puedes registrar y consultar la actividad del zoocriadero
          y terreno.
        </p>
      </div>
    </div>
  </div>
</section>

<div class="container" style="margin-top:-2.75rem;">
  <div class="gov-card rounded-4 shadow-lg p-4 p-md-5 position-relative">

    <div class="d-flex align-items-center gap-3 pb-4 mb-4 ">
      <img src="/BioGuppy/img/logo-secretariaC.png" alt="Alcaldía de Santiago de Cali"
           class="" style="width:58px;height:58px;object-fit:cover; ">
      <div>
        <div class="text-uppercase fw-semibold gov-institution-label small mb-1">Alcaldía Distrital de Santiago de Cali</div>
        <h2 class="h4 fw-semibold gov-main-title mb-0">Secretaría de Salud Pública de Cali</h2>
      </div>
    </div>

    <div class="mx-auto" style="max-width:680px;">
      <p class="lh-lg gov-body-text">
        Através de la Alcaldía Distrital, la Secretaría lidera la prevención, vigilancia
        y control de enfermedades transmitidas por vectores (ETV) en la ciudad, entre ellas
        el dengue. El Subgrupo ETV coordina estrategias de control biológico como la cría y
        siembra de peces guppy en depósitos de agua, con el fin de reducir la proliferación
        del mosquito <em class="fst-italic">Aedes aegypti</em>.
      </p>
      <p class="lh-lg gov-body-text mb-0">
        BioGuppy nace como respuesta a la necesidad de esta Secretaría de contar con una
        herramienta tecnológica que sistematice, agilice y haga trazable la información
        generada por el zoocriadero y el trabajo de campo, apoyando así la toma de decisiones
        y el fortalecimiento de la salud pública en Cali.
      </p>
    </div>

  </div>
</div>