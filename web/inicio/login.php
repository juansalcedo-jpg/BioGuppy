<?php
include_once '../../lib/helpers.php';

$mensajeError = null;
if (isset($_SESSION['ErrorLogin'])) {
    $mensajeError = $_SESSION['ErrorLogin'];
    unset($_SESSION['ErrorLogin']);
}
?>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />

   <link rel="stylesheet" href="styles.css" />


  <main class="contenedor-acceso">

    <section class="panel" aria-labelledby="tituloAcceso">

      <header class="encabezado">
        <span class="encabezado__titulo">Bio<span>Guppy</span></span>
      </header>
      <form class="formulario-acceso" id="formularioAcceso"
            action="<?php echo '../' . getUrl('Acceso', 'Acceso', 'login', false, 'ajax'); ?>"
            method="post" novalidate>
        <h1 id="tituloAcceso">Bienvenido de nuevo</h1>

        <div class="campo">
          <label for="correo">Correo institucional</label>
          <div class="campo__control">
            <i data-lucide="mail" aria-hidden="true"></i>
            <input
              type="email"
              id="correo"
              name="usu_correo"
              placeholder="nombre.apellido@cali.gov.co"
              autocomplete="username"
              required
            />
          </div>
          <span class="campo__error" id="errorCorreo" role="alert"></span>
        </div>

        <div class="campo">
          <label for="clave">Contraseña</label>
          <div class="campo__control">
            <i data-lucide="lock" aria-hidden="true"></i>
            <input
              type="password"
              id="clave"
              name="usu_clave"
              placeholder="••••••••"
              autocomplete="current-password"
              minlength="6"
              required
            />
            <button type="button" class="boton-mostrar-clave" id="botonMostrarClave" aria-label="Mostrar contraseña" aria-pressed="false">
              <i data-lucide="eye" aria-hidden="true"></i>
            </button>
          </div>
          <span class="campo__error" id="errorClave" role="alert"></span>
        </div>

        <div class="fila-campo">
          <a href="#" class="enlace-discreto">¿Olvidaste tu contraseña?</a>
        </div>

        <?php if ($mensajeError): ?>
          <div class="alerta-error" id="alertaError" role="alert">
            <?php echo htmlspecialchars($mensajeError, ENT_QUOTES, 'UTF-8'); ?>
          </div>
        <?php endif; ?>

        <button type="submit" class="boton-acceso" id="botonAcceso">
          <span class="boton-acceso__texto">Iniciar sesión</span>
          <span class="boton-acceso__cargando" aria-hidden="true"></span>
          <i data-lucide="arrow-right" class="boton-acceso__icono" aria-hidden="true"></i>
        </button>

        <p class="nota-formulario">
          Acceso exclusivo para personal autorizado de la Secretaría de Salud Pública de Cali.
        </p>
      </form>
    </section>

    <!-- Panel de animacion radar e informacion -->
    <aside class="panel panel-informativo" aria-label="Información sobre el control biológico con peces guppy">

      <div class="radar" aria-hidden="true">
        <div class="radar__cuadricula"></div>
        <div class="radar__anillos"><span></span><span></span><span></span></div>
        <div class="radar__barrido"></div>

        <!-- Viexbox ="minX minY ancho alto" -->
        <img class="pez pez--1" src="../../img/pez.png" alt="">
        <img class="pez pez--2" src="../../img/pez.png" alt="">
        <img class="pez pez--3" src="../../img/pez.png" alt="">

        <div class="radar__centro">
          <i data-lucide="map-pin" aria-hidden="true"></i>
        </div>
      </div>

      <div class="contenido-informativo">
        <h2>El guppy, centinela de las fuentes hídricas de Cali</h2>
        <p>
          El <em>Poecilia reticulata</em>, conocido como pez guppy, es un aliado clave en el
          control biológico de larvas de mosquito, contribuyendo a reducir focos de dengue,
          zika y chikungunya en quebradas, jagüeyes y reservorios urbanos de la ciudad.
        </p>
      </div>
    </aside>
  </main>

  <!-- ======================= CAPA DE TRANSICIÓN DE ACCESO =======================
       Se activa un instante después de enviar el formulario (ver script.js,
       SECCIÓN 6). No decide a dónde navega la página: eso lo hace PHP. -->
  <div class="capa-transicion" id="capaTransicion" aria-hidden="true">

    <!-- Peces libres nadando por toda la pantalla -->
    <div class="capa-transicion__peces" aria-hidden="true">
            <img class="pez-libre pez-libre--1" src="../../img/pez.png" alt="">
      <img class="pez-libre pez-libre--2" src="../../img/pez.png" alt="">
      <img class="pez-libre pez-libre--3" src="../../img/pez.png" alt="">
      <img class="pez-libre pez-libre--4" src="../../img/pez.png" alt="">
      <img class="pez-libre pez-libre--5" src="../../img/pez.png" alt="">
      <img class="pez-libre pez-libre--6" src="../../img/pez.png" alt="">
    </div>

    <div class="escena-transicion">
      <div class="radar-transicion">
        <div class="radar-transicion__cuadricula"></div>
        <div class="radar-transicion__anillos"><span></span><span></span><span></span></div>
        <div class="radar-transicion__barrido"></div>

        <img class="pez pez--1" src="../../img/pez.png" alt="">
        <img class="pez pez--2" src="../../img/pez.png" alt="">
        <img class="pez pez--3" src="../../img/pez.png" alt="">
        <div class="radar-transicion__centro">
          <i data-lucide="map-pin" aria-hidden="true"></i>
        </div>
      </div>

      <span class="marca-transicion">Bio<span>Guppy</span></span>
      <p class="estado-transicion" id="estadoTransicion">Verificando credenciales…</p>

      <div class="barra-progreso">
        <div class="barra-progreso__relleno" id="rellenoBarra"></div>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
  <script src="script.js"></script>

