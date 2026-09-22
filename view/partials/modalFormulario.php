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

  function ejecutarScriptsInyectados(contenedor) {
      contenedor.querySelectorAll('script').forEach(function (scriptViejo) {
          var scriptNuevo = document.createElement('script');
          scriptNuevo.textContent = scriptViejo.textContent;
          scriptViejo.replaceWith(scriptNuevo);
      });
  }
  function prepararFormularioAjax(contenedorId, urlExito, idTabla) {
      var contenido = document.getElementById('modalFormularioAjaxContenido');
      var form = contenido.querySelector('form');
      if (!form) return;

      form.addEventListener('submit', function (evento) {
          evento.preventDefault();
          enviarFormularioModalPorAjax(form, contenedorId, urlExito, idTabla);
      });
  }

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

  function cerrarFormularioModal() {
      var modalEl = document.getElementById('modalFormularioAjax');
      var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.hide();
  }

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