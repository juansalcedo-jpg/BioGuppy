<div class="mt-3 text-center">
  <h3>Lista de Usuarios</h3>
</div>

<div class="mt-5">
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Apellido</th>
        <th>Correo</th>
        <th>Rol</th>
        <th>Tipo Documento</th>
        <th>Número Documento</th>
        <th>Editar</th>
        <th>Estado</th>
      </tr>
    </thead>
    <tbody>
      <?php
        while($usu = $usuarios->fetch(PDO::FETCH_ASSOC)){
            echo "<tr>";
                echo "<td>".$usu['nombreusuario']."</td>";
                echo "<td>".$usu['apellidousuario']."</td>";
                echo "<td>".$usu['correo']."</td>";
                echo "<td>".$usu['nombrerol']."</td>";
                echo "<td>".$usu['nombredocumento']."</td>";
                echo "<td>".$usu['numerodocumento']."</td>";
                echo "<td>
                        <a href='".getUrl("Usuarios","Usuarios","getUpdateUsu",array("id"=>$usu['codusuario']))."'>
                            <button class='btn btn-primary'>Editar</button>
                        </a>
                      </td>";
                echo "<td>";
                if ($usu['estado'] === 'I') {
                    echo "<a href='".getUrl("Usuarios","Usuarios","activacion",array("id"=>$usu['codusuario'], "estado"=>$usu['estado']))."'>
                            <button class='btn btn-success'>Activar</button>
                          </a>";
                } elseif ($usu['estado'] === 'A') {
                    echo "<a href='".getUrl("Usuarios","Usuarios","activacion",array("id"=>$usu['codusuario'], "estado"=>$usu['estado']))."'>
                            <button class='btn btn-danger'>Inactivar</button>
                          </a>";
                }
                echo "</td>";
            echo "</tr>";
        }
        ?>
    </tbody>
  </table>
</div>
<?php
  if(isset($_SESSION['error'])){
    echo '<div class="d-flex justify-content-center">';
      echo '<div class="alert alert-danger text-center col-md-4 mt-3 mb-3" role="alert">'
          . $_SESSION['error'] .
          '</div>';
    echo '</div>';
      unset($_SESSION['error']);
  }else if(isset($_SESSION['exito'])){
    echo '<div class="d-flex justify-content-center">';
      echo '<div class="alert alert-success text-center col-md-4 mt-3 mb-3" role="alert">'
          . $_SESSION['exito'] .
          '</div>';
    echo '</div>';
      unset($_SESSION['exito']);
  }
?>