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
        <th>Estado</th>
        <th>Editar</th>
        <th>Eliminar</th>
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
                echo "<td>".$usu['estado']."</td>";
                echo "<td>
                        <a href='".getUrl("Usuarios","Usuarios","getUpdate",array("id"=>$usu['numerodocumento']))."'>
                            <button class='btn btn-primary'>Editar</button>
                        </a>
                      </td>";
                echo "<td>
                        <a href='".getUrl("Usuarios","Usuarios","delete",array("id"=>$usu['numerodocumento']))."' 
                          class='btn btn-danger'
                          onclick=\"return confirm('¿Seguro que deseas eliminar al usuario ".$usu['nombreusuario']."?')\">
                          Eliminar
                        </a>
                      </td>";
            echo "</tr>";
        }
        ?>
    </tbody>
  </table>
</div>