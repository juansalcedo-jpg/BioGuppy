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
  <link rel="icon" type="image/png" href="../../img/logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />

   <link rel="stylesheet" href="styles.css" />

  <div id="alertaGlobal" class="alerta-flotante" aria-live="polite"></div>

  <main class="contenedor-acceso">

    <section class="panel" aria-labelledby="tituloAcceso">

      <header class="encabezado">
        <span class="encabezado__titulo">Bio<span>Guppy</span></span>
      </header>
        <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
          <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
          </symbol>
          <symbol id="check-circle-fill" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
          </symbol>
        </svg>
      <form class="formulario-acceso" id="formularioAcceso"
            action="<?php echo '../' . getUrl('Acceso', 'Acceso', 'login', false, 'ajax'); ?>"
            method="post" novalidate>
        <h1 id="tituloAcceso">Bienvenido de nuevo</h1>

        <div class="campo">
          <label for="correo">Correo electronico</label>
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
          <a href="#" class="enlace-discreto" data-bs-toggle="modal" y data-bs-target="#modalRecuperarPass">¿Olvidaste tu contraseña?</a>
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


<div class="modal fade modal-biopass" id="modalRecuperarPass" tabindex="-1" aria-labelledby="modalRecuperarPassLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="modalRecuperarPassLabel">Recuperar contraseña</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form id="formRecuperar" method="POST" action="<?php echo '../' . getUrl('CambioContra','CambioContra','enviarCorreo',false,'ajax');  ?>">
        <div class="modal-body">
          <p class="small">Ingresa tu correo para cambiar o recuperar tu contraseña.</p>

          <label for="correoRecuperar" class="form-label">Correo Electronico</label>
          <input type="text" class="form-control" id="correoRecuperar" name="correo" placeholder="nombre.apellido@cali.gov.co">
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn-enviar-recuperacion w-100">Enviar código de recuperación</button>
        </div>
      </form>

    </div>
  </div>
</div>

<div class="modal fade modal-biopass" id="modalVerificarCodigo" tabindex="-1" aria-labelledby="modalVerificarCodigoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="modalVerificarCodigoLabel">Verifica tu código</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form id="formVerificarCodigo" method="POST" action="<?php echo '../' . getUrl('CambioContra','CambioContra','validar_codigo',false,'ajax'); ?>">
        <div class="modal-body">
          <p>Ingresa el código de 6 dígitos que enviamos a tu correo.</p>

          <div class="otp-inputs" id="otpInputs">
            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="otp-digit" autocomplete="one-time-code">
            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="otp-digit">
            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="otp-digit">
            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="otp-digit">
            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="otp-digit">
            <input type="text" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="otp-digit">
          </div>

          <input type="hidden" name="codigo" id="codigoCompleto">

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn-enviar-recuperacion w-100">Verificar código</button>
        </div>
      </form>

    </div>
  </div>
</div>

<div class="modal fade modal-biopass" id="modalNuevaContrasena" tabindex="-1" aria-labelledby="modalNuevaContrasenaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="modalNuevaContrasenaLabel">Nueva contraseña</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <form id="formNuevaContrasena" method="POST" action="<?php echo '../' . getUrl('CambioContra','CambioContra','cambiar_contraseña',false,'ajax'); ?>">
        <div class="modal-body">
          <p class="small">Crea tu nueva contraseña.</p>

          <label for="nuevaContrasena" class="form-label">Nueva contraseña</label>
          <input type="password" class="form-control" id="nuevaContrasena" name="nuevaContrasena">

          <label for="confirmarContrasena" class="form-label">Confirmar contraseña</label>
          <input type="password" class="form-control" id="confirmarContrasena" name="confirmarContrasena">
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn-enviar-recuperacion w-100">Guardar contraseña</button>
        </div>
      </form>

    </div>
  </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
  <script src="script.js"></script>

