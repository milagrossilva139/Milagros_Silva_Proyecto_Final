<?php
session_start();
require "conexion.php";

if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
    http_response_code(403);
    exit('Acceso denegado. No sos administrador.');
}

$buscar = trim($_GET['buscar'] ?? '');
$sql = "SELECT id, nombre, apellido, dni, email, rol, fecha_creacion FROM usuarios WHERE estado = 'activo'";
$params = [];

if ($buscar !== "") {
    $sql .= " AND (nombre LIKE ? OR apellido LIKE ? OR dni LIKE ? OR email LIKE ?)";
    $term = "%$buscar%";
    $params = [$term, $term, $term, $term];
}
$sql .= " ORDER BY id DESC";
$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$usuarios = $stmt->fetchAll();

function escapar($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel Administrativo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="max-width: 1000px; margin: 20px auto;">
    <div style="background: #fff; padding: 20px; border-radius: 8px;">
        <h1>Panel de Administración</h1>
        <p>Admin: <?= escapar($_SESSION['nombre']) ?> | <a href="cerrar_sesion.php">Cerrar Sesión</a></p>
        <hr>
        <form method="GET" action="admin_usuarios.php" style="margin-bottom: 20px; display: flex; gap: 10px;">
            <input type="search" name="buscar" value="<?= escapar($buscar) ?>" placeholder="Buscar por nombre, apellido, DNI o correo">
            <button type="submit" style="width: 150px; margin-top:0;">Buscar</button>
        </form>
        <p><a href="usuarios_archivados.php">Ver cuentas archivadas 📁</a></p>
        <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <thead>
                <tr style="background: #eee;">
                    <th>ID</th><th>Nombre y Apellido</th><th>DNI</th><th>Correo</th><th>Rol</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr><td colspan="6" style="text-align:center;">No se encontraron usuarios.</td></tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td><?= escapar($u['id']) ?></td>
                            <td><?= escapar($u['nombre'] . ' ' . $u['apellido']) ?></td>
                            <td><?= escapar($u['dni']) ?></td>
                            <td><?= escapar($u['email']) ?></td>
                            <td><?= escapar($u['rol']) ?></td>
                            <td>
                                <a href="editar_usuario.php?id=<?= $u['id'] ?>">Editar</a> | 
                                <form action="archivar_usuario.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="submit" onclick="return confirm('¿Archivar usuario?')" style="background:none; border:none; color:red; cursor:pointer; padding:0; width:auto;">Archivar</button>
                                </form> | 
                                <form action="eliminar_usuario.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                    <button type="submit" onclick="return confirm('¿Estás seguro de eliminar permanentemente este usuario?')" style="background:none; border:none; color:darkred; cursor:pointer; padding:0; width:auto; font-weight:bold;">Eliminar</button>
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