<?php
session_start();

// Si ya hay sesión activa, redirigir directo al inventario
if (isset($_SESSION['usuario_id'])) {
    header("Location: inventario.php");
    exit();
}

require_once 'database.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Por favor completa todos los campos.";
    } else {
        $db   = new Database();
        $conn = $db->conn;

      $stmt = $conn->prepare("SELECT id, nombre, email, password FROM usuarios WHERE email = :email LIMIT 1");
      $stmt->bindParam(':email', $email);
      $stmt->execute();

      $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($usuario && password_verify($password, $usuario['password'])) {
          // Credenciales correctas — iniciar sesión
          $_SESSION['usuario_id']     = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];
            $_SESSION['usuario_email']  = $usuario['email'];

            header("Location: inventario.php");
            exit();
        } else {
            $error = "Correo o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Brangus · Inventario</title>
  <link rel="stylesheet" href="login.css" />
</head>
<body>

  <div class="login-wrap">

    <!-- Panel formulario -->
    <div class="login-panel">

      <div class="brand-mark">
        <img src="./LOGO.png" alt="Brangus" class="brand-logo" />
      </div>

      <h1 class="login-heading">Bienvenido</h1>
      <p class="login-sub">Ingresa tus credenciales para continuar</p>

      <?php if (!empty($error)): ?>
        <div class="login-error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form action="login.php" method="POST">

        <div class="field">
          <label for="email">Correo electrónico</label>
          <input id="email" name="email" type="email" placeholder="usuario@brangus.com"
                 value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required />
        </div>

        <div class="field">
          <label for="password">Contraseña</label>
          <input id="password" name="password" type="password" placeholder="••••••••"
                 class="<?php echo !empty($error) ? 'input-error' : ''; ?>" required />
        </div>

        <div class="forgot">
          <a href="#">¿Olvidaste tu contraseña?</a>
        </div>

        <button class="btn-login" type="submit">Ingresar</button>

      </form>

      <p class="login-footer">Sistema de gestión de inventario · v1.0</p>
    </div>

    <!-- Panel decorativo -->
    <div class="deco-panel">
      <div class="deco-circles"></div>
      <div class="deco-circles2"></div>
      <div class="deco-grid"></div>

      <div class="deco-stat">
        <div class="deco-stat-num">1,248</div>
        <div class="deco-stat-label">productos en inventario</div>
      </div>

      <div class="deco-bottom">
        <p class="deco-tagline">Control total<br>de tu inventario</p>
        <p class="deco-sub">Gestiona productos, precios y stock<br>en un solo lugar.</p>
      </div>
    </div>

  </div>

</body>
</html>
