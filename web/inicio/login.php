<?php
    include_once '../../Lib/helpers.php';
?>

    <link rel="stylesheet" href="styles.css">

    <div class="container">
        <div class="container-form">
            <form action="<?php echo '../' . getUrl("Acceso","Acceso","login",false,"ajax"); ?>" method="post">
                <h2>Iniciar Sesion</h2>
                <span>Ingrese su Usuario</span>
                <div class="container-input">
                    <ion-icon name="person-outline"></ion-icon>
                    <input type="text" placeholder="Usuario"  name = "usu_correo">
                </div>
                <div class="container-input">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" placeholder="Contraseña"  name = "usu_clave">
                </div>
                <a href="#">¿Olvidaste tu contraseña?</a>
                <input type="submit" class="input" value="INICIAR SESION">
            </form>
        </div>
        <?php
            if(isset($_SESSION['Error'])){
                echo "<div class='alert alert-danger'>".$_SESSION['Error']."</div>";
                unset($_SESSION['Error']);
            }
        ?>
        <div class="container-form">
            <form class="sign-up" action="<?php echo '../' . getUrl("Registro","Registro","register",false,"ajax"); ?>" method="post">
                <h2>Registrarse</h2>
                <span>Use su correo electronico para registrarse</span>
                <div class="container-input">
                    <ion-icon name="person-add-outline"></ion-icon>
                    <input type="text" placeholder="Nombre de Usuario">
                </div>
                <div class="container-input">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="text" placeholder="Correo Electronico">
                </div>
                <div class="container-input">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" placeholder="Contraseña">
                </div>
                <div class="container-input">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" placeholder="Confirme su contraseña">
                </div>
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