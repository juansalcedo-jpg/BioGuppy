<style>
  .pie-bioguppy {
    background-color: #10254a;
    color: #c9d4e6;
    margin-left: var(--sidebar-width);
    padding: 8px 6px;
    transition: margin-left .25s ease;
    font-family: 'Segoe UI', Arial, sans-serif;
    text-align: center;
  }

  .app-layout.sidebar-collapsed~.pie-bioguppy {
    margin-left: 0;
  }

  .pie-bioguppy .pie-titulo {
    color: #fff;
    font-weight: 600;
    font-size: .65rem;
    margin-bottom: 4px;
    text-transform: uppercase;
    letter-spacing: .02em;
  }

  .pie-bioguppy ul {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin: 0;
    padding: 0;
    list-style: none;
  }

  .pie-bioguppy li {
    font-size: .65rem;
  }

  .pie-bioguppy a {
    color: #c9d4e6;
    text-decoration: none;
  }

  .pie-bioguppy a:hover {
    color: #159EE8;
  }

  .pie-bioguppy hr {
    border: none;
    border-top: 1px solid rgba(255, 255, 255, 0.15);
    margin: 6px auto 4px;
    max-width: 160px;
  }

  .pie-bioguppy .pie-copy {
    font-size: .6rem;
    color: rgba(255, 255, 255, 0.5);
  }

  @media (max-width: 768px) {
    .pie-bioguppy {
      margin-left: 0;
      padding: 6px 4px;
    }

    .pie-bioguppy ul {
      flex-direction: column;
      gap: 4px;
    }
  }
</style>

<footer class="pie-bioguppy">
  <div class="pie-titulo">Contacto</div>
  <ul>
    <li><i class="bi bi-envelope me-1"></i> bioguppy@gmail.com</li>
    <li><i class="bi bi-geo-alt me-1"></i> Santiago de Cali, Colombia</li>
  </ul>
  <hr>
  <div class="pie-copy">© <?php echo date('Y'); ?> BioGuppy — Todos los derechos reservados.</div>
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