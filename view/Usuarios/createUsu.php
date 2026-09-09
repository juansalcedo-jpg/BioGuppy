<div class="mt-3 text-center">
    <h3 class="display-4">Registrar Usuario</h3>
</div>

<div class="mt-5">
  <form action="<?php echo getUrl("Ciudades","Ciudades","postCreate")?>" method="post">
    <div class="row mb-3">
      <div class="col-md-6">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Juan">
      </div>
      <div class="col-md-6">
        <label for="apellido" class="form-label">Apellido</label>
        <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Pérez">
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label for="tipoDocumento" class="form-label">Tipo de documento</label>
        <select class="form-select" id="tipoDocumento" name="tipoDocumento">
          <option selected disabled>Seleccione...</option>
          <option value="cc">Cédula de ciudadanía</option>
          <option value="ti">Tarjeta de identidad</option>
          <option value="ce">Cédula de extranjería</option>
          <option value="pasaporte">Pasaporte</option>
        </select>
      </div>
      <div class="col-md-6">
        <label for="numeroDocumento" class="form-label">Número de documento</label>
        <input type="text" class="form-control" id="numeroDocumento" name="numeroDocumento" placeholder="1234567890">
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label for="correo" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="correo" name="correo" placeholder="usuario@ejemplo.com">
      </div>
      <div class="col-md-6">
        <label for="celular" class="form-label">Celular</label>
        <input type="tel" class="form-control" id="celular" name="celular" placeholder="3001234567">
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label for="rol" class="form-label">Rol</label>
        <select class="form-select" id="rol" name="rol">
          <option selected disabled>Seleccione...</option>
          <option value="admin">Administrador</option>
          <option value="user">Usuario</option>
          <option value="guest">Invitado</option>
        </select>
      </div>
      <div class="col-md-6">
        <label for="passwordTemp" class="form-label">Contraseña temporal (Número Documento)</label>
        <input type="text" class="form-control" id="passwordTemp" name="passwordTemp" placeholder="1234567890" disabled>
      </div>
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-primary">Registrar</button>
    </div>
  </form>
</div>


