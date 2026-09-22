document.addEventListener('DOMContentLoaded', () => {
  activarMostrarClave();
  activarOndaAlHacerClic();
  activarFormularioDeAcceso();
  activarModalRecuperarContrasena();
  activarModalNuevaContrasena();
  activarValidacionTokenPorLink();
});


// --- Herramientas compartidas ---

function activarIconos() {
  if (window.lucide) lucide.createIcons();
}
activarIconos();

const alertaGlobal = document.getElementById('alertaGlobal');
let temporizadorAlerta = null;

// Mostrar alerta global
function mostrarAlertaGlobal(html) {
  if (!alertaGlobal) return;

  window.clearTimeout(temporizadorAlerta);
  alertaGlobal.innerHTML = html;
  void alertaGlobal.offsetWidth;
  alertaGlobal.classList.add('mostrar');

  temporizadorAlerta = window.setTimeout(() => {
    alertaGlobal.classList.remove('mostrar');
    window.setTimeout(() => { alertaGlobal.innerHTML = ''; }, 300);
  }, 3000);
}

// Alerta de error de conexión
function alertaDeErrorDeConexion() {
  return '<div class="alert alert-danger d-flex align-items-center" role="alert">'
    + '<div>No se pudo conectar con el servidor. Intenta de nuevo.</div></div>';
}

// Enviar formulario por AJAX
function enviarFormularioPorAjax(formulario, { alHaberExito, alHaberError } = {}) {
  const datosFormulario = new FormData(formulario);
  const botonEnviar = formulario.querySelector('button[type="submit"]');
  if (botonEnviar) botonEnviar.disabled = true;

  fetch(formulario.action, { method: 'POST', body: datosFormulario })
    .then((respuesta) => respuesta.text())
    .then((html) => {
      mostrarAlertaGlobal(html);

      if (html.includes('alert-success')) {
        if (alHaberExito) alHaberExito();
      } else if (alHaberError) {
        alHaberError();
      }
    })
    .catch(() => {
      mostrarAlertaGlobal(alertaDeErrorDeConexion());
    })
    .finally(() => {
      if (botonEnviar) botonEnviar.disabled = false;
    });
}


// --- 1. Mostrar / ocultar contraseña ---
function activarMostrarClave() {
  const botones = document.querySelectorAll('.boton-mostrar-clave');

  botones.forEach((boton) => {
    const idCampo = boton.dataset.target || 'clave';
    const campo = document.getElementById(idCampo);
    if (!campo) return;

    boton.addEventListener('click', () => {
      const estabaOculta = campo.type === 'password';
      campo.type = estabaOculta ? 'text' : 'password';

      boton.setAttribute('aria-pressed', String(estabaOculta));
      boton.setAttribute('aria-label', estabaOculta ? 'Ocultar contraseña' : 'Mostrar contraseña');
      boton.innerHTML = `<i data-lucide="${estabaOculta ? 'eye-off' : 'eye'}" aria-hidden="true"></i>`;
      lucide.createIcons();
    });
  });
}


// --- 2. Onda al hacer clic ---
function activarOndaAlHacerClic() {
  const botonAcceso = document.getElementById('botonAcceso');
  if (!botonAcceso) return;

  botonAcceso.addEventListener('click', (evento) => {
    const medida = botonAcceso.getBoundingClientRect();
    const tamano = Math.max(medida.width, medida.height) * 1.4;

    const onda = document.createElement('span');
    onda.className = 'onda-clic';

    const origenX = (evento.clientX ?? medida.left + medida.width / 2) - medida.left;
    const origenY = (evento.clientY ?? medida.top + medida.height / 2) - medida.top;

    onda.style.width = onda.style.height = `${tamano}px`;
    onda.style.left = `${origenX - tamano / 2}px`;
    onda.style.top = `${origenY - tamano / 2}px`;

    botonAcceso.appendChild(onda);
    onda.addEventListener('animationend', () => onda.remove());
  });
}


// --- 3. Formulario de acceso ---
const EXPRESION_CORREO = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

// Marcar estado del campo
function marcarEstadoCampo(input, mensaje) {
  const envoltorio = input.closest('.campo');
  const elementoError = envoltorio.querySelector('.campo__error');
  const esInvalido = Boolean(mensaje);

  envoltorio.classList.toggle('campo--invalido', esInvalido);
  elementoError.textContent = mensaje || '';

  if (esInvalido) {
    envoltorio.classList.remove('campo--temblor');
    void envoltorio.offsetWidth;
    envoltorio.classList.add('campo--temblor');
  }
  return !esInvalido;
}

// Pasos de la animación de carga
const PASOS_TRANSICION = [
  { texto: 'Verificando credenciales…', avance: 28 },
  { texto: 'Sincronizando estaciones de monitoreo…', avance: 64 },
  { texto: 'Cargando panel de rastreo…', avance: 100 },
];

