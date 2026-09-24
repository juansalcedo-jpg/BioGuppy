<?php
?>
<style>
  :root {
    --gov-azul-oscuro: #10254a;
    --gov-marine: #3366CC;
    --gov-azul-claro: #159EE8;
  }

  .cifra-destacada {
    color: var(--gov-azul-oscuro);
  }

  html[data-bs-theme="dark"] .cifra-destacada {
    color: #60a5fa;
  }
</style>
<div class="container-fluid py-2">
  <div class="row justify-content-center">
    <div class="col-xl-9">

      <div class="d-flex align-items-center justify-content-center gap-3 mb-4 text-center">
        <img src="/BioGuppy/img/logo.png" alt="BioGuppy" class="rounded-4" style="width:90px;height:90px;object-fit:contain;">
        <div>
          <h3 class="mb-0"><strong>Bio</strong><span style="color:#159EE8;">Guppy</span></h3>
          <div class="text-muted small">Sobre nosotros</div>
        </div>
      </div>

      <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 text-white" style=" background: linear-gradient(135deg, var(--gov-azul-oscuro) 0%, var(--gov-marine) 100%);">
        <p class="mb-0 lh-lg">
          BioGuppy es el sistema de información que apoya el Control Biológico del dengue mediante
          peces guppies (Poecilia reticulata), desarrollado para el Subgrupo de Prevención, Vigilancia
          y Control de Enfermedades Transmitidas por Vectores de la Secretaría de Salud Pública de
          Santiago de Cali. Su propósito es facilitar el registro, el seguimiento y la trazabilidad de
          las actividades del zoocriadero y del trabajo en campo, para fortalecer la toma de decisiones
          y ampliar la cobertura del control biológico en la ciudad.
        </p>
      </div>

      <div class="row g-3">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm rounded-4 text-center p-4">
            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 text-white" style="width:48px;height:48px;background-color:var(--gov-marine);font-size:1.3rem;">
              <i class="bi bi-calendar-check-fill"></i>
            </div>
            <div class="fs-2 fw-bold cifra-destacada">2026</div>
            <div class="text-muted small">Año de inicio del programa</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm rounded-4 text-center p-4">
            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 text-white" style="width:48px;height:48px;background-color:var(--gov-marine);font-size:1.3rem;">
              <i class="bi bi-droplet-fill"></i>
            </div>
            <div class="fs-2 fw-bold cifra-destacada"><?php echo $zoocriaderosActivos; ?></div>
            <div class="text-muted small">Zoocriaderos activos</div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm rounded-4 text-center p-4">
            <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2 text-white" style="width:48px;height:48px;background-color:var(--gov-marine);font-size:1.3rem;">
              <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div class="fs-2 fw-bold cifra-destacada"><?php echo $sitiosActivos; ?></div>
            <div class="text-muted small">Sitios activos</div>
          </div>
        </div>
      </div>

      <div class="card border-0 shadow-sm rounded-4 p-4 mt-4">
        <h5 class="fw-bold mb-3">Equipo del programa</h5>
        <div class="row g-3">
          <?php
          $equipo = [
            ["nombre" => "Isabella Bastidas Talaga",       "rol" => "Product Owner/Desarrolladora"],
            ["nombre" => "Juan David Salcedo",              "rol" => "Scrum Master/Desarrollador"],
            ["nombre" => "Andrés Felipe Álvarez Cartagena", "rol" => "Desarrollador del sistema"],
            ["nombre" => "Daniel Ceballos",                 "rol" => "Desarrollador del sistema"],
            ["nombre" => "Carlos Esteban Escobar",          "rol" => "Desarrollador del sistema"],
          ];
          foreach ($equipo as $integrante):
          ?>
            <div class="col-md-6 col-lg-4">
              <div class="d-flex align-items-center gap-2 p-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white" style="width:40px;height:40px;background-color:var(--gov-azul-claro);font-size:.9rem;">
                  <i class="bi bi-person-fill"></i>
                </div>
                <div>
                  <div class="small fw-semibold"><?php echo $integrante["nombre"]; ?></div>
                  <div class="text-muted" style="font-size:.75rem;"><?php echo $integrante["rol"]; ?></div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

    </div>
  </div>
</div>