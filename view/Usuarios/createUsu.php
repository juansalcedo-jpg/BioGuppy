<div class="mt-3 text-center">
    <h3 class="display-4">Registrar Usuario</h3>
</div>

<div class="mt-5">
  <form action="<?php echo getUrl('Usuarios','Usuarios','postcreateUsu')?>" method="post">
    <div class="row mb-3">
      <div class="col-md-6">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese su nombre">
      </div>
      <div class="col-md-6">
        <label for="apellido" class="form-label">Apellido</label>
        <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ingrese su apellido">
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label for="tipoDocumento" class="form-label">Tipo de documento</label>
        <select class="form-select" id="tipoDocumento" name="tipoDocumento">
          <option selected disabled>Seleccione...</option>
          <?php
                while($tipoDocu = $resultdocu->fetch(PDO::FETCH_ASSOC)){
                    echo "<option value='".$tipoDocu['codtipodocumento']."'>".$tipoDocu['nombredocumento']."</option>";
                }
            ?>
        </select>
      </div>
      <div class="col-md-6">
        <label for="numeroDocumento" class="form-label">Número de documento</label>
        <input type="text" class="form-control" id="numeroDocumento" name="numeroDocumento" placeholder="Ingrese su número de documento">
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label for="correo" class="form-label">Correo electrónico</label>
        <input type="email" class="form-control" id="correo" name="correo" placeholder="usuario@ejemplo.com">
      </div>
      <div class="col-md-6">
        <label for="celular" class="form-label">Celular</label>
        <input type="tel" class="form-control" id="celular" name="celular" placeholder="Ingrese su celular">
      </div>
    </div>

    <div class="row mb-3">
      <div class="col-md-6">
        <label for="rol" class="form-label">Rol</label>
        <select class="form-select" id="rol" name="rol">
          <option selected disabled>Seleccione...</option>
          <?php
                while($tipoRol = $resultrol->fetch(PDO::FETCH_ASSOC)){
                    echo "<option value='".$tipoRol['codrol']."'>".$tipoRol['nombrerol']."</option>";
                }
            ?>
        </select>
      </div>
      <div class="col-md-6">
        <label for="contraseñaTemp" class="form-label">Contraseña temporal (Número Documento)</label>
        <input type="text" class="form-control" id="contraseñaTemp" name="contraseñaTemp" placeholder="Se llenará con el numero de documento que ingrese" readonly>
      </div>
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-primary">Registrar</button>
    </div>
  </form>
</div>
<?php
  if(isset($_SESSION['error'])){
    echo '<div class="d-flex justify-content-center">';
      echo '<div class="alert alert-danger text-center col-md-4 mt-3 mb-3" role="alert">'
          . $_SESSION['error'] .
          '</div>';
    echo '</div>';
      unset($_SESSION['error']);
  }
?>