function activarFormularioDeAcceso() {
  const formularioAcceso = document.getElementById('formularioAcceso');
  const campoCorreo = document.getElementById('correo');
  const campoClave = document.getElementById('clave');
  const botonAcceso = document.getElementById('botonAcceso');

  // Validar formulario
  function validarFormulario() {
    const correoValido = EXPRESION_CORREO.test(campoCorreo.value.trim())
      ? marcarEstadoCampo(campoCorreo, '')
      : marcarEstadoCampo(campoCorreo, 'Ingresa un correo electronico válido.');

    const claveValida = campoClave.value.trim().length >= 6
      ? marcarEstadoCampo(campoClave, '')
      : marcarEstadoCampo(campoClave, 'La contraseña debe tener al menos 6 caracteres.');

    return correoValido && claveValida;
  }

  // Limpiar error al escribir
  [campoCorreo, campoClave].forEach((input) => {
    if (!input) return;
    input.addEventListener('input', () => {
      const envoltorio = input.closest('.campo');
      if (envoltorio.classList.contains('campo--invalido')) marcarEstadoCampo(input, '');
    });
  });

  // Error de sesión (PHP)
  const alertaError = document.getElementById('alertaError');
  if (alertaError && campoCorreo && campoClave) {
    [campoCorreo, campoClave].forEach((input) => {
      input.closest('.campo').classList.add('campo--invalido', 'campo--temblor');
    });
    campoCorreo.focus();
  }

  // Animación de carga
  function reproducirTransicion() {
    const contenedorAcceso = document.querySelector('.contenedor-acceso');
    const capaTransicion = document.getElementById('capaTransicion');
    const estadoTransicion = document.getElementById('estadoTransicion');
    const rellenoBarra = document.getElementById('rellenoBarra');
    if (!capaTransicion || !contenedorAcceso) return;

    contenedorAcceso.classList.add('saliendo');
    capaTransicion.classList.add('activa');
    capaTransicion.setAttribute('aria-hidden', 'false');
    activarIconos();

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

  // Envío del formulario
  if (!formularioAcceso) return;

  formularioAcceso.addEventListener('submit', (evento) => {
    evento.preventDefault();
    if (!validarFormulario()) return;

    botonAcceso.classList.add('cargando');
    reproducirTransicion();

    window.setTimeout(() => {
      formularioAcceso.submit();
    }, 900);
  });
}


// --- 4. Modal: recuperar contraseña ---
function activarModalRecuperarContrasena() {
  const modalRecuperarPass = document.getElementById('modalRecuperarPass');
  const formRecuperar = document.getElementById('formRecuperar');
  const inputCorreoRecuperar = document.getElementById('correoRecuperar');

  if (modalRecuperarPass) {
    modalRecuperarPass.addEventListener('show.bs.modal', () => {
      if (inputCorreoRecuperar) inputCorreoRecuperar.value = '';
    });

    modalRecuperarPass.addEventListener('hide.bs.modal', () => {
      if (document.activeElement) document.activeElement.blur();
    });
  }

  if (!formRecuperar) return;

  formRecuperar.addEventListener('submit', (evento) => {
    evento.preventDefault();

    if (!inputCorreoRecuperar || !inputCorreoRecuperar.value.trim()) {
      mostrarAlertaGlobal('<div class="alert alert-danger d-flex align-items-center" role="alert">'
        + '<div>Debes ingresar un correo electrónico.</div></div>');
      if (inputCorreoRecuperar) inputCorreoRecuperar.focus();
      return;
    }

    enviarFormularioPorAjax(formRecuperar, {
      alHaberExito: () => {
        // Ya no se abre un modal de código: el usuario recibe el enlace
        // por correo y, al hacer clic en él, el propio link valida el
        // token y abre directamente el modal de nueva contraseña
        // (ver activarValidacionTokenPorLink).
        bootstrap.Modal.getInstance(modalRecuperarPass)?.hide();
        formRecuperar.reset();
      },
    });
  });
}


// --- 5. Modal: nueva contraseña ---
function activarModalNuevaContrasena() {
  const formNuevaContrasena = document.getElementById('formNuevaContrasena');
  const modalNuevaContrasena = document.getElementById('modalNuevaContrasena');
  if (!formNuevaContrasena || !alertaGlobal) return;

  formNuevaContrasena.addEventListener('submit', (evento) => {
    evento.preventDefault();

    enviarFormularioPorAjax(formNuevaContrasena, {
      alHaberExito: () => {
        bootstrap.Modal.getInstance(modalNuevaContrasena)?.hide();
        formNuevaContrasena.reset();
      },
    });
  });
}


// --- 6. Validar el token que llega por el link del correo ---
function activarValidacionTokenPorLink() {
  const parametros = new URLSearchParams(window.location.search);
  const token = parametros.get('token');
  if (!token) return;

  const modalNuevaContrasena = document.getElementById('modalNuevaContrasena');
  if (!modalNuevaContrasena) return;

  const urlBase = modalNuevaContrasena.dataset.urlValidarToken;
  if (!urlBase) return;

  const separador = urlBase.includes('?') ? '&' : '?';
  const urlValidar = `${urlBase}${separador}token=${encodeURIComponent(token)}`;

  // Quitar el token de la barra de direcciones para que no se reutilice
  // si el usuario recarga la página.
  const limpiarUrl = () => {
    window.history.replaceState({}, document.title, window.location.pathname);
  };

  fetch(urlValidar)
    .then((respuesta) => respuesta.text())
    .then((html) => {
      mostrarAlertaGlobal(html);
      limpiarUrl();

      if (html.includes('alert-success')) {
        bootstrap.Modal.getOrCreateInstance(modalNuevaContrasena).show();
      }
    })
    .catch(() => {
      mostrarAlertaGlobal(alertaDeErrorDeConexion());
      limpiarUrl();
    });
}
