<style>
  .pie-bioguppy {
    background-color: #10254a;
    color: #c9d4e6;
    margin-left: var(--sidebar-width);
    padding: 32px 24px 20px;
    transition: margin-left .25s ease;
    font-family: 'Segoe UI', Arial, sans-serif;
    text-align: center;
  }
  .app-layout.sidebar-collapsed ~ .pie-bioguppy {
    margin-left: 0;
  }
  .pie-bioguppy .pie-titulo {
    color: #fff;
    font-weight: 700;
    font-size: .95rem;
    margin-bottom: 14px;
    text-transform: uppercase;
    letter-spacing: .05em;
  }
  .pie-bioguppy ul {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 32px;
    flex-wrap: wrap;
    margin: 0;
    padding: 0;
    list-style: none;
  }
  .pie-bioguppy li {
    font-size: .85rem;
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
    margin: 20px auto 12px;
    max-width: 320px;
  }
  .pie-bioguppy .pie-copy {
    font-size: .75rem;
    color: rgba(255, 255, 255, 0.5);
  }
  @media (max-width: 768px) {
    .pie-bioguppy {
      margin-left: 0;
    }
    .pie-bioguppy ul {
      flex-direction: column;
      gap: 8px;
    }
  }
</style>
<svg width="0" height="0" style="position:absolute">
  <defs>
    <filter id="filtro-protanopia">
      <feColorMatrix type="matrix" values="
        0.567, 0.433, 0,     0, 0
        0.558, 0.442, 0,     0, 0
        0,     0.242, 0.758, 0, 0
        0,     0,     0,     1, 0" />
    </filter>

    <filter id="filtro-deuteranopia">
      <feColorMatrix type="matrix" values="
        0.625, 0.375, 0,   0, 0
        0.7,   0.3,   0,   0, 0
        0,     0.3,   0.7, 0, 0
        0,     0,     0,   1, 0" />
    </filter>

    <filter id="filtro-tritanopia">
      <feColorMatrix type="matrix" values="
        0.95, 0.05,  0,     0, 0
        0,    0.433, 0.567, 0, 0
        0,    0.475, 0.525, 0, 0
        0,    0,     0,     1, 0" />
    </filter>
  </defs>
</svg>
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