<?php
$e = fn($v) => htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
$inicial = strtoupper(mb_substr($usuario['nombreusuario'], 0, 1));
?>
<div class="container-fluid px-1 py-1" style="max-width: 900px;">

  <?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show py-2 px-3 mb-3 border-0 shadow-sm rounded-3 bg-danger-subtle text-danger-emphasis" role="alert">
      <div class="d-flex align-items-center">
        <i class="bi bi-exclamation-triangle-fill fs-5 me-2"></i>
        <div><?php echo $e($_SESSION['error']); ?></div>
      </div>
      <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['error']); ?>
  <?php endif; ?>

  <?php if(isset($_SESSION['exito'])): ?>
    <div class="alert alert-success alert-dismissible fade show py-2 px-3 mb-3 border-0 shadow-sm rounded-3 bg-success-subtle text-success-emphasis" role="alert">
      <div class="d-flex align-items-center">
        <i class="bi bi-check-circle-fill fs-5 me-2"></i>
        <div><?php echo $e($_SESSION['exito']); ?></div>
      </div>
      <button type="button" class="btn-close btn-sm" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['exito']); ?>
  <?php endif; ?>

  <form action="<?php echo getUrl('Perfil', 'Perfil', 'postUpdatePerfil'); ?>" method="post" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="csrf" value="<?php echo $e($_SESSION['csrf_perfil']); ?>">

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

      <div class="perfil-banner"></div>
      <div class="card-body px-4 pt-0 pb-4">

        <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-end gap-3 perfil-cabecera">
          <div class="position-relative">
            <div class="perfil-avatar rounded-circle border border-4 border-white shadow-sm bg-accent-perfil d-flex align-items-center justify-content-center overflow-hidden">
              <img id="fotoPreview" src="<?php echo $e($fotoPerfil); ?>" alt="Foto de perfil"
                   class="w-100 h-100 <?php echo $fotoPerfil ? '' : 'd-none'; ?>" style="object-fit: cover;">
              <span id="fotoInicial" class="text-white fw-bold fs-1 <?php echo $fotoPerfil ? 'd-none' : ''; ?>"><?php echo $e($inicial); ?></span>
            </div>
            <label for="foto" class="btn btn-primary btn-sm rounded-circle position-absolute bottom-0 end-0 shadow perfil-btn-foto"
                   title="Cambiar foto de perfil">
              <i class="bi bi-camera-fill"></i>
            </label>
            <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp" class="d-none">
          </div>

          <div class="text-center text-sm-start pb-1">
            <h2 class= "h4 fw-bold mb-1 text-white" ><?php echo $e($usuario['nombreusuario'] . ' ' . $usuario['apellidousuario']); ?></h2>
            <span class="badge rounded-pill bg-primary-subtle text-primary-emphasis px-3 py-2">
              <i class="bi bi-shield-check me-1"></i><?php echo $e($usuario['nombrerol']); ?>
            </span>
            <div class="small text-muted mt-2" id="fotoAyuda">JPG, PNG o WEBP · máximo 2 MB</div>
          </div>
        </div>

        <h3 class="h6 text-uppercase fw-bold text-secondary mt-4 mb-3 fs-7">
          <i class="bi bi-person-vcard me-1"></i> Información personal
        </h3>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label text-secondary fs-7 fw-bold text-uppercase mb-1">Nombre</label>
            <div class="input-group shadow-sm rounded-3 overflow-hidden border">
              <span class="input-group-text border-0 text-muted ps-3 bg-body-tertiary"><i class="bi bi-person"></i></span>
              <input type="text" class="form-control border-0 py-2 ps-2 shadow-none bg-body-tertiary" value="<?php echo $e($usuario['nombreusuario']); ?>" readonly disabled>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label text-secondary fs-7 fw-bold text-uppercase mb-1">Apellido</label>
            <div class="input-group shadow-sm rounded-3 overflow-hidden border">
              <span class="input-group-text border-0 text-muted ps-3 bg-body-tertiary"><i class="bi bi-person"></i></span>
              <input type="text" class="form-control border-0 py-2 ps-2 shadow-none bg-body-tertiary" value="<?php echo $e($usuario['apellidousuario']); ?>" readonly disabled>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label text-secondary fs-7 fw-bold text-uppercase mb-1">Tipo de documento</label>
            <div class="input-group shadow-sm rounded-3 overflow-hidden border">
              <span class="input-group-text border-0 text-muted ps-3 bg-body-tertiary"><i class="bi bi-card-text"></i></span>
              <input type="text" class="form-control border-0 py-2 ps-2 shadow-none bg-body-tertiary" value="<?php echo $e($usuario['nombredocumento']); ?>" readonly disabled>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label text-secondary fs-7 fw-bold text-uppercase mb-1">Número de documento</label>
            <div class="input-group shadow-sm rounded-3 overflow-hidden border">
              <span class="input-group-text border-0 text-muted ps-3 bg-body-tertiary"><i class="bi bi-credit-card-2-front"></i></span>
              <input type="text" class="form-control border-0 py-2 ps-2 shadow-none bg-body-tertiary" value="<?php echo $e($usuario['numerodocumento']); ?>" readonly disabled>
            </div>
          </div>
          <div class="col-md-12">
            <label class="form-label text-secondary fs-7 fw-bold text-uppercase mb-1">Rol</label>
            <div class="input-group shadow-sm rounded-3 overflow-hidden border">
              <span class="input-group-text border-0 text-muted ps-3 bg-body-tertiary"><i class="bi bi-shield-lock"></i></span>
              <input type="text" class="form-control border-0 py-2 ps-2 shadow-none bg-body-tertiary" value="<?php echo $e($usuario['nombrerol']); ?>" readonly disabled>
            </div>
          </div>
        </div>

        <h3 class="h6 text-uppercase fw-bold text-secondary mt-4 mb-3 fs-7">
          <i class="bi bi-pencil-square me-1"></i> Datos de la cuenta
        </h3>
        <div class="row g-3">
          <div class="col-md-12">
            <label for="correo" class="form-label text-secondary fs-7 fw-bold text-uppercase mb-1">Correo electrónico</label>
            <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
              <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-envelope"></i></span>
              <input type="email" class="form-control border-0 bg-white py-2 ps-2 shadow-none" id="correo" name="correo"
                     value="<?php echo $e($usuario['correo']); ?>" data-original="<?php echo $e($usuario['correo']); ?>" required>
            </div>
          </div>

          <div class="col-md-6">
            <label for="nuevaContrasena" class="form-label text-secondary fs-7 fw-bold text-uppercase mb-1">Nueva contraseña</label>
            <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
              <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-lock"></i></span>
              <input type="password" class="form-control border-0 bg-white py-2 ps-2 shadow-none" id="nuevaContrasena" name="nuevaContrasena"
                     placeholder="Déjala vacía para no cambiarla" autocomplete="new-password">
              <button type="button" class="btn bg-white border-0 text-muted ver-contra" data-target="nuevaContrasena"><i class="bi bi-eye"></i></button>
            </div>
            <div class="form-text fs-7">Mín. 8 caracteres, mayúscula, minúscula, número y símbolo (@#$%^&amp;*!).</div>
          </div>

          <div class="col-md-6">
            <label for="confirmarContrasena" class="form-label text-secondary fs-7 fw-bold text-uppercase mb-1">Confirmar contraseña</label>
            <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
              <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-lock-fill"></i></span>
              <input type="password" class="form-control border-0 bg-white py-2 ps-2 shadow-none" id="confirmarContrasena" name="confirmarContrasena"
                     placeholder="Repite la nueva contraseña" autocomplete="new-password">
              <button type="button" class="btn bg-white border-0 text-muted ver-contra" data-target="confirmarContrasena"><i class="bi bi-eye"></i></button>
            </div>
          </div>

          <div class="col-md-12 d-none" id="bloqueContraActual">
            <div class="p-3 rounded-3 bg-warning-subtle border border-warning-subtle">
              <label for="contrasenaActual" class="form-label text-warning-emphasis fs-7 fw-bold text-uppercase mb-1">
                <i class="bi bi-key me-1"></i> Contraseña actual
              </label>
              <div class="input-group shadow-sm rounded-3 overflow-hidden border bg-white">
                <span class="input-group-text bg-white border-0 text-muted ps-3"><i class="bi bi-key"></i></span>
                <input type="password" class="form-control border-0 bg-white py-2 ps-2 shadow-none" id="contrasenaActual" name="contrasenaActual"
                       autocomplete="current-password">
                <button type="button" class="btn bg-white border-0 text-muted ver-contra" data-target="contrasenaActual"><i class="bi bi-eye"></i></button>
              </div>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-end pt-3 border-top mt-4">
          <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-semibold shadow-sm">
            <i class="bi bi-check-lg me-1"></i> Guardar cambios
          </button>
        </div>

      </div>
    </div>
  </form>
</div>

<style>
  .perfil-banner {
    height: 110px;
    background: linear-gradient(135deg, #10254a 0%, #24406f 55%, #159EE8 100%);
  }
  .perfil-cabecera { margin-top: -55px; }
  .perfil-avatar { width: 120px; height: 120px; }
  .bg-accent-perfil { background-color: #159EE8; }
  .perfil-btn-foto {
    width: 36px; height: 36px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer;
  }
  .input-group:focus-within, .border:focus-within {
    border-color: var(--bs-primary) !important;
    box-shadow: 0 0 0 0.25rem rgba(var(--bs-primary-rgb), 0.15) !important;
  }
  .input-group:focus-within .input-group-text { color: var(--bs-primary) !important; }
  .fs-7 { font-size: 0.75rem; }
</style>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const inputFoto   = document.getElementById("foto");
    const preview     = document.getElementById("fotoPreview");
    const inicial     = document.getElementById("fotoInicial");
    const ayuda       = document.getElementById("fotoAyuda");
    const correo      = document.getElementById("correo");
    const nueva       = document.getElementById("nuevaContrasena");
    const confirmar   = document.getElementById("confirmarContrasena");
    const bloqueActual = document.getElementById("bloqueContraActual");

    inputFoto.addEventListener("change", function () {
      const archivo = this.files[0];
      if (!archivo) return;

      if (archivo.size > 2 * 1024 * 1024) {
        ayuda.textContent = "La foto supera los 2 MB.";
        ayuda.classList.add("text-danger");
        this.value = "";
        return;
      }
      ayuda.textContent = "Nueva foto: " + archivo.name + " (se guarda al presionar Guardar cambios)";
      ayuda.classList.remove("text-danger");

      preview.src = URL.createObjectURL(archivo);
      preview.classList.remove("d-none");
      inicial.classList.add("d-none");
    });

    function revisarCambios() {
      const cambia = correo.value.trim() !== correo.dataset.original || nueva.value !== "" || confirmar.value !== "";
      bloqueActual.classList.toggle("d-none", !cambia);
    }
    [correo, nueva, confirmar].forEach(el => el.addEventListener("input", revisarCambios));

    document.querySelectorAll(".ver-contra").forEach(btn => {
      btn.addEventListener("click", function () {
        const input = document.getElementById(this.dataset.target);
        const icono = this.querySelector("i");
        const oculto = input.type === "password";
        input.type = oculto ? "text" : "password";
        icono.className = oculto ? "bi bi-eye-slash" : "bi bi-eye";
      });
    });
  });
</script>
