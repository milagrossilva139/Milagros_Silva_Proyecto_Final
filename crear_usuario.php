<?php
require_once __DIR__ . '/conexion.php';

$error = "";
$exito = "";

$nombre = "";
$apellido = "";
$dni = "";
$email = "";
$password = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre   = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST['apellido'] ?? '');
    $dni      = trim($_POST['dni'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';

    if ($nombre === "" || $apellido === "" || $dni === "" || $email === "" || $password === "") {
        $error = "Completá todos los campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Ingresá un correo electrónico válido.";
    } elseif (!preg_match('/^[0-9]{7,8}$/', $dni)) {
        $error = "El DNI debe tener entre 7 y 8 números.";
    } elseif (strlen($password) < 8) {
        $error = "La contraseña debe tener al menos 8 caracteres.";
    } else {
        try {
            $sql = "INSERT INTO usuarios (nombre, apellido, dni, email, password, rol, estado) VALUES (:nombre, :apellido, :dni, :email, :password, 'usuario', 'activo')";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'dni' => $dni,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
            ]);
            
            $exito = "Usuario registrado correctamente.";

            $nombre = "";
            $apellido = "";
            $dni = "";
            $email = "";
            $password = "";

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = "El DNI o el correo electrónico ya se encuentran registrados.";
            } else {
                error_log($e->getMessage());
                $error = "No se pudo registrar el usuario. Por favor, intentá nuevamente.";
            }
        }
    }
}

function escapar(string $valor): string {
    return htmlspecialchars($valor, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
        <header class="form-header">
            <span class="badge-sistema">REGISTRO</span>
            <h2>Crear Usuario</h2>
        </header>

        <?php if ($error !== ""): ?>
            <div class="alerta error"><?= escapar($error) ?></div>
        <?php endif; ?>

        <?php if ($exito !== ""): ?>
            <div class="alerta exito"><?= escapar($exito) ?></div>
        <?php endif; ?>

        <form action="crear_usuario.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?= escapar($nombre) ?>" maxlength="80" required>
            </div>

            <div class="form-group">
                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" value="<?= escapar($apellido) ?>" maxlength="80" required>
            </div>

            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" inputmode="numeric" pattern="[0-9]{7,8}" maxlength="8" value="<?= escapar($dni) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" maxlength="254" value="<?= escapar($email) ?>" required>
            </div>

            <div class="form-group">
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
            </div>

            <button type="submit" class="btn">Registrarse</button>
        </form>

        <footer class="form-footer">
            <p style="text-align: center; margin-top: 15px;">¿Ya tenés una cuenta? <a href="index.php">Iniciar Sesión</a></p>
        </footer>
    </main>
</body>
</html>