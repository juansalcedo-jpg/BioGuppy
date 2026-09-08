document.addEventListener('DOMContentLoaded', () => {

  /* ---------- 1. Íconos Lucide ---------- */
  if (window.lucide) lucide.createIcons();

  /* ---------- 2. Mostrar / ocultar contraseña ---------- */
  const botonMostrarClave = document.getElementById('botonMostrarClave');
  const campoClave        = document.getElementById('clave');

  if (botonMostrarClave && campoClave) {
    botonMostrarClave.addEventListener('click', () => {
      const estabaOculta = campoClave.type === 'password';
      campoClave.type = estabaOculta ? 'text' : 'password';
      botonMostrarClave.setAttribute('aria-pressed', String(estabaOculta));
      botonMostrarClave.setAttribute('aria-label', estabaOculta ? 'Ocultar contraseña' : 'Mostrar contraseña');

      botonMostrarClave.innerHTML = `<i data-lucide="${estabaOculta ? 'eye-off' : 'eye'}" aria-hidden="true"></i>`;
      if (window.lucide) lucide.createIcons();
    });
  }

  /* ---------- 3. Onda de clic sobre el botón "Iniciar sesión" ---------- */
  const botonAcceso = document.getElementById('botonAcceso');

  function crearOndaClic(boton, evento) {
    const medida = boton.getBoundingClientRect();
    const tamano = Math.max(medida.width, medida.height) * 1.4;
    const onda = document.createElement('span');
    onda.className = 'onda-clic';

    const origenX = (evento.clientX ?? medida.left + medida.width / 2) - medida.left;
    const origenY = (evento.clientY ?? medida.top + medida.height / 2) - medida.top;

    onda.style.width = onda.style.height = `${tamano}px`;
    onda.style.left = `${origenX - tamano / 2}px`;
    onda.style.top = `${origenY - tamano / 2}px`;

    boton.appendChild(onda);
    onda.addEventListener('animationend', () => onda.remove());
  }

  if (botonAcceso) {
    botonAcceso.addEventListener('click', (evento) => crearOndaClic(botonAcceso, evento));
  }

  /* ---------- 4. Validación del formulario en el navegador ----------
     Esta validación es solo de "primera línea" (evita envíos vacíos o
     con formato inválido). La validación real y definitiva —contra la
     base de datos— siempre la hace AccesoController::login() en PHP. */
  const formularioAcceso = document.getElementById('formularioAcceso');
  const campoCorreo      = document.getElementById('correo');
  const campoClaveInput  = document.getElementById('clave');

  const EXPRESION_CORREO = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function marcarEstadoCampo(input, mensaje) {
    const envoltorio = input.closest('.campo');
    const elementoError = envoltorio.querySelector('.campo__error');
    const esInvalido = Boolean(mensaje);

    envoltorio.classList.toggle('campo--invalido', esInvalido);
    elementoError.textContent = mensaje || '';

    if (esInvalido) {
      envoltorio.classList.remove('campo--temblor');
      // Se fuerza un "reflow" para poder reiniciar la animación de temblor
      // aunque el campo ya estuviera marcado como inválido antes.
      void envoltorio.offsetWidth;
      envoltorio.classList.add('campo--temblor');
    }
    return !esInvalido;
  }

  function validarFormulario() {
    const correoValido = EXPRESION_CORREO.test(campoCorreo.value.trim())
      ? marcarEstadoCampo(campoCorreo, '')
      : marcarEstadoCampo(campoCorreo, 'Ingresa un correo institucional válido.');

    const claveValida = campoClaveInput.value.trim().length >= 6
      ? marcarEstadoCampo(campoClaveInput, '')
      : marcarEstadoCampo(campoClaveInput, 'La contraseña debe tener al menos 6 caracteres.');

    return correoValido && claveValida;
  }

  // Limpia el estado de error apenas el usuario empieza a corregir el campo
  [campoCorreo, campoClaveInput].forEach((input) => {
    if (!input) return;
    input.addEventListener('input', () => {
      const envoltorio = input.closest('.campo');
      if (envoltorio.classList.contains('campo--invalido')) marcarEstadoCampo(input, '');
    });
  });

  /* ---------- 5. Resaltar el error que devuelve el servidor ----------
     Si PHP volvió a mostrar login.php con $_SESSION['ErrorLogin'], el
     HTML trae un elemento #alertaError. Aquí solo lo usamos para darle
     al usuario la misma señal visual (temblor) que un error local. */
  const alertaError = document.getElementById('alertaError');
  if (alertaError && campoCorreo && campoClaveInput) {
    [campoCorreo, campoClaveInput].forEach((input) => {
      const envoltorio = input.closest('.campo');
      envoltorio.classList.add('campo--invalido', 'campo--temblor');
    });
    campoCorreo.focus();
  }

  /* ---------- 6. Transición de acceso + envío real del formulario ---------- */
  const contenedorAcceso = document.querySelector('.contenedor-acceso');
  const capaTransicion   = document.getElementById('capaTransicion');
  const estadoTransicion = document.getElementById('estadoTransicion');
  const rellenoBarra     = document.getElementById('rellenoBarra');

  const PASOS_TRANSICION = [
    { texto: 'Verificando credenciales…', avance: 28 },
    { texto: 'Sincronizando estaciones de monitoreo…', avance: 64 },
    { texto: 'Cargando panel de rastreo…', avance: 100 },
  ];

  function reproducirTransicion() {
    if (!capaTransicion || !contenedorAcceso) return;

    // El formulario se desvanece y aparece la animación de peces a pantalla completa
    contenedorAcceso.classList.add('saliendo');
    capaTransicion.classList.add('activa');
    capaTransicion.setAttribute('aria-hidden', 'false');
    if (window.lucide) lucide.createIcons();

    rellenoBarra.style.width = '0%';
    estadoTransicion.textContent = PASOS_TRANSICION[0].texto;

    let paso = 0;
    const avanzarPaso = () => {
      const actual = PASOS_TRANSICION[paso];
      estadoTransicion.textContent = actual.texto;
      rellenoBarra.style.width = actual.avance + '%';
      paso += 1;
      if (paso < PASOS_TRANSICION.length) window.setTimeout(avanzarPaso, 750);
    };
    window.setTimeout(avanzarPaso, 350);

    window.setTimeout(() => {
      contenedorAcceso.classList.add('oculto');
    }, 450);
  }

  if (formularioAcceso) {
    formularioAcceso.addEventListener('submit', (evento) => {
      // Siempre se detiene el envío automático primero: se valida en el
      // navegador y, si todo está bien, la transición dispara el envío
      // real del formulario un instante después (ver más abajo).
      evento.preventDefault();
      if (!validarFormulario()) return;

      botonAcceso.classList.add('cargando');
      reproducirTransicion();

      // Después de dejar ver la animación un momento, se envía el
      // formulario de verdad. form.submit() NO vuelve a disparar este
      // mismo evento "submit", así que aquí termina el trabajo de JS:
      // de aquí en adelante, quien decide a dónde navegar es PHP
      // (AccesoController -> redirect("index.php") o vuelta a login.php).
      window.setTimeout(() => {
        formularioAcceso.submit();
      }, 900);
    });
  }

  const modalRecuperarPass = document.getElementById('modalRecuperarPass');
  const formRecuperar      = document.getElementById('formRecuperar');
  const inputCorreoRecuperar = document.getElementById('correoRecuperar');
  const alertaGlobal        = document.getElementById('alertaGlobal');

  // Muestra un mensaje (HTML de una alerta de Bootstrap) arriba de toda
  // la página durante 3 segundos y luego se desvanece solo.
  let temporizadorAlerta = null;
  function mostrarAlertaGlobal(html) {
    if (!alertaGlobal) return;

    window.clearTimeout(temporizadorAlerta);
    alertaGlobal.innerHTML = html;
    // Se fuerza un reflow para que la transición se reinicie aunque la
    // alerta anterior siguiera visible.
    void alertaGlobal.offsetWidth;
    alertaGlobal.classList.add('mostrar');

    temporizadorAlerta = window.setTimeout(() => {
      alertaGlobal.classList.remove('mostrar');
      // Se espera a que termine la transición de desvanecido (0.3s)
      // antes de vaciar el contenido.
      window.setTimeout(() => { alertaGlobal.innerHTML = ''; }, 300);
    }, 3000);
  }

  // Cada vez que se abre el modal se limpia el campo de correo.
  if (modalRecuperarPass) {
    modalRecuperarPass.addEventListener('show.bs.modal', () => {
      if (inputCorreoRecuperar) inputCorreoRecuperar.value = '';
    });

    modalRecuperarPass.addEventListener('hide.bs.modal', () => {
      if (document.activeElement) {
        document.activeElement.blur();
      }
    });
  }

  if (formRecuperar && alertaGlobal) {
    formRecuperar.addEventListener('submit', (evento) => {
      evento.preventDefault();

      if (!inputCorreoRecuperar || !inputCorreoRecuperar.value.trim()) {
        mostrarAlertaGlobal('<div class="alert alert-danger d-flex align-items-center" role="alert">'
          + '<div>Debes ingresar un correo electrónico.</div></div>');
        if (inputCorreoRecuperar) inputCorreoRecuperar.focus();
        return;
      }

      const datosFormulario = new FormData(formRecuperar);
      const botonEnviar = formRecuperar.querySelector('button[type="submit"]');
      if (botonEnviar) botonEnviar.disabled = true;

      fetch(formRecuperar.action, {
        method: 'POST',
        body: datosFormulario
      })
        .then((respuesta) => respuesta.text())
        .then((html) => {
          mostrarAlertaGlobal(html);

          if (html.includes('alert-success')) {
            const modalVerificar = document.getElementById('modalVerificarCodigo');
            if (modalVerificar) {
              const instanciaRecuperar = bootstrap.Modal.getInstance(modalRecuperarPass);
              if (instanciaRecuperar) instanciaRecuperar.hide();

              const instanciaVerificar = bootstrap.Modal.getOrCreateInstance(modalVerificar);
              instanciaVerificar.show();
            }
          }
        })
        .catch(() => {
          mostrarAlertaGlobal('<div class="alert alert-danger d-flex align-items-center" role="alert">'
            + '<div>No se pudo conectar con el servidor. Intenta de nuevo.</div></div>');
        })
        .finally(() => {
          if (botonEnviar) botonEnviar.disabled = false;
        });
    });
  }

  /* ---------- 7. Casillas del código OTP (modal "Verifica tu código") ----------
     - Solo deja escribir dígitos (se limpia cualquier otro carácter).
     - Al llenarse una casilla, el foco salta automáticamente a la siguiente.
     - Backspace en una casilla vacía regresa el foco a la anterior.
     - Si se pega un código completo (Ctrl+V), se reparte en las 6 casillas.
     - En todo momento se mantiene actualizado el input oculto #codigoCompleto. */
  const otpInputs      = Array.from(document.querySelectorAll('#otpInputs .otp-digit'));
  const codigoCompleto = document.getElementById('codigoCompleto');

  function actualizarCodigoCompleto() {
    if (codigoCompleto) {
      codigoCompleto.value = otpInputs.map((input) => input.value).join('');
    }
  }

  otpInputs.forEach((input, indice) => {
    input.addEventListener('input', () => {
      // Se descarta todo lo que no sea un dígito (por si pegan texto con letras).
      input.value = input.value.replace(/\D/g, '').slice(0, 1);

      if (input.value && indice < otpInputs.length - 1) {
        otpInputs[indice + 1].focus();
      }
      actualizarCodigoCompleto();
    });

    input.addEventListener('keydown', (evento) => {
      if (evento.key === 'Backspace' && !input.value && indice > 0) {
        otpInputs[indice - 1].focus();
        otpInputs[indice - 1].value = '';
        actualizarCodigoCompleto();
      } else if (evento.key === 'ArrowLeft' && indice > 0) {
        otpInputs[indice - 1].focus();
      } else if (evento.key === 'ArrowRight' && indice < otpInputs.length - 1) {
        otpInputs[indice + 1].focus();
      }
    });

    input.addEventListener('paste', (evento) => {
      evento.preventDefault();
      const textoPegado = (evento.clipboardData || window.clipboardData)
        .getData('text')
        .replace(/\D/g, '')
        .slice(0, otpInputs.length);

      textoPegado.split('').forEach((digito, posicion) => {
        if (otpInputs[posicion]) otpInputs[posicion].value = digito;
      });

      const siguienteVacio = otpInputs.findIndex((campo) => !campo.value);
      (siguienteVacio === -1 ? otpInputs[otpInputs.length - 1] : otpInputs[siguienteVacio]).focus();
      actualizarCodigoCompleto();
    });
  });

  // Al abrir el modal de verificación, se limpian las casillas y se enfoca la primera.
  const modalVerificarCodigo = document.getElementById('modalVerificarCodigo');
  if (modalVerificarCodigo) {
    modalVerificarCodigo.addEventListener('shown.bs.modal', () => {
      otpInputs.forEach((input) => { input.value = ''; });
      actualizarCodigoCompleto();
      if (otpInputs[0]) otpInputs[0].focus();
    });
  }

  /* ---------- 8. Modal "Verifica tu código": envío por AJAX ----------
     Mismo patrón que el modal de enviar correo (más arriba): se
     intercepta el submit, se manda por fetch al controlador y la
     respuesta (HTML de un alert de Bootstrap) se muestra en la alerta
     flotante de arriba de la página.
     - alert-success -> se cierra este modal y se abre el de nueva contraseña.
     - alert-danger  -> se queda en este modal y se borra el código escrito. */
  const formVerificarCodigo = document.getElementById('formVerificarCodigo');

  if (formVerificarCodigo && alertaGlobal) {
    formVerificarCodigo.addEventListener('submit', (evento) => {
      evento.preventDefault();

      actualizarCodigoCompleto();

      if (!codigoCompleto || codigoCompleto.value.length < otpInputs.length) {
        mostrarAlertaGlobal('<div class="alert alert-danger d-flex align-items-center" role="alert">'
          + '<div>Debes ingresar el código completo.</div></div>');
        return;
      }

      const datosFormulario = new FormData(formVerificarCodigo);
      const botonVerificar = formVerificarCodigo.querySelector('button[type="submit"]');
      if (botonVerificar) botonVerificar.disabled = true;

      fetch(formVerificarCodigo.action, {
        method: 'POST',
        body: datosFormulario
      })
        .then((respuesta) => respuesta.text())
        .then((html) => {
          mostrarAlertaGlobal(html);

          if (html.includes('alert-success')) {
            const modalNuevaContrasena = document.getElementById('modalNuevaContrasena');
            if (modalNuevaContrasena) {
              const instanciaVerificar = bootstrap.Modal.getInstance(modalVerificarCodigo);
              if (instanciaVerificar) instanciaVerificar.hide();

              const instanciaNuevaContrasena = bootstrap.Modal.getOrCreateInstance(modalNuevaContrasena);
              instanciaNuevaContrasena.show();
            }
          } else {
            // Código incorrecto: se queda en el modal y se borra lo escrito.
            otpInputs.forEach((input) => { input.value = ''; });
            actualizarCodigoCompleto();
            if (otpInputs[0]) otpInputs[0].focus();
          }
        })
        .catch(() => {
          mostrarAlertaGlobal('<div class="alert alert-danger d-flex align-items-center" role="alert">'
            + '<div>No se pudo conectar con el servidor. Intenta de nuevo.</div></div>');
        })
        .finally(() => {
          if (botonVerificar) botonVerificar.disabled = false;
        });
    });
  }

  /* ---------- 9. Modal "Nueva contraseña": envío por AJAX ----------
     Mismo patrón que los dos modales anteriores. Como este es el
     último paso, en el caso de éxito no hay un modal siguiente que
     abrir: solo se cierra este con .hide(). */
  const formNuevaContrasena = document.getElementById('formNuevaContrasena');
  const modalNuevaContrasena = document.getElementById('modalNuevaContrasena');

  if (formNuevaContrasena && alertaGlobal) {
    formNuevaContrasena.addEventListener('submit', (evento) => {
      evento.preventDefault();

      const datosFormulario = new FormData(formNuevaContrasena);
      const botonGuardar = formNuevaContrasena.querySelector('button[type="submit"]');
      if (botonGuardar) botonGuardar.disabled = true;

      fetch(formNuevaContrasena.action, {
        method: 'POST',
        body: datosFormulario
      })
        .then((respuesta) => respuesta.text())
        .then((html) => {
          mostrarAlertaGlobal(html);

          if (html.includes('alert-success')) {
            // Éxito: se cierra el modal. bootstrap.Modal.getInstance()
            // te devuelve la instancia ya creada (Bootstrap la crea sola
            // la primera vez que se abre con data-bs-toggle o con .show());
            // .hide() es el método que la cierra, con su animación.
            const instancia = bootstrap.Modal.getInstance(modalNuevaContrasena);
            if (instancia) instancia.hide();

            formNuevaContrasena.reset();
          }
          // Si fue alert-danger, no se hace nada más: el modal se queda
          // abierto tal cual (no hace falta ni preventDefault extra ni
          // cerrar nada) y el usuario ve el error en la alerta flotante.
        })
        .catch(() => {
          mostrarAlertaGlobal('<div class="alert alert-danger d-flex align-items-center" role="alert">'
            + '<div>No se pudo conectar con el servidor. Intenta de nuevo.</div></div>');
        })
        .finally(() => {
          if (botonGuardar) botonGuardar.disabled = false;
        });
    });
  }

});
