<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { exit('ID inválido.'); }

$error = ""; 
$exito = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST["nombre"] ?? "");
    $apellido = trim($_POST["apellido"] ?? "");
    $dni = trim($_POST["dni"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if ($nombre === "" || $apellido === "" || $dni === "" || $email === "") {
        $error = "Todos los campos son obligatorios.";
    } else {
        try {
            $stmt = $conexion->prepare("UPDATE usuarios SET nombre = ?, apellido = ?, dni = ?, email = ? WHERE id = ? AND estado = 'activo'");
            $stmt->execute([$nombre, $apellido, $dni, $email, $id]);
            $exito = "Usuario actualizado correctamente. <a href='admin_usuarios.php'>Volver al panel</a>";
        } catch (PDOException $e) {
            $error = "Error al actualizar (DNI o correo duplicados).";
        }
    }
}

$stmt = $conexion->prepare("SELECT * FROM usuarios WHERE id = ? AND estado = 'activo' LIMIT 1");
$stmt->execute([$id]);
$u = $stmt->fetch();
if (!$u) { exit('Usuario no encontrado.'); }

function escapar($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
        <form action="editar_usuario.php?id=<?= $id ?>" method="POST">
            <h2>Editar Usuario</h2>
            <?php if ($error !== ""): ?><p class="alerta error"><?= escapar($error) ?></p><?php endif; ?>
            <?php if ($exito !== ""): ?><p class="alerta exito"><?= $exito ?></p><?php endif; ?>

            <div class="form-group">
                <label>Nombre</label>
                <input type="text" name="nombre" value="<?= escapar($u['nombre']) ?>" required>
            </div>
            <div class="form-group">
                <label>Apellido</label>
                <input type="text" name="apellido" value="<?= escapar($u['apellido']) ?>" required>
            </div>
            <div class="form-group">
                <label>DNI</label>
                <input type="text" name="dni" value="<?= escapar($u['dni']) ?>" maxlength="8" required>
            </div>
            <div class="form-group">
                <label>Correo electrónico</label>
                <input type="email" name="email" value="<?= escapar($u['email']) ?>" required>
            </div>

            <button type="submit" class="btn">Guardar Cambios</button>
            <p style="text-align:center; margin-top:10px;"><a href="admin_usuarios.php">Cancelar</a></p>
        </form>
    </main>
</body>
</html>