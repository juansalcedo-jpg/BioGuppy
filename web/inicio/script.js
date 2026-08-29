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

});
