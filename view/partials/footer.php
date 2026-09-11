<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" 
integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" 
crossorigin="anonymous"></script>

<script src="js/jquery.js"></script>
<script src="js/global.js"></script>
<script>
  $(document).ready(function(){
    $("#numeroDocumento").on("input", function(){
      $("#contraseñaTemp").prop("value", $(this).val());
    });
  });
</script>
<script>
document.addEventListener("DOMContentLoaded", function() {
  const alertas = document.querySelectorAll(".alert");
  alertas.forEach(alerta => {
    setTimeout(() => {
      alerta.classList.remove("show");
      setTimeout(() => alerta.remove(), 300);
    }, 3000);
  });
});
</script>

