<?php
require_once __DIR__ . '/conexion.php';
session_start();

$errores = [];
$email = '';
$password = ''; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '') {
        $errores[] = 'El email es obligatorio.';
    }
    if ($password === '') {
        $errores[] = 'La contraseña es obligatoria.';
    } elseif (!preg_match('/^[0-9]{7,8}$/', $password)) {
        $errores[] = 'La contraseña debe tener entre 7 y 8 números.';
    }

    if (empty($errores)) {
        try {
            $sql = 'SELECT id, nombre, apellido, dni, email, password, rol 
                    FROM usuarios 
                    WHERE email = :email AND estado = "activo" 
                    LIMIT 1';
            
            $stmt = $conexion->prepare($sql);
            $stmt->execute([
                'email' => $email
            ]);
            $usuario = $stmt->fetch();

            if ($usuario !== false && password_verify($password, $usuario['password'])) {
                session_regenerate_id(true);

                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['nombre'] = $usuario['nombre'];
                $_SESSION['apellido'] = $usuario['apellido'];
                $_SESSION['dni'] = $usuario['dni'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['rol'] = $usuario['rol'];

                if ($usuario['rol'] === 'admin') {
                    header('Location: admin_usuarios.php');
                } else {
                    header('Location: panel.php');
                }
                exit;
            } else {
                $errores[] = 'Correo electrónico o contraseña incorrectos, o cuenta inactiva.';
            }

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errores[] = 'Ocurrió un error en el servidor al intentar iniciar sesión.';
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
    <title>Iniciar Sesión - Portal Institucional</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
        <header class="form-header">
            <span class="badge-sistema">ACCESO AL SISTEMA</span>
            <h2>Iniciar Sesión</h2>
            <p class="subtitulo">Ingresá tus credenciales para acceder</p>
        </header>

        <?php if (!empty($errores)): ?>
            <div class="alerta error">
                <ul style="margin-left: 20px;">
                    <?php foreach ($errores as $error): ?>
                        <li><?= escapar($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="index.php" method="POST">
            <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" value="<?= escapar($email) ?>" placeholder="ejemplo@correo.com" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn">Entrar</button>
        </form>

        <footer class="form-footer">
            <p style="text-align: center; margin-top: 15px;">¿No tenés una cuenta? <a href="crear_usuario.php">Registrate acá</a></p>
        </footer>
    </main>
</body>
</html>