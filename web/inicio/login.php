<?php
    include_once '../../Lib/helpers.php';
?>

    <link rel="stylesheet" href="styles.css">

    <div class="container<?php if(isset($_SESSION['MostrarRegistro'])){ echo ' toggle'; unset($_SESSION['MostrarRegistro']); } ?>">
        <div class="container-form">
            <form class="sign-in" action="<?php echo '../' . getUrl("Acceso","Acceso","login",false,"ajax"); ?>" method="post">
                <h2>Iniciar Sesion</h2>
                <span>Ingrese su Usuario</span>
                <div class="container-input">
                    <ion-icon name="person-outline"></ion-icon>
                    <input type="text" placeholder="Correo"  name = "usu_correo">
                </div>
                <div class="container-input">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" placeholder="Contraseña"  name = "usu_clave">
                </div>
                <?php
                    if(isset($_SESSION['ErrorLogin'])){
                        echo "<div class='error-container'>".$_SESSION['ErrorLogin']."</div>";
                        unset($_SESSION['ErrorLogin']);
                    }
                ?>
                <a href="#">¿Olvidaste tu contraseña?</a>
                <input type="submit" class="input" value="INICIAR SESION">
            </form>
        </div>
        <div class="container-form">
            <form class="sign-up" action="<?php echo '../' . getUrl("Registro","Registro","register",false,"ajax"); ?>" method="post">
                <h2>Registrarse</h2>
                <span>Llene toda la información para registrarse</span>
                <div class="container-input">
                    <ion-icon name="person-add-outline"></ion-icon>
                    <input type="text" placeholder="Nombres*" name = "usu_nombre">
                </div>
                <div class="container-input">
                    <ion-icon name="person-add-outline"></ion-icon>
                    <input type="text" placeholder="Apellidos*" name = "usu_apellido">
                </div>
                <div class="container-input">
                    <ion-icon name="id-card-outline"></ion-icon>
                    <input type="text" placeholder="Cedula*" name = "usu_cedula">
                </div>
                <div class="container-input">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="text" placeholder="Correo Electronico*" name = "usu_correo">
                </div>
                <div class="container-input">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" placeholder="Contraseña*" name = "usu_clave1">
                </div>
                <div class="container-input">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" placeholder="Confirme su contraseña*" name = "usu_clave2">
                </div>
                <?php
                    if(isset($_SESSION['ErrorDatos'])){
                        echo "<div class='error-container'>".$_SESSION['ErrorDatos']."</div>";
                        unset($_SESSION['ErrorDatos']);
                    }
                    if(isset($_SESSION['ErrorValidacion'])){
                        echo "<div class='error-container'>".$_SESSION['ErrorValidacion']."</div>";
                        unset($_SESSION['ErrorValidacion']);
                    }
                    if(isset($_SESSION['ConfirmarRegistro'])){
                        echo "<div class='success-container'>".$_SESSION['ConfirmarRegistro']."</div>";
                        unset($_SESSION['ConfirmarRegistro']);
                    }
                ?>
                <input type="submit" class="input" value="REGISTRARSE">
            </form>
        </div>
        <div class="container-welcome">
            <div class="welcome-sign-up welcome">
                <h3>¡Bienvenido!</h3>
                <p>Ingrese sus datos personales para usar todas las funciones del sitio</p>
                <button class="button" id="btn-sign-up">Registrarse</button>
            </div>
            <div class="welcome-sign-in welcome">
                <h3>¡Hola!</h3>
                <p>Registrese con sus datos personales para usar todas las funciones del sitio</p>
                <button class="button" id="btn-sign-in">Iniciar Sesion</button>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@8.0.13/dist/ionicons/ionicons.js"></script>