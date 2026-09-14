<!-- Modal genérico reutilizable para cargar formularios (Registrar/Editar) sin recargar la página -->
<div class="modal fade" id="modalFormularioAjax" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalFormularioAjaxTitulo">Formulario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <div id="modalFormularioAjaxAlerta"></div>
        <div id="modalFormularioAjaxContenido">
          <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Cargando...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  #modalFormularioAjaxAlerta .alert {
    animation: fadeInDownSutil .25s ease;
  }
  @keyframes fadeInDownSutil {
    from { opacity: 0; transform: translateY(-8px); }
    to   { opacity: 1; transform: translateY(0); }
  }
</style>

<script>
  /**
   * Abre el modal genérico y carga dentro el fragmento de formulario que
   * devuelve `url`. `urlExito` es la URL a la que el controlador redirige
   * cuando TODO sale bien (normalmente el listado del módulo) — se usa
   * para distinguir "éxito" de "error de validación" sin tocar el backend.
   * `idTabla` (opcional) es el id de la tabla en la página de listado que
   * se debe refrescar al guardar con éxito (ej: "tablaUsuarios",
   * "tablaDepositos", "tablaMisActividadesTer") -- si no se pasa, se hace
   * una navegación completa a `urlExito` en vez de refrescar solo la tabla.
   */
  function cargarFormularioModal(url, titulo, contenedorId, urlExito, idTabla) {
      var modalEl = document.getElementById('modalFormularioAjax');
      var contenido = document.getElementById('modalFormularioAjaxContenido');
      var alerta = document.getElementById('modalFormularioAjaxAlerta');
      var tituloEl = document.getElementById('modalFormularioAjaxTitulo');
      var modal = bootstrap.Modal.getOrCreateInstance(modalEl);

      tituloEl.textContent = titulo;
      alerta.innerHTML = '';
      contenido.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status">' +
                             '<span class="visually-hidden">Cargando...</span></div></div>';
      modal.show();

      fetch(url)
          .then(function (respuesta) { return respuesta.text(); })
          .then(function (html) {
              var parser = new DOMParser();
              var doc = parser.parseFromString(html, 'text/html');
              var seccion = doc.getElementById(contenedorId);

              if (!seccion) {
                  contenido.innerHTML = '<div class="alert alert-danger mb-0">No se pudo cargar el formulario.</div>';
                  return;
              }

              contenido.innerHTML = seccion.innerHTML;
              ejecutarScriptsInyectados(contenido);
              prepararFormularioAjax(contenedorId, urlExito, idTabla);
          })
          .catch(function () {
              contenido.innerHTML = '<div class="alert alert-danger mb-0">Ocurrió un error al cargar el formulario. Intenta nuevamente.</div>';
          });
  }

  // Los <script> insertados vía innerHTML no se ejecutan solos (limitación del
  // navegador); esto los vuelve a crear para que sí corran (ej: los checkbox
  // de "marcar toda la columna" en Registrar rol).
  function ejecutarScriptsInyectados(contenedor) {
      contenedor.querySelectorAll('script').forEach(function (scriptViejo) {
          var scriptNuevo = document.createElement('script');
          scriptNuevo.textContent = scriptViejo.textContent;
          scriptViejo.replaceWith(scriptNuevo);
      });
  }

  // Intercepta el submit del formulario que se acaba de inyectar en el modal.
  function prepararFormularioAjax(contenedorId, urlExito, idTabla) {
      var contenido = document.getElementById('modalFormularioAjaxContenido');
      var form = contenido.querySelector('form');
      if (!form) return;

      form.addEventListener('submit', function (evento) {
          evento.preventDefault();
          enviarFormularioModalPorAjax(form, contenedorId, urlExito, idTabla);
      });
  }

  /**
   * UNICA version de esta funcion (antes existian DOS declaradas con el
   * mismo nombre en este archivo -- en JavaScript, cuando eso pasa, la
   * segunda pisa silenciosamente a la primera sin ningun error en
   * consola. Esa segunda version tenia escrito a mano "tablaUsuarios",
   * asi que para cualquier otro modulo -Depositos, ActividadesTer- nunca
   * encontraba la tabla, y terminaba mostrando "error" en el modal
   * aunque el guardado en la base de datos SI hubiera funcionado. Esta
   * version unica usa el parametro "idTabla" en vez de un nombre fijo,
   * para que funcione igual en cualquier modulo que la use).
   */
  function enviarFormularioModalPorAjax(form, contenedorId, urlExito, idTabla) {
      var alerta = document.getElementById('modalFormularioAjaxAlerta');
      var datos = new FormData(form);
      var boton = form.querySelector('button[type="submit"]');
      if (boton) boton.disabled = true;

      fetch(form.action, { method: 'POST', body: datos })
          .then(function (respuesta) { return respuesta.text(); })
          .then(function (texto) {
              var match = texto.match(/window\.location\.href\s*=\s*'([^']+)'/);

              if (!match) {
                  alerta.innerHTML = '<div class="alert alert-warning mb-3">No se pudo interpretar la respuesta del servidor.</div>';
                  return;
              }

              var destino = match[1];

              if (destino === urlExito) {
                  // Exito: pedimos la pagina de listado para (a) leer el
                  // mensaje verde que dejo el controlador en sesion y
                  // (b) refrescar solo la tabla, sin recargar toda la pagina.
                  fetch(urlExito)
                      .then(function (r) { return r.text(); })
                      .then(function (html) {
                          var parser = new DOMParser();
                          var doc = parser.parseFromString(html, 'text/html');
                          var mensajeExito = doc.querySelector('.alert-success');

                          cerrarFormularioModal();

                          if (idTabla) {
                              var nuevaTabla = doc.getElementById(idTabla);
                              var tablaActual = document.getElementById(idTabla);
                              if (nuevaTabla && tablaActual) {
                                  tablaActual.innerHTML = nuevaTabla.innerHTML;
                              }
                          }

                          if (mensajeExito) {
                              mostrarAlertaFlotante(mensajeExito.outerHTML);
                          }
                      })
                      .catch(function () {
                          // Si algo falla releyendo la pagina, al menos navegamos
                          window.location.href = urlExito;
                      });
                  return;
              }

              // Error de validación: el controlador dejó el mensaje en sesión
              // y redirige al mismo formulario. Lo pedimos aparte para leerlo,
              // sin perder lo que el usuario ya había escrito en el modal.
              fetch(destino)
                  .then(function (r) { return r.text(); })
                  .then(function (html) {
                      var parser = new DOMParser();
                      var doc = parser.parseFromString(html, 'text/html');
                      var seccion = doc.getElementById(contenedorId);
                      var mensaje = seccion ? seccion.querySelector('.alert') : null;

                      alerta.innerHTML = mensaje
                          ? mensaje.outerHTML
                          : '<div class="alert alert-danger mb-3">Revisa los datos ingresados.</div>';
                  })
                  .catch(function () {
                      alerta.innerHTML = '<div class="alert alert-danger mb-3">Ocurrió un error, intenta nuevamente.</div>';
                  });
          })
          .catch(function () {
              alerta.innerHTML = '<div class="alert alert-danger mb-3">No se pudo conectar con el servidor. Intenta nuevamente.</div>';
          })
          .finally(function () {
              if (boton) boton.disabled = false;
          });
  }
  // Cierra el modal genérico (usado al terminar con éxito).
  function cerrarFormularioModal() {
      var modalEl = document.getElementById('modalFormularioAjax');
      var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.hide();
  }

  // Muestra el mensaje de éxito como una alerta flotante arriba a la
  // derecha (mismo lugar/estilo donde ya aparecen los mensajes normales
  // de la pagina), y se autodestruye sola despues de unos segundos --
  // igual que el resto de alertas del sistema (ver footer.php).
  function mostrarAlertaFlotante(htmlAlerta) {
      var contenedor = document.createElement('div');
      contenedor.style.position = 'fixed';
      contenedor.style.top = '80px';
      contenedor.style.right = '20px';
      contenedor.style.zIndex = '2000';
      contenedor.style.minWidth = '300px';
      contenedor.innerHTML = htmlAlerta;
      document.body.appendChild(contenedor);
      setTimeout(function () {
          contenedor.remove();
      }, 3000);
  }
</script>