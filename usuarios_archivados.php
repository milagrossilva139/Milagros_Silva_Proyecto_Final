<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado.');
}

$stmt = $conexion->prepare("SELECT id, nombre, apellido, dni, email, archivado_en FROM usuarios WHERE archivado_en IS NOT NULL ORDER BY archivado_en DESC");
$stmt->execute();
$archivados = $stmt->fetchAll();

function escapar($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios Archivados</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="max-width: 900px; margin: 20px auto;">
    <div style="background: #fff; padding: 20px; border-radius: 8px;">
        <h1>Cuentas Archivadas</h1>
        <p><a href="admin_usuarios.php">← Volver al panel principal</a></p>
        <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr style="background: #eee;">
                    <th>ID</th><th>Nombre y Apellido</th><th>DNI</th><th>Correo</th><th>Fecha Archivado</th><th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($archivados)): ?>
                    <tr><td colspan="6" style="text-align:center;">No hay usuarios archivados.</td></tr>
                <?php else: ?>
                    <?php foreach ($archivados as $u): ?>
                        <tr>
                            <td><?= escapar($u['id']) ?></td>
                            <td><?= escapar($u['nombre'] . ' ' . $u['apellido']) ?></td>
                            <td><?= escapar($u['dni']) ?></td>
                            <td><?= escapar($u['email']) ?></td>
                            <td><?= escapar($u['archivado_en']) ?></td>
                            <td>
                                <form action="reactivar_usuario.php" method="POST">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="submit" style="background: #28a745; width: auto; padding: 5px 10px; font-size: 14px;">Reactivar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>