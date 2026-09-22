<style>
  .pie-bioguppy {
    background-color: #10254a;
    color: #c9d4e6;
    margin-left: var(--sidebar-width);
    padding: 6px 10px;
    transition: margin-left .25s ease;
    font-family: 'Segoe UI', Arial, sans-serif;
    font-size: .65rem;
    text-align: center;
  }

  .app-layout.sidebar-collapsed~.pie-bioguppy {
    margin-left: 0;
  }

  .pie-bioguppy .pie-contenido {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
  }

  .pie-bioguppy i {
    margin-right: 4px;
  }

  .pie-bioguppy a {
    color: #c9d4e6;
    text-decoration: none;
  }

  .pie-bioguppy a:hover {
    color: #159EE8;
  }

  @media (max-width: 768px) {
    .pie-bioguppy {
      margin-left: 0;
      padding: 6px 8px;
      font-size: .6rem;
    }

    .pie-bioguppy .pie-contenido {
      flex-direction: column;
      gap: 6px;
    }
  }
</style>

<footer class="pie-bioguppy">
  <div class="pie-contenido">
    <span><i class="bi bi-envelope me-1"></i> bioguppy@gmail.com</span>
    <span><i class="bi bi-geo-alt me-1"></i> Santiago de Cali, Colombia</span>
    <span>© <?php echo date('Y'); ?> BioGuppy — Todos los derechos reservados.</span>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
  crossorigin="anonymous"></script>

<script src="js/jquery.js"></script>
<script src="js/global.js"></script>
<script>
  $(document).ready(function() {
    $("#numeroDocumento").on("input", function() {
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