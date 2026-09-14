$(document).ready(function(){

    $(document).on("click", "#btnToggleSidebar", function(){
        $(".app-layout").toggleClass("sidebar-collapsed");
    });

    $(document).on("keyup","#filtro",function(){
        let data = $(this).val();
        let url = $(this).data("url");

        $.ajax({
            url: url,
            type: "GET",
            data: {
                buscar: data
            },
            success: function(data){
                if(data.trim() !== ""){
                    $("tbody").html(data);
                } else {
                    $("tbody").html("<tr><td colspan='5'>Elemento no encontrado</td></tr>");
                }
            }
        })
    });

});
document.getElementById('buscadorUsuarios').addEventListener('keyup', function () {
      var filtro = this.value.toLowerCase();
      document.querySelectorAll('#tablaUsuarios tbody tr').forEach(function (fila) {
          fila.style.display = fila.textContent.toLowerCase().includes(filtro) ? '' : 'none';
      });
  });

function enviarFormularioModalPorAjax(form, contenedorId, urlExito) {
    var alerta = document.getElementById('modalFormularioAjaxAlerta');
    var datos = new FormData(form);
    var boton = form.querySelector('button[type="submit"]');
    if (boton) boton.disabled = true;

    fetch(form.action, { method: 'POST', body: datos })
        .then(function (r) { return r.text(); })
        .then(function (texto) {
            // Extraer la URL del redirect devuelto por PHP
            var match = texto.match(/window\.location\.href\s*=\s*'([^']+)'/);

            if (!match) {
                alerta.innerHTML = '<div class="alert alert-warning mb-3">No se pudo interpretar la respuesta del servidor.</div>';
                return;
            }

            var destino = match[1];

            // Verificamos si la URL de destino contiene la función/vista de éxito (ej: listUsu)
            // o si coincide con urlExito
            if (destino === urlExito || destino.includes('listUsu')) {
                // Navegar a la URL devuelta por el controlador para consumir $_SESSION['exito']
                window.location.href = destino;
                return;
            }

            // Si redirige de nuevo al formulario (error de validación)
            fetch(destino)
                .then(function (r) { return r.text(); })
                .then(function (html) {
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(html, 'text/html');
                    var seccion = doc.getElementById(contenedorId);
                    var mensajeError = seccion ? seccion.querySelector('.alert-danger, .alert-warning') : null;

                    alerta.innerHTML = mensajeError
                        ? mensajeError.outerHTML
                        : '<div class="alert alert-danger mb-3">Revisa los datos ingresados.</div>';
                })
                .catch(function () {
                    alerta.innerHTML = '<div class="alert alert-danger mb-3">Ocurrió un error al procesar la validación.</div>';
                });
        })
        .catch(function () {
            alerta.innerHTML = '<div class="alert alert-danger mb-3">No se pudo conectar con el servidor.</div>';
        })
        .finally(function () {
            if (boton) boton.disabled = false;
        });
}

